<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZakatLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'muzakki_id', 'fitrah_rp', 'fitrah_kg', 'fitrah_lt',
        'maal_rp', 'profesi_rp', 'infaq_rp', 'wakaf_rp',
        'fidyah_kg', 'fidyah_rp', 'kafarat_rp',
    ];

    public function zakat()
    {
        return $this->belongsTo(Zakat::class);
    }
}
