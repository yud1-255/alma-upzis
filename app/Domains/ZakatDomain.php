<?php

namespace App\Domains;

use App\Models\AppConfig;
use App\Models\SequenceNumber;
use App\Models\Zakat;
use App\Models\Family;
use App\Models\Muzakki;
use App\Models\User;
use App\Models\ZakatLog;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\ValidationException;

use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;

class ZakatDomain
{
    protected $errors = [];
    private $user;

    // TODO refactor user-dependent domain logic to class variable
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function isInBankTransferPeriod(Carbon $date)
    {
        $startCfg = AppConfig::getConfigValue('remove_transfer_start_date');
        $endCfg = AppConfig::getConfigValue('remove_transfer_end_date');

        $startDate = Date::createFromFormat('Y-m-d', $startCfg);
        $endDate = Date::createFromFormat('Y-m-d', $endCfg);

        return !$date->between($startDate, $endDate);
    }

    public function isInQRISPaymentPeriod(Carbon $date)
    {
        $startCfg = AppConfig::getConfigValue('remove_qr_start_date');
        $endCfg = AppConfig::getConfigValue('remove_qr_end_date');

        $startDate = Date::createFromFormat('Y-m-d', $startCfg);
        $endDate = Date::createFromFormat('Y-m-d', $endCfg);

        return !$date->between($startDate, $endDate);
    }

    public function submitAsMuzakki(Zakat $zakat, array $zakatLines): Zakat
    {
        if (!$this->validateZakatForSubmission($zakat, $zakatLines)) {
            throw ValidationException::withMessages($this->errors);
        }

        $zakat->zakatPIC()->associate(null);
        $zakat->receiveFrom()->associate($this->user);
        $zakat->receive_from_name = $this->user->name;
        $zakat->is_offline_submission = false;
        $zakat->transaction_no = $this->generateZakatNumber(true);

        $uniqueNumber = rand(0, 500);

        if ($zakat->total_rp == 0) { // for fitrah kg/lt, set total transfer as 0
            $zakat->unique_number = 0;
            $zakat->total_transfer_rp = $zakat->total_rp;
        } else {
            $zakat->unique_number = $uniqueNumber;
            $zakat->total_transfer_rp = $zakat->total_rp + $uniqueNumber;
        }

        $zakat->save();

        $zakat->zakatLines()->createMany($zakatLines);

        $this->addActivityLog($zakat, ZakatLog::ACTIONS['submit']);

        return $zakat;
    }

    public function submitAsUpzis(Zakat $zakat, array $zakatLines): Zakat
    {
        if (!$this->validateZakatForSubmission($zakat, $zakatLines)) {
            throw ValidationException::withMessages($this->errors);
        }

        $zakat->zakatPIC()->associate($this->user);
        $zakat->receiveFrom()->associate($this->user);

        $zakat->receive_from_name = $zakat->receive_from_name;
        $zakat->is_offline_submission = true;
        $zakat->transaction_no = $this->generateZakatNumber(true);

        $zakat->unique_number = 0;
        $zakat->total_transfer_rp = $zakat->total_rp;

        $zakat->save();

        $zakat->zakatLines()->createMany($zakatLines);

        $this->addActivityLog($zakat, ZakatLog::ACTIONS['submit']);

        return $zakat;
    }

    public function voidTransaction(Zakat $zakat)
    {
        $zakat->is_active = false;
        $zakat->save();

        $this->addActivityLog($zakat, ZakatLog::ACTIONS['void']);
    }

    public function deleteTransaction(Zakat $zakat)
    {
        if (!$this->validateZakatForDeletion($this->user, $zakat)) {
            throw ValidationException::withMessages($this->errors);
        }
        $zakat->zakatLines()->delete();
        $zakat->delete();
    }

    public function transactionSummary(string $searchTerm, string $hijriYear, bool $isActiveOnly): Builder
    {
        $zakats = DB::table('zakats')
            ->join('users as user_receive_from', 'user_receive_from.id', '=', 'zakats.receive_from')
            ->leftJoin('users as user_zakat_pic', 'user_zakat_pic.id', '=', 'zakats.zakat_pic')
            ->where(function ($q) use ($searchTerm) {
                $q->where('receive_from_name', 'like', "%{$searchTerm}%")
                    ->orWhere('user_zakat_pic.name', 'like', "%{$searchTerm}%")
                    ->orWhere('family_head', 'like', "%{$searchTerm}%");
            })
            ->where('hijri_year', $hijriYear)
            ->orderBy('transaction_no', 'desc')
            ->select([
                'zakats.*',
                'user_receive_from.name as receive_from_user_name',
                'user_zakat_pic.name as zakat_pic_name'
            ]);

        if ($isActiveOnly) {
            $zakats = $zakats->where('is_active', true);
        }

        return $zakats;
    }

    public function ownTransactionSummary(): Builder
    {
        $zakats = DB::table('zakats')
            ->join('users as user_receive_from', 'user_receive_from.id', '=', 'zakats.receive_from')
            ->leftJoin('users as user_zakat_pic', 'user_zakat_pic.id', '=', 'zakats.zakat_pic')
            ->where('receive_from', $this->user->id)
            ->where('is_active', true)
            ->orderBy('transaction_no', 'desc')
            ->select([
                'zakats.*',
                'user_receive_from.name as receive_from_user_name',
                'user_zakat_pic.name as zakat_pic_name'
            ]);

        return $zakats;
    }

    public function zakatTransactionRecap(string $searchTerm, string $hijriYear)
    {
        $zakats = DB::table('zakats')
            ->join('users as user_receive_from', 'user_receive_from.id', '=', 'zakats.receive_from')
            ->join('zakat_lines as zakat_lines', 'zakat_lines.zakat_id', '=', 'zakats.id')
            ->where(function ($q) use ($searchTerm) {
                $q->where('receive_from_name', 'like', "%{$searchTerm}%")
                    ->orWhere('family_head', 'like', "%{$searchTerm}%");
            })
            ->where('hijri_year', $hijriYear)
            ->where('zakats.is_active', true)
            ->whereNotNull('zakat_pic')
            ->groupBy('zakats.id', 'transaction_date', 'payment_date', 'transaction_no', 'receive_from_name', 'unique_number', 'total_transfer_rp')
            ->selectRaw('zakats.id, transaction_date, payment_date, transaction_no, receive_from_name, unique_number, total_transfer_rp, ' .
                'sum(fitrah_rp) as fitrah_rp, sum(fitrah_kg) as fitrah_kg, sum(fitrah_lt) as fitrah_lt, ' .
                'sum(maal_rp) as maal_rp, sum(profesi_rp) as profesi_rp, sum(infaq_rp) as infaq_rp, sum(wakaf_rp) as wakaf_rp,' .
                'sum(fidyah_kg) as fidyah_kg, sum(fidyah_rp) as fidyah_rp, sum(kafarat_rp) as kafarat_rp');

        return $zakats;
    }

    public function zakatMuzakkiRecap(string $searchTerm, string $hijriYear): Builder
    {
        $zakats = DB::table('zakats')
            ->join('users as user_receive_from', 'user_receive_from.id', '=', 'zakats.receive_from')
            ->join('zakat_lines as zakat_lines', 'zakat_lines.zakat_id', '=', 'zakats.id')
            ->join('muzakkis as muzakkis', 'zakat_lines.muzakki_id', '=', 'muzakkis.id')
            ->join('users as user_zakat_pic', 'user_zakat_pic.id', '=', 'zakats.zakat_pic')
            ->where(function ($q) use ($searchTerm) {
                $q->where('receive_from_name', 'like', "%{$searchTerm}%")
                    ->orWhere('muzakkis.name', 'like', "%{$searchTerm}%")
                    ->orWhere('user_zakat_pic.name', 'like', "%{$searchTerm}%");
            })
            ->where('hijri_year', $hijriYear)
            ->where('zakats.is_active', true)
            ->orderBy('transaction_no', 'desc')
            ->select([
                'zakats.transaction_no',
                'zakats.transaction_date',
                'zakats.payment_date',
                'zakats.receive_from_name as receive_from_name',
                'zakat_lines.*',
                'muzakkis.name as muzakki_name',
                'user_zakat_pic.name as zakat_pic_name'
            ]);

        return $zakats;
    }

    public function zakatOnlinePayments(string $searchTerm, string $hijriYear): Builder
    {
        $zakats = DB::table('zakats')
            ->join('users as user_receive_from', 'user_receive_from.id', '=', 'zakats.receive_from')
            ->leftJoin('families', 'families.id', '=', 'user_receive_from.family_id')
            ->leftJoin('users as user_zakat_pic', 'user_zakat_pic.id', '=', 'zakats.zakat_pic')
            ->where('is_offline_submission', false)
            ->where('receive_from_name', 'like', "%{$searchTerm}%")
            ->where('hijri_year', $hijriYear)
            ->where('is_active', true)
            ->orderBy('transaction_no', 'desc')
            ->select([
                'zakats.*',
                'families.phone as receive_from_phone',
                'families.address as receive_from_address',
                'user_receive_from.email as receive_from_email',
                'user_receive_from.name as receive_from_user_name',
                'user_zakat_pic.name as zakat_pic_name'
            ]);

        return $zakats;
    }

    public function confirmZakatPayment(User $user, Zakat $zakat, Carbon $paymentDate): Zakat
    {
        if ($user->can('confirmPayment', $zakat)) {
            $zakat->zakatPIC()->associate($user);
            $zakat->payment_date = $paymentDate;
            $zakat->save();
        }

        $this->addActivityLog($zakat, ZakatLog::ACTIONS['confirm']);
        return $zakat;
    }

    public function registerUserFamily(User $user, Family $family)
    {
        $family->save();

        $user->family()->associate($family);
        $user->save();

        if ($family->muzakkis->count() == 0) {
            $muzakki = new Muzakki();
            $muzakki->name = $family->head_of_family;
            $muzakki->phone = $family->phone;
            $muzakki->address = $family->address;
            $muzakki->is_bpi = $family->is_bpi;
            $muzakki->bpi_block_no = $family->bpi_block_no;
            $muzakki->bpi_house_no = $family->bpi_house_no;
            $muzakki->is_active = true;


            $muzakki->family()->associate($family);
            $muzakki->save();
        }
    }

    public function registerFamily(Family $family)
    {
        $family->save();

        $muzakki = new Muzakki();
        $muzakki->name = $family->head_of_family;
        $muzakki->phone = $family->phone;
        $muzakki->address = $family->address;
        $muzakki->is_bpi = $family->is_bpi;
        $muzakki->bpi_block_no = $family->bpi_block_no;
        $muzakki->bpi_house_no = $family->bpi_house_no;
        $muzakki->is_active = true;


        $muzakki->family()->associate($family);
        $muzakki->save();
    }


    public function deactivateMuzakki(User $user, Muzakki $muzakki)
    {
        if (!$this->validateMuzakkiForDeletion($user, $muzakki)) {
            throw ValidationException::withMessages($this->errors);
        }

        $muzakki->is_active = false;
        $muzakki->save();
    }

    public function generateZakatNumber(bool $save): string
    {
        $sequence = SequenceNumber::where('type', 'zakat')->first();
        $format = $sequence->format;

        $lastNumber = $sequence->last_number;
        $nextNumber = $lastNumber + 1;

        $nextFormat = str_replace(
            ['%year%', '%seq%'],
            [(string)date("Y"), str_pad((string)$nextNumber, 3, "0", STR_PAD_LEFT)],
            $format
        );

        if ($save) {
            $sequence->last_number = $lastNumber + 1;
            $sequence->save();
        }

        return $nextFormat;
    }

    public function getHijriYears(): array
    {
        return range(AppConfig::getConfigValue('hijri_year_beginning'), AppConfig::getConfigValue('hijri_year'));
    }


    private function validateMuzakkiForDeletion(User $user, Muzakki $muzakki): bool
    {
        if ($muzakki->family != $user->family) {
            array_push($this->errors, "Muzakki {$muzakki->name} hanya bisa diubah oleh anggota keluarga");
            return false;
        }
        return true;
    }

    private function validateZakatForSubmission(Zakat $zakat, array $zakatLines): bool
    {

        $totalRp = $zakat->total_rp;

        $totalKg = array_reduce($zakatLines, function ($totalKg, $item) {
            return $totalKg + $item['fitrah_kg'] + $item['fidyah_kg'];
        });

        $totalLt = array_reduce($zakatLines, function ($totalLt, $item) {
            return $totalLt + $item['fitrah_lt'];
        });

        if ($totalRp == 0 && $totalKg == 0 && $totalLt == 0) {
            array_push($this->errors, "Zakat harus memiliki jumlah dalam rupiah atau kilogram/liter beras");
        }

        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }

    private function validateZakatForDeletion(User $user, Zakat $zakat): bool
    {
        if ($user->can('delete', $zakat)) {
            array_push($this->errors, "Pengguna {$user->name} tidak memiliki otorisasi hapus zakat");
        }

        if ($zakat->hijri_year != AppConfig::getConfigValue('hijri_year')) {
            array_push($this->errors, "Zakat {$zakat->transaction_no} hanya bisa dihapus pada periode zakat yang sama");
        }
        if ($zakat->zakat_pic != null) {
            array_push($this->errors, "Zakat {$zakat->transaction_no} tidak bisa dihapus karena telah dikonfirmasi oleh petugas");
        }

        if (count($this->errors) > 0) {
            return false;
        }

        return true;
    }

    private function addActivityLog(Zakat $zakat, int $action)
    {
        $zakatLog = new ZakatLog();
        $zakatLog->zakat()->associate($zakat);
        $zakatLog->user()->associate($this->user);
        $zakatLog->action = $action;

        $zakatLog->save();
    }

    public function getActivityLogs(Zakat $zakat): array
    {
        $logs = $zakat->zakatLogs()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($log) {
                $log->message = ZakatLog::MESSAGES[$log->action];
                $log->user_name = $log->user->name;

                $arr = $log->toArray();
                unset($arr['user']);
                return $arr;
            });

        return $logs->toArray();
    }
}
