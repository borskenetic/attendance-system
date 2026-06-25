(function (global) {
    'use strict';

    const runPageScripts = () => {
        global.initSidebar?.();
        global.initLogoutConfirm?.();
        global.DataPanel?.initAll();
        global.initPatronImportLabels?.();
    };

    document.addEventListener('turbo:load', runPageScripts);

    if (!global.Turbo) {
        return;
    }

    document.addEventListener('turbo:submit-start', (event) => {
        const form = event.target;

        if (form?.enctype === 'multipart/form-data') {
            event.preventDefault();
            form.removeAttribute('data-turbo');
            form.submit();
        }
    });
})(window);
