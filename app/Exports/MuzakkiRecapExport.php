<?php

namespace App\Exports;

use DateTime;

use App\Domains\ZakatDomain;
use App\Models\User;
use App\Models\AppConfig;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class MuzakkiRecapExport implements
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
            [
                'No. Zakat', 'Muzakki',  'Petugas', 'Fitrah', '', '', 'Maal',
                'Profesi', 'Infaq/Shadaqah', 'Fidyah', '', 'Wakaf', 'Kafarat',
                'Tanggal', 'Terima dari'
            ],
            [
                '', '',  '', 'Rp', 'Kg', 'Lt', '',
                '', '', 'Rp', 'Kg', '', '',
                '', ''
            ],
        ];
    }

    public function query()
    {
        $domain = new ZakatDomain($this->user);
        $hijriYear = AppConfig::getConfigValue('hijri_year');

        return $domain->zakatMuzakkiRecap("", $hijriYear);
    }

    public function map($zakat): array
    {
        return [
            $zakat->transaction_no,
            $zakat->muzakki_name,
            $zakat->fitrah_rp,
            $zakat->fitrah_kg,
            $zakat->fitrah_rp,
            $zakat->fitrah_lt,
            $zakat->maal_rp,
            $zakat->profesi_rp,
            $zakat->infaq_rp,
            $zakat->fidyah_rp,
            $zakat->fidyah_kg,
            $zakat->wakaf_rp,
            $zakat->kafarat_rp,
            Date::dateTimeToExcel(new DateTime($zakat->transaction_date)),
            $zakat->receive_from_name,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'N' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('D1:F1');
        $sheet->mergeCells('J1:K1');

        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('B1:B2');
        $sheet->mergeCells('C1:C2');
        $sheet->mergeCells('G1:G2');
        $sheet->mergeCells('H1:H2');
        $sheet->mergeCells('I1:I2');
        $sheet->mergeCells('L1:L2');
        $sheet->mergeCells('M1:M2');
        $sheet->mergeCells('N1:N2');
        $sheet->mergeCells('O1:O2');

        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]]
        ];
    }
}
