@extends('layouts.app')
@section('content')

<div class="container py-5 page-animate">
    <div class="detail-container">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                    <i class="ri-user-star-line fs-3"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">{{ $data['nama'] }}</h4>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge bg-primary bg-gradient text-white border-0 shadow-sm px-2 py-1"><i class="ri-fingerprint-line me-1"></i>{{ $data['nim'] }}</span>
                        <span class="badge bg-success-subtle text-success border border-success"><i class="ri-checkbox-circle-line me-1"></i>{{ $data['status_pengajuan'] }}</span>
                    </div>
                </div>
            </div>
            <a href="javascript:history.back()" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold shadow-sm">
                <i class="ri-arrow-left-line me-2"></i> Kembali
            </a>
        </div>

        <div class="card mb-4 p-4">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <span class="text-muted small d-block">Program Studi</span>
                    <div class="fw-semibold">{{ $data['kode_prodi'] }} - {{ $data['nama_prodi'] }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="text-muted small d-block">Jenjang Pendidikan</span>
                    <div class="fw-semibold">{{ $data['jenjang'] }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="text-muted small d-block">Angkatan</span>
                    <div class="fw-semibold">{{ $data['angkatan'] }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="text-muted small d-block">Kategori Beasiswa</span>
                    <div class="fw-semibold">{{ $data['kategori'] }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="text-muted small d-block">Pembaruan Status</span>
                    <div class="fw-semibold">
                        <span class="badge rounded-pill bg-light text-dark border px-3">{{ $data['pembaruan_status'] }}</span>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="text-muted small d-block">Perguruan Tinggi</span>
                    <div class="fw-semibold">
                        {{ $data['perguruan_tinggi'] }}
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{!! url('mahasiswa-list') !!}" class="btn btn-light rounded-pill px-4 shadow-sm border text-decoration-none text-dark">
                <i class="ri-arrow-left-line me-2"></i> Kembali ke Daftar
            </a>
            <a href="{!! url('mahasiswa-edit/' . $data['id']) !!}" class="btn btn-warning rounded-pill px-4 shadow-sm text-decoration-none">
                <i class="ri-edit-2-line me-2"></i> Perbarui Data
            </a>
        </div>

    </div>
</div>

@endsection
