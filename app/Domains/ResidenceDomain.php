<?php

namespace App\Domains;

use Illuminate\Validation\ValidationException;

use DB;

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
        return range(1, $this->houseNumbers);
    }
}
