@php
    use App\Support\SchoolLevel;

    $name = $name ?? 'course';
    $id = $id ?? 'course';
    $selected = $selected ?? old($name);
    $required = $required ?? false;
    $inputClass = $inputClass ?? 'form-select';
    $yearTarget = $yearTarget ?? 'year';
    $grouped = $programsByLevel ?? collect();
@endphp

<select name="{{ $name }}"
        id="{{ $id }}"
        class="{{ $inputClass }}"
        data-program-year-select
        data-year-target="{{ $yearTarget }}"
        @if($required) required @endif>
    <option value="">Select course…</option>
    @foreach(SchoolLevel::ordered() as $level)
        @if(($grouped[$level] ?? collect())->isNotEmpty())
            <optgroup label="{{ SchoolLevel::label($level) }}">
                @foreach($grouped[$level] as $program)
                    <option value="{{ $program->program_code }}"
                            data-school-level="{{ $level }}"
                            @if(SchoolLevel::usesIndividualGrades($level)) data-grade-label="{{ $program->program_name }}" @endif
                            @selected((string) $selected === (string) $program->program_code)>
                        {{ $program->program_name }}
                    </option>
                @endforeach
            </optgroup>
        @endif
    @endforeach
</select>
