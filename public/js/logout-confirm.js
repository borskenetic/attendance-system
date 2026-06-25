(function (global) {
    'use strict';

    let abortController = null;

    global.initLogoutConfirm = function initLogoutConfirm() {
        abortController?.abort();
        abortController = new AbortController();
        const { signal } = abortController;

        const dialog = document.getElementById('logoutConfirmDialog');
        if (!dialog || typeof dialog.showModal !== 'function') {
            return;
        }

        const confirmBtn = dialog.querySelector('[data-logout-dialog-confirm]');
        const cancelBtn = dialog.querySelector('[data-logout-dialog-cancel]');
        let pendingLogoutForm = null;

        const closeDialog = () => {
            if (dialog.open) {
                dialog.close();
            }
            pendingLogoutForm = null;
        };

        document.querySelectorAll('[data-logout-confirm]').forEach((trigger) => {
            trigger.addEventListener('click', (event) => {
                event.preventDefault();

                pendingLogoutForm = trigger.closest('form');
                if (!pendingLogoutForm) {
                    return;
                }

                dialog.showModal();
            }, { signal });
        });

        cancelBtn?.addEventListener('click', closeDialog, { signal });

        dialog.addEventListener('cancel', (event) => {
            event.preventDefault();
            closeDialog();
        }, { signal });

        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                closeDialog();
            }
        }, { signal });

        confirmBtn?.addEventListener('click', () => {
            if (pendingLogoutForm) {
                pendingLogoutForm.submit();
                return;
            }

            closeDialog();
        }, { signal });
    };

    if (!global.Turbo) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => global.initLogoutConfirm());
        } else {
            global.initLogoutConfirm();
        }
    }
})(window);
