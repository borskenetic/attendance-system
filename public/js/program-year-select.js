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

    function programMeta(courseSelect) {
        const code = courseSelect.value;

        if (!code) {
            return null;
        }

        return global.PROGRAM_META?.[code] || null;
    }

    function inferLevelFromOption(option) {
        const optgroup = option?.parentElement;

        if (!optgroup || optgroup.tagName !== 'OPTGROUP') {
            return 'college';
        }

        const label = (optgroup.label || '').toLowerCase();

        if (label.includes('senior')) {
            return 'senior_high';
        }

        if (label.includes('junior')) {
            return 'junior_high';
        }

        return 'college';
    }

    function resolveCourseContext(courseSelect) {
        const meta = programMeta(courseSelect);
        const selectedOption = courseSelect.selectedOptions[0] || null;
        const level = meta?.level || inferLevelFromOption(selectedOption);
        const gradeLabel = meta?.gradeLabel || meta?.name || selectedOption?.textContent?.trim() || '';

        return { level, gradeLabel };
    }

    function toggleYearField(yearSelect, show) {
        const wrap = yearSelect.closest('[data-year-field]');

        if (!wrap) {
            yearSelect.required = show;
            return;
        }

        wrap.classList.toggle('d-none', !show);
        wrap.style.display = show ? '' : 'none';
        yearSelect.required = show;
    }

    function syncYearOptions(courseSelect, yearSelect) {
        const currentValue = yearSelect.value;
        const hasCourse = Boolean(courseSelect.value);
        const { level, gradeLabel } = resolveCourseContext(courseSelect);
        const placeholder = yearSelect.querySelector('option[value=""]')?.textContent || 'Select year…';

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

        yearSelect.innerHTML = '';

        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = placeholder;
        yearSelect.appendChild(placeholderOption);

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

    if (!global.PROGRAM_META) {
        global.PROGRAM_META = {};
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initProgramYearSelects());
    } else {
        initProgramYearSelects();
    }
})(window);
