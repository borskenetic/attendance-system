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
     * Draw multi-line address scaled to fit the ID back layout (644px-wide template).
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

    private function yearLevelToRoman(?string $year): ?string
    {
        if ($year === null || trim($year) === '') {
            return null;
        }

        if (! preg_match('/(\d+)/', $year, $matches)) {
            return null;
        }

        $map = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        $level = (int) $matches[1];

        return $map[$level] ?? null;
    }

    private function formatIdCardCourseYear(?string $course, ?string $year): string
    {
        $course = strtoupper(trim((string) $course));
        $roman = $this->yearLevelToRoman($year);

        // Client request: show Junior High as "GRADE - 7" (not "GR7 - VII").
        if ($year !== null && preg_match('/\bgrade\s*(\d+)\b/i', $year, $matches)) {
            $grade = (int) $matches[1];

            if ($grade >= 7 && $grade <= 10) {
                return 'GRADE - ' . $grade;
            }
        }

        if ($course !== '' && $roman !== null) {
            return $course.' - '.$roman;
        }

        if ($course !== '') {
            return $course;
        }

        return $roman ?? '';
    }

    private function isDarkRgb(array $rgb): bool
    {
        return max($rgb['r'], $rgb['g'], $rgb['b']) <= 40;
    }

    private function isRgbBackgroundColor(int $rgba, array $targetRgb, int $tolerance): bool
    {
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;
        $alpha = ($rgba & 0x7F000000) >> 24;

        // GD alpha: 0 = opaque, 127 = fully transparent. Skip already-transparent pixels.
        if ($alpha >= 120) {
            return false;
        }

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $chroma = $max - $min;

        // Black studio backdrops: match only neutral near-black.
        // Euclidean distance to (0,0,0) wrongly treats dark maroon uniforms as background
        // and flood-fills holes through the vest.
        if ($this->isDarkRgb($targetRgb)) {
            return $max <= $tolerance && $chroma <= 18;
        }

        $rgbDistance = sqrt(
            (($r - $targetRgb['r']) ** 2)
            + (($g - $targetRgb['g']) ** 2)
            + (($b - $targetRgb['b']) ** 2)
        );

        if ($rgbDistance <= $tolerance) {
            return true;
        }

        return $r >= 190 && $g >= 190 && $b >= 190 && $chroma <= 70;
    }

    /**
     * Detect a solid studio backdrop (black or white) from border pixels.
     * Returns null when the backdrop looks complex (use AI remover instead).
     *
     * Uniforms often touch left/right edges, so we weight corners + top edge
     * more heavily and allow a modest share of non-backdrop border pixels.
     */
    private function detectSolidBackgroundColor($image): ?array
    {
        $gd = imagecreatefromstring((string) $image->encode('png'));
        imagepalettetotruecolor($gd);

        $width = imagesx($gd);
        $height = imagesy($gd);

        if ($width < 4 || $height < 4) {
            imagedestroy($gd);

            return null;
        }

        $blackVotes = 0;
        $whiteVotes = 0;
        $otherVotes = 0;
        $step = max(1, (int) floor(min($width, $height) / 80));

        $sample = function (int $x, int $y, int $weight = 1) use ($gd, &$blackVotes, &$whiteVotes, &$otherVotes): void {
            $rgba = imagecolorat($gd, $x, $y);
            $r = ($rgba >> 16) & 0xFF;
            $g = ($rgba >> 8) & 0xFF;
            $b = $rgba & 0xFF;
            $alpha = ($rgba & 0x7F000000) >> 24;

            if ($alpha >= 120) {
                return;
            }

            $max = max($r, $g, $b);
            $min = min($r, $g, $b);

            if ($max <= 50 && ($max - $min) <= 35) {
                $blackVotes += $weight;
            } elseif ($min >= 195 && ($max - $min) <= 45) {
                $whiteVotes += $weight;
            } else {
                $otherVotes += $weight;
            }
        };

        // Corners (most reliable for studio cutouts).
        foreach ([[0, 0], [$width - 1, 0], [0, $height - 1], [$width - 1, $height - 1]] as [$cx, $cy]) {
            for ($dx = 0; $dx <= min(12, $width - 1); $dx += 2) {
                for ($dy = 0; $dy <= min(12, $height - 1); $dy += 2) {
                    $x = ($cx === 0) ? $dx : $width - 1 - $dx;
                    $y = ($cy === 0) ? $dy : $height - 1 - $dy;
                    $sample($x, $y, 3);
                }
            }
        }

        // Top edge is usually pure backdrop even when shoulders touch the sides.
        for ($x = 0; $x < $width; $x += $step) {
            $sample($x, 0, 2);
            $sample($x, min(4, $height - 1), 2);
            $sample($x, $height - 1, 1);
        }

        for ($y = $step; $y < $height - $step; $y += $step) {
            $sample(0, $y, 1);
            $sample($width - 1, $y, 1);
        }

        imagedestroy($gd);

        $total = $blackVotes + $whiteVotes + $otherVotes;
        if ($total < 20) {
            return null;
        }

        $otherShare = $otherVotes / $total;

        // Prefer solid removal whenever the border is mostly studio black/white.
        // rembg punches holes in maroon uniforms on black backdrops.
        if ($blackVotes > $whiteVotes && ($blackVotes / $total) >= 0.55 && $otherShare <= 0.35) {
            return ['r' => 0, 'g' => 0, 'b' => 0];
        }

        if ($whiteVotes > $blackVotes && ($whiteVotes / $total) >= 0.55 && $otherShare <= 0.35) {
            return ['r' => 255, 'g' => 255, 'b' => 255];
        }

        return null;
    }

    /**
     * rembg often leaves dark clothing semi-transparent; force those pixels opaque
     * so the campus backdrop cannot show through the uniform.
     */
    private function forceSubjectOpaque($image)
    {
        $gd = imagecreatefromstring((string) $image->encode('png'));
        imagepalettetotruecolor($gd);
        imagealphablending($gd, false);
        imagesavealpha($gd, true);

        $width = imagesx($gd);
        $height = imagesy($gd);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($gd, $x, $y);
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;
                $alpha = ($rgba & 0x7F000000) >> 24;

                // Skip fully transparent and already fully opaque pixels.
                if ($alpha === 0 || $alpha >= 120) {
                    continue;
                }

                // Keep soft fringe (very light / near-transparent edge) as-is.
                if ($alpha > 90 && $r >= 180 && $g >= 180 && $b >= 180) {
                    continue;
                }

                imagesetpixel($gd, $x, $y, imagecolorallocatealpha($gd, $r, $g, $b, 0));
            }
        }

        return Image::make($gd);
    }

    /**
     * Close transparent pockets trapped inside the subject (not connected to the border).
     * Uses nearest opaque neighbor color so maroon vest gaps don't show the campus through.
     */
    private function fillInteriorTransparentHoles($image)
    {
        $gd = imagecreatefromstring((string) $image->encode('png'));
        imagepalettetotruecolor($gd);
        imagealphablending($gd, false);
        imagesavealpha($gd, true);

        $width = imagesx($gd);
        $height = imagesy($gd);
        $visited = array_fill(0, $width * $height, false);
        $borderReachable = array_fill(0, $width * $height, false);
        $queue = new \SplQueue();

        $isTransparent = static function (int $rgba): bool {
            return (($rgba & 0x7F000000) >> 24) >= 64;
        };

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

            if (! $isTransparent(imagecolorat($gd, $x, $y))) {
                continue;
            }

            $borderReachable[$index] = true;
            $queue->enqueue([$x + 1, $y]);
            $queue->enqueue([$x - 1, $y]);
            $queue->enqueue([$x, $y + 1]);
            $queue->enqueue([$x, $y - 1]);
        }

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $index = ($y * $width) + $x;
                if ($borderReachable[$index] || ! $isTransparent(imagecolorat($gd, $x, $y))) {
                    continue;
                }

                $fill = null;
                foreach ([[1, 0], [-1, 0], [0, 1], [0, -1], [2, 0], [-2, 0], [0, 2], [0, -2], [3, 0], [-3, 0], [0, 3], [0, -3]] as [$dx, $dy]) {
                    $nx = $x + $dx;
                    $ny = $y + $dy;
                    if ($nx < 0 || $ny < 0 || $nx >= $width || $ny >= $height) {
                        continue;
                    }
                    $neighbor = imagecolorat($gd, $nx, $ny);
                    if ((($neighbor & 0x7F000000) >> 24) < 64) {
                        $fill = $neighbor;
                        break;
                    }
                }

                if ($fill === null) {
                    continue;
                }

                $r = ($fill >> 16) & 0xFF;
                $g = ($fill >> 8) & 0xFF;
                $b = $fill & 0xFF;
                imagesetpixel($gd, $x, $y, imagecolorallocatealpha($gd, $r, $g, $b, 0));
            }
        }

        return Image::make($gd);
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
            $profile = Image::make($profilePath);
            $profile->orientate();

            // Studio cutouts on solid black/white: flood-fill removal preserves maroon
            // uniforms. rembg alpha-matting often punches holes through dark clothing.
            $solidBackground = $this->detectSolidBackgroundColor($profile);

            if ($solidBackground !== null) {
                $profile->resize(1400, 1400, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $tolerance = $this->isDarkRgb($solidBackground) ? 38 : 85;
                $profile = $this->removeRgbBackground($profile, $solidBackground, false, $tolerance);
            } else {
                $fromService = $this->removeBackgroundWithService($profilePath);

                if ($fromService) {
                    $profile = $fromService;
                    $profile->orientate();
                }

                $profile->resize(1400, 1400, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                if (! $fromService) {
                    $profile = $this->removeRgbBackground($profile, ['r' => 255, 'g' => 255, 'b' => 255], false, 85);
                }
            }

            $profile = $this->cropTransparentMargins($profile);
            $profile = $this->fillInteriorTransparentHoles($profile);
            $profile = $this->forceSubjectOpaque($profile);
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
        $courseYear = $this->formatIdCardCourseYear($student->course, $student->year);
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
            $this->drawFittedAddress($img, $student->emergency_address, 322, 510);
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
