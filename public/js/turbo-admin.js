(function (global) {
    'use strict';

    const runPageScripts = () => {
        document.body.classList.remove('sidebar-open');
        global.initSidebar?.();
        global.initLogoutConfirm?.();
        global.DataPanel?.initAll();
        global.DataPanel?.initDropdowns?.();
        global.initPatronImportLabels?.();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runPageScripts);
    } else {
        runPageScripts();
    }
})(window);
