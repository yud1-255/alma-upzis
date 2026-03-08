<?php

namespace App\Helpers;

use App\Models\AppConfig;
use GeniusTS\HijriDate\HijriDate;
use Illuminate\Support\Facades\Log;

class HijriYearHelper
{
    /**
     * Get the current Hijri year — returns override from AppConfig if set,
     * otherwise auto-detects via HijriDate library.
     *
     * @return string
     */
    public static function current(): string
    {
        $override = AppConfig::getConfigValue('hijri_year');

        if (!empty($override)) {
            return $override;
        }

        return self::autoDetect();
    }

    /**
     * Auto-detect current Hijri year from library.
     *
     * @return string
     */
    public static function autoDetect(): string
    {
        try {
            return (string) HijriDate::now()->year;
        } catch (\Throwable $e) {
            Log::error('HijriYearHelper: Failed to auto-detect Hijri year', [
                'error' => $e->getMessage(),
            ]);

            // Fallback to stored AppConfig value
            $fallback = AppConfig::getConfigValue('hijri_year');
            return $fallback ?: '1447';
        }
    }
}
