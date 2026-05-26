<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttendanceSessionService
{
    public const TZ = 'Asia/Manila';

    public function isInStatus(?string $status): bool
    {
        return $status !== null && strtolower(trim((string) $status)) === 'in';
    }

    public function isOutStatus(?string $status): bool
    {
        return $status !== null && strtolower(trim((string) $status)) === 'out';
    }

    public function closeStaleOpenInForStudent(Student $student): bool
    {
        $last = AttendanceLog::query()
            ->where('student_id', $student->id)
            ->orderByDesc('scanned_at')
            ->orderByDesc('id')
            ->first();

        if (! $last || ! $this->isInStatus($last->status)) {
            return false;
        }

        $inDayStart = $last->scanned_at->copy()->startOfDay();
        $todayStart = Carbon::today();

        if ($inDayStart->greaterThanOrEqualTo($todayStart)) {
            return false;
        }

        $outAt = $last->scanned_at->copy()->endOfDay();

        AttendanceLog::create([
            'student_id' => $student->id,
            'status' => 'OUT',
            'scanned_at' => $outAt,
        ]);

        return true;
    }

    public function closeAllStaleOpenIns(): int
    {
        if (! Schema::hasTable('attendance_logs')) {
            return 0;
        }

        $today = Carbon::now(self::TZ)->toDateString();

        $staleStudentIds = DB::table('attendance_logs as al')
            ->join(DB::raw('(
                SELECT student_id, MAX(id) AS max_id
                FROM attendance_logs
                GROUP BY student_id
            ) AS last'), 'last.max_id', '=', 'al.id')
            ->whereRaw("LOWER(TRIM(al.status)) = 'in'")
            ->whereRaw('DATE(al.scanned_at) < ?', [$today])
            ->pluck('al.student_id');

        $closed = 0;

        foreach ($staleStudentIds as $sid) {
            $student = Student::query()->find($sid);
            if (! $student) {
                continue;
            }
            if ($this->closeStaleOpenInForStudent($student)) {
                $closed++;
            }
        }

        return $closed;
    }
}
