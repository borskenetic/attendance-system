(function (global) {
    'use strict';

    let abortController = null;

    global.initSidebar = function initSidebar() {
        abortController?.abort();
        abortController = new AbortController();
        const { signal } = abortController;

        const toggleBtn = document.getElementById('customMenuToggle');
        const closeBtn = document.getElementById('customMenuClose');
        const collapseBtn = document.getElementById('sidebarCollapseToggle');
        const backdrop = document.getElementById('sidebarBackdrop');
        const sidebar = document.querySelector('.pantas-header');

        if (!toggleBtn || !sidebar) {
            return;
        }

        const dropdowns = Array.from(document.querySelectorAll('.pantas-header .nav-dropdown'));
        let openDropdown = null;

        const isCollapsed = () => document.body.classList.contains('sidebar-collapsed');

        const closeFlyouts = () => {
            dropdowns.forEach((dropdown) => {
                dropdown.classList.remove('flyout-open');
                const content = dropdown.querySelector('.nav-dropdown-content');
                if (content && !dropdown.classList.contains('open')) {
                    content.hidden = true;
                }
            });
        };

        const closeSidebar = () => document.body.classList.remove('sidebar-open');

        const setOpenDropdown = (sectionName) => {
            openDropdown = sectionName;

            dropdowns.forEach((dropdown) => {
                const isOpen = dropdown.dataset.sidebarSection === openDropdown;
                const button = dropdown.querySelector('.nav-dropdown-button');
                const content = dropdown.querySelector('.nav-dropdown-content');

                dropdown.classList.toggle('open', isOpen);
                button?.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                if (content) {
                    content.hidden = !isOpen;
                }
            });
        };

        const setCollapsed = (collapsed) => {
            document.body.classList.toggle('sidebar-collapsed', collapsed);
            collapseBtn?.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            collapseBtn?.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
            localStorage.setItem('sidebar-collapsed', collapsed ? 'true' : 'false');

            if (collapsed) {
                setOpenDropdown(null);
            } else {
                closeFlyouts();
                const activeDropdown = dropdowns.find((dropdown) => {
                    return dropdown.querySelector('.nav-dropdown-button.active, .nav-dropdown-content .active');
                });
                if (activeDropdown) {
                    setOpenDropdown(activeDropdown.dataset.sidebarSection);
                }
            }
        };

        setCollapsed(localStorage.getItem('sidebar-collapsed') === 'true');

        toggleBtn.addEventListener('click', () => document.body.classList.add('sidebar-open'), { signal });
        collapseBtn?.addEventListener('click', () => setCollapsed(!isCollapsed()), { signal });
        closeBtn?.addEventListener('click', closeSidebar, { signal });
        backdrop?.addEventListener('click', closeSidebar, { signal });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 769) {
                closeSidebar();
                closeFlyouts();
            }
        }, { signal });

        const activeDropdown = dropdowns.find((dropdown) => {
            return dropdown.querySelector('.nav-dropdown-button.active, .nav-dropdown-content .active');
        });

        if (activeDropdown && !isCollapsed()) {
            setOpenDropdown(activeDropdown.dataset.sidebarSection);
        }

        dropdowns.forEach((dropdown) => {
            const btn = dropdown.querySelector('.nav-dropdown-button');
            if (!btn) {
                return;
            }

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                const nextSection = dropdown.dataset.sidebarSection;

                if (isCollapsed()) {
                    const willOpen = !dropdown.classList.contains('flyout-open');
                    closeFlyouts();
                    if (willOpen) {
                        dropdown.classList.add('flyout-open');
                        const content = dropdown.querySelector('.nav-dropdown-content');
                        if (content) {
                            content.hidden = false;
                        }
                    }
                    return;
                }

                setOpenDropdown(openDropdown === nextSection ? null : nextSection);
            }, { signal });
        });

        document.addEventListener('click', (event) => {
            if (!isCollapsed() || !sidebar.contains(event.target)) {
                closeFlyouts();
            }
        }, { signal });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeFlyouts();
                closeSidebar();
            }
        }, { signal });
    };

    if (!global.Turbo) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => global.initSidebar());
        } else {
            global.initSidebar();
        }
    }
})(window);
