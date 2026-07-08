@php
    use App\Support\SchoolLevel;

    $schoolYearOptions = [
        SchoolLevel::COLLEGE => SchoolLevel::yearOptions(SchoolLevel::COLLEGE),
        SchoolLevel::SENIOR_HIGH => SchoolLevel::yearOptions(SchoolLevel::SENIOR_HIGH),
        SchoolLevel::JUNIOR_HIGH => SchoolLevel::yearOptions(SchoolLevel::JUNIOR_HIGH),
    ];
@endphp
<script>
window.SCHOOL_YEAR_OPTIONS = @json($schoolYearOptions);
</script>
