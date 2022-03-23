<?php

namespace App\Domains;

use App\Models\AppConfig;
use App\Models\SequenceNumber;
use App\Models\Zakat;
use App\Models\Family;
use App\Models\Muzakki;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\ValidationException;

use DB;
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
        $zakat->zakatPIC()->associate(null);
        $zakat->receiveFrom()->associate($this->user);
        $zakat->receive_from_name = $this->user->name;
        $zakat->is_offline_submission = false;
        $zakat->transaction_no = $this->generateZakatNumber(true);

        $uniqueNumber = rand(0, 500);
        $zakat->unique_number = $uniqueNumber;
        $zakat->total_transfer_rp = $zakat->total_rp + $uniqueNumber;

        $zakat->save();

        $zakat->zakatLines()->createMany($zakatLines);

        return $zakat;
    }

    public function submitAsUpzis(Zakat $zakat, array $zakatLines): Zakat
    {
        $zakat->zakatPIC()->associate($this->user);
        $zakat->receiveFrom()->associate($this->user);

        $zakat->receive_from_name = $zakat->receive_from_name;
        $zakat->is_offline_submission = true;
        $zakat->transaction_no = $this->generateZakatNumber(true);

        $zakat->unique_number = 0;
        $zakat->total_transfer_rp = $zakat->total_rp;

        $zakat->save();

        $zakat->zakatLines()->createMany($zakatLines);

        return $zakat;
    }

    public function deleteTransaction(Zakat $zakat)
    {
        if (!$this->validateZakatForDeletion($this->user, $zakat)) {
            throw ValidationException::withMessages($this->errors);
        }
        $zakat->zakatLines()->delete();
        $zakat->delete();
    }

    public function transactionSummary(string $searchTerm, string $hijriYear): Builder
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

        return $zakats;
    }

    public function ownTransactionSummary(): Builder
    {
        $zakats = DB::table('zakats')
            ->join('users as user_receive_from', 'user_receive_from.id', '=', 'zakats.receive_from')
            ->leftJoin('users as user_zakat_pic', 'user_zakat_pic.id', '=', 'zakats.zakat_pic')
            ->where('receive_from', $this->user->id)
            ->orderBy('transaction_no', 'desc')
            ->select([
                'zakats.*',
                'user_receive_from.name as receive_from_user_name',
                'user_zakat_pic.name as zakat_pic_name'
            ]);

        return $zakats;
    }

    public function zakatMuzakkiRecap(string $searchTerm, string $hijriYear): Builder
    {
        $zakats = DB::table('zakats')
            ->join('users as user_receive_from', 'user_receive_from.id', '=', 'zakats.receive_from')
            ->join('zakat_lines as zakat_lines', 'zakat_lines.zakat_id', '=', 'zakats.id')
            ->join('muzakkis as muzakkis', 'zakat_lines.muzakki_id', '=', 'muzakkis.id')
            ->leftJoin('users as user_zakat_pic', 'user_zakat_pic.id', '=', 'zakats.zakat_pic')
            ->where(function ($q) use ($searchTerm) {
                $q->where('receive_from_name', 'like', "%{$searchTerm}%")
                    ->orWhere('muzakkis.name', 'like', "%{$searchTerm}%")
                    ->orWhere('user_zakat_pic.name', 'like', "%{$searchTerm}%");
            })
            ->where('hijri_year', $hijriYear)
            ->orderBy('transaction_no', 'desc')
            ->select([
                'zakats.transaction_no',
                'zakats.transaction_date',
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

    public function confirmZakatPayment(User $user, Zakat $zakat): Zakat
    {
        if ($user->can('confirmPayment', $zakat)) {
            $zakat->zakatPIC()->associate($user);
            $zakat->save();
        }
        return $zakat;
    }

    public function registerUserFamily(User $user, Family $family)
    {
        $family->save();

        $user->family()->associate($family);
        $user->save();

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

    private function validateZakatForDeletion(User $user, Zakat $zakat): bool
    {
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
}
