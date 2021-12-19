<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZakatLine extends Model
{
    use HasFactory;

    public function zakat()
    {
        return $this->belongsTo(Zakat::class);
    }
}
