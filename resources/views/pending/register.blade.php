@extends('layouts.public')

@section('title', 'Registration')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pending/register.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/patron-signature-pad.js') }}" defer></script>
    <script src="{{ asset('js/pending-register.js') }}" defer></script>
@endpush

@section('content')
<div class="registration-page">
    <div class="card registration-card">
        <div class="registration-card__header">
            <h1>Patron Registration</h1>
            <p>Register as a student or employee for library attendance and ID services.</p>
        </div>

        <div class="registration-card__body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="registration-toggle" role="tablist" aria-label="Registration type">
                <button type="button" class="btn btn-primary" id="btnStudent" role="tab" aria-selected="true">Student</button>
                <button type="button" class="btn btn-outline-primary" id="btnEmployee" role="tab" aria-selected="false">Employee</button>
            </div>

            <form id="studentForm" class="registration-form" method="POST" action="{{ route('pending.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-section">
                    <div class="form-section-title">Personal information</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="student_firstname">First name <span class="required">*</span></label>
                            <input type="text" name="firstname" id="student_firstname" class="form-control" value="{{ old('firstname') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="student_middle_initial">Middle initial</label>
                            <input type="text" name="middle_initial" id="student_middle_initial" class="form-control" value="{{ old('middle_initial') }}" maxlength="5">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="student_lastname">Last name <span class="required">*</span></label>
                            <input type="text" name="lastname" id="student_lastname" class="form-control" value="{{ old('lastname') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="student_birth_date">Birthday <span class="required">*</span></label>
                            <input type="date" name="birth_date" id="student_birth_date" class="form-control" value="{{ old('birth_date') }}" required>
                            <p class="field-hint">Use your actual birthdate.</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="student_mobile">Mobile number <span class="required">*</span></label>
                            <input type="text" name="mobile_number" id="student_mobile" class="form-control" value="{{ old('mobile_number') }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Academic information</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="student_id">Student ID</label>
                            <input type="text" name="student_id" id="student_id" class="form-control" value="{{ old('student_id') }}" placeholder="Leave blank if unknown">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="student_course">Course <span class="required">*</span></label>
                            <input type="text" name="course" id="student_course" class="form-control" value="{{ old('course') }}" placeholder="e.g. BSIT" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="student_year">Year level <span class="required">*</span></label>
                            <select name="year" id="student_year" class="form-select" required>
                                <option value="">Select year level</option>
                                @foreach(['1st','2nd','3rd','4th','5th','6th'] as $y)
                                    <option value="{{ $y }} Year" {{ old('year') === $y . ' Year' ? 'selected' : '' }}>{{ $y }} Year</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Emergency contact</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="emergency_person">Contact name <span class="required">*</span></label>
                            <input type="text" name="emergency_person" id="emergency_person" class="form-control" value="{{ old('emergency_person') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emergency_relationship">Relationship <span class="required">*</span></label>
                            <input type="text" name="emergency_relationship" id="emergency_relationship" class="form-control" value="{{ old('emergency_relationship') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emergency_number">Contact number <span class="required">*</span></label>
                            <input type="text" name="emergency_number" id="emergency_number" class="form-control" value="{{ old('emergency_number') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="emergency_address">Address</label>
                            <input type="text" name="emergency_address" id="emergency_address" class="form-control" value="{{ old('emergency_address') }}">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Photo &amp; signature</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="profile_picture">Profile picture</label>
                            <div class="photo-callout">
                                Upload a <strong>1×1 ID photo</strong> with a <strong>plain white background</strong>.
                            </div>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept=".jpg,.jpeg,.png">
                            <img src="" alt="Profile preview" class="profile-preview d-none" id="profilePreview">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Signature</label>
                            <div class="signature-wrap">
                                <canvas id="studentSignaturePad"
                                        data-signature-pad
                                        data-signature-input="studentSignatureInput"
                                        data-signature-clear="clearStudentSignature"></canvas>
                            </div>
                            <input type="hidden" name="student_signature" id="studentSignatureInput">
                            <div class="signature-actions">
                                <span class="signature-hint">Sign inside the box using your mouse or finger.</span>
                                <button type="button" id="clearStudentSignature" class="btn btn-sm btn-outline-secondary">Clear signature</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-submit">Submit student registration</button>
                </div>
            </form>

            <form id="employeeForm" class="registration-form hidden" method="POST" action="{{ route('pendingEmployee.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-section">
                    <div class="form-section-title">Personal information</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="employee_firstname">First name <span class="required">*</span></label>
                            <input type="text" name="firstname" id="employee_firstname" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employee_lastname">Last name <span class="required">*</span></label>
                            <input type="text" name="lastname" id="employee_lastname" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employee_birth_date">Birthday <span class="required">*</span></label>
                            <input type="date" name="birth_date" id="employee_birth_date" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employee_sex">Sex <span class="required">*</span></label>
                            <select name="sex" id="employee_sex" class="form-select" required>
                                <option value="">Select sex</option>
                                <option value="MALE">Male</option>
                                <option value="FEMALE">Female</option>
                                <option value="OTHER">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employee_blood_type">Blood type</label>
                            <input type="text" name="blood_type" id="employee_blood_type" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employee_civil_status">Civil status</label>
                            <input type="text" name="civil_status" id="employee_civil_status" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Employment details</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="employee_id">Employee ID <span class="required">*</span></label>
                            <input type="text" name="employee_id" id="employee_id" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employee_department">Department <span class="required">*</span></label>
                            <input type="text" name="department" id="employee_department" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employee_position">Position <span class="required">*</span></label>
                            <input type="text" name="position" id="employee_position" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employee_address">Address</label>
                            <textarea name="address" id="employee_address" class="form-control" rows="2" placeholder="Home address"></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Government IDs</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="tin_id_number">TIN</label>
                            <input type="text" name="tin_id_number" id="tin_id_number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="philhealth_number">PhilHealth</label>
                            <input type="text" name="philhealth_number" id="philhealth_number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="sss_number">SSS</label>
                            <input type="text" name="sss_number" id="sss_number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="hdmf_number">HDMF</label>
                            <input type="text" name="hdmf_number" id="hdmf_number" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Emergency contact</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="employee_emergency_name">Contact name <span class="required">*</span></label>
                            <input type="text" name="emergency_contact_name" id="employee_emergency_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employee_emergency_relationship">Relationship <span class="required">*</span></label>
                            <input type="text" name="emergency_contact_relationship" id="employee_emergency_relationship" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employee_emergency_number">Contact number <span class="required">*</span></label>
                            <input type="text" name="emergency_contact_number" id="employee_emergency_number" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Photo &amp; signature</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="formal_picture">Formal picture</label>
                            <input type="file" name="formal_picture" id="formal_picture" class="form-control" accept=".jpg,.jpeg,.png">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Signature</label>
                            <div class="signature-wrap">
                                <canvas id="employeeSignaturePad"
                                        data-signature-pad
                                        data-signature-input="employeeSignatureInput"
                                        data-signature-clear="clearEmployeeSignature"></canvas>
                            </div>
                            <input type="hidden" name="employee_signature" id="employeeSignatureInput">
                            <div class="signature-actions">
                                <span class="signature-hint">Sign inside the box using your mouse or finger.</span>
                                <button type="button" id="clearEmployeeSignature" class="btn btn-sm btn-outline-secondary">Clear signature</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-submit">Submit employee registration</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
