<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeesListExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected Collection $employees
    ) {}

    public function collection()
    {
        return $this->employees->map(fn ($e) => [
            $e->employee_id ?? '',
            $e->lastname,
            $e->firstname,
            $e->department ?? '',
            $e->position ?? '',
            $e->qrcode ?? '',
        ]);
    }

    public function headings(): array
    {
        return [
            'employee_id',
            'lastname',
            'firstname',
            'department',
            'position',
            'qrcode',
        ];
    }
}
