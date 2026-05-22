@extends('layouts.sec')

@section('content')
<div class="container py-4" style="max-width: 820px;">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="mb-0">Section picker (attendance scanner)</h3>
        <a href="{{ route('attendance.scan') }}" class="btn btn-outline-secondary btn-sm" target="_blank" rel="noopener">
            Open scanner
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('attendance.section.settings.update') }}" id="sectionSettingsForm">
        @csrf
        <input type="hidden" name="enabled" value="0">

        <div class="card mb-4">
            <div class="card-body">
                <p class="text-muted">
                    When enabled, students who scan <strong>IN</strong> choose a section before their visit is logged.
                    Edit the list below to match your layout.
                </p>

                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="sectionPickerEnabled"
                           name="enabled" value="1" {{ $enabled ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="sectionPickerEnabled">
                        Ask for section when scanning IN
                    </label>
                </div>
                <p class="small text-muted mt-2 mb-0">
                    Picker status: <strong>{{ $enabled ? 'Enabled' : 'Disabled' }}</strong>
                </p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span class="fw-semibold">Scanner sections</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addSectionRow">+ Add section</button>
            </div>
            <div class="card-body">
                <p class="small text-muted">These buttons appear on the kiosk. Order is top to bottom, left to right on the scanner.</p>
                <div id="sectionRows" class="d-flex flex-column gap-2 mb-2">
                    @foreach(old('sections', $sections) as $index => $sectionName)
                        <div class="section-row input-group">
                            <span class="input-group-text text-muted section-drag-handle" title="Order">⋮⋮</span>
                            <input type="text" name="sections[]" class="form-control"
                                   value="{{ $sectionName }}" maxlength="120" required
                                   placeholder="e.g. Circulation Section">
                            <button type="button" class="btn btn-outline-danger remove-section-row" title="Remove">×</button>
                        </div>
                    @endforeach
                </div>
                <p class="small text-muted mb-0">Keep at least one section. Long names wrap on the scanner buttons.</p>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save settings</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const rows = document.getElementById('sectionRows');
    const addBtn = document.getElementById('addSectionRow');
    if (!rows || !addBtn) return;

    function bindRemove(btn) {
        btn.addEventListener('click', () => {
            if (rows.querySelectorAll('.section-row').length <= 1) {
                alert('You need at least one section.');
                return;
            }
            btn.closest('.section-row')?.remove();
        });
    }

    rows.querySelectorAll('.remove-section-row').forEach(bindRemove);

    addBtn.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'section-row input-group';
        row.innerHTML = `
            <span class="input-group-text text-muted section-drag-handle" title="Order">⋮⋮</span>
            <input type="text" name="sections[]" class="form-control" maxlength="120" required placeholder="Section name">
            <button type="button" class="btn btn-outline-danger remove-section-row" title="Remove">×</button>
        `;
        rows.appendChild(row);
        bindRemove(row.querySelector('.remove-section-row'));
        row.querySelector('input')?.focus();
    });
})();
</script>
@endsection
