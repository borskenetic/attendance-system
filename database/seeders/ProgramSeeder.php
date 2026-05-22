<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            ['program_code' => 'BSCS', 'program_name' => 'Bachelor of Science in Computer Science', 'total_years' => 4],
            ['program_code' => 'BSIT', 'program_name' => 'Bachelor of Science in Information Technology', 'total_years' => 4],
            ['program_code' => 'BSED', 'program_name' => 'Bachelor of Secondary Education', 'total_years' => 4],
            ['program_code' => 'BSBA', 'program_name' => 'Bachelor of Science in Business Administration', 'total_years' => 4],
            ['program_code' => 'BSA', 'program_name' => 'Bachelor of Science in Accountancy', 'total_years' => 4],
        ];

        foreach ($programs as $row) {
            Program::updateOrCreate(
                ['program_code' => $row['program_code']],
                $row
            );
        }
    }
}
