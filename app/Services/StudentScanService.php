<?php

namespace App\Services;

use App\Console\Commands\NormalizeStudentNames;
use App\Http\Controllers\SmsController;
use App\Models\AttendanceLog;
use App\Models\GateDevice;
use App\Models\Setting;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StudentScanService
{
    public function __construct(
        protected AttendanceSessionService $sessions,
    ) {}

    public function resolveStudent(string $raw): ?Student
    {
        $token = trim(str_replace("\r", '', $raw));
        $student = Student::where('qrcode', $token)->first();

        $parsed = $this->parseQr($raw);

        if (! $student && $parsed['student_no']) {
            $student = Student::where('student_id', $parsed['student_no'])->first();
        }

        if (! $student && $parsed['full_name']) {
            $qrName = NormalizeStudentNames::normalizeFullName($parsed['full_name']);
            $student = Student::where('normalized_name', $qrName)->first();
        }

        return $student;
    }

    /** @return array{student_no: ?string, full_name: ?string, course: ?string} */
    public function parseQr(string $raw): array
    {
        $raw = trim(str_replace("\r", '', $raw));

        if (str_contains($raw, "\n")) {
            $lines = array_values(array_filter(array_map('trim', explode("\n", $raw))));

            return [
                'student_no' => $lines[0] ?? null,
                'full_name' => $lines[1] ?? null,
                'course' => $lines[2] ?? null,
            ];
        }

        $parts = array_map('trim', explode(',', $raw));

        if (preg_match('/^\d{2}-\d+$/', $parts[0] ?? '')) {
            return [
                'student_no' => $parts[0] ?? null,
                'full_name' => $parts[1] ?? null,
                'course' => $parts[2] ?? null,
            ];
        }

        return [
            'student_no' => null,
            'full_name' => $parts[0] ?? null,
            'course' => $parts[1] ?? null,
        ];
    }

    /**
     * Record a scan uploaded from an offline gate terminal.
     *
     * Kiosk-supplied IN/OUT is ignored: the server derives status from this student's
     * existing history so a stale kiosk cannot create a second IN at another gate.
     * After insert, same-day rows are re-sequenced for out-of-order uploads.
     */
    public function recordSyncedScan(
        Student $student,
        string $status,
        Carbon $scannedAt,
        ?string $section,
        string $clientUuid,
        GateDevice $gateDevice,
    ): AttendanceLog {
        return DB::transaction(function () use ($student, $status, $scannedAt, $section, $clientUuid, $gateDevice) {
            Student::query()->whereKey($student->id)->lockForUpdate()->first();

            $existing = AttendanceLog::where('client_uuid', $clientUuid)->first();
            if ($existing) {
                return $existing;
            }

            $status = strtoupper(trim($status));
            if (! in_array($status, ['IN', 'OUT'], true)) {
                throw new \InvalidArgumentException('Invalid scan status.');
            }

            if ($section !== null && $section !== '') {
                $allowed = Setting::attendanceSections();
                if (! in_array($section, $allowed, true)) {
                    $section = null;
                }
            } else {
                $section = null;
            }

            $this->sessions->closeStaleOpenInForStudent($student);

            $scannedAt = $scannedAt->copy()->timezone(config('app.timezone'));
            $status = $this->resolveStatusFromHistory($student, $scannedAt);

            $log = AttendanceLog::create([
                'student_id' => $student->id,
                'section' => $section,
                'kiosk_name' => $gateDevice->name,
                'status' => $status,
                'scanned_at' => $scannedAt,
                'client_uuid' => $clientUuid,
                'gate_device_id' => $gateDevice->id,
                'source' => 'gate_sync',
            ]);

            $this->reconcileStudentDayToggleStatuses($student, $scannedAt);

            $log->refresh();
            $status = strtoupper((string) $log->status);

            $this->sendScanSms($student, $status);

            return $log;
        });
    }

    /** Next IN/OUT from logs strictly before $scannedAt (server history wins). */
    public function resolveStatusFromHistory(Student $student, Carbon $scannedAt): string
    {
        $prev = $this->lastLogBefore($student, $scannedAt);

        return ($prev && $this->sessions->isInStatus($prev->status)) ? 'OUT' : 'IN';
    }

    /**
     * Re-apply IN/OUT along the calendar day so late-arriving earlier scans stay consistent.
     * Does not re-send SMS for corrected rows.
     */
    public function reconcileStudentDayToggleStatuses(Student $student, Carbon $at): int
    {
        $tz = config('app.timezone', 'Asia/Manila');
        $dayStart = $at->copy()->timezone($tz)->startOfDay();
        $dayEnd = $dayStart->copy()->endOfDay();

        $open = false;

        $logs = AttendanceLog::query()
            ->where('student_id', $student->id)
            ->whereBetween('scanned_at', [$dayStart, $dayEnd])
            ->orderBy('scanned_at')
            ->orderBy('id')
            ->get();

        $updated = 0;
        foreach ($logs as $log) {
            $expected = $open ? 'OUT' : 'IN';
            $actual = strtoupper((string) $log->status);
            if ($actual !== $expected) {
                AttendanceLog::query()->whereKey($log->id)->update(['status' => $expected]);
                $updated++;
            }
            $open = $expected === 'IN';
        }

        return $updated;
    }

    public function lastLogBefore(Student $student, Carbon $scannedAt): ?AttendanceLog
    {
        return AttendanceLog::query()
            ->where('student_id', $student->id)
            ->where('scanned_at', '<', $scannedAt)
            ->orderByDesc('scanned_at')
            ->orderByDesc('id')
            ->first();
    }

    public function lastLogForStudent(Student $student): ?AttendanceLog
    {
        return AttendanceLog::where('student_id', $student->id)
            ->orderByDesc('scanned_at')
            ->orderByDesc('id')
            ->first();
    }

    /** @return array<string, mixed> */
    public function studentPayload(Student $student): array
    {
        return [
            'id' => $student->id,
            'record_id' => null,
            'student_id' => $student->student_id,
            'qrcode' => $student->qrcode,
            'rfid' => null,
            'firstname' => $student->firstname,
            'lastname' => $student->lastname,
            'middle_initial' => $student->middle_initial,
            'profile_picture' => $student->profile_picture,
            'normalized_name' => $student->normalized_name,
            'educational_level' => null,
            'year' => $student->year,
        ];
    }

    public function logoutFeedbackEnabled(): bool
    {
        return Setting::logoutFeedbackEnabled();
    }

    public function sectionPickerEnabled(): bool
    {
        return Setting::sectionPickerEnabled();
    }

    public function sendScanSms(Student $student, string $status): void
    {
        if (empty($student->mobile_number)) {
            return;
        }

        $template = Setting::where('key', Setting::KEY_SCAN_SMS)->value('value')
            ?? 'Hello {name}, you scanned {status} at the library at {time}.';

        $message = str_replace(
            ['{name}', '{status}', '{time}'],
            [
                trim($student->firstname.' '.$student->lastname),
                $status,
                Carbon::now('Asia/Manila')->format('h:i A'),
            ],
            $template
        );

        app(SmsController::class)->sendDirect($student->mobile_number, $message);
    }
}
