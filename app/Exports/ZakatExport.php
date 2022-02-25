<?php

namespace App\Exports;

use App\Models\Zakat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ZakatExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public function headings(): array
    {
        return [
            'id', 'transaction_no', 'receive_from', 'zakat_pic',
            'transaction_date', 'hijri_year',
            'family_head', 'receive_from_name', 'is_offline_submission', 'total_rp',
            'unique_number', 'total_number', 'total_transfer_rp', 'created_at', 'updated_at'

        ];
    }
    public function collection()
    {
        return Zakat::all();
    }
}
