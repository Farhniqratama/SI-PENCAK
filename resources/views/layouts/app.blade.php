<!DOCTYPE html>
<html lang="en" data-layout="topnav">

<head>
    <meta charset="utf-8" />
    <title>{{ $title ?? 'Dashboard' }} | Sipencak LLDIKTI III</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Sipencak Lldikti Wilayah III" name="description" />
    <meta content="Coderthemes" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{!! url('assets/attex/images/favicon.ico') !!}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/logo-lldikti3.jpg') }}">
    <meta name="theme-color" content="#0f5a8f">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="SIPENCAK">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="application-name" content="SIPENCAK">

    <!-- Theme Config Js -->
    @vite(['resources/js/head.js'])

    <!-- App css -->
    @vite(['resources/scss/app.scss'])
    <!-- Icons css -->
    @vite(['resources/scss/icons.scss'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @yield('css')

</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        @include('layouts.topbar')
        @include('layouts.horizontal-nav')

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->


        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                <div class="container">
                    
                    <!-- start page title -->
                    <div class="pt-bar">
                        <nav class="pt-breadcrumb" aria-label="breadcrumb">
                            <a href="{{ url(session('role') === 'operator' ? 'dashboard' : 'home') }}" class="pt-bc-home">
                                <i class="ri-home-4-line"></i>
                            </a>
                            <span class="pt-bc-sep">/</span>
                            <span class="pt-bc-current">{{ $title ?? 'Dashboard' }}</span>
                        </nav>
                        <h1 class="pt-heading">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                    <!-- end page title -->

                    @yield('content')
                </div> <!-- container -->
            </div> <!-- content -->
            
            @include('layouts.footer')
        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- App js -->
    @vite(['resources/js/app.js', 'resources/js/layout.js'])
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.bootstrap) {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                    new bootstrap.Tooltip(el);
                });
            }

            if (!window.bootstrap) {
                document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (toggle) {
                    toggle.addEventListener('click', function (event) {
                        event.preventDefault();
                        const menu = toggle.parentElement.querySelector('.dropdown-menu');
                        document.querySelectorAll('.dropdown-menu.show').forEach(function (openMenu) {
                            if (openMenu !== menu) {
                                openMenu.classList.remove('show');
                            }
                        });
                        if (menu) {
                            menu.classList.toggle('show');
                            toggle.setAttribute('aria-expanded', menu.classList.contains('show') ? 'true' : 'false');
                        }
                    });
                });

                document.addEventListener('click', function (event) {
                    if (!event.target.closest('.dropdown')) {
                        document.querySelectorAll('.dropdown-menu.show').forEach(function (menu) {
                            menu.classList.remove('show');
                        });
                    }
                });

                document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function (toggle) {
                    toggle.addEventListener('click', function () {
                        const modal = document.querySelector(toggle.getAttribute('data-bs-target'));
                        if (!modal) {
                            return;
                        }
                        modal.classList.add('show');
                        modal.style.display = 'block';
                        modal.removeAttribute('aria-hidden');
                        document.body.classList.add('modal-open');
                    });
                });

                document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function (toggle) {
                    toggle.addEventListener('click', function () {
                        const modal = toggle.closest('.modal');
                        if (!modal) {
                            return;
                        }
                        modal.classList.remove('show');
                        modal.style.display = 'none';
                        modal.setAttribute('aria-hidden', 'true');
                        document.body.classList.remove('modal-open');
                    });
                });
            }

            const attexConfigKey = '__ATTEX_CONFIG__';
            const html = document.documentElement;

            function readAttexConfig() {
                try {
                    return JSON.parse(sessionStorage.getItem(attexConfigKey) || '{}') || {};
                } catch (error) {
                    return {};
                }
            }

            function persistSidenavSize(size) {
                const baseConfig = window.config || {};
                const storedConfig = readAttexConfig();
                const nextConfig = Object.assign({}, baseConfig, storedConfig);
                nextConfig.nav = nextConfig.nav || 'vertical';
                nextConfig.layout = Object.assign({ mode: 'fluid', position: 'fixed' }, nextConfig.layout || {});
                nextConfig.topbar = Object.assign({ color: 'light' }, nextConfig.topbar || {});
                nextConfig.menu = Object.assign({ color: 'dark' }, nextConfig.menu || {});
                nextConfig.sidenav = Object.assign({ size: 'default', user: false }, nextConfig.sidenav || {}, { size: size });
                window.config = nextConfig;
                sessionStorage.setItem(attexConfigKey, JSON.stringify(nextConfig));
            }

            function applyPersistedSidenav() {
                const storedSize = readAttexConfig()?.sidenav?.size;
                if (window.innerWidth >= 768 && storedSize === 'condensed') {
                    html.classList.remove('sidebar-enable');
                    html.setAttribute('data-sidenav-size', 'condensed');
                }
            }

            applyPersistedSidenav();

            document.querySelector('.button-toggle-menu')?.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopImmediatePropagation();

                const html = document.documentElement;
                if (window.innerWidth < 768) {
                    html.classList.toggle('sidebar-enable');
                    return;
                }
                html.classList.remove('sidebar-enable');
                const current = html.getAttribute('data-sidenav-size') || 'default';
                const nextSize = current === 'condensed' ? 'default' : 'condensed';
                html.setAttribute('data-sidenav-size', nextSize);
                persistSidenavSize(nextSize);
            }, true);

            document.querySelectorAll('.leftside-menu .side-nav-link').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth >= 768 && html.getAttribute('data-sidenav-size') === 'condensed') {
                        persistSidenavSize('condensed');
                        html.setAttribute('data-sidenav-size', 'condensed');
                    }
                }, true);
            });

            document.querySelectorAll('.button-sm-hover').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    if (window.innerWidth >= 768 && html.getAttribute('data-sidenav-size') === 'condensed') {
                        event.preventDefault();
                        event.stopImmediatePropagation();
                        persistSidenavSize('condensed');
                        html.setAttribute('data-sidenav-size', 'condensed');
                    }
                }, true);
            });

            document.querySelector('.button-close-fullsidebar')?.addEventListener('click', function () {
                document.documentElement.classList.remove('sidebar-enable');
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) {
                    document.documentElement.classList.remove('sidebar-enable');
                    applyPersistedSidenav();
                }
            });

            function tableHasPagerNearby(table) {
                const pagerSelector = '.sipencak-pager, .sipencak-auto-pager, .pagination';
                const tableWrap = table.closest('.table-responsive') || table;

                let sibling = tableWrap.nextElementSibling;
                for (let i = 0; sibling && i < 6; i += 1) {
                    if (sibling.matches?.(pagerSelector) || sibling.querySelector?.(pagerSelector)) {
                        return true;
                    }
                    if (sibling.querySelector?.('table')) {
                        break;
                    }
                    sibling = sibling.nextElementSibling;
                }

                let parentSibling = tableWrap.parentElement?.nextElementSibling;
                for (let i = 0; parentSibling && i < 3; i += 1) {
                    if (parentSibling.matches?.(pagerSelector) || parentSibling.querySelector?.(pagerSelector)) {
                        return true;
                    }
                    if (parentSibling.querySelector?.('table')) {
                        break;
                    }
                    parentSibling = parentSibling.nextElementSibling;
                }

                return false;
            }

            function initAutoTablePager() {
                const pageSize = 10;

                document.querySelectorAll('.content-page table').forEach(function (table) {
                    if (table.dataset.autoPagerReady === '1' || table.dataset.noAutoPager === '1') {
                        return;
                    }

                    if (table.closest('.dataTables_wrapper') || tableHasPagerNearby(table)) {
                        table.dataset.autoPagerReady = '1';
                        return;
                    }

                    const tbody = table.tBodies[0];
                    if (!tbody) {
                        return;
                    }

                    const rows = Array.from(tbody.rows).filter(function (row) {
                        return !row.querySelector('td[colspan]');
                    });

                    if (rows.length === 0) {
                        table.dataset.autoPagerReady = '1';
                        return;
                    }

                    let currentPage = 1;
                    const totalPages = Math.ceil(rows.length / pageSize);
                    const tableWrap = table.closest('.table-responsive') || table;
                    const pager = document.createElement('div');
                    pager.className = 'table-footer-pager sipencak-auto-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3';

                    const info = document.createElement('div');
                    info.className = 'text-muted small fw-bold';

                    const nav = document.createElement('nav');
                    nav.className = 'sipencak-pager d-flex justify-content-end';
                    nav.setAttribute('aria-label', 'Table pagination');

                    const list = document.createElement('ul');
                    list.className = 'pagination pagination-sm mb-0';
                    nav.appendChild(list);
                    pager.appendChild(info);
                    pager.appendChild(nav);
                    tableWrap.insertAdjacentElement('afterend', pager);

                    function makeItem(label, page, disabled, active) {
                        const item = document.createElement('li');
                        item.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');

                        const link = document.createElement(disabled || active ? 'span' : 'button');
                        link.className = 'page-link';
                        link.innerHTML = label;

                        if (!disabled && !active) {
                            link.type = 'button';
                            link.addEventListener('click', function () {
                                currentPage = page;
                                render();
                            });
                        }

                        item.appendChild(link);
                        return item;
                    }

                    function render() {
                        const start = (currentPage - 1) * pageSize;
                        const end = start + pageSize;

                        rows.forEach(function (row, index) {
                            row.style.display = index >= start && index < end ? '' : 'none';
                        });

                        info.innerHTML = 'Menampilkan <span class="text-primary">' + (start + 1) + '-' + Math.min(end, rows.length) + '</span> dari total <span class="text-primary">' + rows.length + '</span> data';
                        list.innerHTML = '';
                        list.appendChild(makeItem('<i class="ri-arrow-left-s-line"></i>', Math.max(1, currentPage - 1), currentPage === 1, false));

                        for (let page = 1; page <= totalPages; page += 1) {
                            if (page === 1 || page === totalPages || Math.abs(page - currentPage) <= 1) {
                                list.appendChild(makeItem(String(page), page, false, page === currentPage));
                            } else if (page === currentPage - 2 || page === currentPage + 2) {
                                list.appendChild(makeItem('...', page, true, false));
                            }
                        }

                        list.appendChild(makeItem('<i class="ri-arrow-right-s-line"></i>', Math.min(totalPages, currentPage + 1), currentPage === totalPages, false));
                    }

                    table.dataset.autoPagerReady = '1';
                    render();
                });
            }

            initAutoTablePager();
        });
    </script>
    <script>
        window.addEventListener('load', function () {
            if (!('serviceWorker' in navigator)) {
                return;
            }

            navigator.serviceWorker.register('{{ asset('sw.js') }}')
                .then(function (registration) {
                    if (registration.waiting) {
                        registration.waiting.postMessage('SIPENCAK_SKIP_WAITING');
                    }

                    registration.addEventListener('updatefound', function () {
                        const nextWorker = registration.installing;
                        if (!nextWorker) {
                            return;
                        }

                        nextWorker.addEventListener('statechange', function () {
                            if (nextWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                nextWorker.postMessage('SIPENCAK_SKIP_WAITING');
                            }
                        });
                    });
                })
                .catch(function () {
                    // PWA support is progressive; the app keeps working normally if registration fails.
                });
        });

        window.addEventListener('beforeinstallprompt', function (event) {
            event.preventDefault();
            window.sipencakInstallPrompt = event;
            document.dispatchEvent(new CustomEvent('sipencak:pwa-install-ready'));
        });

        window.addEventListener('appinstalled', function () {
            window.sipencakInstallPrompt = null;
            localStorage.setItem('sipencak_pwa_installed', '1');
        });
    </script>
    
    @yield('js')
</body>

</html>
