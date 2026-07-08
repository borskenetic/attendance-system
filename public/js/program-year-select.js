(function (global) {
    'use strict';

    const GRADE_LEVELS = new Set(['senior_high', 'junior_high']);

    const DEFAULT_SCHOOL_YEAR_OPTIONS = {
        college: ['1st Year', '2nd Year', '3rd Year', '4th Year'],
        senior_high: ['Grade 11', 'Grade 12'],
        junior_high: ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'],
    };

    function schoolYearOptions() {
        return global.SCHOOL_YEAR_OPTIONS || DEFAULT_SCHOOL_YEAR_OPTIONS;
    }

    function yearOptionsForLevel(level) {
        const options = schoolYearOptions();

        return options[level] || options.college || DEFAULT_SCHOOL_YEAR_OPTIONS.college;
    }

    function isGradeLevel(level) {
        return GRADE_LEVELS.has(level);
    }

    function selectedCourseOption(courseSelect) {
        const value = courseSelect.value;

        if (!value) {
            return courseSelect.selectedOptions[0] || null;
        }

        return (
            courseSelect.querySelector(`option[value="${CSS.escape(value)}"]`)
            || courseSelect.selectedOptions[0]
            || null
        );
    }

    function toggleYearField(yearSelect, show) {
        const wrap = yearSelect.closest('[data-year-field]');

        if (!wrap) {
            yearSelect.required = show;
            return;
        }

        wrap.classList.toggle('d-none', !show);
        yearSelect.required = show;
    }

    function syncYearOptions(courseSelect, yearSelect) {
        const selectedOption = selectedCourseOption(courseSelect);
        const level = selectedOption?.dataset?.schoolLevel || 'college';
        const gradeLabel = (
            selectedOption?.dataset?.gradeLabel
            || (isGradeLevel(level) ? selectedOption?.textContent?.trim() : '')
            || ''
        );
        const currentValue = yearSelect.value;
        const hasCourse = Boolean(courseSelect.value);

        if (isGradeLevel(level) && hasCourse && gradeLabel) {
            yearSelect.innerHTML = '';
            const option = document.createElement('option');
            option.value = gradeLabel;
            option.textContent = gradeLabel;
            option.selected = true;
            yearSelect.appendChild(option);
            toggleYearField(yearSelect, false);
            return;
        }

        toggleYearField(yearSelect, true);

        const years = hasCourse ? yearOptionsForLevel(level) : [];

        yearSelect.innerHTML = '<option value="">Select year…</option>';

        years.forEach((year) => {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;

            if (year === currentValue) {
                option.selected = true;
            }

            yearSelect.appendChild(option);
        });

        if (currentValue && !years.includes(currentValue)) {
            const legacy = document.createElement('option');
            legacy.value = currentValue;
            legacy.textContent = currentValue + ' (current)';
            legacy.selected = true;
            yearSelect.appendChild(legacy);
        }
    }

    function initProgramYearSelects(root) {
        const scope = root || document;

        scope.querySelectorAll('[data-program-year-select]').forEach((courseSelect) => {
            const yearId = courseSelect.dataset.yearTarget || 'year';
            const yearSelect = document.getElementById(yearId);

            if (!yearSelect) {
                return;
            }

            if (courseSelect._programYearSyncHandler) {
                courseSelect.removeEventListener('change', courseSelect._programYearSyncHandler);
            }

            const handler = () => syncYearOptions(courseSelect, yearSelect);
            courseSelect._programYearSyncHandler = handler;
            courseSelect.addEventListener('change', handler);
            handler();
        });
    }

    global.initProgramYearSelects = initProgramYearSelects;
    global.DEFAULT_SCHOOL_YEAR_OPTIONS = DEFAULT_SCHOOL_YEAR_OPTIONS;

    if (!global.SCHOOL_YEAR_OPTIONS) {
        global.SCHOOL_YEAR_OPTIONS = DEFAULT_SCHOOL_YEAR_OPTIONS;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initProgramYearSelects());
    } else {
        initProgramYearSelects();
    }
})(window);
