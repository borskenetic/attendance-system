<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;

class NormalizeStudentNames extends Command
{
    protected $signature = 'students:normalize';

    protected $description = 'Generate normalized_name for QR name matching';

    public function handle(): int
    {
        $this->info('Normalizing student names...');

        Student::query()->chunkById(500, function ($students) {
            foreach ($students as $student) {
                $student->normalized_name = $this->normalizeFullName(
                    $student->firstname.' '.$student->lastname
                );
                $student->saveQuietly();
            }
        });

        $this->info('Done.');

        return self::SUCCESS;
    }

    public static function normalizeFullName(string $fullName): string
    {
        $full = strtoupper($fullName);
        $full = preg_replace('/[^A-Z\s]/', '', $full);
        $full = preg_replace('/\b[A-Z]\b/', '', $full);

        return preg_replace('/\s+/', '', $full);
    }
}
