<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="theme-color" content="#2B79B4">
    <title>Hasil Pencarian Mahasiswa - SIPENCAK</title>
    <link rel="shortcut icon" href="{{ url('/assets/img/lldikti3.png') }}">
    <link rel="manifest" href="{{ url('/manifest.webmanifest') }}">

    <link rel="stylesheet" href="{{ url('/assets/synox/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/synox/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/synox/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/synox/css/style.css') }}">

    <style>
        :root {
            --sipencak-primary: #2B79B4;
            --sipencak-dark: #142334;
            --sipencak-secondary: #3F96CD;
            --sipencak-soft: #EEF7FC;
            --sipencak-muted: #6B7C8F;
            --sipencak-line: rgba(43, 121, 180, 0.18);
            --sipencak-radius: 10px;
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

        .sipencak-login-btn {
            min-height: 52px;
            padding: 0 24px !important;
            border: 1px solid var(--sipencak-primary) !important;
            border-radius: var(--sipencak-radius) !important;
            background: var(--sipencak-primary) !important;
            color: #ffffff !important;
            font-weight: 800;
        }

        .sipencak-login-btn .btn_icon {
            color: #ffffff;
        }

        .sipencak-login-btn:hover {
            background: #236596 !important;
            border-color: #236596 !important;
            color: #ffffff !important;
        }

        .sipencak-login-btn:hover .btn_icon,
        .sipencak-login-btn:hover .btn_label,
        .sipencak-login-btn:hover i {
            color: #ffffff !important;
        }

        .sipencak-login-btn::before,
        .sipencak-login-btn::after {
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
            color: var(--sipencak-primary);
        }

        .sipencak-page-hero h1 {
            margin: 12px 0 0;
            color: #ffffff;
            font-size: clamp(2.5rem, 5vw, 5rem);
            line-height: 1.02;
        }

        .sipencak-shell {
            margin-top: 0;
            margin-bottom: -104px;
            position: relative;
            z-index: 3;
            border: 1px solid var(--sipencak-line);
            border-radius: 22px;
            background: #ffffff;
            padding: 30px;
            box-shadow: 0 30px 80px rgba(27, 91, 143, 0.18);
            transform: translateY(-104px);
        }

        .sipencak-search-area {
            position: relative;
            overflow: visible;
            padding-top: 0;
            padding-bottom: 150px;
            background: #f3f8fc;
        }

        .sipencak-search-area::before {
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

        .sipencak-search-area .container {
            position: relative;
            z-index: 1;
        }

        .sipencak-search-form {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 12px;
        }

        .sipencak-search-input {
            display: flex;
            min-width: 0;
            min-height: 66px;
            overflow: hidden;
            border: 1px solid var(--sipencak-line);
            border-radius: var(--sipencak-radius);
            background: #ffffff;
        }

        .sipencak-search-input span {
            width: 66px;
            flex: 0 0 66px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-right: 1px solid var(--sipencak-line);
            color: var(--sipencak-dark);
        }

        .sipencak-search-input input {
            width: 100%;
            min-width: 0;
            border: 0;
            outline: 0;
            padding: 0 18px;
            color: var(--sipencak-dark);
            font: inherit;
            font-weight: 700;
        }

        .sipencak-search-submit {
            min-height: 66px;
            padding-inline: 34px;
            border: 0;
            border-radius: var(--sipencak-radius);
            background: var(--sipencak-primary);
            color: #ffffff;
            font-weight: 900;
        }

        .sipencak-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin: 28px 0;
            padding: 18px 20px;
            border: 1px solid var(--sipencak-line);
            border-radius: var(--sipencak-radius);
            background: #f3f8fc;
            color: var(--sipencak-muted);
            font-weight: 700;
        }

        .sipencak-status-stack {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 8px;
        }

        .sipencak-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 10px;
            border-radius: var(--sipencak-radius);
            background: rgba(43, 121, 180, 0.12);
            color: var(--sipencak-primary);
            font-size: 0.72rem;
            font-weight: 900;
            line-height: 1;
            white-space: nowrap;
            text-transform: uppercase;
        }

        .sipencak-status.is-muted {
            background: #f3f8fc;
            color: var(--sipencak-muted);
        }

        .sipencak-directory {
            overflow: hidden;
            border: 1px solid var(--sipencak-line);
            border-radius: 18px;
            background: #ffffff;
        }

        .sipencak-directory-table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
        }

        .sipencak-directory-table thead {
            background: #f3f8fc;
        }

        .sipencak-directory-table th {
            padding: 22px 24px;
            color: #3b5870;
            font-size: 0.76rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .sipencak-directory-table td {
            padding: 26px 24px;
            border-top: 1px solid rgba(43, 121, 180, 0.14);
            color: var(--sipencak-dark);
            vertical-align: middle;
        }

        .sipencak-directory-number {
            color: var(--sipencak-primary);
            font-weight: 900;
        }

        .sipencak-directory-name {
            display: grid;
            gap: 7px;
        }

        .sipencak-directory-name strong {
            color: var(--sipencak-dark);
            font-size: 1.08rem;
            font-weight: 900;
            line-height: 1.25;
            text-transform: uppercase;
        }

        .sipencak-directory-muted {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--sipencak-muted);
            font-weight: 800;
            line-height: 1.4;
        }

        .sipencak-directory-primary {
            color: var(--sipencak-dark);
            font-weight: 900;
        }

        .sipencak-directory-action {
            text-align: right;
        }

        .sipencak-detail-btn {
            min-height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 0 18px;
            border: 1px solid var(--sipencak-line);
            border-radius: var(--sipencak-radius);
            background: var(--sipencak-primary);
            color: #ffffff;
            font-weight: 900;
            white-space: nowrap;
        }

        .sipencak-detail-btn:hover {
            background: #1B5B8F;
            color: #ffffff;
        }

        .sipencak-registry {
            overflow: hidden;
            border: 1px solid var(--sipencak-line);
            border-radius: 18px;
            background: #ffffff;
        }

        .sipencak-registry-head,
        .sipencak-registry-row {
            display: grid;
            grid-template-columns: 74px minmax(240px, 1.35fr) minmax(230px, 1fr) minmax(170px, 0.8fr) minmax(280px, 1.1fr);
        }

        .sipencak-registry-head {
            border-bottom: 1px solid var(--sipencak-line);
            background: #f3f8fc;
            color: #3b5870;
            font-size: 0.73rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .sipencak-registry-head > div,
        .sipencak-registry-cell {
            min-width: 0;
            padding: 18px 20px;
        }

        .sipencak-registry-head > div:nth-child(4),
        .sipencak-registry-row > .sipencak-registry-cell:nth-child(4) {
            text-align: center;
        }

        .sipencak-registry-row > .sipencak-registry-cell:nth-child(4) .sipencak-status-stack {
            justify-content: center;
        }

        .sipencak-registry-row {
            position: relative;
            border-bottom: 1px solid rgba(43, 121, 180, 0.14);
        }

        .sipencak-registry-row:last-child {
            border-bottom: 0;
        }

        .sipencak-registry-row::before {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 3px;
            background: var(--sipencak-primary);
            content: "";
            opacity: 0.72;
        }

        .sipencak-registry-number {
            color: var(--sipencak-primary);
            font-size: 0.92rem;
            font-weight: 900;
        }

        .sipencak-registry-title {
            margin: 0 0 8px;
            color: var(--sipencak-dark);
            font-size: 1.08rem;
            line-height: 1.3;
            font-weight: 900;
        }

        .sipencak-registry-muted {
            display: flex;
            align-items: center;
            gap: 7px;
            color: var(--sipencak-muted);
            font-size: 0.9rem;
            font-weight: 750;
            line-height: 1.5;
        }

        .sipencak-registry-kicker {
            display: block;
            margin-bottom: 6px;
            color: var(--sipencak-muted);
            font-size: 0.7rem;
            font-weight: 900;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .sipencak-registry-cell strong {
            display: block;
            color: var(--sipencak-dark);
            font-weight: 900;
            line-height: 1.35;
        }

        .sipencak-registry-money {
            margin: 10px 0 5px;
            color: #2fac66;
            font-size: 1.05rem;
            font-weight: 900;
        }

        .sipencak-registry-empty {
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--sipencak-muted);
            font-weight: 800;
            line-height: 1.45;
        }

        .sipencak-registry-extra {
            grid-column: 2 / -1;
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px 18px;
            margin: 0 20px 18px 0;
            padding: 16px 0 0;
            border-top: 1px dashed rgba(43, 121, 180, 0.24);
        }

        .sipencak-registry-extra-item {
            min-width: 0;
        }

        .sipencak-registry-extra-item span {
            display: block;
            margin-bottom: 4px;
            color: var(--sipencak-muted);
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.07em;
            text-transform: uppercase;
        }

        .sipencak-registry-extra-item strong {
            display: block;
            color: var(--sipencak-dark);
            font-size: 0.88rem;
            line-height: 1.38;
            font-weight: 850;
        }

        .sipencak-registry-extra-item.is-wide {
            grid-column: span 2;
        }

        .sipencak-registry-alert {
            grid-column: 1 / -1;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
            background: rgba(239, 68, 68, 0.07);
            color: #991b1b;
            font-weight: 800;
        }

        .sipencak-registry-note {
            grid-column: 2 / -1;
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 20px 18px 0;
            color: var(--sipencak-muted);
            font-size: 0.84rem;
            font-weight: 700;
        }

        .sipencak-info {
            min-width: 0;
            display: grid;
            gap: 6px;
            padding: 16px;
            border: 1px solid rgba(43, 121, 180, 0.14);
            border-radius: var(--sipencak-radius);
            background: rgba(243, 248, 252, 0.72);
        }

        .sipencak-info span {
            color: var(--sipencak-muted);
            font-size: 0.72rem;
            font-weight: 900;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .sipencak-info strong {
            color: var(--sipencak-dark);
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .sipencak-empty {
            overflow: hidden;
            border: 1px solid var(--sipencak-line);
            border-radius: 20px;
            background: #ffffff;
            padding: 64px 24px;
            text-align: center;
        }

        .sipencak-empty i {
            color: var(--sipencak-dark);
            font-size: 3.2rem;
        }

        .sipencak-pager-wrap {
            display: flex;
            justify-content: flex-end;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--sipencak-line);
        }

        .sipencak-pager-wrap .pagination {
            overflow: hidden;
            border: 1px solid var(--sipencak-line);
            border-radius: var(--sipencak-radius);
            background: #ffffff;
        }

        .sipencak-pager-wrap .page-link {
            min-width: 46px;
            min-height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0 !important;
            color: var(--sipencak-dark);
            font-weight: 900;
        }

        .sipencak-pager-wrap .page-item.active .page-link {
            background: var(--sipencak-primary) !important;
            color: #ffffff !important;
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

        .sipencak-footer .container {
            position: relative;
            z-index: 1;
        }

        .sipencak-footer-brand-logos {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 18px;
        }

        .sipencak-footer img {
            display: block;
            width: auto;
            padding: 0;
            border-radius: 0;
            background: transparent;
        }

        .sipencak-footer .sipencak-footer-logo-sipencak {
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

        .sipencak-footer-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-height: 42px;
            padding: 0 14px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: var(--sipencak-radius);
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-weight: 800;
        }

        .sipencak-footer-bottom {
            margin-top: 44px;
            padding-top: 22px;
            border-top: 1px solid rgba(255, 255, 255, 0.16);
        }

        @media (max-width: 767.98px) {
            .sipencak-public-header .site_logo img {
                width: 142px;
            }

            .sipencak-registry-head {
                display: none;
            }

            .sipencak-registry-row {
                grid-template-columns: 1fr;
            }

            .sipencak-registry-cell {
                padding: 14px 18px;
            }

            .sipencak-registry-number {
                padding-top: 18px;
                padding-bottom: 0;
            }

            .sipencak-registry-extra,
            .sipencak-registry-note {
                grid-column: 1;
                margin: 0 18px 18px;
            }

            .sipencak-registry-extra {
                grid-template-columns: 1fr;
            }

            .sipencak-registry-extra-item.is-wide {
                grid-column: auto;
            }

            .sipencak-status-stack {
                justify-content: flex-start;
            }

            .sipencak-search-form,
            .sipencak-meta {
                grid-template-columns: 1fr;
                flex-direction: column;
                align-items: stretch;
            }

            .sipencak-search-submit {
                width: 100%;
            }
        }
    </style>
</head>

<body class="online_banking public-home">
    @php
        $dashboardUrl = session('role') === 'operator' ? url('dashboard') : (session('role') === 'admin' ? url('home') : null);
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
                        <span>Hasil Pencarian</span>
                    </div>
                    <h1>Pencarian Data Mahasiswa Terdaftar</h1>
                </div>
            </section>

            <section class="sipencak-search-area">
                <div class="container">
                    <div class="sipencak-shell">
                        <form class="sipencak-search-form" action="{{ route('public.search') }}" method="get">
                            <label class="sipencak-search-input mb-0">
                                <span aria-hidden="true"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <input type="search" name="q" value="{{ $keyword }}" placeholder="Nama, NIM, prodi, atau PT..." autocomplete="off">
                            </label>
                            <button class="sipencak-search-submit" type="submit">Cari</button>
                        </form>

                        <div class="sipencak-meta">
                            <div>
                                <strong class="text-dark">Hasil Data Mahasiswa</strong>
                                <div>
                                    Menampilkan {{ $mahasiswas->firstItem() ?? 0 }}-{{ $mahasiswas->lastItem() ?? 0 }}
                                    dari total {{ number_format($mahasiswas->total(), 0, ',', '.') }} data
                                </div>
                            </div>
                            @if($keyword !== '')
                                <div>
                                    Pencarian: <strong class="text-dark">"{{ $keyword }}"</strong>
                                    <a class="ms-2 fw-bold" href="{{ route('public.search') }}">Reset</a>
                                </div>
                            @else
                                <div>Masukkan kata kunci untuk hasil lebih spesifik</div>
                            @endif
                        </div>

                        @if($mahasiswas->count() > 0)
                            <div class="sipencak-directory">
                                <div class="table-responsive">
                                    <table class="sipencak-directory-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 84px;">No</th>
                                                <th>Nama Mahasiswa</th>
                                                <th>Universitas</th>
                                                <th>Program Studi</th>
                                                <th>Status</th>
                                                <th class="text-end">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($mahasiswas as $mahasiswa)
                                                @php
                                                    $rowNumber = ($mahasiswas->firstItem() ?? 1) + $loop->index;
                                                    $statusPengajuan = $mahasiswa->status_pengajuan ?: 'Terdaftar';
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <span class="sipencak-directory-number">{{ str_pad($rowNumber, 2, '0', STR_PAD_LEFT) }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="sipencak-directory-name">
                                                            <strong>{{ $mahasiswa->nama }}</strong>
                                                            <span class="sipencak-directory-muted">
                                                                <i class="fa-solid fa-id-card"></i>
                                                                NIM {{ $mahasiswa->nim ?: '-' }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="sipencak-directory-name">
                                                            <span class="sipencak-directory-primary">{{ $mahasiswa->perguruan_tinggi ?: '-' }}</span>
                                                            <span class="sipencak-directory-muted">Kode PT {{ $mahasiswa->kode_pt ?: '-' }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="sipencak-directory-name">
                                                            <span class="sipencak-directory-primary">{{ $mahasiswa->nama_prodi ?: '-' }}</span>
                                                            <span class="sipencak-directory-muted">{{ $mahasiswa->jenjang ?: '-' }}{{ $mahasiswa->angkatan ? ' / ' . $mahasiswa->angkatan : '' }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="sipencak-status">{{ $statusPengajuan }}</span>
                                                    </td>
                                                    <td class="sipencak-directory-action">
                                                        <a class="sipencak-detail-btn" href="{{ route('public.student-detail', $mahasiswa->id) }}">
                                                            <i class="fa-solid fa-eye"></i>
                                                            Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @if($mahasiswas->hasPages())
                                <div class="sipencak-pager-wrap">
                                    {{ $mahasiswas->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @else
                            <div class="sipencak-empty">
                                <i class="fa-solid fa-folder-open"></i>
                                <h3 class="mt-3">Data mahasiswa tidak ditemukan</h3>
                                <p class="mb-0">Coba gunakan nama, NIM, program studi, atau perguruan tinggi lain.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </main>

        <footer class="sipencak-footer">
            <div class="container">
                <div class="row g-5">
                    <div class="col-lg-5">
                        <div class="sipencak-footer-brand-logos">
                            <img class="sipencak-footer-logo-sipencak" src="{{ url('/assets/img/sipencak3.png') }}" alt="SIPENCAK">
                        </div>
                        <p class="mt-4 mb-4">Sistem Pengelolaan Pencairan KIP Kuliah untuk pencarian data, pengajuan, verifikasi, dan pelaporan LLDIKTI Wilayah III Jakarta.</p>
                        <span class="sipencak-footer-chip">
                            <i class="fa-solid fa-code"></i>
                            Pengembang: Tama
                        </span>
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
                            <li><a href="{{ route('public.home') }}#cari">Cari Mahasiswa</a></li>
                            <li><a href="{{ route('public.search') }}">Data Terdaftar</a></li>
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
    <script src="{{ url('/assets/synox/js/popper.min.js') }}"></script>
    <script src="{{ url('/assets/synox/js/bootstrap.min.js') }}"></script>
    <script src="{{ url('/assets/synox/js/wow.min.js') }}"></script>
    <script src="{{ url('/assets/synox/js/main.js') }}"></script>
</body>
</html>
