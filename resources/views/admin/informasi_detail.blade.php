@extends('layouts.app')
@section('content')


<div class="container py-4">
    <div class="container-fluid">

        <div class="nav-top">
            <a href="#" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{!! url('/papan-informasi') !!}';" class="btn-circle-back shadow-sm">
                <i class="ri-arrow-left-line"></i>
            </a>
            <span class="fw-700 text-muted small uppercase">Detail Informasi</span>
        </div>

        <div class="card mb-4">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex flex-wrap align-items-center mb-4 border-bottom pb-4 px-3"
                   >

                    <div class="d-flex align-items-center">
                        <i class="ri-calendar-line text-primary me-2"></i>
                        <span>{!! tgl_indo($data['tanggal']); !!}</span>
                    </div>

                    <div class="d-flex align-items-center">
                        <i class="ri-eye-line text-primary me-3"></i>
                        <span>Official Update</span>
                    </div>

                    <div class="ms-md-auto d-none d-md-flex align-items-center text-success fw-bold">
                        <i class="ri-shield-check-line me-2"></i>
                        <span>Terverifikasi Sistem</span>
                    </div>

                </div>

                <h1 class="h3">{!! e($data['judul']); !!}</h1>

                <div class="article-box">
                    <div class="article-text">
                        @php
                        $deskripsi = html_entity_decode($data['deskripsi']);
                        echo $deskripsi;
                        @endphp
                    </div>
                </div>

                @if(!empty($data['file']))
                    <div class="mt-4">
                        <a href="{!! url('/informasi/' . $data['file']); !!}" class="btn btn-primary w-100 py-3 rounded-pill fw-bold" download="{!! $data['file']; !!}">
                            <i class="ri-download-cloud-2-line me-2"></i> Download Lampiran Dokumen ({!! e($data['file']); !!})
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="footer-card-mini">
                <div class="avatar-sm rounded bg-light d-inline-flex align-items-center justify-content-center me-2"><i class="ri-bank-line"></i></div>
                <div>
                    <div class="fw-800 small text-dark">Sumber Informasi</div>
                    <div class="text-muted small">LLDIKTI Wilayah III Jakarta</div>
                </div>
            </div>

            <div class="footer-card-mini">
                <div class="avatar-sm rounded bg-light d-inline-flex align-items-center justify-content-center me-2"><i class="ri-customer-service-2-line"></i></div>
                <div>
                    <div class="fw-800 small text-dark">Pusat Bantuan</div>
                    <div class="text-muted small">Helpdesk KIP-K Terintegrasi</div>
                </div>
            </div>

            <div class="footer-card-mini">
                <div class="avatar-sm rounded bg-light d-inline-flex align-items-center justify-content-center me-2"><i class="ri-lock-line"></i></div>
                <div>
                    <div class="fw-800 small text-dark">Enkripsi Data</div>
                    <div class="text-muted small">Informasi Publik Aman</div>
                </div>
            </div>
        </div>

        <div class="alert alert-light">
            <p class="mb-1"><strong>Penting:</strong> Pastikan Anda membaca seluruh isi pengumuman sebelum melakukan tindakan lebih lanjut.</p>
            <p>&copy; 2026 Pusat Informasi KIP-Kuliah LLDIKTI III. Seluruh hak cipta dilindungi undang-undang.</p>
            <div class="mt-3">
                <a href="#" class="text-muted mx-2 small text-decoration-none">Syarat & Ketentuan</a>
                <a href="#" class="text-muted mx-2 small text-decoration-none">Kebijakan Privasi</a>
            </div>
        </div>

    </div>
</div>

@php
function tgl_indo($tanggal)
{
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $pecah = explode('-', date('Y-m-d', strtotime($tanggal)));
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}
@endphp

@endsection
