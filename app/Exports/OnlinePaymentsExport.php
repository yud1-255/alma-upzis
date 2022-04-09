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
    private $hijriYear;

    public function __construct(User $user, string $hijriYear)
    {
        $this->user = $user;
        $this->hijriYear = $hijriYear;
    }

    public function headings(): array
    {
        return [
            'No. Zakat', 'Tanggal Transaksi', 'Tanggal Terima', 'Terima dari', 'Telepon', 'Email',
            'Konfirmasi Petugas', 'Jumlah Bayar (Rp)'

        ];
    }

    public function query()
    {
        $domain = new ZakatDomain($this->user);

        return $domain->zakatOnlinePayments("", $this->hijriYear);
    }

    public function map($zakat): array
    {
        return [
            $zakat->transaction_no,
            Date::dateTimeToExcel(new DateTime($zakat->transaction_date)),
            $zakat->payment_date != null ? Date::dateTimeToExcel(new DateTime($zakat->payment_date)) : '-',
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
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]]
        ];
    }
}
