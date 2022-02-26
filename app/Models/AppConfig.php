<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppConfig extends Model
{
    protected $table = 'app_config';

    protected $fillable = ['key', 'value'];
    protected $hidden = ['created_at', 'updated_at'];

    public static function getConfigValue(string $key): string
    {
        return (new static)::where('key', $key)->value('value');
    }

    public static function getConfigValues(string $key): array
    {
        return (new static)::where('key', $key)->pluck('value')->all();
    }
}
