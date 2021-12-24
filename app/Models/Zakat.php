<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zakat extends Model
{
    use HasFactory;

    public function receiveFrom()
    {
        return $this->belongsTo(User::class);
    }

    public function zakatPIC()
    {
        return $this->belongsTo(User::class);
    }
    public function zakatLines()
    {
        return $this->hasMany(ZakatLine::class);
    }
}
