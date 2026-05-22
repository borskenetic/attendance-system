<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeesImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    public function rules(): array
    {
        return [
            '*.employee_id' => 'required|distinct|unique:employees,employee_id',
            '*.firstname' => 'required|string|max:255',
            '*.lastname' => 'required|string|max:255',
            '*.qrcode' => 'nullable|distinct|unique:employees,qrcode',
        ];
    }
    public function model(array $row)
    {
        $employeeId = trim((string) ($row['employee_id'] ?? ''));
        $firstname = trim((string) ($row['firstname'] ?? ''));
        $lastname = trim((string) ($row['lastname'] ?? ''));

        if ($employeeId === '' || $firstname === '' || $lastname === '') {
            return null;
        }

        $qrcode = trim((string) ($row['qrcode'] ?? ''));
        if ($qrcode === '') {
            $qrcode = 'E-'.$employeeId;
        }

        return new Employee([
            'role_id' => 2,
            'employee_id' => $employeeId,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'department' => trim((string) ($row['department'] ?? '')) ?: null,
            'position' => trim((string) ($row['position'] ?? '')) ?: null,
            'birth_date' => $this->parseDate($row['birth_date'] ?? null),
            'qrcode' => $qrcode,
        ]);
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $str = trim((string) $value);
        if ($str === '') {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($str)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
