(function (global) {
    'use strict';

    const initBootstrapDropdowns = (root = document) => {
        if (typeof bootstrap === 'undefined') {
            return;
        }

        root.querySelectorAll('[data-bs-toggle="dropdown"]').forEach((el) => {
            const existing = bootstrap.Dropdown.getInstance(el);
            if (existing) {
                existing.dispose();
            }

            bootstrap.Dropdown.getOrCreateInstance(el);
        });
    };

    const bindDropdownDelegation = () => {
        if (global.__patronDropdownDelegation) {
            return;
        }

        global.__patronDropdownDelegation = true;

        document.addEventListener('click', (event) => {
            const toggle = event.target.closest('[data-bs-toggle="dropdown"]');
            if (!toggle || typeof bootstrap === 'undefined') {
                return;
            }

            if (bootstrap.Dropdown.getInstance(toggle)) {
                return;
            }

            event.preventDefault();
            bootstrap.Dropdown.getOrCreateInstance(toggle).toggle();
        }, true);
    };

    bindDropdownDelegation();

    const defaultOnHydrated = (panel) => {
        initBootstrapDropdowns(panel);
    };

    const resolveElement = (root, selector) => {
        if (!selector) {
            return null;
        }

        if (selector instanceof Element) {
            return selector;
        }

        if (typeof selector !== 'string') {
            return null;
        }

        if (selector.startsWith('#') || selector.startsWith('.')) {
            return document.querySelector(selector);
        }

        return root.querySelector(selector) || document.querySelector(selector);
    };

    const isPanelVisible = (panel) => !panel.classList.contains('hidden');

    const buildFilterUrl = (form, panel) => {
        const url = new URL(form.action, window.location.origin);
        const formData = new FormData(form);

        url.search = '';

        formData.forEach((value, key) => {
            if (value !== '') {
                url.searchParams.set(key, value);
            }
        });

        return appendTabInput(url.toString(), panel);
    };

    const appendTabInput = (url, panel) => {
        const tabInputSelector = panel.dataset.tabInput;

        if (!tabInputSelector) {
            return url;
        }

        const tabInput = document.querySelector(tabInputSelector);

        if (!tabInput?.value) {
            return url;
        }

        const nextUrl = new URL(url, window.location.origin);
        nextUrl.searchParams.set('tab', tabInput.value);

        return nextUrl.toString();
    };

    const disposePanel = (panel) => {
        panel.__dataPanelAbort?.abort();
        panel.__dataPanelAbort = null;
        delete panel.dataset.hydratableBound;
    };

    const initPanel = (panel, options = {}) => {
        if (!panel) {
            return null;
        }

        disposePanel(panel);

        const formSelector = panel.dataset.form;
        const skeletonSelector = panel.dataset.skeleton;
        const paginationSelector = panel.dataset.pagination || '.data-panel-pagination';
        const pathMatch = options.pathMatch || panel.dataset.pathMatch || window.location.pathname;
        const enabledWhenVisible = options.enabledWhenVisible ?? panel.dataset.enabledWhenVisible === 'true';
        const onHydrated = options.onHydrated || defaultOnHydrated;

        const form = resolveElement(panel, formSelector);
        const skeletonTemplate = resolveElement(panel, skeletonSelector);

        if (!form || !skeletonTemplate) {
            return null;
        }

        const abortController = new AbortController();
        panel.__dataPanelAbort = abortController;
        const { signal } = abortController;

        let activeController = null;

        const showSkeleton = () => {
            panel.dataset.loading = 'true';
            panel.innerHTML = skeletonTemplate.innerHTML;
        };

        const loadPanel = async (url, { pushState = true } = {}) => {
            if (enabledWhenVisible && !isPanelVisible(panel)) {
                return;
            }

            if (activeController) {
                activeController.abort();
            }

            activeController = new AbortController();
            showSkeleton();

            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'text/html',
                    },
                    signal: activeController.signal,
                });

                if (!response.ok) {
                    throw new Error('Request failed');
                }

                panel.innerHTML = await response.text();
                panel.dataset.loading = 'false';
                onHydrated(panel);

                if (pushState) {
                    window.history.pushState({ hydratablePanel: panel.id || true }, '', url);
                }
            } catch (error) {
                if (error.name === 'AbortError') {
                    return;
                }

                window.location.href = url;
            } finally {
                activeController = null;
            }
        };

        const onFormSubmit = (event) => {
            event.preventDefault();
            loadPanel(buildFilterUrl(form, panel));
        };

        const onPanelClick = (event) => {
            const paginationLink = event.target.closest(`${paginationSelector} a`);

            if (!paginationLink || !panel.contains(paginationLink)) {
                return;
            }

            event.preventDefault();
            loadPanel(appendTabInput(paginationLink.href, panel));
        };

        form.addEventListener('submit', onFormSubmit, { signal });
        panel.addEventListener('click', onPanelClick, { signal });

        if (!global.__dataPanelPopstateBound && !global.Turbo) {
            global.__dataPanelPopstateBound = true;

            window.addEventListener('popstate', () => {
                document.querySelectorAll('[data-hydratable-panel]').forEach((boundPanel) => {
                    const match = boundPanel.dataset.pathMatch || boundPanel.__dataPanelPathMatch;

                    if (match && window.location.pathname.includes(match)) {
                        boundPanel.__dataPanelLoad?.(window.location.href, { pushState: false });
                    }
                });
            });
        }

        panel.__dataPanelPathMatch = pathMatch;
        panel.__dataPanelLoad = loadPanel;
        panel.dataset.hydratableBound = 'true';
        onHydrated(panel);

        return { loadPanel };
    };

    const initAll = () => {
        document.querySelectorAll('[data-hydratable-panel]').forEach((panel) => {
            initPanel(panel);
        });
    };

    global.DataPanel = {
        init: initPanel,
        initAll,
        initDropdowns: initBootstrapDropdowns,
    };

    if (global.Turbo) {
        document.addEventListener('turbo:before-cache', () => {
            document.querySelectorAll('[data-hydratable-panel]').forEach(disposePanel);
        });
    }

    const bootDataPanels = () => {
        initAll();
        initBootstrapDropdowns();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootDataPanels);
    } else {
        bootDataPanels();
    }
})(window);
