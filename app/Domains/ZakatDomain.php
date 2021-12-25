<?php

namespace App\Domains;

use App\Models\Zakat;
use App\Models\User;

use DB;
use phpDocumentor\Reflection\Types\Boolean;

class ZakatDomain
{
    public function submitAsMuzakki(User $user, Zakat $zakat): Zakat
    {
        // TODO implement submission logic (from muzakki; set zakat_pic as null)

        $zakat->zakatPIC()->associate(null);
        $zakat->receiveFrom()->associate($user);

        $zakat->save();

        return $zakat;
    }

    public function transactionSummaryList()
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
