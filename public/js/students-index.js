(function () {
    const panel = document.getElementById('students-data-panel');
    const filterForm = document.getElementById('students-filter-form');
    const skeletonTemplate = document.getElementById('students-table-skeleton');

    if (!panel || !filterForm || !skeletonTemplate) {
        return;
    }

    let activeController = null;

    const showSkeleton = () => {
        panel.dataset.loading = 'true';
        panel.innerHTML = skeletonTemplate.innerHTML;
    };

    const buildFilterUrl = () => {
        const url = new URL(filterForm.action, window.location.origin);

        new FormData(filterForm).forEach((value, key) => {
            if (value !== '') {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        });

        return url.toString();
    };

    const loadStudents = async (url, { pushState = true } = {}) => {
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

            if (pushState) {
                window.history.pushState({ studentsPanel: true }, '', url);
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

    filterForm.addEventListener('submit', (event) => {
        event.preventDefault();
        loadStudents(buildFilterUrl());
    });

    panel.addEventListener('click', (event) => {
        const paginationLink = event.target.closest('.students-data-pagination a');

        if (!paginationLink || !panel.contains(paginationLink)) {
            return;
        }

        event.preventDefault();
        loadStudents(paginationLink.href);
    });

    window.addEventListener('popstate', () => {
        if (window.location.pathname.includes('/students')) {
            loadStudents(window.location.href, { pushState: false });
        }
    });
})();
