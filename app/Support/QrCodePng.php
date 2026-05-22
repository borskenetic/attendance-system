<?php

namespace App\Support;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use RuntimeException;

/**
 * PNG QR codes via GD (no Imagick required).
 */
final class QrCodePng
{
    public static function generate(string $content, int $size = 200, int $margin = 1): string
    {
        if (! extension_loaded('gd')) {
            throw new RuntimeException('The GD extension is required to generate QR code PNG images.');
        }

        $qrCode = Encoder::encode($content, ErrorCorrectionLevel::M());
        $matrix = $qrCode->getMatrix();
        $matrixWidth = $matrix->getWidth();
        $matrixHeight = $matrix->getHeight();

        $totalModules = $matrixWidth + ($margin * 2);
        $moduleSize = max(1, (int) floor($size / $totalModules));
        $imageSize = $moduleSize * $totalModules;

        $image = imagecreatetruecolor($imageSize, $imageSize);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $white);

        for ($y = 0; $y < $matrixHeight; $y++) {
            for ($x = 0; $x < $matrixWidth; $x++) {
                if ($matrix->get($x, $y) !== 1) {
                    continue;
                }

                $px = ($x + $margin) * $moduleSize;
                $py = ($y + $margin) * $moduleSize;
                imagefilledrectangle(
                    $image,
                    $px,
                    $py,
                    $px + $moduleSize - 1,
                    $py + $moduleSize - 1,
                    $black
                );
            }
        }

        ob_start();
        imagepng($image);
        $png = (string) ob_get_clean();
        imagedestroy($image);

        return $png;
    }

    public static function toBase64(string $content, int $size = 200, int $margin = 1): string
    {
        return base64_encode(self::generate($content, $size, $margin));
    }
}
