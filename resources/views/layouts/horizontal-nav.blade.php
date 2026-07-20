@php
    $role = session('role');
    $isOperator = $role === 'operator';
    
    $operatorMenu = [
        ['title' => 'Navigation', 'icon' => 'ri-dashboard-line', 'items' => [
            ['label' => 'Dashboard', 'icon' => 'ri-home-4-line', 'url' => url('dashboard'), 'active' => ['dashboard']],
            ['label' => 'Notifikasi', 'icon' => 'ri-notification-3-line', 'url' => url('operator/notifikasi'), 'active' => ['operator/notifikasi']],
        ]],
        ['title' => 'Master Data', 'icon' => 'ri-database-2-line', 'items' => [
            ['label' => 'Data User PT', 'icon' => 'ri-user-star-line', 'url' => url('userpt-list'), 'active' => ['userpt-*']],
            ['label' => 'Data Perguruan Tinggi', 'icon' => 'ri-bank-line', 'url' => url('pt-list'), 'active' => ['pt-*']],
        ]],
        ['title' => 'Operasional', 'icon' => 'ri-briefcase-line', 'items' => [
            ['label' => 'Papan Informasi', 'icon' => 'ri-megaphone-line', 'url' => url('informasi-list'), 'active' => ['informasi-list', 'informasi-create', 'informasi-edit/*', 'informasi-show/*']],
            ['label' => 'Verifikasi Pencairan', 'icon' => 'ri-wallet-3-line', 'url' => url('pencairan-list'), 'active' => ['pencairan-*']],
            ['label' => 'Laporan Pencairan', 'icon' => 'ri-file-chart-line', 'url' => url('laporan'), 'active' => ['laporan', 'laporan-*']],
            ['label' => 'Audit Trail', 'icon' => 'ri-history-line', 'url' => url('activity-logs'), 'active' => ['activity-logs']],
        ]],
    ];

    $adminMenu = [
        ['title' => 'Navigation', 'icon' => 'ri-dashboard-line', 'items' => [
            ['label' => 'Dashboard', 'icon' => 'ri-home-4-line', 'url' => url('home'), 'active' => ['home']],
            ['label' => 'Notifikasi', 'icon' => 'ri-notification-3-line', 'url' => url('admin/notifikasi'), 'active' => ['admin/notifikasi']],
        ]],
        ['title' => 'Akademik', 'icon' => 'ri-book-open-line', 'items' => [
            ['label' => 'Data Prodi', 'icon' => 'ri-book-read-line', 'url' => url('prodi-list'), 'active' => ['prodi-*']],
            ['label' => 'Data Mahasiswa', 'icon' => 'ri-group-line', 'url' => url('mahasiswa-list'), 'active' => ['mahasiswa-*']],
        ]],
        ['title' => 'Pencairan KIP-K', 'icon' => 'ri-money-dollar-circle-line', 'items' => [
            ['label' => 'Pembaharuan Status', 'icon' => 'ri-refresh-line', 'url' => url('verifikasi-pembaharuan-status'), 'active' => ['verifikasi-pembaharuan-status', 'verifikasi-detail/*', 'verifikasi-edit/*']],
            ['label' => 'Draft Permohonan', 'icon' => 'ri-draft-line', 'url' => url('admin/pencairan/draft'), 'active' => ['admin/pencairan/draft']],
            ['label' => 'Ajukan Permohonan', 'icon' => 'ri-send-plane-line', 'url' => url('permohonan-pencairan'), 'active' => ['permohonan-pencairan', 'verifikasi-mahasiswa/*', 'finalisasi-verifikasi/*']],
            ['label' => 'Riwayat Pencairan', 'icon' => 'ri-file-chart-line', 'url' => url('admin/laporan'), 'active' => ['admin/laporan', 'admin/laporan-*']],
        ]],
        ['title' => 'Informasi', 'icon' => 'ri-information-line', 'items' => [
            ['label' => 'Papan Informasi', 'icon' => 'ri-megaphone-line', 'url' => url('papan-informasi'), 'active' => ['papan-informasi', 'informasi-detail/*']],
        ]],
    ];

    $menuGroups = $isOperator ? $operatorMenu : $adminMenu;
@endphp

<!-- ========== Horizontal Menu Start ========== -->
<div class="topnav">
    <div class="container active">
        <nav class="navbar navbar-expand-lg">
            <div class="collapse navbar-collapse active" id="topnav-menu-content">
                <ul class="navbar-nav">
                    @foreach($menuGroups as $group)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-{{ Str::slug($group['title']) }}" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="{{ $group['icon'] }}"></i>{{ $group['title'] }} <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="topnav-{{ Str::slug($group['title']) }}">
                                @foreach($group['items'] as $item)
                                    @php $active = collect($item['active'])->contains(fn ($pattern) => request()->is($pattern)); @endphp
                                    <a href="{{ $item['url'] }}" class="dropdown-item {{ $active ? 'active' : '' }}">
                                        <i class="{{ $item['icon'] }} me-1"></i> {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </nav>
    </div>
</div>
<!-- ========== Horizontal Menu End ========== -->
