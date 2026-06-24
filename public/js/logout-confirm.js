(function () {
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
        });
    });

    cancelBtn?.addEventListener('click', closeDialog);

    dialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        closeDialog();
    });

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            closeDialog();
        }
    });

    confirmBtn?.addEventListener('click', () => {
        if (pendingLogoutForm) {
            pendingLogoutForm.submit();
            return;
        }

        closeDialog();
    });
})();
