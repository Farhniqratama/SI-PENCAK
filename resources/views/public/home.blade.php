<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="theme-color" content="#2B79B4">
    <meta name="description" content="Portal publik SIPENCAK untuk pencarian data mahasiswa KIP Kuliah LLDIKTI Wilayah III Jakarta.">
    <title>SIPENCAK - Portal Publik KIP Kuliah</title>
    <link rel="shortcut icon" href="{{ url('/assets/img/lldikti3.png') }}">
    <link rel="manifest" href="{{ url('/manifest.webmanifest') }}">

    <link rel="stylesheet" href="{{ url('/assets/synox/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/synox/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/synox/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/synox/css/swiper-bundle.min.css') }}">
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

        html {
            scroll-behavior: smooth;
        }

        body.public-home {
            background: #ffffff;
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
            box-shadow: 0 18px 48px rgba(43, 121, 180, 0.08);
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

        body.public-home .sipencak-public-header .main_menu_list > li > a:hover {
            color: var(--sipencak-primary) !important;
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

        body.public-home .page_content {
            padding-top: 0;
        }

        .sipencak-hero {
            position: relative;
            overflow: hidden;
            padding: 150px 0 104px;
            background:
                radial-gradient(circle at 82% 24%, rgba(63, 150, 205, 0.2), transparent 28%),
                linear-gradient(135deg, #ffffff 0%, #f3f8fc 48%, #e8f5fc 100%);
            color: var(--sipencak-dark);
        }

        .sipencak-hero::before {
            position: absolute;
            inset: 0;
            background-color: rgba(43, 121, 180, 0.13);
            -webkit-mask-image: url("{{ url('/images/tutwuri.png') }}");
            mask-image: url("{{ url('/images/tutwuri.png') }}");
            -webkit-mask-repeat: repeat;
            mask-repeat: repeat;
            -webkit-mask-size: 96px 96px;
            mask-size: 96px 96px;
            opacity: 0.30;
            content: "";
        }

        .sipencak-hero::after {
            position: absolute;
            right: -12%;
            bottom: -36%;
            width: 64%;
            height: 50%;
            border-radius: 48% 0 0 0;
            background: linear-gradient(135deg, rgba(118, 179, 216, 0.58) 0%, rgba(63, 150, 205, 0.72) 48%, rgba(43, 121, 180, 0.9) 100%);
            filter: blur(0.2px);
            content: "";
        }

        .sipencak-hero .container {
            position: relative;
            z-index: 2;
        }

        .sipencak-eyebrow {
            display: inline-flex;
            align-items: center;
            min-height: 42px;
            padding: 0 16px;
            border: 1px solid rgba(43, 121, 180, 0.22);
            border-radius: var(--sipencak-radius);
            background: rgba(43, 121, 180, 0.1);
            color: var(--sipencak-primary);
            font-size: 0.78rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .sipencak-hero h1 {
            max-width: 820px;
            margin: 0 0 22px;
            color: var(--sipencak-dark);
            font-size: clamp(3.2rem, 5.6vw, 6.8rem);
            line-height: 0.98;
            letter-spacing: 0;
        }

        .sipencak-hero h1 span {
            color: var(--sipencak-primary);
        }

        .sipencak-hero p {
            max-width: 790px;
            color: var(--sipencak-muted);
            font-size: 1.1rem;
            line-height: 1.85;
            font-weight: 500;
        }

        .sipencak-search-panel {
            position: relative;
            overflow: hidden;
            scroll-margin-top: 116px;
            border: 1px solid var(--sipencak-line);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 34px;
            box-shadow: 0 28px 80px rgba(43, 121, 180, 0.16);
            backdrop-filter: blur(16px);
        }

        .sipencak-search-form {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 12px;
        }

        .sipencak-search-input {
            display: flex;
            min-width: 0;
            min-height: 68px;
            overflow: hidden;
            border: 1px solid rgba(228, 238, 239, 0.85);
            border-radius: var(--sipencak-radius);
            background: #ffffff;
        }

        .sipencak-search-input span {
            display: inline-flex;
            width: 68px;
            flex: 0 0 68px;
            align-items: center;
            justify-content: center;
            border-right: 1px solid var(--sipencak-line);
            color: var(--sipencak-dark);
            font-size: 1.25rem;
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
            min-height: 68px;
            padding-inline: 32px;
            border: 0;
            border-radius: var(--sipencak-radius);
            background: var(--sipencak-primary);
            color: #ffffff;
            font-weight: 900;
        }

        .sipencak-stat {
            position: relative;
            overflow: hidden;
            min-height: 128px;
            border: 1px solid var(--sipencak-line);
            border-radius: var(--sipencak-radius);
            background: #ffffff;
            padding: 22px;
        }

        .sipencak-stat span {
            display: block;
            color: var(--sipencak-muted);
            font-size: 0.72rem;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .sipencak-stat strong {
            display: block;
            margin-top: 12px;
            color: var(--sipencak-primary);
            font-size: 2.2rem;
            line-height: 1;
        }

        .sipencak-stat-icon {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            border-radius: var(--sipencak-radius);
            background: rgba(43, 121, 180, 0.12);
            color: var(--sipencak-primary);
            font-size: 1rem;
        }

        .sipencak-hero-primary {
            min-height: 62px;
            border-color: var(--sipencak-primary) !important;
            background: var(--sipencak-primary) !important;
            color: #ffffff !important;
            font-weight: 900;
        }

        .sipencak-hero-outline {
            min-height: 62px;
            border-color: rgba(43, 121, 180, 0.32) !important;
            color: var(--sipencak-primary) !important;
            font-weight: 900;
        }

        .sipencak-section {
            padding: 96px 0;
            scroll-margin-top: 116px;
        }

        .sipencak-section.bg-soft {
            background: #f3f8fc;
        }

        .sipencak-section-title {
            max-width: 780px;
        }

        .sipencak-section-title .badge {
            border: 1px solid var(--sipencak-line);
            border-radius: var(--sipencak-radius);
            background: var(--sipencak-soft);
            color: var(--sipencak-secondary);
            letter-spacing: 0.08em;
        }

        .sipencak-section-title h2 {
            margin: 18px 0 0;
            color: var(--sipencak-dark);
            font-size: clamp(2.2rem, 4vw, 4.25rem);
            line-height: 1.04;
        }

        .sipencak-card {
            position: relative;
            height: 100%;
            overflow: hidden;
            border: 1px solid var(--sipencak-line);
            border-radius: 20px;
            background: #ffffff;
            padding: 30px;
            box-shadow: 0 18px 50px rgba(43, 121, 180, 0.06);
            transition: transform 220ms ease, box-shadow 220ms ease;
        }

        .sipencak-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 26px 70px rgba(43, 121, 180, 0.1);
        }

        .sipencak-card-icon {
            width: 62px;
            height: 62px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            border-radius: var(--sipencak-radius);
            background: var(--sipencak-primary);
            color: #ffffff;
            font-size: 1.45rem;
        }

        .sipencak-function-section {
            position: relative;
            overflow: hidden;
            background: #ffffff;
        }

        .sipencak-function-section::after {
            position: absolute;
            top: -190px;
            right: -170px;
            width: 620px;
            height: 620px;
            background-color: var(--sipencak-primary);
            -webkit-mask-image: url("{{ url('/images/tutwuri.png') }}");
            mask-image: url("{{ url('/images/tutwuri.png') }}");
            -webkit-mask-position: center;
            mask-position: center;
            -webkit-mask-repeat: no-repeat;
            mask-repeat: no-repeat;
            -webkit-mask-size: contain;
            mask-size: contain;
            filter: url("#sipencak-tutwuri-thin");
            content: "";
            pointer-events: none;
        }

        .sipencak-function-section .container {
            position: relative;
            z-index: 1;
        }

        .sipencak-service-section {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 86% 16%, rgba(63, 150, 205, 0.18), transparent 24rem),
                linear-gradient(120deg, #fbfdff 0%, #f3f8fc 52%, #eef7fc 100%);
        }

        .sipencak-service-section::before {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(43, 121, 180, 0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(43, 121, 180, 0.08) 1px, transparent 1px);
            background-size: 86px 86px;
            content: "";
            pointer-events: none;
        }

        .sipencak-service-section::after {
            position: absolute;
            right: -18%;
            bottom: -52%;
            width: 56%;
            height: 48%;
            border-radius: 52% 0 0 0;
            background: rgba(63, 150, 205, 0.18);
            content: "";
            pointer-events: none;
        }

        .sipencak-service-section .container {
            position: relative;
            z-index: 1;
        }

        .sipencak-process {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .sipencak-process-item {
            display: grid;
            grid-template-columns: 58px minmax(0, 1fr);
            gap: 18px;
            align-items: start;
            padding: 28px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .sipencak-process-item span {
            width: 58px;
            height: 58px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--sipencak-radius);
            background: var(--sipencak-primary);
            color: #ffffff;
            font-weight: 900;
        }

        .sipencak-process-item h3 {
            color: #ffffff;
            font-size: 1.15rem;
        }

        .sipencak-process-item p {
            color: rgba(255, 255, 255, 0.7);
        }

        .sipencak-logo-marquee {
            position: relative;
            overflow: hidden;
            border-top: 1px solid var(--sipencak-line);
            border-bottom: 1px solid var(--sipencak-line);
            background:
                radial-gradient(circle at 82% 18%, rgba(255, 255, 255, 0.22), transparent 22rem),
                linear-gradient(135deg, #3F96CD 0%, #2B79B4 58%, #1B5B8F 100%);
            padding: 28px 0;
        }

        .sipencak-logo-marquee::before,
        .sipencak-logo-marquee::after {
            position: absolute;
            top: 0;
            bottom: 0;
            z-index: 2;
            width: 120px;
            content: "";
            pointer-events: none;
        }

        .sipencak-logo-marquee::before {
            left: 0;
            background: linear-gradient(90deg, #3F96CD 0%, rgba(63, 150, 205, 0) 100%);
        }

        .sipencak-logo-marquee::after {
            right: 0;
            background: linear-gradient(270deg, #1B5B8F 0%, rgba(27, 91, 143, 0) 100%);
        }

        .sipencak-logo-track {
            display: flex;
            width: max-content;
            gap: 18px;
            animation: sipencakLogoRun 28s linear infinite;
        }

        .sipencak-logo-item {
            width: 184px;
            height: 82px;
            display: inline-flex;
            flex: 0 0 184px;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .sipencak-logo-item img {
            max-width: 132px;
            max-height: 48px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .sipencak-logo-item img[src*="lldikti3"] {
            filter: none;
        }

        @keyframes sipencakLogoRun {
            to {
                transform: translateX(-50%);
            }
        }

        .sipencak-download {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, var(--sipencak-secondary), var(--sipencak-primary));
            color: #ffffff;
        }

        .sipencak-download::after {
            position: absolute;
            right: -9rem;
            bottom: -12rem;
            width: 34rem;
            height: 34rem;
            border-radius: 50%;
            background: #ffffff;
            opacity: 0.16;
            content: "";
        }

        .sipencak-download h2 {
            color: #ffffff;
        }

        .sipencak-store-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 58px;
            padding: 8px 16px;
            border-radius: var(--sipencak-radius);
            background: #ffffff;
        }

        .sipencak-store-badge img {
            height: 42px;
        }

        .sipencak-faq-item {
            border: 1px solid var(--sipencak-line);
            border-radius: 18px;
            background: #ffffff;
            padding: 24px 26px;
        }

        .sipencak-faq-item + .sipencak-faq-item {
            margin-top: 14px;
        }

        .sipencak-footer {
            position: relative;
            overflow: hidden;
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

        @media (max-width: 991.98px) {
            body.public-home .page_content {
                padding-top: 0;
            }

            .sipencak-hero {
                padding: 126px 0 78px;
            }

            .sipencak-search-panel {
                margin-top: 34px;
            }

            .sipencak-process {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .sipencak-public-header .site_logo img {
                width: 142px;
            }

            body.public-home .page_content {
                padding-top: 0;
            }

            .sipencak-hero {
                padding-top: 116px;
            }

            .sipencak-search-form {
                grid-template-columns: 1fr;
            }

            .sipencak-search-submit {
                width: 100%;
            }

            .sipencak-hero h1 {
                font-size: clamp(2.7rem, 14vw, 4rem);
            }
        }
    </style>
</head>

<body class="online_banking public-home">
    <svg width="0" height="0" aria-hidden="true" focusable="false" style="position:absolute">
        <filter id="sipencak-tutwuri-thin" color-interpolation-filters="sRGB">
            <feMorphology in="SourceAlpha" operator="erode" result="thinLine"/>
            <feFlood flood-color="#2B79B4" flood-opacity="0.04" result="lineColor"/>
            <feComposite in="lineColor" in2="thinLine" operator="in"/>
        </filter>
    </svg>

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
                                        <li><a href="#fungsi">Fungsi</a></li>
                                        <li><a href="#layanan">Layanan</a></li>
                                        <li><a href="#alur">Alur</a></li>
                                        <li><a href="#download">Download</a></li>
                                        <li><a href="#faq">FAQ</a></li>
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
            <section class="sipencak-hero" id="top">
                <div class="container">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-7">
                            <h1 class="wow fadeInUp" data-wow-delay=".1s">
                                Cari status data mahasiswa <span>KIP Kuliah</span> secara cepat.
                            </h1>
                            <p class="wow fadeInUp" data-wow-delay=".2s">
                                Cari nama mahasiswa, NIM, program studi, atau perguruan tinggi yang tercatat pada Sistem Pengelolaan Pencairan KIP Kuliah LLDIKTI Wilayah III.
                            </p>
                            <ul class="btns_group pb-0 unordered_list justify-content-lg-start wow fadeInUp" data-wow-delay=".3s">
                                <li>
                                    <a class="btn sipencak-hero-primary" href="#cari">
                                        <span class="btn_label">Mulai Cari Data</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="btn sipencak-hero-outline" href="#faq">
                                        <span class="btn_label">Lihat FAQ</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-5">
                            <div class="sipencak-search-panel wow fadeInRight" data-wow-delay=".2s" id="cari">
                                <form class="sipencak-search-form" action="{{ route('public.search') }}" method="get">
                                    <label class="sipencak-search-input mb-0">
                                        <span aria-hidden="true"><i class="fa-solid fa-magnifying-glass"></i></span>
                                        <input type="search" name="q" placeholder="Cari nama mahasiswa..." autocomplete="off" required>
                                    </label>
                                    <button class="sipencak-search-submit" type="submit">Cari</button>
                                </form>

                                <div class="row g-3 mt-3">
                                    <div class="col-6">
                                        <div class="sipencak-stat">
                                            <i class="sipencak-stat-icon fa-solid fa-user-graduate" aria-hidden="true"></i>
                                            <span>Mahasiswa</span>
                                            <strong>{{ number_format($stats['mahasiswa'], 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="sipencak-stat">
                                            <i class="sipencak-stat-icon fa-solid fa-building-columns" aria-hidden="true"></i>
                                            <span>Perguruan Tinggi</span>
                                            <strong>{{ number_format($stats['perguruan_tinggi'], 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="sipencak-stat">
                                            <i class="sipencak-stat-icon fa-solid fa-book-open" aria-hidden="true"></i>
                                            <span>Program Studi</span>
                                            <strong>{{ number_format($stats['program_studi'], 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="sipencak-stat">
                                            <i class="sipencak-stat-icon fa-solid fa-file-invoice-dollar" aria-hidden="true"></i>
                                            <span>Pengajuan</span>
                                            <strong>{{ number_format($stats['pencairan'], 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sipencak-logo-marquee" aria-label="Logo ekosistem berjalan">
                <div class="sipencak-logo-track">
                    @foreach([1, 2] as $loopIndex)
                        <span class="sipencak-logo-item"><img src="{{ url('/assets/img/sipencak3.png') }}" alt="SIPENCAK"></span>
                        <span class="sipencak-logo-item"><img src="{{ url('/assets/img/lldikti3.png') }}" alt="LLDIKTI Wilayah III"></span>
                        <span class="sipencak-logo-item"><img src="{{ url('/assets/synox/images/clients/client_logo_1.webp') }}" alt="Mitra layanan"></span>
                        <span class="sipencak-logo-item"><img src="{{ url('/assets/synox/images/clients/client_logo_2.webp') }}" alt="Mitra layanan"></span>
                        <span class="sipencak-logo-item"><img src="{{ url('/assets/synox/images/clients/client_logo_3.webp') }}" alt="Mitra layanan"></span>
                        <span class="sipencak-logo-item"><img src="{{ url('/assets/synox/images/clients/client_logo_4.webp') }}" alt="Mitra layanan"></span>
                    @endforeach
                </div>
            </section>

            <section class="sipencak-section sipencak-service-section" id="fungsi">
                <div class="container">
                    <div class="row align-items-end mb-5">
                        <div class="col-lg-8">
                            <div class="sipencak-section-title">
                                <span class="badge text-uppercase">Fungsi SIPENCAK</span>
                                <h2>Portal data publik dan pengelolaan KIP Kuliah.</h2>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <div class="sipencak-card">
                                <span class="sipencak-card-icon"><i class="fa-solid fa-database"></i></span>
                                <h3>Basis Data Terpadu</h3>
                                <p class="mb-0">Data mahasiswa, program studi, perguruan tinggi, dan status pengajuan tersaji dalam struktur yang mudah dicari.</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="sipencak-card">
                                <span class="sipencak-card-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                                <h3>Pengajuan Pencairan</h3>
                                <p class="mb-0">Admin PT dapat menyusun dokumen pencairan, daftar mahasiswa, dan berkas pendukung secara tertata.</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="sipencak-card">
                                <span class="sipencak-card-icon"><i class="fa-solid fa-chart-line"></i></span>
                                <h3>Monitoring Laporan</h3>
                                <p class="mb-0">Operator dapat melihat progres, rekap dana, status proses, dan histori laporan pencairan KIP Kuliah.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sipencak-section sipencak-function-section" id="layanan">
                <div class="container">
                    <div class="sipencak-section-title mb-5">
                        <span class="badge text-uppercase">Layanan Publik</span>
                        <h2>Informasi yang bisa diakses.</h2>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="sipencak-card">
                                <span class="sipencak-card-icon"><i class="fa-solid fa-user-graduate"></i></span>
                                <h3>Mahasiswa</h3>
                                <p class="mb-0">Cari nama atau NIM mahasiswa yang tercatat di SIPENCAK.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="sipencak-card">
                                <span class="sipencak-card-icon"><i class="fa-solid fa-building-columns"></i></span>
                                <h3>Perguruan Tinggi</h3>
                                <p class="mb-0">Lihat relasi mahasiswa dengan perguruan tinggi dan kode PT.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="sipencak-card">
                                <span class="sipencak-card-icon"><i class="fa-solid fa-book-open"></i></span>
                                <h3>Program Studi</h3>
                                <p class="mb-0">Informasi prodi, jenjang, dan angkatan mahasiswa.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="sipencak-card">
                                <span class="sipencak-card-icon"><i class="fa-solid fa-circle-check"></i></span>
                                <h3>Status Data</h3>
                                <p class="mb-0">Ringkasan status pengajuan atau status terdaftar yang tersedia.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sipencak-section" id="alur" style="background: var(--sipencak-dark);">
                <div class="container">
                    <div class="sipencak-section-title mb-5">
                        <span class="sipencak-eyebrow">Alur Singkat</span>
                        <h2 class="text-white">Cara menggunakan portal publik.</h2>
                    </div>
                    <div class="sipencak-process">
                        <div class="sipencak-process-item">
                            <span>01</span>
                            <div>
                                <h3>Masukkan kata kunci</h3>
                                <p class="mb-0">Gunakan nama mahasiswa, NIM, program studi, atau perguruan tinggi.</p>
                            </div>
                        </div>
                        <div class="sipencak-process-item">
                            <span>02</span>
                            <div>
                                <h3>Buka hasil pencarian</h3>
                                <p class="mb-0">Sistem membuka halaman hasil khusus berisi data yang sesuai.</p>
                            </div>
                        </div>
                        <div class="sipencak-process-item">
                            <span>03</span>
                            <div>
                                <h3>Login pengelola</h3>
                                <p class="mb-0">Operator dan Admin PT masuk untuk mengelola data internal.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sipencak-section sipencak-download" id="download">
                <div class="container">
                    <div class="row align-items-center g-5">
                        <div class="col-lg-6">
                            <span class="sipencak-eyebrow" style="color: #ffffff !important; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3);">Aplikasi SIPENCAK Mobile</span>
                            <h2 class="mt-4 mb-3 fw-bolder text-white" style="font-size: 2.75rem; letter-spacing: -1px; line-height: 1.2;">
                                Belum punya aplikasi <br><span style="color: #cce4f7; border-bottom: 3px solid #cce4f7;">SIPENCAK?</span>
                            </h2>
                            <p class="mb-4" style="color: rgba(255, 255, 255, 0.85); font-size: 1.15rem; line-height: 1.7;">
                                Unduh aplikasi SIPENCAK sekarang untuk akses data dan pemantauan layanan KIP Kuliah yang lebih praktis, cepat, dan aman melalui perangkat genggam Anda.
                            </p>
                            
                            <style>
                                .btn-modern-download {
                                    background: linear-gradient(135deg, #ffffff, #f0f8ff);
                                    color: #2B79B4 !important;
                                    border-radius: 16px;
                                    padding: 14px 28px;
                                    font-size: 1.1rem;
                                    border: none;
                                    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
                                    display: inline-flex;
                                    align-items: center;
                                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15), inset 0 2px 0 rgba(255,255,255,0.8);
                                }
                                .btn-modern-download:hover {
                                    transform: translateY(-4px);
                                    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25), inset 0 2px 0 rgba(255,255,255,0.9);
                                    color: #1c5b8b !important;
                                    text-decoration: none;
                                }
                                .store-badge-modern {
                                    transition: transform 0.3s ease, filter 0.3s ease;
                                    filter: drop-shadow(0 8px 16px rgba(0,0,0,0.15));
                                    border-radius: 12px;
                                }
                                .store-badge-modern:hover {
                                    transform: translateY(-3px) scale(1.02);
                                    filter: drop-shadow(0 12px 20px rgba(0,0,0,0.25));
                                }
                            </style>

                            <div class="d-flex flex-wrap align-items-center gap-4 mt-2">
                                <a class="btn-modern-download text-decoration-none" href="{{ url('/assets/apk/SIPENCAK.apk') }}" download="SIPENCAK-Mobile.apk">
                                    <span class="btn_icon me-3" style="font-size: 1.4rem;"><i class="fa-brands fa-android"></i></span>
                                    <span class="btn_label fw-bold">Download .APK</span>
                                </a>
                                <a class="store-badge-modern d-inline-block" href="#" aria-label="Download di Google Play">
                                    <img src="{{ url('/assets/synox/images/google_play.webp') }}" alt="Google Play" style="height: 54px; object-fit: contain;">
                                </a>
                            </div>
                        </div>
                        <style>
                            .iphone-frame {
                                position: absolute;
                                border: 12px solid #1c1c1c;
                                border-radius: 45px;
                                overflow: hidden;
                                background: #000;
                                box-shadow: 0 0 0 2px #333;
                            }
                            .dynamic-island {
                                position: absolute;
                                top: 10px;
                                left: 50%;
                                transform: translateX(-50%);
                                width: 90px;
                                height: 26px;
                                background: #000;
                                border-radius: 20px;
                                z-index: 10;
                            }
                            .iphone-frame img {
                                width: 100%;
                                height: 100%;
                                object-fit: cover;
                                border-radius: 32px;
                            }
                            .mockup-left {
                                width: 180px; height: 380px;
                                transform: rotate(-12deg) translateX(-60%);
                                z-index: 1;
                            }
                            .mockup-right {
                                width: 180px; height: 380px;
                                transform: rotate(12deg) translateX(60%);
                                z-index: 2;
                            }
                            .mockup-center {
                                position: relative !important;
                                width: 220px; height: 460px;
                                z-index: 3;
                            }
                        </style>
                        <div class="col-lg-6 text-center position-relative d-flex justify-content-center align-items-center" style="min-height: 480px;">
                            <div class="iphone-frame mockup-left">
                                <div class="dynamic-island"></div>
                                <img src="{{ url('/assets/img/mockup3.png') }}" alt="Preview aplikasi SIPENCAK 2">
                            </div>
                            <div class="iphone-frame mockup-right">
                                <div class="dynamic-island"></div>
                                <img src="{{ url('/assets/img/mockup1.png') }}" alt="Preview aplikasi SIPENCAK 3">
                            </div>
                            <div class="iphone-frame mockup-center">
                                <div class="dynamic-island"></div>
                                <img src="{{ url('/assets/img/mockup2.png') }}" alt="Preview aplikasi SIPENCAK Home">
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="sipencak-section sipencak-service-section" id="faq">
                <div class="container">
                    <div class="row g-5">
                        <div class="col-lg-5">
                            <div class="sipencak-section-title">
                                <span class="badge text-uppercase">FAQ / QNA</span>
                                <h2>Pertanyaan umum.</h2>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <div class="sipencak-faq-item">
                                <h3>Apa itu SIPENCAK?</h3>
                                <p class="mb-0">Sistem Pengelolaan Pencairan KIP Kuliah untuk mendukung pengajuan, verifikasi, dan pelaporan.</p>
                            </div>
                            <div class="sipencak-faq-item">
                                <h3>Apakah publik bisa melihat semua data?</h3>
                                <p class="mb-0">Portal publik hanya menampilkan informasi ringkas. Pengelolaan detail tetap berada di area login.</p>
                            </div>
                            <div class="sipencak-faq-item">
                                <h3>Kenapa data mahasiswa tidak ditemukan?</h3>
                                <p class="mb-0">Pastikan nama atau NIM benar. Data juga bisa belum diperbarui oleh perguruan tinggi terkait.</p>
                            </div>
                            <div class="sipencak-faq-item">
                                <h3>Siapa yang bisa login?</h3>
                                <p class="mb-0">Akses login digunakan oleh operator LLDIKTI dan Admin PT yang memiliki akun resmi SIPENCAK.</p>
                            </div>
                        </div>
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
                            <li><a href="#fungsi">Fungsi</a></li>
                            <li><a href="#layanan">Layanan</a></li>
                            <li><a href="#alur">Alur</a></li>
                            <li><a href="#download">Download</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-lg-2">
                        <h3>Layanan</h3>
                        <ul class="sipencak-footer-list">
                            <li><a href="#cari">Cari Mahasiswa</a></li>
                            <li><a href="{{ route('public.search') }}">Data Terdaftar</a></li>
                            <li><a href="{{ url('login') }}">Login Pengelola</a></li>
                            <li><a href="#faq">FAQ / QNA</a></li>
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
    <script src="{{ url('/assets/synox/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ url('/assets/synox/js/main.js') }}"></script>
</body>
</html>
