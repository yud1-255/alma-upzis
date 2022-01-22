<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'head_of_family', 'phone', 'address'
    ];

    public function muzakkis()
    {
        return $this->hasMany(Muzakki::class);
    }
}
