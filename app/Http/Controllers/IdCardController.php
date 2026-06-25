<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Intervention\Image\Facades\Image;
use App\Support\QrCodePng;
use ZipArchive;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class IdCardController extends Controller
{
    private function drawText($img, $text, $x, $y, $size, $color = '#000', $align = 'center', $valign = 'top')
    {
        $fontPathBold = public_path('fonts/arialbd.ttf');
        $fontPathRegular = public_path('fonts/arialbd.ttf');

        // If bold font exists, use it
        if (file_exists($fontPathBold)) {
            $img->text($text, $x, $y, function ($font) use ($fontPathBold, $size, $color, $align, $valign) {
                $font->file($fontPathBold);
                $font->size($size);
                $font->color($color);
                $font->align($align);
                $font->valign($valign);
            });
        } else {
            // Otherwise draw text several times for a bolder effect
            foreach ([[-1,0], [1,0], [0,-1], [0,1]] as [$ox, $oy]) {
                $img->text($text, $x + $ox, $y + $oy, function ($font) use ($fontPathRegular, $size, $color, $align, $valign) {
                    $font->file($fontPathRegular);
                    $font->size($size);
                    $font->color($color);
                    $font->align($align);
                    $font->valign($valign);
                });
            }

            // Center text (main pass)
            $img->text($text, $x, $y, function ($font) use ($fontPathRegular, $size, $color, $align, $valign) {
                $font->file($fontPathRegular);
                $font->size($size);
                $font->color($color);
                $font->align($align);
                $font->valign($valign);
            });
        }
    }

    private function isRgbBackgroundColor(int $rgba, array $targetRgb, int $tolerance): bool
    {
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;
        $alpha = ($rgba & 0x7F000000) >> 24;

        if ($alpha >= 120) {
            return false;
        }

        $rgbDistance = sqrt(
            (($r - $targetRgb['r']) ** 2)
            + (($g - $targetRgb['g']) ** 2)
            + (($b - $targetRgb['b']) ** 2)
        );

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $isWhiteLike = $r >= 190 && $g >= 190 && $b >= 190 && ($max - $min) <= 70;

        return $rgbDistance <= $tolerance || $isWhiteLike;
    }

    private function removeRgbBackground($image, array $targetRgb = ['r' => 255, 'g' => 255, 'b' => 255], bool $removeAllMatchingPixels = false, int $tolerance = 80)
    {
        $gd = imagecreatefromstring((string) $image->encode('png'));
        imagepalettetotruecolor($gd);
        imagealphablending($gd, false);
        imagesavealpha($gd, true);

        $width = imagesx($gd);
        $height = imagesy($gd);
        $transparent = imagecolorallocatealpha($gd, 0, 0, 0, 127);

        if ($removeAllMatchingPixels) {
            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    if ($this->isRgbBackgroundColor(imagecolorat($gd, $x, $y), $targetRgb, $tolerance)) {
                        imagesetpixel($gd, $x, $y, $transparent);
                    }
                }
            }

            return Image::make($gd);
        }

        $visited = array_fill(0, $width * $height, false);
        $queue = new \SplQueue();

        for ($x = 0; $x < $width; $x++) {
            $queue->enqueue([$x, 0]);
            $queue->enqueue([$x, $height - 1]);
        }

        for ($y = 1; $y < $height - 1; $y++) {
            $queue->enqueue([0, $y]);
            $queue->enqueue([$width - 1, $y]);
        }

        while (! $queue->isEmpty()) {
            [$x, $y] = $queue->dequeue();
            if ($x < 0 || $y < 0 || $x >= $width || $y >= $height) {
                continue;
            }

            $index = ($y * $width) + $x;
            if ($visited[$index]) {
                continue;
            }

            $visited[$index] = true;

            if (! $this->isRgbBackgroundColor(imagecolorat($gd, $x, $y), $targetRgb, $tolerance)) {
                continue;
            }

            imagesetpixel($gd, $x, $y, $transparent);
            $queue->enqueue([$x + 1, $y]);
            $queue->enqueue([$x - 1, $y]);
            $queue->enqueue([$x, $y + 1]);
            $queue->enqueue([$x, $y - 1]);
        }

        return Image::make($gd);
    }

    private function cropTransparentMargins($image)
    {
        $gd = imagecreatefromstring((string) $image->encode('png'));
        imagepalettetotruecolor($gd);
        imagesavealpha($gd, true);

        $width = imagesx($gd);
        $height = imagesy($gd);
        $minX = $width;
        $minY = $height;
        $maxX = -1;
        $maxY = -1;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $alpha = (imagecolorat($gd, $x, $y) & 0x7F000000) >> 24;
                if ($alpha < 120) {
                    $minX = min($minX, $x);
                    $minY = min($minY, $y);
                    $maxX = max($maxX, $x);
                    $maxY = max($maxY, $y);
                }
            }
        }

        if ($maxX < $minX || $maxY < $minY) {
            return $image;
        }

        return Image::make($gd)->crop($maxX - $minX + 1, $maxY - $minY + 1, $minX, $minY);
    }

    private function removeBackgroundWithService(string $path)
    {
        $url = config('services.background_remover.url', 'http://127.0.0.1:8010/remove-bg');

        if (! $url || ! file_exists($path)) {
            return null;
        }

        try {
            $response = Http::connectTimeout(2)
                ->timeout(60)
                ->attach('photo', file_get_contents($path), basename($path))
                ->post($url);

            if (! $response->successful()) {
                return null;
            }

            $image = $response->json('image');
            if (! is_string($image) || $image === '') {
                return null;
            }

            $base64 = str_contains($image, ',') ? explode(',', $image, 2)[1] : $image;
            $bytes = base64_decode($base64, true);

            return $bytes === false ? null : Image::make($bytes);
        } catch (\Throwable $e) {
            \Log::error('Background remover failed: ' . $e->getMessage(), ['path' => $path]);
            return null;
        }
    }

    public function front($id)
    {
        $student = Student::findOrFail($id);

        $img = Image::make(public_path('images/id_templates/front.png'));
        $templateWidth = $img->width();

        if ($student->profile_picture && file_exists(base_path($student->profile_picture))) {
            $profilePath = base_path($student->profile_picture);
            $profile = $this->removeBackgroundWithService($profilePath);
            $usedBackgroundService = $profile !== null;

            if (! $profile) {
                $profile = Image::make($profilePath);
            }

            $profile->orientate();

            $profile->resize(1400, 1400, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            if (! $usedBackgroundService) {
                $profile = $this->removeRgbBackground($profile, ['r' => 255, 'g' => 255, 'b' => 255], false, 85);
            }

            $profile = $this->cropTransparentMargins($profile);

            $photoTop = 180;
            $photoBottom = 695;
            $photoZoneHeight = $photoBottom - $photoTop;
            $maxPhotoWidth = (int) ($templateWidth * 0.66);

            $scale = min($maxPhotoWidth / $profile->width(), $photoZoneHeight / $profile->height());
            $photoWidth = (int) ($profile->width() * $scale);
            $photoHeight = (int) ($profile->height() * $scale);

            $profile->resize($photoWidth, $photoHeight);
            $profile->sharpen(8);

            $img->insert(
                $profile,
                'top-left',
                (int) (($templateWidth - $photoWidth) / 2),
                (int) ($photoBottom - $photoHeight)
            );
        }

        if ($student->student_signature && file_exists(base_path($student->student_signature))) {
            $signature = Image::make(base_path($student->student_signature));
            $signature = $this->cropTransparentMargins(
                $this->removeRgbBackground($signature, ['r' => 255, 'g' => 255, 'b' => 255], true, 95)
            );
            $signatureWidth = (int) ($templateWidth * 0.32);
            $signatureHeight = (int) ($signature->height() * $signatureWidth / $signature->width());
            $signature->resize($signatureWidth, $signatureHeight);

            $img->insert(
                $signature,
                'top-left',
                (int) (($templateWidth - $signatureWidth) / 2),
                695 - $signatureHeight - 10
            );
        }

        $fullName = strtoupper(trim($student->firstname . ' ' . $student->middle_initial . ' ' . $student->lastname));
        $courseYear = strtoupper(trim($student->course . ' ' . $student->year));
        $centerX = (int) ($templateWidth / 2);

        $this->drawText($img, $fullName, $centerX, 731, 36, '#fff', 'center', 'middle');

        $img->rectangle(0, 768, $templateWidth, 824, function ($draw) {
            $draw->background('#ffb50d');
        });

        if ($student->student_id) {
            $this->drawText($img, 'STUDENT NO.: ' . $student->student_id, $centerX, 795, 36, '#000', 'center', 'middle');
        }

        if ($courseYear !== '') {
            $this->drawText($img, $courseYear, $centerX, 967, 34, '#fff', 'center', 'middle');
        }

        return $img->response('png');
    }

    public function back($id)
    {
        $student = Student::findOrFail($id);

        // Background
        $img = Image::make(public_path('images/id_templates/back.png'));
        // QR Code
        $qrPng = QrCodePng::generate($student->qrcode, 200, 0);
        $qrImage = Image::make((string) $qrPng);
        if ($student->qrcode) {
            $this->drawText($img, $student->qrcode, 1555, 1540, -50, '#000');
        }

        // Birth date
        if ($student->birth_date) {
            $formattedDate = Carbon::parse($student->birth_date)->format('m-d-Y');
            $this->drawText($img, $formattedDate, 130, 140, 28, '#000');
        }

        // Blood type
        if ($student->blood_type) {
            $this->drawText($img, $student->blood_type, 3000, 1550, 300, '#000');
        }

        // Emergency contact details
        if ($student->emergency_person) {
            $this->drawText($img, $student->emergency_person, 320, 440, 35, '#000');
        }
        
        // Emergency contact details
        if ($student->mobile_number) {
            $this->drawText($img, $student->mobile_number, 510, 140, 28, '#000');
        }

        if ($student->emergency_relationship) {
            $this->drawText($img, $student->emergency_relationship, 300, 2900, 250, '#000');
        }

        if ($student->emergency_number) {
            $this->drawText($img, $student->emergency_number, 320, 480, 30, '#000');
        }
        if ($student->emergency_address) {

            $address = strtoupper($student->emergency_address);
        
            // --------- AUTO LINE WRAP (max ~30 chars per line) ---------
            $maxChars = 60; // adjust if needed
            $words = explode(' ', $address);
        
            $lines = [];
            $current = '';
        
            foreach ($words as $word) {
                if (strlen($current . ' ' . $word) <= $maxChars) {
                    $current .= ($current ? ' ' : '') . $word;
                } else {
                    $lines[] = $current;
                    $current = $word;
                }
            }
            if ($current) {
                $lines[] = $current;
            }
        
            // Measure longest line for font size logic
            $maxLength = max(array_map('strlen', $lines));
        
            // --------- FONT SIZE RESIZING ---------
            $fontSize = 25;
        
            if ($maxLength > 25 && $maxLength <= 35) {
                $fontSize = 200;
            } elseif ($maxLength > 35 && $maxLength <= 45) {
                $fontSize = 150;
            } elseif ($maxLength > 45) {
                $fontSize = 100;
            }
        
            // --------- DRAW CENTERED MULTI-LINE TEXT ---------
            $centerX = 320;  
            $startY  = 510;
            $spacing = $fontSize + 10; // dynamic vertical spacing
        
            foreach ($lines as $i => $line) {
                $this->drawText($img, $line, $centerX, $startY + ($i * $spacing), $fontSize, '#000');
            }
        }


        // Signature
        if ($student->student_signature && file_exists(base_path($student->student_signature))) {
            $signature = Image::make(base_path($student->student_signature))->resize(2000, 1000);
            $img->insert($signature, 'center', 50, 2875);
        }

        // QR code
        $img->insert($qrImage, 'top-left', 225, 180);
        return $img->response('png');
    }

    public function download($id)
    {
        $student = Student::findOrFail($id);

        // Generate both sides
        $front = $this->front($id)->getContent();
        $back = $this->back($id)->getContent();

        // Paths
        $zipPath = storage_path("app/temp_id_{$id}.zip");
        $frontPath = storage_path("app/front_{$id}.png");
        $backPath = storage_path("app/back_{$id}.png");

        // Save temporary images
        file_put_contents($frontPath, $front);
        file_put_contents($backPath, $back);

        // Create zip
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $zip->addFile($frontPath, "{$student->lastname}_{$student->firstname}_front.png");
            $zip->addFile($backPath, "{$student->lastname}_{$student->firstname}_back.png");
            $zip->close();
        }

        // Clean up
        unlink($frontPath);
        unlink($backPath);

        // Download
        return response()->download($zipPath, "{$student->lastname}_{$student->firstname}_ID.zip")
                         ->deleteFileAfterSend(true);
    }
}
