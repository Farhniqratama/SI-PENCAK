@php
    $role = session('role');
    $isOperator = $role === 'operator';
    $homeUrl = $isOperator ? url('dashboard') : url('home');
    $displayName = session('nama') ?? session('username') ?? ($isOperator ? 'Operator Sipencak' : 'Admin PT');
    $displayRole = $isOperator ? 'Operator LLDIKTI' : 'Admin PT';

    $operatorMenu = [
        ['title' => 'Navigation', 'items' => [
            ['label' => 'Dashboard', 'icon' => 'ri-home-4-line', 'url' => url('dashboard'), 'active' => ['dashboard']],
            ['label' => 'Notifikasi', 'icon' => 'ri-notification-3-line', 'url' => url('operator/notifikasi'), 'active' => ['operator/notifikasi']],
        ]],
        ['title' => 'Master Data', 'items' => [
            ['label' => 'Data User PT', 'icon' => 'ri-user-star-line', 'url' => url('userpt-list'), 'active' => ['userpt-*']],
            ['label' => 'Data Perguruan Tinggi', 'icon' => 'ri-bank-line', 'url' => url('pt-list'), 'active' => ['pt-*']],
        ]],
        ['title' => 'Operasional', 'items' => [
            ['label' => 'Papan Informasi', 'icon' => 'ri-megaphone-line', 'url' => url('informasi-list'), 'active' => ['informasi-list', 'informasi-create', 'informasi-edit/*', 'informasi-show/*']],
            ['label' => 'Verifikasi Pencairan', 'icon' => 'ri-wallet-3-line', 'url' => url('pencairan-list'), 'active' => ['pencairan-*']],
            ['label' => 'Laporan Pencairan', 'icon' => 'ri-file-chart-line', 'url' => url('laporan'), 'active' => ['laporan', 'laporan-*']],
            ['label' => 'Audit Trail', 'icon' => 'ri-history-line', 'url' => url('activity-logs'), 'active' => ['activity-logs']],
        ]],
    ];

    $adminMenu = [
        ['title' => 'Navigation', 'items' => [
            ['label' => 'Dashboard', 'icon' => 'ri-home-4-line', 'url' => url('home'), 'active' => ['home']],
            ['label' => 'Notifikasi', 'icon' => 'ri-notification-3-line', 'url' => url('admin/notifikasi'), 'active' => ['admin/notifikasi']],
        ]],
        ['title' => 'Akademik', 'items' => [
            ['label' => 'Data Prodi', 'icon' => 'ri-book-read-line', 'url' => url('prodi-list'), 'active' => ['prodi-*']],
            ['label' => 'Data Mahasiswa', 'icon' => 'ri-group-line', 'url' => url('mahasiswa-list'), 'active' => ['mahasiswa-*']],
        ]],
        ['title' => 'Pencairan KIP-K', 'items' => [
            ['label' => 'Pembaharuan Status', 'icon' => 'ri-refresh-line', 'url' => url('verifikasi-pembaharuan-status'), 'active' => ['verifikasi-pembaharuan-status', 'verifikasi-detail/*', 'verifikasi-edit/*']],
            ['label' => 'Draft Permohonan', 'icon' => 'ri-draft-line', 'url' => url('admin/pencairan/draft'), 'active' => ['admin/pencairan/draft']],
            ['label' => 'Ajukan Permohonan', 'icon' => 'ri-send-plane-line', 'url' => url('permohonan-pencairan'), 'active' => ['permohonan-pencairan', 'verifikasi-mahasiswa/*', 'finalisasi-verifikasi/*']],
            ['label' => 'Riwayat Pencairan', 'icon' => 'ri-file-chart-line', 'url' => url('admin/laporan'), 'active' => ['admin/laporan', 'admin/laporan-*']],
        ]],
        ['title' => 'Informasi', 'items' => [
            ['label' => 'Papan Informasi', 'icon' => 'ri-megaphone-line', 'url' => url('papan-informasi'), 'active' => ['papan-informasi', 'informasi-detail/*']],
        ]],
    ];

    $menuGroups = $isOperator ? $operatorMenu : $adminMenu;
@endphp


<!-- ========== Left Sidebar Start ========== -->
<div class="leftside-menu bg-white border-end">
    <a href="{{ $homeUrl }}" class="logo logo-light">
        <span class="logo-lg d-flex align-items-center justify-content-center gap-2" style="height: 70px;">
            <img src="{{ url('assets/img/sipencak3.png') }}" alt="Sipencak" height="28" style="object-fit: contain;">
            <span style="font-size: 18px; color: #cbd5e1; font-weight: 300; line-height: 1;">|</span>
            <img src="{{ url('assets/img/lldikti3.png') }}" alt="LLDIKTI" height="28" style="object-fit: contain;">
        </span>
        <span class="logo-sm">
            <img src="{{ url('assets/img/sipencak3.png') }}" alt="Sipencak" height="32" width="32" style="object-fit: contain;">
        </span>
    </a>

    <a href="{{ $homeUrl }}" class="logo logo-dark">
        <span class="logo-lg d-flex align-items-center justify-content-center gap-2" style="height: 70px;">
            <img src="{{ url('assets/img/sipencak3.png') }}" alt="Sipencak" height="28" style="object-fit: contain;">
            <span style="font-size: 18px; color: #cbd5e1; font-weight: 300; line-height: 1;">|</span>
            <img src="{{ url('assets/img/lldikti3.png') }}" alt="LLDIKTI" height="28" style="object-fit: contain;">
        </span>
        <span class="logo-sm">
            <img src="{{ url('assets/img/sipencak3.png') }}" alt="Sipencak" height="32" width="32" style="object-fit: contain;">
        </span>
    </a>

    <button type="button" class="button-sm-hover border-0 bg-transparent" data-bs-toggle="tooltip" data-bs-placement="right" title="Perbesar sidebar">
        <i class="ri-checkbox-blank-circle-line align-middle"></i>
    </button>

    <button type="button" class="button-close-fullsidebar border-0 bg-transparent" aria-label="Tutup sidebar">
        <i class="ri-close-fill align-middle"></i>
    </button>

    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <div class="leftbar-user">
            <a href="{{ $homeUrl }}">
                <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm" style="width: 48px; height: 48px; overflow: hidden;">
                    <img src="{{ url('assets/img/lldikti3.png') }}" alt="user-image" width="36" style="object-fit: contain;">
                </div>
                <span class="leftbar-user-name mt-2">{{ $displayName }}</span>
            </a>
        </div>

        <ul class="side-nav">
            @foreach($menuGroups as $group)
                <li class="side-nav-title">{{ $group['title'] }}</li>

                @foreach($group['items'] as $item)
                    @php $active = collect($item['active'])->contains(fn ($pattern) => request()->is($pattern)); @endphp
                    <li class="side-nav-item {{ $active ? 'menuitem-active' : '' }}">
                        <a href="{{ $item['url'] }}" class="side-nav-link {{ $active ? 'active' : '' }}" {!! isset($item['data_toggle']) ? 'data-bs-toggle="offcanvas" role="button" aria-controls="notificationOffcanvas"' : '' !!}>
                            <i class="{{ $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            @endforeach
        </ul>

        <div class="clearfix"></div>
    </div>
</div>
<!-- ========== Left Sidebar End ========== -->
