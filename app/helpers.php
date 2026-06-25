<?php

if (! function_exists('patron_media_url')) {
    /**
     * Public URL for patron uploads stored under project /images (not always in public/).
     */
    function patron_media_url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');

        return route('patron.media', ['path' => $normalized]);
    }
}
