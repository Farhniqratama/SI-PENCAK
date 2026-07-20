<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="theme-color" content="#2B79B4">
    <title>Detail Mahasiswa - SIPENCAK</title>
    <link rel="shortcut icon" href="{{ url('/assets/img/lldikti3.png') }}">
    <link rel="manifest" href="{{ url('/manifest.webmanifest') }}">

    <link rel="stylesheet" href="{{ url('/assets/synox/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/synox/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/synox/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/synox/css/style.css') }}">

    <style>
        :root {
            --sipencak-primary: #2B79B4;
            --sipencak-secondary: #3F96CD;
            --sipencak-dark: #142334;
            --sipencak-muted: #6B7C8F;
            --sipencak-line: rgba(43, 121, 180, 0.18);
            --sipencak-radius: 10px;
            --sipencak-soft: #EEF7FC;
            --sipencak-success: #2fac66;
        }

        body.public-home {
            background: #f3f8fc;
            color: var(--sipencak-dark);
        }

        .sipencak-public-header {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1001;
            padding: 18px 0;
            background: rgba(255, 255, 255, 0.94);
            border-bottom: 1px solid var(--sipencak-line);
            box-shadow: 0 20px 60px rgba(43, 121, 180, 0.08);
            backdrop-filter: blur(16px);
        }

        .sipencak-public-header .xb-header {
            background: transparent;
        }

        .sipencak-public-header .site_logo .site_link {
            max-width: 188px;
        }

        .sipencak-public-header .site_logo img {
            width: 188px;
            max-height: 56px;
            object-fit: contain;
        }

        body.public-home .sipencak-public-header .main_menu_list {
            gap: 38px;
        }

        body.public-home .sipencak-public-header .main_menu_list > li > a,
        body.public-home .sipencak-public-header .main_menu_list > li > a::after {
            color: var(--sipencak-dark) !important;
            font-weight: 700;
        }

        .sipencak-login-btn,
        .sipencak-primary-btn {
            min-height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 0 24px !important;
            border: 1px solid var(--sipencak-primary) !important;
            border-radius: var(--sipencak-radius) !important;
            background: var(--sipencak-primary) !important;
            color: #ffffff !important;
            font-weight: 900;
        }

        .sipencak-login-btn:hover,
        .sipencak-primary-btn:hover {
            background: #236596 !important;
            border-color: #236596 !important;
            color: #ffffff !important;
        }

        .sipencak-login-btn:hover .btn_icon,
        .sipencak-login-btn:hover .btn_label,
        .sipencak-login-btn:hover i,
        .sipencak-primary-btn:hover i {
            color: #ffffff !important;
        }

        .sipencak-login-btn::before,
        .sipencak-login-btn::after,
        .sipencak-primary-btn::before,
        .sipencak-primary-btn::after {
            display: none !important;
        }

        .sipencak-page-hero {
            position: relative;
            overflow: hidden;
            padding: 154px 0 124px;
            background:
                radial-gradient(circle at 84% 16%, rgba(118, 179, 216, 0.3), transparent 24%),
                linear-gradient(135deg, #3F96CD 0%, #2B79B4 54%, #1B5B8F 100%);
            color: #ffffff;
        }

        .sipencak-page-hero::before {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.12) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.12) 1px, transparent 1px);
            background-size: 120px 120px;
            opacity: 0.34;
            content: "";
        }

        .sipencak-page-hero .container {
            position: relative;
            z-index: 2;
        }

        .sipencak-breadcrumb {
            color: rgba(255, 255, 255, 0.72);
            font-weight: 800;
        }

        .sipencak-breadcrumb a {
            color: #ffffff;
        }

        .sipencak-page-hero h1 {
            margin: 12px 0 0;
            color: #ffffff;
            font-size: clamp(2.5rem, 5vw, 4.6rem);
            line-height: 1.04;
        }

        .sipencak-detail-area {
            position: relative;
            overflow: visible;
            padding-bottom: 150px;
            background: #f3f8fc;
        }

        .sipencak-detail-area::before {
            position: absolute;
            inset: 0;
            background-color: rgba(43, 121, 180, 0.08);
            -webkit-mask-image: url("{{ url('/images/tutwuri.png') }}");
            mask-image: url("{{ url('/images/tutwuri.png') }}");
            -webkit-mask-repeat: repeat;
            mask-repeat: repeat;
            -webkit-mask-size: 118px 118px;
            mask-size: 118px 118px;
            opacity: 0.42;
            content: "";
            pointer-events: none;
        }

        .sipencak-detail-area .container {
            position: relative;
            z-index: 2;
        }

        .sipencak-detail-shell {
            margin-bottom: -104px;
            padding: 30px;
            border: 1px solid var(--sipencak-line);
            border-radius: 22px;
            background: #ffffff;
            box-shadow: 0 30px 80px rgba(27, 91, 143, 0.18);
            transform: translateY(-104px);
        }

        .sipencak-profile-hero {
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(280px, 0.75fr);
            gap: 24px;
            margin-bottom: 26px;
            padding: 28px;
            border: 1px solid rgba(43, 121, 180, 0.18);
            border-radius: 18px;
            background:
                linear-gradient(135deg, rgba(43, 121, 180, 0.1), rgba(255, 255, 255, 0) 52%),
                #ffffff;
        }

        .sipencak-profile-main,
        .sipencak-profile-side {
            position: relative;
            z-index: 1;
        }

        .sipencak-avatar-mark {
            width: 64px;
            height: 64px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            border-radius: 16px;
            background: var(--sipencak-primary);
            color: #ffffff;
            font-size: 1.65rem;
        }

        .sipencak-profile-hero h2 {
            margin: 0;
            color: var(--sipencak-dark);
            font-size: clamp(2rem, 3vw, 3.1rem);
            line-height: 1.08;
        }

        .sipencak-profile-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 16px;
            margin-top: 10px;
            color: var(--sipencak-muted);
            font-weight: 800;
        }

        .sipencak-profile-side {
            display: grid;
            gap: 14px;
        }

        .sipencak-amount-card {
            display: grid;
            gap: 8px;
            padding: 22px;
            border-radius: 16px;
            background: var(--sipencak-primary);
            color: rgba(255, 255, 255, 0.78);
        }

        .sipencak-amount-card span {
            font-size: 0.76rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .sipencak-amount-card strong {
            color: #ffffff;
            font-size: clamp(1.6rem, 2.5vw, 2.2rem);
            line-height: 1.1;
        }

        .sipencak-amount-meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 8px;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.18);
        }

        .sipencak-amount-meta small {
            display: grid;
            gap: 3px;
            color: rgba(255, 255, 255, 0.74);
            font-weight: 800;
        }

        .sipencak-amount-meta b {
            color: #ffffff;
            font-size: 0.9rem;
        }

        .sipencak-status-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .sipencak-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 12px;
            border-radius: var(--sipencak-radius);
            background: rgba(43, 121, 180, 0.12);
            color: var(--sipencak-primary);
            font-size: 0.74rem;
            font-weight: 900;
            line-height: 1;
            white-space: nowrap;
            text-transform: uppercase;
        }

        .sipencak-status.is-solid {
            background: var(--sipencak-primary);
            color: #ffffff;
        }

        .sipencak-action-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 24px;
        }

        .sipencak-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .sipencak-summary-card {
            min-width: 0;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px;
            border: 1px solid var(--sipencak-line);
            border-radius: 14px;
            background: #ffffff;
        }

        .sipencak-summary-card .sipencak-summary-icon {
            width: 44px;
            height: 44px;
            flex: 0 0 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: var(--sipencak-soft);
            color: var(--sipencak-primary);
            font-size: 1.15rem;
            text-transform: none;
            letter-spacing: normal;
        }

        .sipencak-summary-card span {
            display: block;
            color: var(--sipencak-muted);
            font-size: 0.7rem;
            font-weight: 900;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .sipencak-summary-card strong {
            display: block;
            color: var(--sipencak-dark);
            font-weight: 900;
            line-height: 1.35;
        }

        .sipencak-detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }

        .sipencak-detail-panel {
            overflow: hidden;
            border: 1px solid var(--sipencak-line);
            border-radius: 16px;
            background: #ffffff;
        }

        .sipencak-detail-panel h3 {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 0;
            padding: 20px 22px;
            border-bottom: 1px solid var(--sipencak-line);
            background: #f3f8fc;
            color: var(--sipencak-dark);
            font-size: 1rem;
        }

        .sipencak-detail-panel h3 i {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 11px;
            background: #ffffff;
            color: var(--sipencak-primary);
        }

        .sipencak-detail-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }

        .sipencak-detail-table th,
        .sipencak-detail-table td {
            padding: 18px 22px;
            border-top: 1px solid rgba(43, 121, 180, 0.12);
            vertical-align: top;
        }

        .sipencak-detail-table tr:first-child th,
        .sipencak-detail-table tr:first-child td {
            border-top: 0;
        }

        .sipencak-detail-table th {
            width: 36%;
            color: var(--sipencak-muted);
            font-size: 0.74rem;
            font-weight: 900;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .sipencak-detail-table td {
            color: var(--sipencak-dark);
            font-weight: 850;
        }

        .sipencak-field-label {
            display: inline-flex;
            align-items: center;
            gap: 9px;
        }

        .sipencak-field-label i {
            width: 18px;
            color: var(--sipencak-primary);
            text-align: center;
        }

        .sipencak-track {
            display: grid;
            gap: 14px;
            padding: 22px;
        }

        .sipencak-track-item {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            gap: 14px;
            align-items: start;
        }

        .sipencak-track-dot {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: rgba(43, 121, 180, 0.12);
            color: var(--sipencak-primary);
        }

        .sipencak-track-item.is-active .sipencak-track-dot {
            background: var(--sipencak-primary);
            color: #ffffff;
        }

        .sipencak-track-title {
            color: var(--sipencak-dark);
            font-weight: 900;
        }

        .sipencak-track-text {
            color: var(--sipencak-muted);
            font-weight: 750;
            line-height: 1.55;
        }

        .sipencak-footer {
            position: relative;
            overflow: hidden;
            margin-top: 0;
            background:
                radial-gradient(circle at 86% 20%, rgba(255, 255, 255, 0.12), transparent 22rem),
                linear-gradient(135deg, #1B5B8F 0%, #0b4f82 56%, #06375d 100%);
            color: rgba(255, 255, 255, 0.7);
            padding: 72px 0 28px;
        }

        .sipencak-footer img {
            display: block;
            max-width: 178px;
            max-height: 54px;
            filter: brightness(0) invert(1);
        }

        .sipencak-footer h3 {
            color: #ffffff;
            font-size: 1rem;
            margin-bottom: 18px;
        }

        .sipencak-footer a {
            display: inline-flex;
            color: rgba(255, 255, 255, 0.74);
            font-weight: 700;
        }

        .sipencak-footer a:hover {
            color: #ffffff;
        }

        .sipencak-footer-list {
            display: grid;
            gap: 10px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .sipencak-footer-bottom {
            margin-top: 44px;
            padding-top: 22px;
            border-top: 1px solid rgba(255, 255, 255, 0.16);
        }

        @media (max-width: 991.98px) {
            .sipencak-profile-hero,
            .sipencak-detail-grid {
                grid-template-columns: 1fr;
            }

            .sipencak-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .sipencak-public-header .site_logo img {
                width: 142px;
            }

            .sipencak-summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="online_banking public-home">
    @php
        $dashboardUrl = session('role') === 'operator' ? url('dashboard') : (session('role') === 'admin' ? url('home') : null);
        $statusPengajuan = $mahasiswa->status_pengajuan ?: 'Terdaftar';
        $statusPembaruan = $mahasiswa->pembaruan_status ?: 'Belum diperbarui';
        $pencairanStatus = $mahasiswa->pencairan_status ?: 'Belum masuk pencairan';
        $periodePencairan = $mahasiswa->pencairan_periode ?: trim(($mahasiswa->pencairan_semester ?: '-') . ($mahasiswa->pencairan_tanggal_entry ? ' / ' . date('Y', strtotime($mahasiswa->pencairan_tanggal_entry)) : ''));
        $tanggalEntry = $mahasiswa->pencairan_tanggal_entry ? date('d M Y', strtotime($mahasiswa->pencairan_tanggal_entry)) : '-';
        $tanggalSurat = $mahasiswa->pencairan_tanggal_surat ? date('d M Y', strtotime($mahasiswa->pencairan_tanggal_surat)) : '-';
    @endphp

    <div class="page_wrapper">
        <header class="site_header header_layout_1 sipencak-public-header">
            <div class="xb-header">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-3 col-6">
                            <div class="site_logo">
                                <a class="site_link" href="{{ route('public.home') }}">
                                    <img src="{{ url('/assets/img/sipencak3.png') }}" alt="SIPENCAK">
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-6 col-1">
                            <nav class="main_menu navbar navbar-expand-lg">
                                <div class="main_menu_inner collapse navbar-collapse justify-content-lg-center" id="main_menu_dropdown">
                                    <ul class="main_menu_list unordered_list justify-content-center">
                                        <li><a href="{{ route('public.home') }}#fungsi">Fungsi</a></li>
                                        <li><a href="{{ route('public.home') }}#layanan">Layanan</a></li>
                                        <li><a href="{{ route('public.home') }}#alur">Alur</a></li>
                                        <li><a href="{{ route('public.home') }}#download">Download</a></li>
                                        <li><a href="{{ route('public.home') }}#faq">FAQ</a></li>
                                    </ul>
                                </div>
                            </nav>
                        </div>
                        <div class="col-lg-3 col-5">
                            <ul class="btns_group p-0 unordered_list justify-content-end">
                                <li>
                                    <button class="mobile_menu_btn" type="button" data-bs-toggle="collapse" data-bs-target="#main_menu_dropdown" aria-expanded="false" aria-label="Toggle navigation">
                                        <i class="far fa-bars"></i>
                                    </button>
                                </li>
                                @if($dashboardUrl)
                                    <li class="d-none d-sm-inline-flex">
                                        <a class="btn btn-outline-dark" href="{{ $dashboardUrl }}">
                                            <span class="btn_label">Dashboard</span>
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a class="btn sipencak-login-btn" href="{{ url('login') }}">
                                        <span class="btn_icon"><i class="fa-solid fa-user"></i></span>
                                        <span class="btn_label">Masuk</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="page_content">
            <section class="sipencak-page-hero">
                <div class="container">
                    <div class="sipencak-breadcrumb">
                        <a href="{{ route('public.home') }}">SIPENCAK</a>
                        <span class="mx-2">/</span>
                        <a href="{{ route('public.search') }}">Hasil Pencarian</a>
                        <span class="mx-2">/</span>
                        <span>Detail Mahasiswa</span>
                    </div>
                    <h1>Detail Data Mahasiswa</h1>
                </div>
            </section>

            <section class="sipencak-detail-area">
                <div class="container">
                    <div class="sipencak-detail-shell">
                        <div class="sipencak-profile-hero">
                            <div class="sipencak-profile-main">
                                <div class="sipencak-avatar-mark">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                                <h2>{{ $mahasiswa->nama }}</h2>
                                <div class="sipencak-profile-meta">
                                    <span><i class="fa-solid fa-id-card"></i> NIM {{ $mahasiswa->nim ?: '-' }}</span>
                                    <span><i class="fa-solid fa-building-columns"></i> {{ $mahasiswa->perguruan_tinggi ?: '-' }}</span>
                                    <span><i class="fa-solid fa-book-open"></i> {{ $mahasiswa->nama_prodi ?: '-' }}</span>
                                </div>
                            </div>
                            <div class="sipencak-profile-side">
                                <div class="sipencak-amount-card">
                                    <span>Nominal Pencairan</span>
                                    <strong>Rp {{ number_format((float) ($mahasiswa->pencairan_nominal ?? 0), 0, ',', '.') }}</strong>
                                    <small>{{ $periodePencairan ?: 'Belum ada periode pencairan' }}</small>
                                    <div class="sipencak-amount-meta">
                                        <small>
                                            Status
                                            <b>{{ $pencairanStatus }}</b>
                                        </small>
                                        <small>
                                            Jumlah Mhs
                                            <b>{{ number_format((int) ($mahasiswa->pencairan_jumlah_mahasiswa ?? 0), 0, ',', '.') }}</b>
                                        </small>
                                    </div>
                                </div>
                                <div class="sipencak-status-strip">
                                    <span class="sipencak-status is-solid">{{ $statusPengajuan }}</span>
                                    <span class="sipencak-status">{{ $statusPembaruan }}</span>
                                    <span class="sipencak-status">{{ $pencairanStatus }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="sipencak-action-row">
                            <a class="sipencak-primary-btn" href="{{ url()->previous() != url()->current() ? url()->previous() : route('public.search') }}">
                                <i class="fa-solid fa-arrow-left"></i>
                                Kembali ke Pencarian
                            </a>
                            <a class="sipencak-primary-btn" href="{{ route('public.home') }}">
                                <i class="fa-solid fa-house"></i>
                                Beranda
                            </a>
                        </div>

                        <div class="sipencak-summary-grid">
                            <div class="sipencak-summary-card">
                                <span class="sipencak-summary-icon"><i class="fa-solid fa-building-columns"></i></span>
                                <div>
                                    <span>Kode PT</span>
                                    <strong>{{ $mahasiswa->kode_pt ?: '-' }}</strong>
                                </div>
                            </div>
                            <div class="sipencak-summary-card">
                                <span class="sipencak-summary-icon"><i class="fa-solid fa-layer-group"></i></span>
                                <div>
                                    <span>Kode Prodi</span>
                                    <strong>{{ $mahasiswa->kode_prodi ?: '-' }}</strong>
                                </div>
                            </div>
                            <div class="sipencak-summary-card">
                                <span class="sipencak-summary-icon"><i class="fa-solid fa-calendar-check"></i></span>
                                <div>
                                    <span>Semester</span>
                                    <strong>{{ $mahasiswa->pencairan_semester ?: '-' }}</strong>
                                </div>
                            </div>
                            <div class="sipencak-summary-card">
                                <span class="sipencak-summary-icon"><i class="fa-solid fa-file-signature"></i></span>
                                <div>
                                    <span>No. SK / Surat</span>
                                    <strong>{{ $mahasiswa->pencairan_no_sk ?: '-' }}</strong>
                                </div>
                            </div>
                            <div class="sipencak-summary-card">
                                <span class="sipencak-summary-icon"><i class="fa-solid fa-user-check"></i></span>
                                <div>
                                    <span>Status Data</span>
                                    <strong>{{ $statusPengajuan }}</strong>
                                </div>
                            </div>
                            <div class="sipencak-summary-card">
                                <span class="sipencak-summary-icon"><i class="fa-solid fa-tags"></i></span>
                                <div>
                                    <span>Kategori</span>
                                    <strong>{{ $mahasiswa->kategori ?: '-' }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="sipencak-detail-grid">
                            <section class="sipencak-detail-panel">
                                <h3><i class="fa-solid fa-graduation-cap"></i> Informasi Akademik</h3>
                                <table class="sipencak-detail-table">
                                    <tbody>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-user-graduate"></i> Nama Mahasiswa</span></th>
                                            <td>{{ $mahasiswa->nama ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-id-card"></i> NIM</span></th>
                                            <td>{{ $mahasiswa->nim ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-building-columns"></i> Perguruan Tinggi</span></th>
                                            <td>{{ $mahasiswa->perguruan_tinggi ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-hashtag"></i> Kode PT</span></th>
                                            <td>{{ $mahasiswa->kode_pt ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-book-open"></i> Program Studi</span></th>
                                            <td>{{ $mahasiswa->nama_prodi ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-layer-group"></i> Kode Prodi</span></th>
                                            <td>{{ $mahasiswa->kode_prodi ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-calendar-days"></i> Jenjang / Angkatan</span></th>
                                            <td>{{ $mahasiswa->jenjang ?: '-' }}{{ $mahasiswa->angkatan ? ' / ' . $mahasiswa->angkatan : '' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-tags"></i> Kategori Penerima</span></th>
                                            <td>{{ $mahasiswa->kategori ?: '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </section>

                            <section class="sipencak-detail-panel">
                                <h3><i class="fa-solid fa-money-check-dollar"></i> Status Pencairan</h3>
                                <table class="sipencak-detail-table">
                                    <tbody>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-circle-check"></i> Status Pencairan</span></th>
                                            <td><span class="sipencak-status">{{ $pencairanStatus }}</span></td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-rupiah-sign"></i> Nominal Pencairan</span></th>
                                            <td>Rp {{ number_format((float) ($mahasiswa->pencairan_nominal ?? 0), 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-calendar-check"></i> Periode</span></th>
                                            <td>{{ $periodePencairan ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-calendar-days"></i> Semester</span></th>
                                            <td>{{ $mahasiswa->pencairan_semester ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-hand-holding-heart"></i> Kategori Bantuan</span></th>
                                            <td>{{ $mahasiswa->pencairan_kategori_penerima ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-wallet"></i> Jenis Bantuan</span></th>
                                            <td>{{ $mahasiswa->pencairan_jenis_bantuan ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-users"></i> Jumlah Mahasiswa</span></th>
                                            <td>{{ number_format((int) ($mahasiswa->pencairan_jumlah_mahasiswa ?? 0), 0, ',', '.') }} Mhs</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-file-signature"></i> No. SK / Surat</span></th>
                                            <td>{{ $mahasiswa->pencairan_no_sk ?: '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-calendar-plus"></i> Tanggal Entry</span></th>
                                            <td>{{ $tanggalEntry }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-calendar-day"></i> Tanggal Surat</span></th>
                                            <td>{{ $tanggalSurat }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-note-sticky"></i> Keterangan</span></th>
                                            <td>{{ $mahasiswa->pencairan_keterangan ?: 'Tidak ada keterangan tambahan.' }}</td>
                                        </tr>
                                        @if(!empty($mahasiswa->pencairan_alasan_tolak))
                                            <tr>
                                                <th><span class="sipencak-field-label"><i class="fa-solid fa-triangle-exclamation"></i> Alasan Ditolak</span></th>
                                                <td>{{ $mahasiswa->pencairan_alasan_tolak }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </section>

                            <section class="sipencak-detail-panel">
                                <h3><i class="fa-solid fa-route"></i> Tracking Pengajuan</h3>
                                <div class="sipencak-track">
                                    <div class="sipencak-track-item is-active">
                                        <span class="sipencak-track-dot"><i class="fa-solid fa-id-card"></i></span>
                                        <div>
                                            <div class="sipencak-track-title">Data Mahasiswa Terdaftar</div>
                                            <div class="sipencak-track-text">Mahasiswa tercatat pada basis data SIPENCAK dengan NIM {{ $mahasiswa->nim ?: '-' }}.</div>
                                        </div>
                                    </div>
                                    <div class="sipencak-track-item {{ $mahasiswa->pencairan_id ? 'is-active' : '' }}">
                                        <span class="sipencak-track-dot"><i class="fa-solid fa-paper-plane"></i></span>
                                        <div>
                                            <div class="sipencak-track-title">Pengajuan Pencairan</div>
                                            <div class="sipencak-track-text">{{ $mahasiswa->pencairan_id ? 'Data mahasiswa telah masuk pada pengajuan pencairan.' : 'Belum ada data pengajuan pencairan aktif untuk mahasiswa ini.' }}</div>
                                        </div>
                                    </div>
                                    <div class="sipencak-track-item {{ $mahasiswa->pencairan_status ? 'is-active' : '' }}">
                                        <span class="sipencak-track-dot"><i class="fa-solid fa-circle-check"></i></span>
                                        <div>
                                            <div class="sipencak-track-title">Status Verifikasi</div>
                                            <div class="sipencak-track-text">Status saat ini: {{ $pencairanStatus }}.</div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="sipencak-detail-panel">
                                <h3><i class="fa-solid fa-file-lines"></i> Catatan Publik</h3>
                                <table class="sipencak-detail-table">
                                    <tbody>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-calendar-plus"></i> Tanggal Entry</span></th>
                                            <td>{{ $tanggalEntry }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-calendar-day"></i> Tanggal Surat</span></th>
                                            <td>{{ $tanggalSurat }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-note-sticky"></i> Keterangan</span></th>
                                            <td>{{ $mahasiswa->pencairan_keterangan ?: 'Tidak ada keterangan tambahan.' }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="sipencak-field-label"><i class="fa-solid fa-database"></i> Sumber Data</span></th>
                                            <td>Ringkasan publik SIPENCAK LLDIKTI Wilayah III.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </section>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="sipencak-footer">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-5">
                        <img src="{{ url('/assets/img/sipencak3.png') }}" alt="SIPENCAK">
                        <p class="mt-4 mb-4">Sistem Pengelolaan Pencairan KIP Kuliah untuk pencarian data, pengajuan, verifikasi, dan pelaporan LLDIKTI Wilayah III Jakarta.</p>
                    </div>
                    <div class="col-6 col-lg-2">
                        <h3>Navigasi</h3>
                        <ul class="sipencak-footer-list">
                            <li><a href="{{ route('public.home') }}#fungsi">Fungsi</a></li>
                            <li><a href="{{ route('public.home') }}#layanan">Layanan</a></li>
                            <li><a href="{{ route('public.home') }}#alur">Alur</a></li>
                            <li><a href="{{ route('public.home') }}#download">Download</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-lg-2">
                        <h3>Layanan</h3>
                        <ul class="sipencak-footer-list">
                            <li><a href="{{ route('public.search') }}">Cari Mahasiswa</a></li>
                            <li><a href="{{ url('login') }}">Login Pengelola</a></li>
                            <li><a href="{{ route('public.home') }}#faq">FAQ / QNA</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <h3>Kontak & Instansi</h3>
                        <ul class="sipencak-footer-list">
                            <li><a href="#">LLDIKTI Wilayah III Jakarta</a></li>
                            <li><a href="#">KIP Kuliah</a></li>
                            <li><a href="#">SIPENCAK Public Portal</a></li>
                        </ul>
                    </div>
                </div>
                <div class="sipencak-footer-bottom d-flex flex-wrap justify-content-between gap-3">
                    <div>© {{ date('Y') }} SIPENCAK. LLDIKTI Wilayah III Jakarta.</div>
                    <div class="d-flex flex-wrap gap-4">
                        <a href="{{ route('public.home') }}">Beranda</a>
                        <a href="{{ url('login') }}">Masuk</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="{{ url('/assets/synox/js/jquery.min.js') }}"></script>
    <script src="{{ url('/assets/synox/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ url('/assets/synox/js/main.js') }}"></script>
</body>
</html>
