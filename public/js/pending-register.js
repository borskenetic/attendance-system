(function () {
    'use strict';

    function resizeVisiblePads() {
        document.querySelectorAll('[data-signature-pad]').forEach((canvas) => {
            if (canvas.offsetParent !== null) {
                canvas._patronSignaturePadResize?.();
            }
        });
    }

    function initRegistrationPage() {
        const fileInput = document.getElementById('profile_picture');
        const preview = document.getElementById('profilePreview');

        if (fileInput && preview) {
            fileInput.onchange = () => {
                const file = fileInput.files?.[0];
                if (!file) {
                    return;
                }

                preview.src = URL.createObjectURL(file);
                preview.classList.remove('d-none');
            };
        }

        const btnStudent = document.getElementById('btnStudent');
        const btnEmployee = document.getElementById('btnEmployee');
        const studentForm = document.getElementById('studentForm');
        const employeeForm = document.getElementById('employeeForm');

        if (!btnStudent || !btnEmployee || !studentForm || !employeeForm) {
            return;
        }

        function showStudentForm() {
            studentForm.classList.remove('hidden');
            employeeForm.classList.add('hidden');
            btnStudent.className = 'btn btn-primary';
            btnEmployee.className = 'btn btn-outline-primary';
            btnStudent.setAttribute('aria-selected', 'true');
            btnEmployee.setAttribute('aria-selected', 'false');
            window.initPatronSignaturePads?.(studentForm);
            requestAnimationFrame(resizeVisiblePads);
        }

        function showEmployeeForm() {
            employeeForm.classList.remove('hidden');
            studentForm.classList.add('hidden');
            btnEmployee.className = 'btn btn-primary';
            btnStudent.className = 'btn btn-outline-primary';
            btnEmployee.setAttribute('aria-selected', 'true');
            btnStudent.setAttribute('aria-selected', 'false');
            window.initPatronSignaturePads?.(employeeForm);
            requestAnimationFrame(resizeVisiblePads);
        }

        btnStudent.addEventListener('click', showStudentForm);
        btnEmployee.addEventListener('click', showEmployeeForm);
    }

    document.addEventListener('DOMContentLoaded', initRegistrationPage);
})();
