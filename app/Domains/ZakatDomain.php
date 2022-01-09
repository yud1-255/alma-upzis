<?php

namespace App\Domains;

use App\Models\SequenceNumber;
use App\Models\Zakat;
use App\Models\User;
use Illuminate\Database\Query\Builder;

use DB;


class ZakatDomain
{
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
        $last_number = $sequence->last_number;
        $next_number = $last_number + 1;

        $next_format = str_replace(
            ['%year%', '%seq%'],
            [(string)date("Y"), str_pad((string)$next_number, 3, "0", STR_PAD_LEFT)],
            $format
        );

        if ($save) {
            $sequence->last_number = $last_number + 1;
            $sequence->save();
        }

        return $next_format;
    }
}
