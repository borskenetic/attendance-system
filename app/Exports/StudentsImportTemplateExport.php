<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsImportTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'student_id',
            'firstname',
            'lastname',
            'middle_initial',
            'course',
            'year',
            'mobile_number',
            'birth_date',
            'qrcode',
        ];
    }

    public function array(): array
    {
        return [
            [
                '2024-00001',
                'Juan',
                'Dela Cruz',
                'M',
                'BSCS',
                '1st Year',
                '09171234567',
                '2004-03-15',
                '',
            ],
        ];
    }
}
