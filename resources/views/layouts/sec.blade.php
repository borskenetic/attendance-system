@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/layout/data-pages.css') }}?v={{ @filemtime(public_path('css/layout/data-pages.css')) ?: time() }}">
@endpush
