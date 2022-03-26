<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Muzakki extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'family_id', 'phone', 'address', 'is_bpi', 'bpi_block_no', 'bpi_house_no', 'use_family_address'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function zakatLines()
    {
        return $this->hasMany(ZakatLine::class);
    }
}
