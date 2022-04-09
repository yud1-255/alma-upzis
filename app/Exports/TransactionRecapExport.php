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

class TransactionRecapExport implements
    FromQuery,
    WithMapping,
    WithColumnFormatting,
    WithHeadings,
    WithStyles,
    ShouldAutoSize
{
    private $user;
    private $hijriYear;

    public function __construct(User $user, string $hijriYear)
    {
        $this->user = $user;
        $this->hijriYear = $hijriYear;
    }

    public function headings(): array
    {
        return [
            [
                'No. Zakat', 'Fitrah', '', '', 'Maal',
                'Profesi', 'Infaq/Shadaqah', 'Fidyah', '', 'Wakaf', 'Kafarat',
                'Biaya Unik', 'Total (Rp)', 'Tanggal Transaksi', 'Tanggal Terima', 'Terima dari'
            ],
            [
                '', 'Rp', 'Kg', 'Lt', '',
                '', '', 'Rp', 'Kg', '', '',
                '', '', ''
            ],
        ];
    }

    public function query()
    {
        $domain = new ZakatDomain($this->user);

        return $domain->zakatTransactionRecap("", $this->hijriYear)->orderBy('transaction_no', 'asc');
    }

    public function map($zakat): array
    {
        return [
            $zakat->transaction_no,
            $zakat->fitrah_rp,
            $zakat->fitrah_kg,
            $zakat->fitrah_lt,
            $zakat->maal_rp,
            $zakat->profesi_rp,
            $zakat->infaq_rp,
            $zakat->fidyah_rp,
            $zakat->fidyah_kg,
            $zakat->wakaf_rp,
            $zakat->kafarat_rp,
            $zakat->unique_number,
            $zakat->total_transfer_rp,
            Date::dateTimeToExcel(new DateTime($zakat->transaction_date)),
            $zakat->payment_date != null ? Date::dateTimeToExcel(new DateTime($zakat->payment_date)) : Date::dateTimeToExcel(new DateTime($zakat->transaction_date)),
            $zakat->receive_from_name,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'N' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'O' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('B1:D1');
        $sheet->mergeCells('H1:I1');

        $sheet->mergeCells('A1:A2');
        $sheet->mergeCells('E1:E2');
        $sheet->mergeCells('F1:F2');
        $sheet->mergeCells('G1:G2');
        $sheet->mergeCells('J1:J2');
        $sheet->mergeCells('K1:K2');
        $sheet->mergeCells('L1:L2');
        $sheet->mergeCells('M1:M2');
        $sheet->mergeCells('N1:N2');
        $sheet->mergeCells('O1:O2');
        $sheet->mergeCells('P1:P2');

        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]]
        ];
    }
}
