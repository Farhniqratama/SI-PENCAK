@extends('layouts.app')
@section('css')
<style>
    .dashboard-display {
        --display-blue: #2B79B4;
        --display-blue-soft: #eaf4fb;
        --display-blue-dark: #1b5b8f;
        --display-ink: #142334;
        --display-muted: #667789;
        --display-surface: #ffffff;
        --display-surface-soft: #f6fafd;
        --display-border: rgba(43, 121, 180, 0.16);
        --display-radius: 0.35rem;
        color: var(--display-ink);
    }

    .dashboard-display .dashboard-shell {
        display: grid;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .dashboard-display .min-w-0 {
        min-width: 0;
    }

    .dashboard-display .display-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 1.5rem;
        align-items: center;
        border: 1px solid rgba(27, 91, 143, 0.2);
        border-radius: var(--display-radius);
        background: var(--display-blue-dark);
        color: #ffffff;
        padding: 1.5rem;
        box-shadow: 0 18px 44px rgba(27, 91, 143, 0.18);
    }

    .dashboard-display .display-kicker,
    .dashboard-display .panel-kicker {
        color: var(--display-blue);
        font-size: 0.7rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .dashboard-display .display-hero .display-kicker {
        color: rgba(255, 255, 255, 0.72);
    }

    .dashboard-display .hero-title {
        font-size: clamp(1.35rem, 2vw, 2rem);
        font-weight: 800;
        letter-spacing: 0;
        margin-bottom: 0.45rem;
    }

    .dashboard-display .hero-copy {
        color: rgba(255, 255, 255, 0.78);
        max-width: 620px;
    }

    .dashboard-display .hero-status {
        display: grid;
        gap: 0.65rem;
        min-width: 210px;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: var(--display-radius);
        background: rgba(255, 255, 255, 0.08);
    }

    .dashboard-display .hero-status span {
        color: rgba(255, 255, 255, 0.72);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .dashboard-display .hero-status strong {
        font-size: 1.25rem;
    }

    .dashboard-display .metric-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
    }

    .dashboard-display .metric-tile,
    .dashboard-display .dashboard-panel {
        border: 1px solid var(--display-border);
        border-radius: var(--display-radius);
        background: var(--display-surface);
        box-shadow: 0 14px 34px rgba(27, 91, 143, 0.08);
    }

    .dashboard-display .metric-tile {
        position: relative;
        min-height: 148px;
        padding: 1.15rem;
        overflow: hidden;
        transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    }

    .dashboard-display .metric-tile::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 4px;
        background: var(--tile-color, var(--display-blue));
    }

    .dashboard-display .metric-tile:hover {
        transform: translateY(-2px);
        border-color: rgba(43, 121, 180, 0.28);
        box-shadow: 0 18px 42px rgba(27, 91, 143, 0.12);
    }

    .dashboard-display .metric-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .dashboard-display .metric-icon {
        display: inline-flex;
        width: 2.55rem;
        height: 2.55rem;
        align-items: center;
        justify-content: center;
        border-radius: var(--display-radius);
        background: var(--display-blue-soft);
        color: var(--tile-color, var(--display-blue));
        font-size: 1.45rem;
    }

    .dashboard-display .metric-label,
    .dashboard-display .panel-subtitle,
    .dashboard-display .activity-meta {
        color: var(--display-muted);
    }

    .dashboard-display .metric-value {
        color: var(--display-ink);
        font-size: clamp(1.35rem, 2vw, 1.85rem);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 0.35rem;
    }

    .dashboard-display .metric-track {
        height: 4px;
        margin-top: 1rem;
        overflow: hidden;
        border-radius: 1rem;
        background: rgba(43, 121, 180, 0.12);
    }

    .dashboard-display .metric-track span {
        display: block;
        width: var(--track-width, 64%);
        height: 100%;
        background: var(--tile-color, var(--display-blue));
    }

    .dashboard-display .category-mini {
        display: grid;
        grid-template-columns: 1fr 1px 1fr;
        gap: 0.75rem;
        align-items: center;
        margin-top: 0.4rem;
    }

    .dashboard-display .category-divider {
        height: 2.4rem;
        background: var(--display-border);
    }

    .dashboard-display .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(320px, 0.75fr);
        gap: 1rem;
    }

    .dashboard-display .dashboard-panel {
        overflow: hidden;
    }

    .dashboard-display .panel-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.1rem 1.2rem;
        border-bottom: 1px solid var(--display-border);
        background: var(--display-surface-soft);
    }

    .dashboard-display .panel-title {
        color: var(--display-ink);
        font-weight: 800;
        margin-bottom: 0.2rem;
    }

    .dashboard-display .panel-action {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border: 1px solid rgba(43, 121, 180, 0.2);
        border-radius: var(--display-radius);
        background: #ffffff;
        color: var(--display-blue-dark);
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.45rem 0.65rem;
        text-decoration: none;
        white-space: nowrap;
    }

    .dashboard-display .chart-wrap {
        height: 320px;
        padding: 1.2rem;
    }

    .dashboard-display .status-wrap {
        height: 210px;
        padding: 1.1rem 1.2rem 0.25rem;
    }

    .dashboard-display .status-list,
    .dashboard-display .info-list {
        display: grid;
        gap: 0.7rem;
        padding: 0 1.2rem 1.2rem;
    }

    .dashboard-display .status-item,
    .dashboard-display .info-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        border: 1px solid var(--display-border);
        border-radius: var(--display-radius);
        background: var(--display-surface);
        padding: 0.75rem;
    }

    .dashboard-display .status-dot {
        width: 0.65rem;
        height: 0.65rem;
        border-radius: 999px;
        background: var(--dot-color);
    }

    .dashboard-display .table {
        margin-bottom: 0;
    }

    .dashboard-display .table thead th {
        border-bottom: 1px solid var(--display-border);
        background: var(--display-surface-soft);
        color: #31546f;
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding-top: 0.95rem;
        padding-bottom: 0.95rem;
    }

    .dashboard-display .table tbody td {
        border-color: rgba(43, 121, 180, 0.1);
        padding-top: 0.9rem;
        padding-bottom: 0.9rem;
        vertical-align: middle;
    }

    .dashboard-display .status-pill {
        border-radius: var(--display-radius);
        font-weight: 800;
        padding: 0.38rem 0.55rem;
    }

    .dashboard-display .info-item {
        color: inherit;
        text-decoration: none;
        transition: border-color 0.18s ease, background-color 0.18s ease;
    }

    .dashboard-display .info-item:hover {
        border-color: rgba(43, 121, 180, 0.3);
        background: var(--display-blue-soft);
    }

    .dashboard-display .info-date {
        display: grid;
        place-items: center;
        width: 3rem;
        min-width: 3rem;
        height: 3rem;
        border-radius: var(--display-radius);
        background: var(--display-blue-dark);
        color: #ffffff;
        line-height: 1;
    }

    .dashboard-display .info-date strong {
        font-size: 1rem;
    }

    .dashboard-display .info-date small {
        color: rgba(255, 255, 255, 0.76);
        font-size: 0.64rem;
        text-transform: uppercase;
    }

    @media (max-width: 1199.98px) {
        .dashboard-display .metric-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .dashboard-display .display-hero,
        .dashboard-display .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-display .hero-status {
            min-width: 0;
        }
    }

    @media (max-width: 575.98px) {
        .dashboard-display .display-hero,
        .dashboard-display .metric-tile,
        .dashboard-display .panel-header {
            padding: 1rem;
        }

        .dashboard-display .metric-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-display .panel-header {
            display: grid;
        }
    }

    html[data-bs-theme="dark"] .dashboard-display {
        --display-ink: #edf4fb;
        --display-muted: #aebdcc;
        --display-surface: #313a46;
        --display-surface-soft: #26303a;
        --display-blue-soft: rgba(118, 179, 216, 0.12);
        --display-border: rgba(255, 255, 255, 0.1);
    }

    html[data-bs-theme="dark"] .dashboard-display .display-hero {
        box-shadow: 0 18px 44px rgba(0, 0, 0, 0.2);
    }

    html[data-bs-theme="dark"] .dashboard-display .metric-tile,
    html[data-bs-theme="dark"] .dashboard-display .dashboard-panel {
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.18);
    }

    html[data-bs-theme="dark"] .dashboard-display .panel-action {
        background: #26303a;
        color: #cfe5f5;
        border-color: rgba(255, 255, 255, 0.12);
    }

    html[data-bs-theme="dark"] .dashboard-display .table thead th {
        color: #cfe5f5;
    }

    .dashboard-display {
        --display-blue: #2B79B4;
        --display-blue-dark: #1b5b8f;
        --display-blue-deep: #143f66;
        --display-blue-soft: #eaf4fb;
        --display-ink: #142334;
        --display-muted: #667789;
        --display-surface: #ffffff;
        --display-surface-soft: #f6fafd;
        --display-border: rgba(43, 121, 180, 0.16);
        --display-radius: 0.35rem;
    }

    .dashboard-display .dashboard-shell {
        gap: 1.15rem;
        margin-top: 1.25rem;
    }

    .dashboard-display .dashboard-command-strip {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.8rem;
    }

    .dashboard-display .command-link {
        display: flex;
        min-height: 66px;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 0.95rem;
        border: 1px solid var(--display-border);
        border-left: 4px solid var(--command-color, var(--display-blue-dark));
        border-radius: var(--display-radius);
        background: var(--display-surface);
        color: var(--display-ink);
        text-decoration: none;
        box-shadow: 0 12px 26px rgba(27, 91, 143, 0.07);
        transition: border-color 0.18s ease, transform 0.18s ease, box-shadow 0.18s ease;
    }

    .dashboard-display .command-link:hover {
        transform: translateY(-1px);
        border-color: rgba(43, 121, 180, 0.3);
        color: var(--display-ink);
        box-shadow: 0 16px 34px rgba(27, 91, 143, 0.11);
    }

    .dashboard-display .command-icon {
        display: inline-flex;
        width: 2.3rem;
        height: 2.3rem;
        align-items: center;
        justify-content: center;
        border-radius: var(--display-radius);
        background: var(--display-blue-soft);
        color: var(--command-color, var(--display-blue-dark));
        font-size: 1.2rem;
    }

    .dashboard-display .command-title {
        display: block;
        color: var(--display-ink);
        font-weight: 800;
        line-height: 1.2;
    }

    .dashboard-display .command-subtitle {
        color: var(--display-muted);
        font-size: 0.76rem;
        font-weight: 700;
    }

    .dashboard-display .display-hero {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 290px;
        align-items: stretch;
        min-height: 218px;
        gap: 0;
        overflow: hidden;
        padding: 0;
        border-color: rgba(27, 91, 143, 0.24);
        background: var(--display-blue-deep);
        box-shadow: 0 18px 42px rgba(27, 91, 143, 0.16);
    }

    .dashboard-display .display-hero > div:first-child {
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 1.35rem 1.45rem;
        border-left: 5px solid #76b3d8;
    }

    .dashboard-display .display-hero .display-kicker {
        width: fit-content;
        margin-bottom: 0.9rem !important;
        padding: 0.38rem 0.6rem;
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: var(--display-radius);
        background: rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.82);
    }

    .dashboard-display .hero-title {
        max-width: 720px;
        color: #ffffff;
        font-size: 1.9rem;
        line-height: 1.15;
        margin-bottom: 0.65rem;
    }

    .dashboard-display .hero-copy {
        max-width: 680px;
        color: rgba(255, 255, 255, 0.76);
        font-size: 0.92rem;
        line-height: 1.65;
    }

    .dashboard-display .hero-status {
        align-content: center;
        min-width: 0;
        margin: 1rem;
        padding: 1.1rem;
        border-color: rgba(255, 255, 255, 0.18);
        background: #ffffff;
        color: var(--display-ink);
        box-shadow: 0 12px 28px rgba(10, 37, 60, 0.16);
    }

    .dashboard-display .hero-status span {
        color: var(--display-muted);
    }

    .dashboard-display .hero-status strong {
        color: var(--display-blue-dark);
    }

    .dashboard-display .hero-status small {
        color: var(--display-muted) !important;
    }

    .dashboard-display .metric-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        align-items: stretch;
    }

    .dashboard-display .metric-tile {
        display: flex;
        min-height: 136px;
        flex-direction: column;
        justify-content: space-between;
        padding: 1rem;
        border-color: rgba(43, 121, 180, 0.18);
        background: var(--display-surface);
        box-shadow: 0 12px 26px rgba(27, 91, 143, 0.07);
    }

    .dashboard-display .metric-tile::before {
        inset: 0 0 auto;
        width: 100%;
        height: 4px;
    }

    .dashboard-display .metric-tile:first-child {
        grid-column: span 2;
        background: var(--display-blue-soft);
    }

    .dashboard-display .metric-tile:hover {
        transform: translateY(-1px);
    }

    .dashboard-display .metric-head {
        margin-bottom: 0.8rem;
    }

    .dashboard-display .metric-icon {
        width: 2.35rem;
        height: 2.35rem;
        background: #ffffff;
        box-shadow: inset 0 0 0 1px rgba(43, 121, 180, 0.14);
    }

    .dashboard-display .metric-value {
        font-size: 1.55rem;
        letter-spacing: 0;
    }

    .dashboard-display .metric-tile:first-child .metric-value {
        font-size: 2rem;
    }

    .dashboard-display .dashboard-grid {
        grid-template-columns: minmax(0, 1.35fr) minmax(330px, 0.8fr);
    }

    .dashboard-display .dashboard-panel {
        border-color: rgba(43, 121, 180, 0.17);
        box-shadow: 0 12px 30px rgba(27, 91, 143, 0.08);
    }

    .dashboard-display .panel-header {
        align-items: center;
        padding: 1rem 1.1rem;
        border-bottom-color: rgba(43, 121, 180, 0.14);
        background: var(--display-surface);
    }

    .dashboard-display .panel-header > div:first-child {
        padding-left: 0.8rem;
        border-left: 4px solid var(--display-blue-dark);
    }

    .dashboard-display .panel-title {
        font-size: 1rem;
    }

    .dashboard-display .panel-action {
        background: var(--display-blue-soft);
    }

    .dashboard-display .chart-wrap {
        height: 340px;
        padding: 1.15rem;
        background: var(--display-surface-soft);
    }

    .dashboard-display .status-wrap {
        height: 225px;
        padding: 1.05rem 1.1rem 0.3rem;
        background: var(--display-surface-soft);
    }

    .dashboard-display .status-list,
    .dashboard-display .info-list {
        padding: 1rem 1.1rem 1.1rem;
    }

    .dashboard-display .status-item,
    .dashboard-display .info-item {
        min-height: 58px;
        background: var(--display-surface-soft);
    }

    .dashboard-display .table-responsive {
        border: 0;
        border-radius: 0;
    }

    .dashboard-display .table thead th {
        background: var(--display-blue-soft) !important;
    }

    .dashboard-display .info-date {
        background: var(--display-blue-dark);
    }

    html[data-bs-theme="dark"] .dashboard-display {
        --display-ink: #edf4fb;
        --display-muted: #aebdcc;
        --display-surface: #313a46;
        --display-surface-soft: #26303a;
        --display-blue-soft: rgba(118, 179, 216, 0.12);
        --display-border: rgba(255, 255, 255, 0.1);
    }

    html[data-bs-theme="dark"] .dashboard-display .metric-tile:first-child {
        background: #26303a;
    }

    html[data-bs-theme="dark"] .dashboard-display .hero-status,
    html[data-bs-theme="dark"] .dashboard-display .metric-icon {
        background: #26303a;
        box-shadow: none;
    }

    html[data-bs-theme="dark"] .dashboard-display .hero-status strong {
        color: #cfe5f5;
    }

    @media (max-width: 1199.98px) {
        .dashboard-display .dashboard-command-strip {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-display .metric-tile:first-child {
            grid-column: span 1;
        }
    }

    @media (max-width: 991.98px) {
        .dashboard-display .display-hero,
        .dashboard-display .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .dashboard-display .display-hero {
            min-height: 0;
        }
    }

    @media (max-width: 575.98px) {
        .dashboard-display .display-hero > div:first-child,
        .dashboard-display .metric-tile,
        .dashboard-display .panel-header {
            padding: 1rem;
        }

        .dashboard-display .hero-title {
            font-size: 1.35rem;
        }

        .dashboard-display .hero-status {
            margin: 0 1rem 1rem;
        }

        .dashboard-display .dashboard-command-strip {
            grid-template-columns: 1fr;
        }
    }

    .dashboard-display.finance-dashboard .dashboard-shell {
        gap: 1rem;
    }

    .dashboard-display.finance-dashboard .display-hero {
        grid-template-columns: minmax(0, 1fr) 340px;
        min-height: 190px;
        border-color: var(--display-border);
        background: var(--display-surface);
        color: var(--display-ink);
        box-shadow: 0 12px 30px rgba(27, 91, 143, 0.08);
    }

    .dashboard-display.finance-dashboard .display-hero > div:first-child {
        border-left-color: var(--display-blue-dark);
    }

    .dashboard-display.finance-dashboard .display-hero .display-kicker {
        border-color: var(--display-border);
        background: var(--display-blue-soft);
        color: var(--display-blue-dark);
    }

    .dashboard-display.finance-dashboard .hero-title {
        color: var(--display-ink);
        font-size: 1.65rem;
    }

    .dashboard-display.finance-dashboard .hero-copy {
        color: var(--display-muted);
    }

    .dashboard-display.finance-dashboard .hero-status {
        border-color: rgba(27, 91, 143, 0.22);
        background: var(--display-blue-dark);
        color: #ffffff;
        box-shadow: none;
    }

    .dashboard-display.finance-dashboard .hero-status span,
    .dashboard-display.finance-dashboard .hero-status small {
        color: rgba(255, 255, 255, 0.74) !important;
    }

    .dashboard-display.finance-dashboard .hero-status strong {
        color: #ffffff;
        font-size: 1.45rem;
        line-height: 1.25;
    }

    .dashboard-display.finance-dashboard .dashboard-command-strip {
        display: none;
    }

    .dashboard-display.finance-dashboard .metric-grid {
        grid-template-columns: 1.3fr repeat(3, minmax(0, 1fr));
    }

    .dashboard-display.finance-dashboard .metric-tile,
    .dashboard-display.finance-dashboard .dashboard-panel {
        box-shadow: 0 10px 24px rgba(27, 91, 143, 0.06);
    }

    .dashboard-display.finance-dashboard .metric-tile {
        min-height: 126px;
        border-left: 4px solid var(--tile-color, var(--display-blue-dark));
    }

    .dashboard-display.finance-dashboard .metric-tile::before {
        display: none;
    }

    .dashboard-display.finance-dashboard .metric-tile:first-child {
        grid-column: auto;
        background: var(--display-surface);
    }

    .dashboard-display.finance-dashboard .metric-icon {
        background: var(--display-blue-soft);
        box-shadow: none;
    }

    .dashboard-display.finance-dashboard .metric-track {
        display: none;
    }

    .dashboard-display.finance-dashboard .dashboard-grid {
        grid-template-columns: minmax(0, 1.45fr) minmax(340px, 0.75fr);
    }

    .dashboard-display.finance-dashboard .panel-header {
        background: var(--display-surface-soft);
    }

    .dashboard-display.finance-dashboard .panel-header > div:first-child {
        border-left-color: var(--display-blue-dark);
    }

    .dashboard-display.finance-dashboard .chart-wrap,
    .dashboard-display.finance-dashboard .status-wrap {
        background: var(--display-surface);
    }

    html[data-bs-theme="dark"] .dashboard-display.finance-dashboard .display-hero,
    html[data-bs-theme="dark"] .dashboard-display.finance-dashboard .metric-tile:first-child {
        background: var(--display-surface);
    }

    @media (max-width: 1199.98px) {
        .dashboard-display.finance-dashboard .metric-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        .dashboard-display.finance-dashboard .display-hero,
        .dashboard-display.finance-dashboard .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .dashboard-display.finance-dashboard .metric-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php
    $pt_id = session('pt');
    $total_selesai = \Illuminate\Support\Facades\DB::table('pencairans')->where('id_pt', $pt_id)->where('status', 'selesai')->count();
    $total_pengajuan = $total_selesai + $total_pencairan_proses + $total_pencairan_ditolak;
@endphp

<div class="dashboard-display finance-dashboard">
    <div class="dashboard-shell">
        <div class="dashboard-main-card">
        <section class="display-hero">
            <div>
                <div class="display-kicker mb-2">Ringkasan Pencairan dan Keuangan</div>
                <h4 class="hero-title">Dashboard Keuangan Pencairan PT</h4>
                <p class="hero-copy small mb-0">Pemantauan formal untuk total dana diterima, status pengajuan, dan riwayat pencairan institusi Anda.</p>
            </div>
            <div class="hero-status">
                <span>Total Dana Pencairan</span>
                <strong>Rp {!! number_format($total_nominal_selesai, 0, ',', '.') !!}</strong>
                <small class="text-white-50">Akumulasi pencairan selesai per {!! date('d M Y') !!}</small>
            </div>
        </section>

        <section class="dashboard-command-strip">
            <a href="{!! url('permohonan-pencairan') !!}" class="command-link" style="--command-color: #1b5b8f;">
                <span>
                    <span class="command-title">Ajukan Permohonan</span>
                    <span class="command-subtitle">Mulai pencairan</span>
                </span>
                <span class="command-icon"><i class="ri-send-plane-line"></i></span>
            </a>
            <a href="{!! url('admin/pencairan/draft') !!}" class="command-link" style="--command-color: #0ea5e9;">
                <span>
                    <span class="command-title">Draft Permohonan</span>
                    <span class="command-subtitle">Lanjutkan data</span>
                </span>
                <span class="command-icon"><i class="ri-draft-line"></i></span>
            </a>
            <a href="{!! url('mahasiswa-list') !!}" class="command-link" style="--command-color: #10b981;">
                <span>
                    <span class="command-title">Mahasiswa</span>
                    <span class="command-subtitle">Data penerima</span>
                </span>
                <span class="command-icon"><i class="ri-group-line"></i></span>
            </a>
            <a href="{!! url('admin/laporan') !!}" class="command-link" style="--command-color: #f59e0b;">
                <span>
                    <span class="command-title">Riwayat Pencairan</span>
                    <span class="command-subtitle">Lihat laporan</span>
                </span>
                <span class="command-icon"><i class="ri-file-chart-line"></i></span>
            </a>
        </section>

        <section class="metric-grid">
            <div class="metric-tile" style="--tile-color: #1b5b8f; --track-width: 76%;">
                <div class="metric-head">
                    <div>
                        <div class="display-kicker">Total Dana</div>
                        <div class="metric-label small">Pencairan selesai</div>
                    </div>
                    <div class="metric-icon"><i class="ri-money-dollar-circle-line"></i></div>
                </div>
                <div class="metric-value">Rp {!! number_format($total_nominal_selesai, 0, ',', '.') !!}</div>
                <div class="metric-label small">Akumulasi dana diterima</div>
                <div class="metric-track"><span></span></div>
            </div>

            <div class="metric-tile" style="--tile-color: #2B79B4; --track-width: 86%;">
                <div class="metric-head">
                    <div>
                        <div class="display-kicker">Pengajuan</div>
                        <div class="metric-label small">Total permohonan</div>
                    </div>
                    <div class="metric-icon"><i class="ri-file-list-3-line"></i></div>
                </div>
                <div class="metric-value">{!! number_format($total_pengajuan) !!}</div>
                <div class="metric-label small">Seluruh pengajuan institusi</div>
                <div class="metric-track"><span></span></div>
            </div>

            <div class="metric-tile" style="--tile-color: #f59e0b; --track-width: 64%;">
                <div class="metric-head">
                    <div>
                        <div class="display-kicker">Dalam Proses</div>
                        <div class="metric-label small">Menunggu penyelesaian</div>
                    </div>
                    <div class="metric-icon"><i class="ri-time-line"></i></div>
                </div>
                <div class="metric-value">{!! number_format($total_pencairan_proses) !!}</div>
                <div class="metric-label small">Pengajuan sedang diproses</div>
                <div class="metric-track"><span></span></div>
            </div>

            <div class="metric-tile" style="--tile-color: #ef4444; --track-width: 72%;">
                <div class="metric-head">
                    <div>
                        <div class="display-kicker">Ditolak</div>
                        <div class="metric-label small">Perlu evaluasi</div>
                    </div>
                    <div class="metric-icon"><i class="ri-close-circle-line"></i></div>
                </div>
                <div class="metric-value">{!! number_format($total_pencairan_ditolak) !!}</div>
                <div class="metric-label small">Pengajuan tidak disetujui</div>
            </div>
        </section>

        <section class="dashboard-grid">
            <div class="dashboard-panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-kicker">Analitik Keuangan {!! date('Y') !!}</div>
                        <h5 class="panel-title">Tren Pengajuan Pencairan</h5>
                        <p class="panel-subtitle small mb-0">Rekap bulanan pengajuan pencairan institusi tahun berjalan.</p>
                    </div>
                </div>
                <div class="chart-wrap">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <div class="dashboard-panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-kicker">Komposisi Status</div>
                        <h5 class="panel-title">Rasio Pencairan</h5>
                        <p class="panel-subtitle small mb-0">Perbandingan selesai, proses, dan ditolak.</p>
                    </div>
                </div>
                <div class="status-wrap">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="status-list">
                    <div class="status-item">
                        <span class="d-flex align-items-center gap-2 small"><i class="status-dot" style="--dot-color: #10b981;"></i> Selesai</span>
                        <strong>{!! number_format($total_selesai) !!}</strong>
                    </div>
                    <div class="status-item">
                        <span class="d-flex align-items-center gap-2 small"><i class="status-dot" style="--dot-color: #f59e0b;"></i> Proses</span>
                        <strong>{!! number_format($total_pencairan_proses) !!}</strong>
                    </div>
                    <div class="status-item">
                        <span class="d-flex align-items-center gap-2 small"><i class="status-dot" style="--dot-color: #ef4444;"></i> Ditolak</span>
                        <strong>{!! number_format($total_pencairan_ditolak) !!}</strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="dashboard-grid mb-4">
            <div class="dashboard-panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-kicker">Riwayat Keuangan</div>
                        <h5 class="panel-title">Pencairan Terbaru</h5>
                        <p class="panel-subtitle small mb-0">Daftar pengajuan terakhir beserta status dan nominal.</p>
                    </div>
                    <a href="{!! url('verifikasi-pembaharuan-status') !!}" class="panel-action">
                        Lihat Semua <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-centered">
                        <thead>
                            <tr>
                                <th class="ps-4">No Surat</th>
                                <th>Tanggal</th>
                                <th>Jenis Bantuan</th>
                                <th>Status</th>
                                <th class="pe-4 text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pencairan_terbaru as $p)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">{{ $p->no_sk ?? '-' }}</td>
                                    <td>{{ date('d M Y', strtotime($p->tanggal)) }}</td>
                                    <td>{{ $p->jenis_bantuan ?? '-' }}</td>
                                    <td>
                                        @if(strtolower($p->status) === 'selesai')
                                            <span class="badge status-pill bg-success-subtle text-success">Selesai</span>
                                        @elseif(strtolower($p->status) === 'ditolak')
                                            <span class="badge status-pill bg-danger-subtle text-danger">Ditolak</span>
                                        @else
                                            <span class="badge status-pill bg-warning-subtle text-warning">Proses</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end fw-bold text-success">Rp {{ number_format($p->nominal_pencairan ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pengajuan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dashboard-panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-kicker">Informasi</div>
                        <h5 class="panel-title">Papan Informasi</h5>
                        <p class="panel-subtitle small mb-0">Pengumuman terbaru untuk institusi.</p>
                    </div>
                    <a href="{!! url('papan-informasi') !!}" class="panel-action">Semua</a>
                </div>
                <div class="info-list pt-3">
                    @forelse($informasi->take(4) as $info)
                        <a href="{!! url('informasi-detail/' . $info['id']) !!}" class="info-item">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <div class="info-date">
                                    <strong>{!! date('d', strtotime($info['tanggal'])) !!}</strong>
                                    <small>{!! date('M', strtotime($info['tanggal'])) !!}</small>
                                </div>
                                <div class="min-w-0">
                                    <h6 class="mb-1 fw-semibold text-truncate">{{ $info['judul'] }}</h6>
                                    <div class="activity-meta small"><i class="ri-time-line me-1"></i>{!! date('Y', strtotime($info['tanggal'])) !!}</div>
                                </div>
                            </div>
                            <i class="ri-arrow-right-s-line text-muted"></i>
                        </a>
                    @empty
                        <div class="text-center py-4 text-muted">Belum ada informasi terbaru.</div>
                    @endforelse
                </div>
            </div>
        </section>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        Chart.defaults.font.family = "'Inter', sans-serif";

        var dashboard = document.querySelector('.dashboard-display');
        var styles = getComputedStyle(dashboard);
        var textColor = styles.getPropertyValue('--display-muted').trim();
        var borderColor = styles.getPropertyValue('--display-border').trim();
        var surfaceColor = styles.getPropertyValue('--display-surface').trim();

        var trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: {!! json_encode($chart_bulanan) !!},
                    borderColor: '#2B79B4',
                    backgroundColor: 'rgba(43, 121, 180, 0.12)',
                    borderWidth: 3,
                    tension: 0.36,
                    fill: true,
                    pointBackgroundColor: surfaceColor,
                    pointBorderColor: '#2B79B4',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: textColor, precision: 0 },
                        grid: { color: borderColor }
                    },
                    x: {
                        ticks: { color: textColor },
                        grid: { display: false }
                    }
                }
            }
        });

        var statusCtx = document.getElementById('statusChart').getContext('2d');
        var dataStatus = [{!! $total_selesai !!}, {!! $total_pencairan_proses !!}, {!! $total_pencairan_ditolak !!}];
        var bgColors = ['#10b981', '#f59e0b', '#ef4444'];
        var labels = ['Selesai', 'Proses', 'Ditolak'];

        if(dataStatus[0] === 0 && dataStatus[1] === 0 && dataStatus[2] === 0) {
            dataStatus = [1];
            bgColors = ['#d8e2ef'];
            labels = ['Kosong'];
        }

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataStatus,
                    backgroundColor: bgColors,
                    borderColor: surfaceColor,
                    borderWidth: 3,
                    hoverOffset: 4
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        filter: function(tooltipItem) {
                            return tooltipItem.label !== 'Kosong';
                        }
                    }
                }
            }
        });
    });
</script>

@endsection
