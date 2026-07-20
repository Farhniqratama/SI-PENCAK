@php
$role  = session('role');
$uname = session('username') ?? 'Pengguna';
$initial = strtoupper(mb_substr($uname, 0, 1));
@endphp

<header class="top-navbar">
    <div class="top-navbar-left">
        <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle Sidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="page-title-breadcrumbs">
            <span>Sistem Informasi Pencairan Dana KIP-K (SIPENCAK)</span>
        </div>
    </div>

    <div class="top-navbar-right">
        <!-- Status -->
        <div class="header-status-chip">
            <span class="status-dot"></span>
            <span>Online</span>
        </div>

        <!-- Notification -->
        <div class="cb-pop-wrap" id="cbNotifWrap">
            <button class="header-icon-btn" id="cbNotifBtn" title="Notifikasi">
                <i class="fa-solid fa-bell"></i>
                <span class="notif-badge">1</span>
            </button>
            <div class="cb-popup" id="cbNotifPop">
                <div class="cb-popup__hd">
                    <span>Notifikasi</span>
                    <span class="cb-pill-count">1 Baru</span>
                </div>
                <div class="cb-popup__bd">
                    <a href="#" class="cb-notif-row">
                        <div class="cb-notif-ico"><i class="fa-solid fa-circle-info"></i></div>
                        <div>
                            <p class="cb-notif-msg">Sistem berhasil diperbarui ke tampilan baru.</p>
                            <span class="cb-notif-time"><i class="fa-regular fa-clock"></i> Baru saja</span>
                        </div>
                    </a>
                </div>
                <a href="#" class="cb-popup__ft-link">Semua Notifikasi &rarr;</a>
            </div>
        </div>

        <!-- Vertical Divider -->
        <div class="header-divider"></div>

        <!-- Profile -->
        <div class="cb-pop-wrap" id="cbProfileWrap">
            <button class="header-user-btn" id="cbProfileBtn" title="Profil">
                <div class="header-user-avatar">
                    <img src="{!! url('assets/img/undraw_profile.svg') !!}" alt="Avatar"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <span class="header-user-init" style="display:none">{!! $initial !!}</span>
                </div>
                <div class="header-user-meta">
                    <span class="header-user-name">{!! htmlspecialchars($uname) !!}</span>
                    <span class="header-user-role">{!! strtoupper($role ?? 'USER') !!}</span>
                </div>
                <i class="fa-solid fa-chevron-down header-user-caret"></i>
            </button>
            <div class="cb-popup cb-popup--user" id="cbProfilePop">
                <div class="cb-popup__profile-hd">
                    <div class="header-user-avatar">
                        <img src="{!! url('assets/img/undraw_profile.svg') !!}" alt="Avatar">
                    </div>
                    <div>
                        <div class="cb-ph__name">{!! htmlspecialchars($uname) !!}</div>
                        <div class="cb-ph__chip">{!! strtoupper($role ?? 'USER') !!}</div>
                    </div>
                </div>
                <div class="cb-popup__bd">
                    <a href="#" class="cb-pm"><i class="fa-solid fa-user-circle cb-pm__ico cb-pm__ico--blue"></i> Profil Saya</a>
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#modalPassword" class="cb-pm">
                        <i class="fa-solid fa-shield-halved cb-pm__ico cb-pm__ico--green"></i> Ubah Kata Sandi
                    </a>
                </div>
                <div class="cb-popup__bd cb-popup__bd--border">
                    <a href="{!! url('logout') !!}" class="cb-pm cb-pm--danger">
                        <i class="fa-solid fa-right-from-bracket cb-pm__ico"></i> Akhiri Sesi
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
(function () {
    'use strict';

    /* Toggle popup dropdowns (Notif & Profile) */
    function initPopup(btnId, popId, wrapId) {
        var btn  = document.getElementById(btnId);
        var pop  = document.getElementById(popId);
        var wrap = wrapId ? document.getElementById(wrapId) : null;
        if (!btn || !pop) return;
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var isOpen = pop.classList.contains('open');
            document.querySelectorAll('.cb-popup.open').forEach(function (el) { el.classList.remove('open'); });
            document.querySelectorAll('.cb-pop-wrap.open').forEach(function (el) { el.classList.remove('open'); });
            if (!isOpen) { pop.classList.add('open'); if (wrap) wrap.classList.add('open'); }
        });
    }
    initPopup('cbNotifBtn',   'cbNotifPop',   'cbNotifWrap');
    initPopup('cbProfileBtn', 'cbProfilePop', 'cbProfileWrap');
    document.addEventListener('click', function () {
        document.querySelectorAll('.cb-popup.open').forEach(function (el) { el.classList.remove('open'); });
        document.querySelectorAll('.cb-pop-wrap.open').forEach(function (el) { el.classList.remove('open'); });
    });
})();
</script>
