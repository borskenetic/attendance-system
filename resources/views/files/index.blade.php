@extends('layouts.sec')

@section('title', 'File Repository')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/file/index.css') }}">
    <style>
        .file-manager-title {
            background-color: #22333b;
            color: white;
            padding: 20px;
            border-radius: 10px;
        }
    </style>
@endpush

@section('content')
    <h2 class="text-center mb-4 file-manager-title">
        <i class="bi bi-folder2-open text-primary me-2"></i>Admin File Manager
    </h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="upload-card mb-5">
        <form action="{{ route('files.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-folder-plus me-1"></i>Folder Name (optional)</label>
                <input type="text" name="folder" class="form-control" placeholder="e.g., Documents">
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-upload me-1"></i>Select File</label>
                <input type="file" name="file" class="form-control">
            </div>
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <button class="btn btn-primary">
                    <i class="bi bi-cloud-upload-fill me-1"></i>Upload File
                </button>
                <a href="{{ route('home') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to home
                </a>
            </div>
        </form>
    </div>

    @foreach($files as $file)
        <div class="card file-card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="mb-1"><i class="bi bi-file-earmark-text me-1 text-secondary"></i>{{ $file->filename }}</h6>
                    <small class="text-muted">Folder: {{ $file->folder ?: 'Root' }}</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('files.view', $file->id) }}" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('files.download', $file->id) }}" class="btn btn-outline-success btn-sm" download>
                        <i class="bi bi-download"></i>
                    </a>
                    <form action="{{ route('files.delete', $file->id) }}" method="POST" onsubmit="return confirm('Delete this file?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
