<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Setting;
use App\Models\Student;
use App\Services\AttendanceSessionService;
use App\Services\StudentScanService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function showScanner()
    {
        return view('attendance.scan', [
            'logoutFeedbackEnabled' => Setting::logoutFeedbackEnabled(),
            'sectionPickerEnabled' => Setting::sectionPickerEnabled(),
            'attendanceSections' => Setting::attendanceSections(),
        ]);
    }

    public function feedbackSettings()
    {
        return view('attendance.feedback_settings', [
            'enabled' => Setting::logoutFeedbackEnabled(),
        ]);
    }

    public function updateFeedbackSettings(Request $request)
    {
        $request->validate([
            'enabled' => 'required|in:0,1',
        ]);

        Setting::setLogoutFeedbackEnabled($request->input('enabled') === '1');

        return back()->with(
            'success',
            $request->input('enabled') === '1'
                ? 'Logout feedback is now enabled on the attendance scanner.'
                : 'Logout feedback is now disabled on the attendance scanner.'
        );
    }

    public function sectionSettings()
    {
        return view('attendance.section_settings', [
            'enabled' => Setting::sectionPickerEnabled(),
            'sections' => Setting::attendanceSections(),
        ]);
    }

    public function updateSectionSettings(Request $request)
    {
        $request->validate([
            'enabled' => 'required|in:0,1',
            'sections' => 'required|array|min:1',
            'sections.*' => 'required|string|max:120|distinct',
        ]);

        $sections = array_values(array_unique(array_filter(array_map(
            fn ($name) => trim((string) $name),
            $request->input('sections', [])
        ))));

        Setting::setSectionPickerEnabled($request->input('enabled') === '1');
        Setting::setAttendanceSections($sections);

        $pickerOn = $request->input('enabled') === '1';

        return back()->with(
            'success',
            $pickerOn
                ? 'Section picker enabled with '.count($sections).' section(s) on the scanner.'
                : 'Section picker disabled. '.count($sections).' section(s) saved for logs and filters.'
        );
    }

    public function scan(Request $request, StudentScanService $scanService)
    {
        $request->validate(['qrcode' => 'required|string']);

        $student = $scanService->resolveStudent($request->qrcode);

        if (! $student) {
            return response()->json([
                'type' => 'error',
                'message' => 'RFID or QR code not recognized.',
            ]);
        }

        app(AttendanceSessionService::class)->closeStaleOpenInForStudent($student);

        $sessions = app(AttendanceSessionService::class);
        $lastLog = $scanService->lastLogForStudent($student);

        $nextStatus = ($lastLog && $sessions->isInStatus($lastLog->status)) ? 'OUT' : 'IN';

        return response()->json([
            'type' => 'student',
            'next_status' => $nextStatus,
            'student_id' => $student->id,
            'logout_feedback_enabled' => Setting::logoutFeedbackEnabled(),
            'section_picker_enabled' => Setting::sectionPickerEnabled(),
            'student' => [
                'id' => $student->id,
                'firstname' => $student->firstname,
                'lastname' => $student->lastname,
                'profile_picture' => $student->profile_picture,
            ],
        ]);
    }

    public function processSection(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'section' => 'nullable|string|max:255',
        ]);

        $section = $request->section ? trim((string) $request->section) : null;
        if ($section !== null && $section !== '') {
            $allowed = Setting::attendanceSections();
            if (! in_array($section, $allowed, true)) {
                return response()->json(['message' => 'Invalid section selected.'], 422);
            }
        } else {
            $section = null;
        }

        $student = Student::findOrFail($request->student_id);
        $sessions = app(AttendanceSessionService::class);
        $scanService = app(StudentScanService::class);
        $sessions->closeStaleOpenInForStudent($student);

        $lastLog = $scanService->lastLogForStudent($student);

        $newStatus = ($lastLog && $sessions->isInStatus($lastLog->status)) ? 'OUT' : 'IN';

        $log = AttendanceLog::create([
            'student_id' => $student->id,
            'section' => $section,
            'status' => $newStatus,
            'scanned_at' => now(),
            'source' => 'web',
        ]);

        $scanService->sendScanSms($student, $newStatus);

        return response()->json([
            'status' => $newStatus,
            'scanned_at' => $log->scanned_at->format('Y-m-d h:i:s A'),
            'logout_feedback_enabled' => Setting::logoutFeedbackEnabled(),
        ]);
    }

    public function showChangeVideo()
    {
        return view('attendance.change_video');
    }

    public function uploadVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4|max:512000',
        ]);

        $video = $request->file('video');
        $filename = 'area51_product_slideshow.mp4';
        $video->move(base_path('videos'), $filename);

        return redirect()->route('attendance.changeVideo')->with('success', 'Video uploaded successfully!');
    }

}
