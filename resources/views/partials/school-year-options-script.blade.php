@php
    use App\Support\SchoolLevel;

    $schoolYearOptions = [
        SchoolLevel::COLLEGE => SchoolLevel::yearOptions(SchoolLevel::COLLEGE),
        SchoolLevel::SENIOR_HIGH => SchoolLevel::yearOptions(SchoolLevel::SENIOR_HIGH),
        SchoolLevel::JUNIOR_HIGH => SchoolLevel::yearOptions(SchoolLevel::JUNIOR_HIGH),
    ];

    $programMeta = [];
    $grouped = $programsByLevel ?? collect();

    foreach (SchoolLevel::ordered() as $level) {
        foreach ($grouped[$level] ?? [] as $program) {
            $programMeta[$program->program_code] = [
                'level' => $level,
                'name' => $program->program_name,
                'gradeLabel' => SchoolLevel::usesIndividualGrades($level) ? $program->program_name : null,
            ];
        }
    }
@endphp
<script>
window.SCHOOL_YEAR_OPTIONS = @json($schoolYearOptions);
window.PROGRAM_META = @json($programMeta);
</script>
<script src="{{ asset('js/program-year-select.js') }}?v=4"></script>
