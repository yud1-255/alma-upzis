<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZakatLog extends Model
{
    use HasFactory;

    public const ACTIONS = [
        'submit' => 1,
        'confirm' => 2,
        'void' => 3
    ];

    public const MESSAGES = [
        self::ACTIONS['submit'] => 'Pembuatan transaksi baru',
        self::ACTIONS['confirm'] => 'Konfirmasi penerimaan zakat',
        self::ACTIONS['void'] => 'Transaksi dibatalkan',

    ];

    public function zakat()
    {
        return $this->belongsTo(Zakat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
