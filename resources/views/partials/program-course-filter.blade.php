@php
    use App\Support\SchoolLevel;

    $name = $name ?? 'program_id';
    $id = $id ?? 'program_id';
    $selected = $selected ?? request($name);
    $inputClass = $inputClass ?? 'form-select form-select-sm';
    $grouped = $programsByLevel ?? collect();
@endphp

<select name="{{ $name }}" id="{{ $id }}" class="{{ $inputClass }}">
    <option value="">All Courses</option>
    @foreach(SchoolLevel::ordered() as $level)
        @if(($grouped[$level] ?? collect())->isNotEmpty())
            <optgroup label="{{ SchoolLevel::label($level) }}">
                @foreach($grouped[$level] as $program)
                    <option value="{{ $program->program_code }}"
                            @selected((string) $selected === (string) $program->program_code)>
                        {{ $program->program_name }}
                    </option>
                @endforeach
            </optgroup>
        @endif
    @endforeach
</select>
