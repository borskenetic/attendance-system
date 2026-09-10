<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\Setting;
use App\Models\Student;
use Carbon\Carbon;

class GateRosterService
{
    public function __construct(
        protected StudentScanService $scanService,
    ) {}

    /** @return array<string, mixed> */
    public function build(?Carbon $since = null): array
    {
        $serverTime = now()->toIso8601String();

        $studentsQuery = Student::query()->orderBy('id');
        if ($since) {
            $studentsQuery->where(function ($q) use ($since) {
                $q->where('updated_at', '>=', $since)
                    ->orWhere('created_at', '>=', $since);
            });
        }

        $students = $studentsQuery->get();

        $studentIds = $since
            ? $students->pluck('id')
            : Student::query()->pluck('id');

        $lastLogs = $this->lastLogsForStudents($studentIds);
        $todayScans = $this->todayScansForStudents($studentIds);

        $roster = $students->map(function (Student $student) use ($lastLogs, $todayScans) {
            $payload = $this->scanService->studentPayload($student);
            $last = $lastLogs[$student->id] ?? null;
            $payload['last_log'] = $last ? [
                'status' => strtoupper((string) $last->status),
                'scanned_at' => $last->scanned_at?->toIso8601String(),
                'section' => $last->section,
            ] : null;
            $payload['today_scans'] = $todayScans[$student->id] ?? [];

            return $payload;
        })->values();

        $logsSince = null;
        if ($since) {
            $logsSince = AttendanceLog::query()
                ->where('scanned_at', '>=', $since)
                ->orderBy('scanned_at')
                ->get()
                ->map(fn (AttendanceLog $log) => [
                    'student_id' => $log->student_id,
                    'status' => strtoupper((string) $log->status),
                    'scanned_at' => $log->scanned_at?->toIso8601String(),
                    'section' => $log->section,
                    'client_uuid' => $log->client_uuid,
                ])
                ->values();
        }

        return [
            'server_time' => $serverTime,
            'since' => $since?->toIso8601String(),
            'full_snapshot' => $since === null,
            'settings' => $this->settingsPayload(),
            'students' => $roster,
            'logs_since' => $logsSince,
        ];
    }

    /** @return array<string, mixed> */
    public function settingsPayload(): array
    {
        return [
            'logout_feedback_enabled' => $this->scanService->logoutFeedbackEnabled(),
            'section_picker_enabled' => $this->scanService->sectionPickerEnabled(),
            'attendance_sections' => Setting::attendanceSections(),
            'timezone' => config('app.timezone', 'Asia/Manila'),
        ];
    }

    /** @param  \Illuminate\Support\Collection<int, int>|iterable<int, int>  $studentIds */
    protected function lastLogsForStudents(iterable $studentIds): array
    {
        $ids = collect($studentIds)->filter()->unique()->values();
        if ($ids->isEmpty()) {
            return [];
        }

        $logs = AttendanceLog::query()
            ->whereIn('student_id', $ids)
            ->orderByDesc('scanned_at')
            ->orderByDesc('id')
            ->get()
            ->unique('student_id')
            ->keyBy('student_id');

        return $logs->all();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, int>|iterable<int, int>  $studentIds
     * @return array<int, list<array{status: string, scanned_at: ?string}>>
     */
    protected function todayScansForStudents(iterable $studentIds): array
    {
        $ids = collect($studentIds)->filter()->unique()->values();
        if ($ids->isEmpty()) {
            return [];
        }

        $tz = config('app.timezone', 'Asia/Manila');
        $today = Carbon::now($tz)->toDateString();

        $grouped = [];
        AttendanceLog::query()
            ->whereIn('student_id', $ids)
            ->whereDate('scanned_at', $today)
            ->orderBy('scanned_at')
            ->orderBy('id')
            ->get()
            ->each(function (AttendanceLog $log) use (&$grouped) {
                $grouped[$log->student_id][] = [
                    'status' => strtoupper((string) $log->status),
                    'scanned_at' => $log->scanned_at?->toIso8601String(),
                ];
            });

        return $grouped;
    }
}
