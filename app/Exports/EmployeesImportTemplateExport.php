<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeesImportTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'employee_id',
            'firstname',
            'lastname',
            'department',
            'position',
            'birth_date',
            'qrcode',
        ];
    }

    public function array(): array
    {
        return [
            [
                'EMP-2024-001',
                'Maria',
                'Santos',
                'Library',
                'Librarian',
                '1990-06-20',
                '',
            ],
        ];
    }
}
