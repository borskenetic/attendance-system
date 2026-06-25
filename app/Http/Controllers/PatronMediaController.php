<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PatronMediaController extends Controller
{
    public function show(Request $request, string $path): BinaryFileResponse
    {
        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        if ($normalized === '' || str_contains($normalized, '..')) {
            abort(404);
        }

        foreach ([base_path($normalized), public_path($normalized)] as $fullPath) {
            if (is_file($fullPath)) {
                return response()->file($fullPath);
            }
        }

        abort(404);
    }
}
