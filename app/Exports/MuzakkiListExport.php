<?php

namespace App\Exports;

use DateTime;

use App\Models\User;
use App\Models\Family;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class MuzakkiListExport implements
    FromQuery,
    WithMapping,
    WithColumnFormatting,
    WithHeadings,
    WithStyles,
    ShouldAutoSize
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function headings(): array
    {
        return [
            'Kepala Keluarga', 'Alamat', 'Kontak', 'Muzakki',
            'Akun Pengguna (Email)', 'Akun Pengguna (Nama)'

        ];
    }

    public function query()
    {
        return Family::with('muzakkis', 'user');
    }

    public function map($family): array
    {
        return [
            $family->head_of_family,
            $family->address,
            $family->phone,
            $family->muzakkis->map(function ($item) {
                return $item->name;
            })->join(", "),
            $family->user != null ? $family->user->email : '',
            $family->user != null ? $family->user->name : ''
        ];
    }

    public function columnFormats(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
