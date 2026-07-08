(function (global) {
    'use strict';

    const GRADE_LEVELS = new Set(['senior_high', 'junior_high']);

    function yearOptionsForLevel(level) {
        return global.SCHOOL_YEAR_OPTIONS?.[level] || global.SCHOOL_YEAR_OPTIONS?.college || [];
    }

    function isGradeLevel(level) {
        return GRADE_LEVELS.has(level);
    }

    function toggleYearField(yearSelect, show) {
        const wrap = yearSelect.closest('[data-year-field]');
        if (!wrap) {
            return;
        }

        wrap.classList.toggle('d-none', !show);
        yearSelect.required = show;
    }

    function syncYearOptions(courseSelect, yearSelect) {
        const selectedOption = courseSelect.selectedOptions[0];
        const level = selectedOption?.dataset.schoolLevel || 'college';
        const gradeLabel = selectedOption?.dataset.gradeLabel || '';
        const currentValue = yearSelect.value;

        if (isGradeLevel(level) && gradeLabel) {
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

        const years = yearOptionsForLevel(level);

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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => initProgramYearSelects());
    } else {
        initProgramYearSelects();
    }
})(window);
