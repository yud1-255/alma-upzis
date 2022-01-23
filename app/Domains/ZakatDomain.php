<?php

namespace App\Domains;

use App\Models\SequenceNumber;
use App\Models\Zakat;
use App\Models\Family;
use App\Models\Muzakki;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Validation\ValidationException;

use DB;

class ZakatDomain
{
    protected $errors = [];

    public function submitAsMuzakki(User $user, Zakat $zakat, array $zakatLines): Zakat
    {
        $zakat->zakatPIC()->associate(null);
        $zakat->receiveFrom()->associate($user);
        $zakat->transaction_no = $this->generateZakatNumber(true);

        $zakat->save();

        $zakat->zakatLines()->createMany($zakatLines);

        return $zakat;
    }

    public function deleteTransaction(Zakat $zakat)
    {
        $zakat->zakatLines()->delete();
        $zakat->delete();
    }

    public function transactionSummary(): Builder
    {
        $zakats = DB::table('zakats')
            ->join('users as user_receive_from', 'user_receive_from.id', '=', 'zakats.receive_from')
            ->leftJoin('users as user_zakat_pic', 'user_zakat_pic.id', '=', 'zakats.zakat_pic')
            ->orderBy('transaction_no', 'desc')
            ->select([
                'zakats.*',
                'user_receive_from.name as receive_from_name',
                'user_zakat_pic.name as zakat_pic_name'
            ]);

        return $zakats;
    }

    public function ownTransactionSummary(User $user): Builder
    {
        $zakats = DB::table('zakats')
            ->join('users as user_receive_from', 'user_receive_from.id', '=', 'zakats.receive_from')
            ->leftJoin('users as user_zakat_pic', 'user_zakat_pic.id', '=', 'zakats.zakat_pic')
            ->where('receive_from', $user->id)
            ->orderBy('transaction_no', 'desc')
            ->select([
                'zakats.*',
                'user_receive_from.name as receive_from_name',
                'user_zakat_pic.name as zakat_pic_name'
            ]);

        return $zakats;
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

    public function confirmZakatPayment(User $user, Zakat $zakat): Zakat
    {
        if ($user->can('confirmPayment', $zakat)) {
            $zakat->zakatPIC()->associate($user);
            $zakat->save();
        }
        return $zakat;
    }

    public function registerFamily(User $user, Family $family)
    {
        $family->save();

        $user->family()->associate($family);
        $user->save();
    }


    public function deleteMuzakki(User $user, Muzakki $muzakki)
    {
        if (!$this->validateForDeletion($user, $muzakki)) {
            throw ValidationException::withMessages($this->errors);
        }

        $muzakki->delete();
    }

    private function validateForDeletion(User $user, Muzakki $muzakki): bool
    {
        if ($muzakki->family != $user->family) {
            array_push($this->errors, "Muzakki {$muzakki->name} hanya bisa diubah oleh anggota keluarga");
        }
        if (!$muzakki->zakatLines->isEmpty()) {
            array_push($this->errors, "Muzakki {$muzakki->name} sudah memiliki transaksi zakat");
            return false;
        }
        return true;
    }
}
