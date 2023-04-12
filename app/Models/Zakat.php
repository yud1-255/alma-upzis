<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zakat extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_no', 'receive_from', 'zakat_pic',
        'transaction_date', 'hijri_year', 'family_head',
        'receive_from_name', 'total_rp', 'payment_date'
    ];

    public function receiveFrom()
    {
        return $this->belongsTo(User::class, 'receive_from', 'id');
    }

    public function zakatPIC()
    {
        return $this->belongsTo(User::class, 'zakat_pic', 'id');
    }
    public function zakatLines()
    {
        return $this->hasMany(ZakatLine::class);
    }

    public function zakatLogs()
    {
        return $this->hasMany(ZakatLog::class);
    }
}
