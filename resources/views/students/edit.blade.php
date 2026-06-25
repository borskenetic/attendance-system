@extends('layouts.sec')

@section('title', 'Edit Student')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/students/create.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/patron-signature-pad.js') }}" defer></script>
@endpush

@section('content')
@php
    $birthValue = old('birth_date', $student->birth_date);
    if ($birthValue) {
        $birthValue = substr((string) $birthValue, 0, 10);
    }
    $yearOptions = ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year', '6th Year'];
    $currentYear = old('year', $student->year);
@endphp

<div class="data-page student-form-page mt-2">
    <div class="card shadow-sm">
        <div class="card-header text-center py-3">
            <h4 class="mb-1">Edit Student</h4>
            <p class="page-intro">QR code is assigned by the system and cannot be changed.</p>
        </div>

        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="studentForm" method="POST" action="{{ route('students.update', $student->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-section">
                    <div class="form-section-title">Student information</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="qrcode" class="form-label">QR code</label>
                            <input type="text" id="qrcode" class="form-control bg-light" value="{{ $student->qrcode ?? '—' }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label for="firstname" class="form-label">First name <span class="text-danger">*</span></label>
                            <input type="text" name="firstname" id="firstname" class="form-control @error('firstname') is-invalid @enderror"
                                   value="{{ old('firstname', $student->firstname) }}" required>
                            @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="lastname" class="form-label">Last name <span class="text-danger">*</span></label>
                            <input type="text" name="lastname" id="lastname" class="form-control @error('lastname') is-invalid @enderror"
                                   value="{{ old('lastname', $student->lastname) }}" required>
                            @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="middle_initial" class="form-label">Middle initial</label>
                            <input type="text" name="middle_initial" id="middle_initial" class="form-control"
                                   value="{{ old('middle_initial', $student->middle_initial) }}" maxlength="5">
                        </div>
                        <div class="col-md-6">
                            <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                            <input type="text" name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror"
                                   value="{{ old('student_id', $student->student_id) }}" required>
                            @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="mobile_number" class="form-label">Mobile number</label>
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control"
                                   value="{{ old('mobile_number', $student->mobile_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label for="course" class="form-label">Course / program</label>
                            <select name="course" id="course" class="form-select @error('course') is-invalid @enderror">
                                <option value="">Select course…</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->program_code }}"
                                        {{ old('course', $student->course) == $program->program_code ? 'selected' : '' }}>
                                        {{ $program->program_name }}
                                    </option>
                                @endforeach
                                @if($student->course && !$programs->contains('program_code', $student->course))
                                    <option value="{{ $student->course }}" selected>{{ $student->course }} (current)</option>
                                @endif
                            </select>
                            @error('course')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="year" class="form-label">Year level</label>
                            <select name="year" id="year" class="form-select @error('year') is-invalid @enderror">
                                <option value="">Select year…</option>
                                @foreach($yearOptions as $y)
                                    <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                                @if($currentYear && !in_array($currentYear, $yearOptions))
                                    <option value="{{ $currentYear }}" selected>{{ $currentYear }} (current)</option>
                                @endif
                            </select>
                            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="birth_date" class="form-label">Birthday</label>
                            <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror"
                                   value="{{ $birthValue }}">
                            @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Emergency contact</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="emergency_person" class="form-label">Contact name</label>
                            <input type="text" name="emergency_person" id="emergency_person" class="form-control"
                                   value="{{ old('emergency_person', $student->emergency_person) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="emergency_relationship" class="form-label">Relationship</label>
                            <input type="text" name="emergency_relationship" id="emergency_relationship" class="form-control"
                                   value="{{ old('emergency_relationship', $student->emergency_relationship) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="emergency_number" class="form-label">Contact number</label>
                            <input type="text" name="emergency_number" id="emergency_number" class="form-control"
                                   value="{{ old('emergency_number', $student->emergency_number) }}">
                        </div>
                        <div class="col-12">
                            <label for="emergency_address" class="form-label">Emergency address</label>
                            <textarea name="emergency_address" id="emergency_address" class="form-control" rows="2">{{ old('emergency_address', $student->emergency_address) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Photo &amp; signature</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="profile_picture" class="form-label">Profile picture</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/jpeg,image/png,image/jpg">
                            <p class="photo-hint">Leave empty to keep the current photo.</p>
                            @if($student->profile_picture)
                                <div class="mt-2">
                                    <img src="{{ patron_media_url($student->profile_picture) }}" alt="Current profile" width="100" class="rounded border" id="currentProfilePreview">
                                </div>
                            @else
                                <div class="mt-2">
                                    <img src="" alt="Profile preview" width="100" class="rounded border d-none" id="currentProfilePreview">
                                </div>
                            @endif
                        </div>
                        <div class="col-12">
                            <label class="form-label">Signature</label>
                            @if($student->student_signature)
                                <p class="photo-hint mb-2">Current signature (draw below to replace):</p>
                                <img src="{{ patron_media_url($student->student_signature) }}" alt="Current signature" height="60" class="mb-2 d-block border rounded p-1 bg-white">
                            @endif
                            <div class="signature-wrap">
                                <canvas id="studentSignaturePad"
                                        data-signature-pad
                                        data-signature-input="studentSignatureInput"
                                        data-signature-clear="clearStudentSignature"></canvas>
                            </div>
                            <input type="hidden" name="student_signature" id="studentSignatureInput">
                            <button type="button" id="clearStudentSignature" class="btn btn-sm btn-outline-secondary mt-2">Clear new signature</button>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('students.index') }}" class="btn-form-back">Cancel</a>
                    <button type="submit" class="btn-form-submit">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function initProfilePreview() {
        const fileInput = document.getElementById('profile_picture');
        const preview = document.getElementById('currentProfilePreview');
        if (!fileInput || !preview) return;

        fileInput.onchange = () => {
            const file = fileInput.files?.[0];
            if (!file) return;

            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        };
    }

    document.addEventListener('DOMContentLoaded', initProfilePreview);
    document.addEventListener('turbo:load', initProfilePreview);
})();
</script>
@endsection
