<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramYear;
use App\Support\SchoolLevel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            // College
            ['program_code' => 'BSCS', 'program_name' => 'Bachelor of Science in Computer Science', 'school_level' => SchoolLevel::COLLEGE],
            ['program_code' => 'BSIT', 'program_name' => 'Bachelor of Science in Information Technology', 'school_level' => SchoolLevel::COLLEGE],
            ['program_code' => 'BSED', 'program_name' => 'Bachelor of Secondary Education', 'school_level' => SchoolLevel::COLLEGE],
            ['program_code' => 'BSBA', 'program_name' => 'Bachelor of Science in Business Administration', 'school_level' => SchoolLevel::COLLEGE],
            ['program_code' => 'BSA', 'program_name' => 'Bachelor of Science in Accountancy', 'school_level' => SchoolLevel::COLLEGE],
        ];

        $programs = array_merge($programs, SchoolLevel::defaultGradePrograms());

        $seededCodes = array_column($programs, 'program_code');

        Program::query()
            ->whereIn('school_level', [SchoolLevel::SENIOR_HIGH, SchoolLevel::JUNIOR_HIGH])
            ->whereNotIn('program_code', $seededCodes)
            ->get()
            ->each(fn (Program $program) => $program->delete());

        foreach ($programs as $row) {
            $level = $row['school_level'];
            $totalYears = SchoolLevel::defaultYearCount($level);

            $program = Program::updateOrCreate(
                ['program_code' => $row['program_code']],
                [
                    'program_name' => $row['program_name'],
                    'school_level' => $level,
                    'total_years' => $totalYears,
                ]
            );

            for ($i = 1; $i <= $totalYears; $i++) {
                ProgramYear::firstOrCreate([
                    'program_id' => $program->id,
                    'year_level' => $i,
                ]);
            }

            $program->years()
                ->where('year_level', '>', $totalYears)
                ->get()
                ->each(fn (ProgramYear $year) => $year->delete());
        }

        Cache::forget('students.programs_by_level.v2');
    }
}
