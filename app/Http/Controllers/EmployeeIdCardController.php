<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Intervention\Image\Facades\Image;
use App\Support\QrCodePng;
use ZipArchive;

class EmployeeIdCardController extends Controller
{
    public function front($id)
    {
        $employee = Employee::findOrFail($id);
    
        // Load background
        $img = Image::make(base_path('images/id_templates/front_employee.png'));
    
        // --- Formal Picture ---
        if ($employee->formal_picture && file_exists(base_path($employee->formal_picture))) {
            $profile = Image::make(base_path($employee->formal_picture))->resize(290, 290);
    
            $width = 2200;
            $height = 2200;
            $radius = 350;
    
            // Rounded mask
            $mask = Image::canvas($width, $height);
            $shape = Image::canvas($width, $height, null);
    
            $shape->rectangle($radius, 0, $width - $radius, $height, fn($draw) => $draw->background('#fff'));
            $shape->rectangle(0, $radius, $width, $height - $radius, fn($draw) => $draw->background('#fff'));
    
            // Corner circles
            foreach ([[1,1],[1,-1],[-1,1],[-1,-1]] as [$xMul, $yMul]) {
                $shape->circle($radius * 2, $xMul > 0 ? $radius : $width - $radius, $yMul > 0 ? $radius : $height - $radius, fn($draw) => $draw->background('#fff'));
            }
    
            $profile->mask($shape, false);
            $img->insert($profile, 'top-left', 40, 267);
        }
    
        // --- Name (auto-resize if long) ---
        $fullName = trim("{$employee->firstname} {$employee->lastname}");
        $nameLength = strlen($fullName);
    
        $fontSize = 62.5;
        if ($nameLength > 15 && $nameLength <= 20) $fontSize = 52.5;
        elseif ($nameLength > 20 && $nameLength <= 25) $fontSize = 42.5;
        elseif ($nameLength > 25) $fontSize = 32.5;
    
        $img->text($fullName, 715, 300, function ($font) use ($fontSize) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size($fontSize);
            $font->color('#000');
            $font->align('center');
            $font->valign('top');
        });
    
        // --- Department (auto-resize) ---
        if ($employee->department) {
            $deptLength = strlen($employee->department);
            $deptFontSize = 25; // base size
            if ($deptLength > 15 && $deptLength <= 20) $deptFontSize = 20;
            elseif ($deptLength > 20 && $deptLength <= 25) $deptFontSize = 18;
            elseif ($deptLength > 25) $deptFontSize = 15;
    
            $img->text($employee->department, 190, 575, function ($font) use ($deptFontSize) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size($deptFontSize);
                $font->color('#fff');
                $font->align('center');
                $font->valign('top');
            });
        }
    
        // --- Position (auto-resize) ---
        if ($employee->position) {
            $posLength = strlen($employee->position);
            $posFontSize = 45; // base size
            if ($posLength > 15 && $posLength <= 20) $posFontSize = 35;
            elseif ($posLength > 20 && $posLength <= 25) $posFontSize = 30;
            elseif ($posLength > 25) $posFontSize = 25;
    
            $img->text($employee->position, 715, 378, function ($font) use ($posFontSize) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size($posFontSize);
                $font->color('#000');
                $font->align('center');
                $font->valign('top');
            });
        }
    
        // --- Employee ID ---
        if ($employee->employee_id) {
            $img->text($employee->employee_id, 715, 440, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(30);
                $font->color('#000');
                $font->align('center');
                $font->valign('top');
            });
        }
    
        return $img->response('png');
    }

    public function back($id)
    {
        $employee = Employee::findOrFail($id);
        $img = Image::make(base_path('images/id_templates/back_employee.png'));

        // --- QR Code (Employee ID) ---
        $qrPng = QrCodePng::generate((string) ($employee->employee_id ?? $employee->id), 270, 0);

        $qrImage = Image::make((string) $qrPng);
        $img->insert($qrImage, 'top-left', 750, 45);

        // --- Signature ---
        if ($employee->employee_signature && file_exists(base_path($employee->employee_signature))) {
            $signature = Image::make(base_path($employee->employee_signature))->resize(400, 150);
            $img->insert($signature, 'center', 290, 285);
        }

       if ($employee->birth_date) {
            $formattedDate = \Carbon\Carbon::parse($employee->birth_date)->format('m-d-Y');
            $img->text($formattedDate, 400, 40, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(25);
                $font->color('#000');
                $font->align('left');
                $font->valign('top');
            });
        }

        if ($employee->sex) {
            $img->text($employee->sex, 400, 88, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(25);
                $font->color('#000');
                $font->align('left');
                $font->valign('top');
            });
        }

        if ($employee->tin_id_number) {
            $img->text($employee->tin_id_number, 400, 128, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(25);
                $font->color('#000');
                $font->align('left');
                $font->valign('top');
            });
        }

        if ($employee->philhealth_number) {
            $img->text($employee->philhealth_number, 400, 170, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(25);
                $font->color('#000');
                $font->align('left');
                $font->valign('top');
            });
        }

        if ($employee->civil_status) {
            $img->text($employee->civil_status, 400, 208, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(25);
                $font->color('#000');
                $font->align('left');
                $font->valign('top');
            });
        }

        if ($employee->blood_type) {
            $img->text($employee->blood_type, 400, 252, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(25);
                $font->color('#000');
                $font->align('left');
                $font->valign('top');
            });
        }

        if ($employee->sss_number) {
            $img->text($employee->sss_number, 400, 295, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(25);
                $font->color('#000');
                $font->align('left');
                $font->valign('top');
            });
        }

        if ($employee->hdmf_number) {
            $img->text($employee->hdmf_number, 400, 334, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(25);
                $font->color('#000');
                $font->align('left');
                $font->valign('top');
            });
        }

        if ($employee->emergency_contact_name) {
            $img->text($employee->emergency_contact_name, 550, 445, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(35);
                $font->color('#000');
                $font->align('center');
                $font->valign('top');
            });
        }

        if ($employee->emergency_contact_relationship) {
            $img->text($employee->emergency_contact_relationship, 550, 490, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(25);
                $font->color('#000');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        if ($employee->emergency_contact_number) {
            $img->text($employee->emergency_contact_number, 550, 587, function ($font) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size(25);
                $font->color('#000');
                $font->align('center');
                $font->valign('top');
            });
        }
        
        $addLength = strlen($employee->address);
    
        // Base font size
        $addFontSize = 25;
    
        // Adjust font size based on name length
        if ($addLength > 70 && $addLength <= 80) {
            $addFontSize = 20;
        } elseif ($addLength > 80 && $addLength <= 90) {
            $addFontSize = 15;
        } elseif ($addLength > 90) {
            $addFontSize = 10;
        }

        if ($employee->address) {
            $img->text($employee->address, 550, 535, function ($font) use ($addFontSize) {
                $font->file(public_path('fonts/arial.ttf'));
                $font->size($addFontSize);
                $font->color('#000');
                $font->align('center');
                $font->valign('top');
            });
        }

        return $img->response('png');
    }

    public function download($id)
    {
        $employee = Employee::findOrFail($id);

        $front = $this->front($id)->getContent();
        $back = $this->back($id)->getContent();

        $zipPath = storage_path("app/employee_id_{$id}.zip");
        $frontPath = storage_path("app/employee_front_{$id}.png");
        $backPath = storage_path("app/employee_back_{$id}.png");

        file_put_contents($frontPath, $front);
        file_put_contents($backPath, $back);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $zip->addFile($frontPath, 'front.png');
            $zip->addFile($backPath, 'back.png');
            $zip->close();
        }

        unlink($frontPath);
        unlink($backPath);

        return response()->download($zipPath, "{$employee->lastname}_{$employee->firstname}_EmployeeID.zip")->deleteFileAfterSend(true);
    }
}
