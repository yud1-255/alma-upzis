<?php

namespace App\Exports;

use DateTime;

use App\Domains\ZakatDomain;
use App\Models\User;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;

class ZakatExport implements
    FromQuery,
    WithMapping,
    WithColumnFormatting,
    WithHeadings,
    WithStyles,
    ShouldAutoSize
{
    private $user;

    /**
     * @return \Illuminate\Support\Collection
     */

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function headings(): array
    {
        return [
            'No. Zakat', 'Tanggal', 'Terima dari', 'Petugas',
            'Periode', 'Kepala Keluarga', 'Terima lewat', 'Jumlah Bayar (Rp)'

        ];
    }

    public function query()
    {
        $domain = new ZakatDomain($this->user);
        return $domain->transactionSummary("");
    }

    public function map($zakat): array
    {
        $d = new DateTime($zakat->transaction_date);
        // dd(Date::dateTimeToExcel($d));
        return [
            $zakat->transaction_no,
            Date::dateTimeToExcel($d),
            $zakat->receive_from_name,
            $zakat->zakat_pic_name,
            $zakat->hijri_year,
            $zakat->family_head,
            $zakat->is_offline_submission ? 'Gerai' : 'Online',
            $zakat->total_transfer_rp
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
