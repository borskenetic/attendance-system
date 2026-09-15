<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Support\QrCodePng;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use ZipArchive;

class EmployeeIdCardController extends Controller
{
    private function drawText($img, $text, $x, $y, $size, $color = '#000', $align = 'center', $valign = 'top')
    {
        $fontPathBold = public_path('fonts/arialbd.ttf');
        $fontPathRegular = public_path('fonts/arialbd.ttf');

        if (file_exists($fontPathBold)) {
            $img->text($text, $x, $y, function ($font) use ($fontPathBold, $size, $color, $align, $valign) {
                $font->file($fontPathBold);
                $font->size($size);
                $font->color($color);
                $font->align($align);
                $font->valign($valign);
            });
        } else {
            foreach ([[-1, 0], [1, 0], [0, -1], [0, 1]] as [$ox, $oy]) {
                $img->text($text, $x + $ox, $y + $oy, function ($font) use ($fontPathRegular, $size, $color, $align, $valign) {
                    $font->file($fontPathRegular);
                    $font->size($size);
                    $font->color($color);
                    $font->align($align);
                    $font->valign($valign);
                });
            }

            $img->text($text, $x, $y, function ($font) use ($fontPathRegular, $size, $color, $align, $valign) {
                $font->file($fontPathRegular);
                $font->size($size);
                $font->color($color);
                $font->align($align);
                $font->valign($valign);
            });
        }
    }

    private function wrapTextByWords(string $text, int $maxCharsPerLine): array
    {
        $words = preg_split('/\s+/', trim($text));
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            if ($word === '') {
                continue;
            }

            $candidate = $current === '' ? $word : $current.' '.$word;

            if (strlen($candidate) <= $maxCharsPerLine) {
                $current = $candidate;
            } else {
                if ($current !== '') {
                    $lines[] = $current;
                }
                $current = $word;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    /**
     * Draw multi-line address scaled to fit the ID back layout (same as student ID).
     */
    private function drawFittedAddress($img, string $address, int $centerX, int $startY, int $maxLines = 4, int $maxBottomY = 620): void
    {
        $text = strtoupper(trim($address));

        if ($text === '') {
            return;
        }

        $configs = [
            ['size' => 20, 'chars' => 22],
            ['size' => 18, 'chars' => 26],
            ['size' => 16, 'chars' => 30],
            ['size' => 14, 'chars' => 34],
            ['size' => 12, 'chars' => 38],
        ];

        $chosen = null;

        foreach ($configs as $config) {
            $lines = $this->wrapTextByWords($text, $config['chars']);
            $lineHeight = (int) round($config['size'] * 1.2);
            $bottomY = $startY + (count($lines) * $lineHeight);

            if (count($lines) <= $maxLines && $bottomY <= $maxBottomY) {
                $chosen = [
                    'lines' => $lines,
                    'size' => $config['size'],
                    'lineHeight' => $lineHeight,
                ];
                break;
            }
        }

        if ($chosen === null) {
            $fallback = end($configs);
            $lines = array_slice($this->wrapTextByWords($text, $fallback['chars']), 0, $maxLines);
            $chosen = [
                'lines' => $lines,
                'size' => $fallback['size'],
                'lineHeight' => (int) round($fallback['size'] * 1.2),
            ];
        }

        foreach ($chosen['lines'] as $index => $line) {
            $this->drawText(
                $img,
                $line,
                $centerX,
                $startY + ($index * $chosen['lineHeight']),
                $chosen['size'],
                '#000',
                'center',
                'top'
            );
        }
    }

    public function front($id)
    {
        abort(503, 'Employee front ID is temporarily disabled.');
    }

    public function back($id)
    {
        $employee = Employee::findOrFail($id);

        // Same template and layout as student ID back
        $img = Image::make(public_path('images/id_templates/back.png'));

        $qrPayload = (string) ($employee->qrcode ?: $employee->employee_id ?: $employee->id);
        $qrPng = QrCodePng::generate($qrPayload, 200, 0);
        $qrImage = Image::make((string) $qrPng);

        if ($employee->qrcode) {
            $this->drawText($img, $employee->qrcode, 1555, 1540, -50, '#000');
        }

        if ($employee->birth_date) {
            $formattedDate = Carbon::parse($employee->birth_date)->format('m-d-Y');
            $this->drawText($img, $formattedDate, 130, 140, 28, '#000');
        }

        if ($employee->blood_type) {
            $this->drawText($img, $employee->blood_type, 3000, 1550, 300, '#000');
        }

        if ($employee->emergency_contact_name) {
            $this->drawText($img, $employee->emergency_contact_name, 320, 440, 35, '#000');
        }

        if ($employee->emergency_contact_relationship) {
            $this->drawText($img, $employee->emergency_contact_relationship, 300, 2900, 250, '#000');
        }

        if ($employee->emergency_contact_number) {
            $this->drawText($img, $employee->emergency_contact_number, 320, 480, 30, '#000');
        }

        if ($employee->address) {
            $this->drawFittedAddress($img, $employee->address, 322, 510);
        }

        if ($employee->employee_signature && file_exists(base_path($employee->employee_signature))) {
            $signature = Image::make(base_path($employee->employee_signature))->resize(2000, 1000);
            $img->insert($signature, 'center', 50, 2875);
        }

        $img->insert($qrImage, 'top-left', 225, 180);

        return $img->response('png');
    }

    public function download($id)
    {
        $employee = Employee::findOrFail($id);

        $back = $this->back($id)->getContent();

        $zipPath = storage_path("app/employee_id_{$id}.zip");
        $backPath = storage_path("app/employee_back_{$id}.png");

        file_put_contents($backPath, $back);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            $zip->addFile($backPath, 'back.png');
            $zip->close();
        }

        unlink($backPath);

        return response()->download($zipPath, "{$employee->lastname}_{$employee->firstname}_EmployeeID.zip")->deleteFileAfterSend(true);
    }
}
