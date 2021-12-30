<?php

namespace App\Domains;

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

        $zakat->save();

        $zakat->zakatLines()->createMany($zakatLines);

        return $zakat;
    }

    public function deleteTransaction(Zakat $zakat)
    {
        $zakat->zakatLines()->delete();
        $zakat->delete();
    }

    public function transactionSummaryList(): Builder
    {
        $zakats = DB::table('zakats')
            ->join('users as user_receive_from', 'user_receive_from.id', '=', 'zakats.receive_from')
            ->leftJoin('users as user_zakat_pic', 'user_zakat_pic.id', '=', 'zakats.zakat_pic')
            ->orderBy('updated_at', 'desc')
            ->select([
                'zakats.*',
                'user_receive_from.name as receive_from_name',
                'user_zakat_pic.name as zakat_pic_name'
            ]);

        return $zakats;
    }
}
