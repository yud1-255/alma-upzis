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

class OnlinePaymentsExport implements
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
            'No. Zakat', 'Tanggal', 'Terima dari', 'Telepon', 'Email',
            'Konfirmasi Petugas', 'Jumlah Bayar (Rp)'

        ];
    }

    public function query()
    {
        $domain = new ZakatDomain($this->user);
        $hijriYear = AppConfig::getConfigValue('hijri_year');

        return $domain->zakatOnlinePayments("", $hijriYear);
    }

    public function map($zakat): array
    {
        return [
            $zakat->transaction_no,
            Date::dateTimeToExcel(new DateTime($zakat->transaction_date)),
            $zakat->receive_from_name,
            $zakat->receive_from_phone,
            $zakat->receive_from_email,
            $zakat->zakat_pic_name,
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
