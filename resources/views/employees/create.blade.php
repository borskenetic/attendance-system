@extends('layouts.sec')

@section('title', 'Register Employee')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/employees/create.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
@endpush

@section('content')
<div class="data-page employee-form-page mt-2">
    <div class="card shadow-sm">
        <div class="card-header text-center py-3">
            <h4 class="mb-1">Register Employee</h4>
            <p class="page-intro">Staff record for attendance and ID generation.</p>
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

            <form id="employeeForm" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-section">
                    <div class="form-section-title">Work information</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="employee_id" class="form-label">Employee ID</label>
                            <input type="text" name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror"
                                   value="{{ old('employee_id') }}" placeholder="e.g. EMP-2024-001">
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="employee_number" class="form-label">Employee number</label>
                            <input type="text" name="employee_number" id="employee_number" class="form-control"
                                   value="{{ old('employee_number') }}" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label for="firstname" class="form-label">First name</label>
                            <input type="text" name="firstname" id="firstname" class="form-control @error('firstname') is-invalid @enderror"
                                   value="{{ old('firstname') }}" required>
                            @error('firstname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="lastname" class="form-label">Last name</label>
                            <input type="text" name="lastname" id="lastname" class="form-control @error('lastname') is-invalid @enderror"
                                   value="{{ old('lastname') }}" required>
                            @error('lastname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="department" class="form-label">Department</label>
                            <input type="text" name="department" id="department" class="form-control"
                                   value="{{ old('department') }}" placeholder="e.g. Library Services">
                        </div>
                        <div class="col-md-6">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" name="position" id="position" class="form-control"
                                   value="{{ old('position') }}" placeholder="e.g. Librarian">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Personal details</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="birth_date" class="form-label">Birth date</label>
                            <input type="date" name="birth_date" id="birth_date" class="form-control" value="{{ old('birth_date') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="sex" class="form-label">Sex</label>
                            <select name="sex" id="sex" class="form-select">
                                <option value="">Select…</option>
                                <option value="Male" {{ old('sex') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('sex') === 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="civil_status" class="form-label">Civil status</label>
                            <input type="text" name="civil_status" id="civil_status" class="form-control"
                                   value="{{ old('civil_status') }}" placeholder="e.g. Single">
                        </div>
                        <div class="col-md-4">
                            <label for="blood_type" class="form-label">Blood type</label>
                            <input type="text" name="blood_type" id="blood_type" class="form-control"
                                   value="{{ old('blood_type') }}" placeholder="e.g. O+">
                        </div>
                        <div class="col-md-8">
                            <label for="address" class="form-label">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="2" placeholder="Home address">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Government &amp; benefits IDs</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tin_id_number" class="form-label">TIN</label>
                            <input type="text" name="tin_id_number" id="tin_id_number" class="form-control" value="{{ old('tin_id_number') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="philhealth_number" class="form-label">PhilHealth</label>
                            <input type="text" name="philhealth_number" id="philhealth_number" class="form-control" value="{{ old('philhealth_number') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="sss_number" class="form-label">SSS</label>
                            <input type="text" name="sss_number" id="sss_number" class="form-control" value="{{ old('sss_number') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="hdmf_number" class="form-label">HDMF</label>
                            <input type="text" name="hdmf_number" id="hdmf_number" class="form-control" value="{{ old('hdmf_number') }}">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Emergency contact</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="emergency_contact_name" class="form-label">Contact name</label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control"
                                   value="{{ old('emergency_contact_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="emergency_contact_relationship" class="form-label">Relationship</label>
                            <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" class="form-control"
                                   value="{{ old('emergency_contact_relationship') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="emergency_contact_number" class="form-label">Contact number</label>
                            <input type="text" name="emergency_contact_number" id="emergency_contact_number" class="form-control"
                                   value="{{ old('emergency_contact_number') }}">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">Photo &amp; signature</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="formal_picture" class="form-label">Formal picture</label>
                            <input type="file" name="formal_picture" id="formal_picture" class="form-control" accept="image/jpeg,image/png,image/jpg">
                            <p class="photo-hint">1×1 or formal ID photo, JPG or PNG.</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Signature</label>
                            <div class="signature-wrap">
                                <canvas id="employeeSignaturePad"></canvas>
                            </div>
                            <input type="hidden" name="employee_signature" id="employeeSignatureInput">
                            <button type="button" id="clearEmployeeSignature" class="btn btn-sm btn-outline-secondary mt-2">Clear signature</button>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('employees.index') }}" class="btn-form-back">Cancel</a>
                    <button type="submit" class="btn-form-submit">Register employee</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const canvas = document.getElementById('employeeSignaturePad');
    const input = document.getElementById('employeeSignatureInput');
    if (!canvas || typeof SignaturePad === 'undefined') return;

    const signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const data = signaturePad.isEmpty() ? null : signaturePad.toData();
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = 150 * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        canvas.style.width = '100%';
        canvas.style.height = '150px';
        signaturePad.clear();
        if (data) signaturePad.fromData(data);
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    document.getElementById('clearEmployeeSignature')?.addEventListener('click', () => {
        signaturePad.clear();
        input.value = '';
    });

    document.getElementById('employeeForm')?.addEventListener('submit', () => {
        if (!signaturePad.isEmpty()) {
            input.value = signaturePad.toDataURL();
        }
    });
})();
</script>
@endsection
