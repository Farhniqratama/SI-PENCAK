@php
    $role = session('role');
    $isOperator = $role === 'operator';
    $displayName = $displayName ?? session('nama') ?? session('username') ?? ($isOperator ? 'Operator' : 'Admin PT');
    $roleLabel = $isOperator ? 'Operator LLDIKTI' : ($role === 'admin' ? 'Admin PT' : 'Pengguna');
    $dashboardUrl = url($isOperator ? 'dashboard' : 'home');
    $notificationIndexUrl = url($isOperator ? 'operator/notifikasi' : 'admin/notifikasi');
    $passwordUrl = $dashboardUrl . '#password';
    $topSearchAction = $isOperator ? url('pencairan-list') : url('verifikasi-pembaharuan-status');
    $topSearchName = $isOperator ? 'search' : 'keyword';
    $quickLinks = $isOperator
        ? [
            ['label' => 'Dashboard', 'icon' => 'ri-dashboard-3-line', 'url' => url('dashboard')],
            ['label' => 'Pencairan', 'icon' => 'ri-money-dollar-circle-line', 'url' => url('pencairan-list')],
            ['label' => 'Perguruan Tinggi', 'icon' => 'ri-building-4-line', 'url' => url('pt-list')],
            ['label' => 'Notifikasi', 'icon' => 'ri-notification-3-line', 'url' => url('operator/notifikasi')],
        ]
        : [
            ['label' => 'Dashboard', 'icon' => 'ri-dashboard-3-line', 'url' => url('home')],
            ['label' => 'Permohonan Pencairan', 'icon' => 'ri-money-dollar-circle-line', 'url' => url('verifikasi-pembaharuan-status')],
            ['label' => 'Mahasiswa', 'icon' => 'ri-graduation-cap-line', 'url' => url('mahasiswa-list')],
            ['label' => 'Notifikasi', 'icon' => 'ri-notification-3-line', 'url' => url('admin/notifikasi')],
        ];

    $topbarNotifications = collect();
    $unreadNotificationCount = 0;

    if ($role) {
        $notificationQuery = \App\Support\NotificationService::queryForCurrentUser($role);

        $topbarNotifications = (clone $notificationQuery)
            ->orderBy('notifications.created_at', 'desc')
            ->take(6)
            ->get();

        $unreadNotificationCount = (clone $notificationQuery)
            ->whereRaw('COALESCE(personal_state.is_read, notifications.is_read) = 0')
            ->count();
    }
@endphp

<!-- ========== Topbar Start ========== -->
<div class="navbar-custom">
    <div class="topbar container">
        <div class="d-flex align-items-center gap-lg-2 gap-1">
            <div class="logo-topbar">
                <a href="{{ $dashboardUrl }}" class="d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ url('/assets/img/sipencak3.png') }}" alt="logo" height="30">
                </a>
            </div>

            <button class="button-toggle-menu">
                <i class="ri-menu-2-fill"></i>
            </button>

            <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>

            <div class="app-search dropdown d-none d-lg-block">
                <form action="{{ $topSearchAction }}" method="get">
                    <div class="input-group">
                        <input type="search" name="{{ $topSearchName }}" class="form-control dropdown-toggle" placeholder="Cari data..." id="top-search" value="{{ request($topSearchName) }}">
                        <span class="ri-search-line search-icon"></span>
                    </div>
                </form>

                <div class="dropdown-menu dropdown-menu-animated dropdown-lg" id="search-dropdown">
                    <div class="dropdown-header noti-title">
                        <h5 class="text-overflow mb-1">Shortcut SIPENCAK</h5>
                    </div>

                    @foreach($quickLinks as $link)
                        <a href="{{ $link['url'] }}" class="dropdown-item notify-item">
                            <i class="{{ $link['icon'] }} fs-16 me-1"></i>
                            <span>{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <ul class="topbar-menu d-flex align-items-center gap-3">
            <li class="d-none d-md-inline-block">
                <a class="nav-link d-flex align-items-center gap-1" href="{{ route('public.home') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Ke Halaman Publik">
                    <i class="ri-home-8-line fs-22"></i>
                </a>
            </li>

            <li class="dropdown d-lg-none">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="ri-search-line fs-22"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                    <form class="p-3" action="{{ $topSearchAction }}" method="get">
                        <input type="search" name="{{ $topSearchName }}" class="form-control" placeholder="Cari data..." value="{{ request($topSearchName) }}">
                    </form>
                </div>
            </li>

            <li class="dropdown notification-list">
                <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <i class="ri-notification-3-line fs-22"></i>
                    @if($unreadNotificationCount > 0)
                        <span class="noti-icon-badge">{{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                    <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0 fs-16 fw-semibold">Notifikasi</h6>
                            </div>
                            <div class="col-auto d-flex align-items-center gap-2">
                                @if($unreadNotificationCount > 0)
                                    <form action="{{ route('notifications.read-all') }}" method="post" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm p-0 text-dark text-decoration-underline">
                                            Read all
                                        </button>
                                    </form>
                                @else
                                    <small class="text-muted">Semua terbaca</small>
                                @endif
                                @if($topbarNotifications->count() > 0)
                                    <form action="{{ route('notifications.delete-all') }}" method="post" class="m-0" onsubmit="return confirm('Hapus semua notifikasi dari akun ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm p-0 text-danger text-decoration-underline">
                                            Delete all
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div style="max-height: 320px;" data-simplebar>
                        @forelse($topbarNotifications as $notif)
                            @php
                                $notifType = $notif->type ?: 'primary';
                                $notifIcon = match ($notifType) {
                                    'success' => 'ri-checkbox-circle-line',
                                    'danger' => 'ri-close-circle-line',
                                    'warning' => 'ri-alert-line',
                                    'info' => 'ri-information-line',
                                    default => 'ri-notification-3-line',
                                };
                            @endphp

                            <div class="dropdown-item p-0 notify-item {{ $notif->is_read ? 'read-noti' : 'unread-noti' }} card m-0 shadow-none">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <div class="notify-icon bg-{{ $notifType }}">
                                                <i class="{{ $notifIcon }} fs-18"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <div class="d-flex align-items-start justify-content-between gap-2">
                                                <h5 class="noti-item-title fw-semibold fs-14 mb-1">{{ $notif->title }}</h5>
                                                <form action="{{ route('notifications.delete', $notif->id) }}" method="post" class="m-0 flex-shrink-0">
                                                    @csrf
                                                    <button type="submit" class="notification-mini-delete" title="Hapus notifikasi" onclick="return confirm('Hapus notifikasi ini dari akun ini?')">
                                                        <i class="ri-close-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <small class="noti-item-subtitle text-muted">{{ \Illuminate\Support\Str::limit($notif->message, 76) }}</small>
                                            <div class="d-flex align-items-center justify-content-between gap-2 mt-2">
                                                <small class="fw-normal text-muted">{{ $notif->created_at?->diffForHumans() }}</small>
                                                <a href="{{ route('notifications.open', $notif->id) }}" class="notification-mini-link">
                                                    Buka
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center px-3 py-4">
                                <i class="ri-notification-off-line fs-28 text-muted"></i>
                                <p class="mb-0 mt-2 text-muted">Belum ada notifikasi.</p>
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ $notificationIndexUrl }}" class="dropdown-item text-center text-primary text-decoration-underline fw-bold notify-item border-top border-light py-2">
                        Lihat Semua
                    </a>
                </div>
            </li>

            <li class="d-none d-sm-inline-block">
                <a class="nav-link" data-bs-toggle="offcanvas" href="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas">
                    <i class="ri-settings-3-line fs-22"></i>
                </a>
            </li>

            <li class="d-none d-sm-inline-block">
                <div class="nav-link" id="light-dark-mode" data-bs-toggle="tooltip" data-bs-placement="left" title="Theme Mode">
                    <i class="ri-moon-line fs-22"></i>
                </div>
            </li>

            <li class="d-none d-md-inline-block">
                <a class="nav-link" href="" data-toggle="fullscreen">
                    <i class="ri-fullscreen-line fs-22"></i>
                </a>
            </li>

            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="account-user-avatar">
                        <img src="{{ url('assets/img/logo-lldikti3.jpg') }}" alt="user-image" width="32" class="rounded-circle">
                    </span>
                    <span class="d-lg-flex flex-column gap-1 d-none">
                        <h5 class="my-0">{{ $displayName }}</h5>
                        <h6 class="my-0 fw-normal">{{ $roleLabel }}</h6>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Selamat datang</h6>
                    </div>

                    <a href="{{ $dashboardUrl }}" class="dropdown-item">
                        <i class="ri-dashboard-3-line fs-18 align-middle me-1"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ $notificationIndexUrl }}" class="dropdown-item">
                        <i class="ri-notification-3-line fs-18 align-middle me-1"></i>
                        <span>Notifikasi</span>
                    </a>

                    <a href="#theme-settings-offcanvas" data-bs-toggle="offcanvas" class="dropdown-item">
                        <i class="ri-settings-4-line fs-18 align-middle me-1"></i>
                        <span>Pengaturan</span>
                    </a>

                    <a href="{{ $passwordUrl }}" class="dropdown-item">
                        <i class="ri-lock-password-line fs-18 align-middle me-1"></i>
                        <span>Ubah Password</span>
                    </a>

                    <a href="{{ url('logout') }}" class="dropdown-item">
                        <i class="ri-logout-box-line fs-18 align-middle me-1"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>
<!-- ========== Topbar End ========== -->

<div class="offcanvas offcanvas-end" tabindex="-1" id="theme-settings-offcanvas" aria-labelledby="theme-settings-offcanvas-label">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title mb-1" id="theme-settings-offcanvas-label">Pengaturan Tampilan</h5>
            <p class="text-muted mb-0">Atur mode dan akses cepat aplikasi.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="mb-4">
            <h6 class="fw-semibold text-uppercase text-muted mb-3">Mode Tema</h6>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-outline-primary text-start" onclick="window.setSipencakTheme && window.setSipencakTheme('light');">
                    <i class="ri-sun-line me-1"></i> Mode Terang
                </button>
                <button type="button" class="btn btn-outline-primary text-start" onclick="window.setSipencakTheme && window.setSipencakTheme('dark');">
                    <i class="ri-moon-line me-1"></i> Mode Malam
                </button>
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-semibold text-uppercase text-muted mb-3">Akses Cepat</h6>
            <div class="list-group list-group-flush">
                @foreach($quickLinks as $link)
                    <a href="{{ $link['url'] }}" class="list-group-item list-group-item-action px-0">
                        <i class="{{ $link['icon'] }} me-2 text-primary"></i>{{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="alert alert-primary bg-primary-subtle text-primary border-0 mb-0">
            <i class="ri-user-settings-line me-1"></i>
            Login sebagai <strong>{{ $roleLabel }}</strong>.
        </div>
    </div>
</div>

<script>
    window.setSipencakTheme = function (theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('sipencak-theme', theme);
    };

    (function () {
        var savedTheme = localStorage.getItem('sipencak-theme');

        if (savedTheme) {
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        }
    })();
</script>
