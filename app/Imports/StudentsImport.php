<?php

namespace App\Imports;

use App\Console\Commands\NormalizeStudentNames;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    public function rules(): array
    {
        return [
            '*.student_id' => 'required|distinct|unique:students,student_id',
            '*.firstname' => 'required|string|max:255',
            '*.lastname' => 'required|string|max:255',
            '*.qrcode' => 'nullable|distinct|unique:students,qrcode',
        ];
    }

    public function model(array $row)
    {
        $studentId = trim((string) ($row['student_id'] ?? $row['id_number'] ?? ''));
        $firstname = trim((string) ($row['firstname'] ?? ''));
        $lastname = trim((string) ($row['lastname'] ?? ''));

        if ($studentId === '' || $firstname === '' || $lastname === '') {
            return null;
        }

        $qrcode = trim((string) ($row['qrcode'] ?? ''));
        if ($qrcode === '') {
            $qrcode = $this->nextStudentQrCode();
        }

        return new Student([
            'student_id' => $studentId,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'middle_initial' => trim((string) ($row['middle_initial'] ?? '')) ?: null,
            'course' => trim((string) ($row['course'] ?? '')) ?: null,
            'year' => trim((string) ($row['year'] ?? '')) ?: null,
            'mobile_number' => trim((string) ($row['mobile_number'] ?? '')) ?: null,
            'birth_date' => $this->parseDate($row['birth_date'] ?? null),
            'qrcode' => $qrcode,
            'normalized_name' => NormalizeStudentNames::normalizeFullName($firstname.' '.$lastname),
        ]);
    }

    private function nextStudentQrCode(): string
    {
        $last = Student::whereNotNull('qrcode')
            ->where('qrcode', 'like', 'S-%')
            ->orderByDesc('id')
            ->value('qrcode');

        $nextNumber = 1;
        if ($last && preg_match('/S-(\d+)/', $last, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return 'S-'.str_pad((string) $nextNumber, 8, '0', STR_PAD_LEFT);
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
