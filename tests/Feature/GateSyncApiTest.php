<?php

namespace Tests\Feature;

use App\Models\AttendanceLog;
use App\Models\GateDevice;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GateSyncApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_gate_health_requires_token(): void
    {
        $this->getJson('/api/gate/health')->assertUnauthorized();
    }

    public function test_roster_and_attendance_push_are_idempotent(): void
    {
        Http::fake();

        $issued = GateDevice::issue('Test Gate');
        $token = $issued['plain_token'];

        $student = Student::create([
            'firstname' => 'Jane',
            'lastname' => 'Doe',
            'qrcode' => 'S-00000099',
            'student_id' => '24-00099',
            'normalized_name' => 'Jane Doe',
        ]);

        $this->withToken($token)
            ->getJson('/api/gate/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('device.name', 'Test Gate');

        $roster = $this->withToken($token)->getJson('/api/gate/roster');
        $roster->assertOk();
        $roster->assertJsonPath('full_snapshot', true);
        $this->assertNotEmpty($roster->json('students'));

        $uuid = '11111111-1111-4111-8111-111111111111';
        $payload = [
            'scans' => [[
                'client_uuid' => $uuid,
                'scan_token' => $student->qrcode,
                'status' => 'IN',
                'section' => null,
                'scanned_at' => now()->toIso8601String(),
            ]],
        ];

        $first = $this->withToken($token)->postJson('/api/gate/attendance', $payload);
        $first->assertOk();
        $first->assertJsonPath('accepted', 1);
        $first->assertJsonPath('results.0.status', 'IN');

        $second = $this->withToken($token)->postJson('/api/gate/attendance', $payload);
        $second->assertOk();
        $second->assertJsonPath('accepted', 1);

        $this->assertSame(1, AttendanceLog::where('client_uuid', $uuid)->count());
        $this->assertSame('gate_sync', AttendanceLog::where('client_uuid', $uuid)->value('source'));
        $this->assertSame('Test Gate', AttendanceLog::where('client_uuid', $uuid)->value('kiosk_name'));
    }
}
