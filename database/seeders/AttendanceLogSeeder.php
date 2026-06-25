<?php

namespace Database\Seeders;

use App\Models\AttendanceLog;
use App\Models\Setting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceLogSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::query()->orderBy('id')->get();

        if ($students->isEmpty()) {
            $this->command?->warn('No students found — run StudentSeeder first.');

            return;
        }

        $sections = Setting::DEFAULT_ATTENDANCE_SECTIONS;
        $tz = 'Asia/Manila';
        $today = Carbon::now($tz)->startOfDay();

        foreach ($students as $index => $student) {
            $visitDays = match ($index % 5) {
                0 => 10,
                1 => 7,
                2 => 12,
                3 => 5,
                default => 8,
            };

            for ($day = $visitDays; $day >= 1; $day--) {
                $date = $today->copy()->subDays($day);

                if ($date->isWeekend()) {
                    continue;
                }

                $sessions = ($index + $day) % 4 === 0 ? 2 : 1;

                for ($session = 0; $session < $sessions; $session++) {
                    $inHour = 8 + (($index + $session) % 4);
                    $inMinute = ($index * 7 + $session * 11) % 60;
                    $durationHours = 1 + (($index + $day + $session) % 4);

                    $inAt = $date->copy()->setTime($inHour, $inMinute, 0);
                    $outAt = $inAt->copy()->addHours($durationHours)->addMinutes(($session + 1) * 5);

                    $section = $sections[($index + $day + $session) % count($sections)];

                    AttendanceLog::create([
                        'student_id' => $student->id,
                        'status' => 'IN',
                        'section' => $section,
                        'scanned_at' => $inAt,
                    ]);

                    AttendanceLog::create([
                        'student_id' => $student->id,
                        'status' => 'OUT',
                        'section' => $section,
                        'scanned_at' => $outAt,
                    ]);
                }
            }
        }
    }
}
