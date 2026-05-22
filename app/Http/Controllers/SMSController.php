<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Student;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{

    public function index()
    {
        $courses = \App\Models\Student::select('course')
            ->whereNotNull('course')
            ->distinct()
            ->orderBy('course')
            ->pluck('course');
    
        return view('sms.blast', [
            'courses' => $courses
        ]);
    }

    public function scanMessage()
    {
        $setting = Setting::where('key','scan_sms')->first();
    
        return view('sms.scan_message',[
            'message' => $setting ? $setting->value : 'Hello {name}, you scanned {status} at the library.'
        ]);
    }
    
    public function updateScanMessage(Request $request)
    {
        $request->validate([
            'message' => 'required'
        ]);
    
        Setting::updateOrCreate(
            ['key'=>'scan_sms'],
            ['value'=>$request->message]
        );
    
        return back()->with('success','Scan SMS updated');
    }
    
    public function count(Request $request)
    {
    
        $query = Student::whereNotNull('mobile_number');
    
        if ($request->year) {
            $query->where('year', $request->year);
        }
    
        if ($request->course) {
            $query->where('course', $request->course);
        }
    
        return response()->json([
            'count' => $query->count()
        ]);
    
    }

    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);
    
        $students = Student::whereNotNull('mobile_number')->get();
    
        $payload = [];
    
        foreach ($students as $student) {
            $name = $student->firstname . ' ' . $student->lastname;
            $message = str_replace('{name}', $name, $request->message);
            $number = $student->mobile_number;
    
            if(substr($number,0,1) == "0"){
                $number = "+63" . substr($number,1);
            }
    
            $payload[] = [
                'number' => $number,
                'message' => $message
            ];
        }
    
        // send to your local Python server
        $python_server = "https://cloakedly-ineffective-amara.ngrok-free.dev/send-sms"; // your ngrok URL
        $api_key = "library123"; // must match Python server
        
        $response = Http::withHeaders([
            'X-API-KEY' => $api_key
        ])->timeout(300) 
        ->post($python_server, $payload);
            
        return back()->with('success','SMS sent successfully');
    }

    public function sendDirect(string $number, string $message): bool
    {
        $number = $this->normalizePhilippineMobile($number);

        if ($number === '') {
            return false;
        }

        $url = config('services.sms_modem.url', env('SMS_MODEM_URL'));
        $apiKey = config('services.sms_modem.key', env('SMS_MODEM_API_KEY'));

        if (! $url) {
            return false;
        }

        try {
            $response = Http::withHeaders(['X-API-KEY' => $apiKey])
                ->timeout(30)
                ->post($url, [
                    ['number' => $number, 'message' => $message],
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    private function normalizePhilippineMobile(string $number): string
    {
        $number = preg_replace('/\s+/', '', $number);

        if (str_starts_with($number, '0')) {
            return '+63'.substr($number, 1);
        }

        if (str_starts_with($number, '63')) {
            return '+'.$number;
        }

        return $number;
    }
}