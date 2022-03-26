<?php

namespace App\Domains;

use App\Models\Family;
use Illuminate\Validation\ValidationException;

use DB;
use Illuminate\Database\Eloquent\Collection;

class ResidenceDomain
{
    private $blockNumbers = [
        "A" => 20,
        "B" => 23,
        "C" => 20,
        "E" => 24,
        "F" => 19,
        "G" => 11,
    ];

    private $houseNumbers = 20;

    public function getBlockNumbers(string $blockLetter): array
    {
        $rangeSize = $this->blockNumbers[$blockLetter] ?? 0;

        if ($rangeSize != null) {
            return range(1, $rangeSize);
        }
        return [];
    }

    public function getBlockNumberOptions(): array
    {
        $arr = [];
        foreach (array_keys($this->blockNumbers) as $key) {
            $arr[$key] = $this->getBlockNumbers($key);
        }

        return $arr;
    }

    public function getHouseNumbers(): array
    {
        return range(0, $this->houseNumbers);
    }

    public function getFamily(string $kkNumber): Family
    {
        return Family::where('kk_number', $kkNumber)->orderBy('id', 'desc')->first();
    }

    public function searchFamily(string $search): Collection
    {
        $families = Family::select(['families.*', 'muzakkis.name as muzakki_name'])
            ->join('muzakkis', 'families.id', '=', 'muzakkis.family_id')
            ->where('head_of_family', 'like', "%{$search}%")
            ->orWhere('muzakkis.name', 'like', "%{$search}%");
        return $families->take(10)->get();
    }
}
