<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsListExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected Collection $students
    ) {}

    public function collection()
    {
        return $this->students->map(fn ($s) => [
            $s->student_id ?? '',
            $s->lastname,
            $s->firstname,
            $s->middle_initial ?? '',
            $s->course ?? '',
            $s->year ?? '',
            $s->qrcode ?? '',
            $s->mobile_number ?? '',
        ]);
    }

    public function headings(): array
    {
        return [
            'student_id',
            'lastname',
            'firstname',
            'middle_initial',
            'course',
            'year',
            'qrcode',
            'mobile_number',
        ];
    }
}
