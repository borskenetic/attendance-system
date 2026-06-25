(function (global) {
    'use strict';

    global.initPatronImportLabels = function initPatronImportLabels() {
        document.querySelectorAll('.patron-import-file input[type="file"]').forEach((input) => {
            if (input.dataset.labelBound === 'true') {
                return;
            }

            input.dataset.labelBound = 'true';

            input.addEventListener('change', () => {
                const label = input.closest('.patron-import-file')?.querySelector('span');
                if (label && input.files && input.files[0]) {
                    label.textContent = input.files[0].name.length > 22
                        ? `${input.files[0].name.slice(0, 19)}…`
                        : input.files[0].name;
                }
            });
        });
    };

    if (!global.Turbo) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => global.initPatronImportLabels());
        } else {
            global.initPatronImportLabels();
        }
    }
})(window);
