@extends('layouts.sec')

@section('title', 'Register Student')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/students/create.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/patron-signature-pad.js') }}" defer></script>
@endpush

@section('content')
<div class="data-page student-form-page mt-2">
    <div class="card shadow-sm">
        <div class="card-header text-center py-3">
            <h4 class="mb-1">Register Student</h4>
            <p class="page-intro">Creates a patron record for attendance scanning and ID cards. QR code is assigned automatically.</p>
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

            <form id="studentForm" action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-section">
                    <div class="form-section-title">Student information</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="firstname" class="form-label">First name <span class="text-danger">*</span></label>
                            <input type="text" name="firstname" id="firstname" class="form-control @error('firstname') is-invalid @enderror"
                                   value="{{ old('firstname') }}" required>
                            @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="lastname" class="form-label">Last name <span class="text-danger">*</span></label>
                            <input type="text" name="lastname" id="lastname" class="form-control @error('lastname') is-invalid @enderror"
                                   value="{{ old('lastname') }}" required>
                            @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="middle_initial" class="form-label">Middle initial</label>
                            <input type="text" name="middle_initial" id="middle_initial" class="form-control"
                                   value="{{ old('middle_initial') }}" maxlength="5" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
                            <input type="text" name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror"
                                   value="{{ old('student_id') }}" placeholder="School ID number" required>
                            @error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="mobile_number" class="form-label">Mobile number</label>
                            <input type="text" name="mobile_number" id="mobile_number" class="form-control"
                                   value="{{ old('mobile_number') }}" placeholder="09XXXXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label for="course" class="form-label">Course / program <span class="text-danger">*</span></label>
                            <select name="course" id="course" class="form-select @error('course') is-invalid @enderror" required>
                                <option value="">Select course…</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->program_code }}"
                                        {{ old('course') == $program->program_code ? 'selected' : '' }}>
                                        {{ $program->program_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="year" class="form-label">Year level <span class="text-danger">*</span></label>
                            <select name="year" id="year" class="form-select @error('year') is-invalid @enderror" required>
                                <option value="">Select year…</option>
                                @foreach(['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year', '6th Year'] as $y)
                                    <option value="{{ $y }}" {{ old('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="birth_date" class="form-label">Birthday</label>
                            <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror"
                                   value="{{ old('birth_date') }}">
                            @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" name="address" id="address" class="form-control"
                                   value="{{ old('address') }}" placeholder="Optional">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Emergency contact</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="emergency_person" class="form-label">Contact name</label>
                            <input type="text" name="emergency_person" id="emergency_person" class="form-control"
                                   value="{{ old('emergency_person') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="emergency_relationship" class="form-label">Relationship</label>
                            <input type="text" name="emergency_relationship" id="emergency_relationship" class="form-control"
                                   value="{{ old('emergency_relationship') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="emergency_number" class="form-label">Contact number</label>
                            <input type="text" name="emergency_number" id="emergency_number" class="form-control"
                                   value="{{ old('emergency_number') }}">
                        </div>
                        <div class="col-12">
                            <label for="emergency_address" class="form-label">Emergency address</label>
                            <textarea name="emergency_address" id="emergency_address" class="form-control" rows="2"
                                      placeholder="Optional">{{ old('emergency_address') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Photo &amp; signature</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="profile_picture" class="form-label">Profile picture</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control @error('profile_picture') is-invalid @enderror"
                                   accept="image/jpeg,image/png,image/jpg">
                            <p class="photo-hint">1×1 ID photo preferred. JPG or PNG, max 4 MB.</p>
                            <div class="mt-2">
                                <img src="" alt="Profile preview" width="100" class="rounded border d-none" id="profilePreview">
                            </div>
                            @error('profile_picture')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Signature</label>
                            <div class="signature-wrap">
                                <canvas id="studentSignaturePad"
                                        data-signature-pad
                                        data-signature-input="studentSignatureInput"
                                        data-signature-clear="clearStudentSignature"></canvas>
                            </div>
                            <input type="hidden" name="student_signature" id="studentSignatureInput" value="{{ old('student_signature') }}">
                            <button type="button" id="clearStudentSignature" class="btn btn-sm btn-outline-secondary mt-2">Clear signature</button>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('students.index') }}" class="btn-form-back">Cancel</a>
                    <button type="submit" class="btn-form-submit">Register student</button>
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
        const preview = document.getElementById('profilePreview');
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
