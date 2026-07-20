@extends('layouts.app')
@section('content')

@php
    $ptItems = $pts ?? collect();
    $ptCount = method_exists($ptItems, 'count') ? $ptItems->count() : count($ptItems);
@endphp



<div class="container-fluid report-admin-home">
    <div class="report-admin-hero mb-3">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h2 class="report-admin-title">Laporan Perguruan Tinggi</h2>
                <p class="text-muted mb-0">Manajemen dokumentasi dan arsip pelaporan dana akademik periode 2026</p>
            </div>
            <div class="d-flex align-items-end">
                <div class="report-admin-pill">
                    <i class="ri-bank-line"></i>
                    <span>Database Institusi: {!! number_format($pager->getTotal(), 0, ',', '.') !!} PT</span>
                </div>
            </div>
        </div>
    </div>

    <form action="{!! current_url('/') !!}" method="get" class="report-admin-filter report-search-card mb-4">
        <div class="row g-3 align-items-end report-filter-grid">
            <div class="col-12 col-lg-9 col-xl-10">
                <label class="form-label small text-muted fw-bold text-uppercase">Cari Perguruan Tinggi</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                    <input type="text" name="keyword" class="form-control" placeholder="Cari nama atau kode PT..." value="{{ $keyword ?? '' }}">
                </div>
            </div>
            <div class="col-12 col-lg-3 col-xl-2 d-grid">
                <button class="btn btn-primary" type="submit">
                    <i class="ri-search-line me-1"></i> Cari
                </button>
            </div>
        </div>
    </form>

    <div class="row g-4 report-pt-grid">
        @if($ptCount > 0)
            @foreach($pts as $pt)
                <div class="col-12 col-md-6 col-xl-4">
                    <a href="{!! url('admin/laporan-list/' . $pt['id']) !!}" class="pt-card">
                        <div class="pt-card-top">
                            <div class="pt-icon">
                                <i class="ri-bank-line"></i>
                            </div>
                            <div class="pt-status">
                                {{ $pt['status'] }}
                            </div>
                        </div>

                        <div class="pt-card-body">
                            <h5 class="pt-name">{{ $pt['perguruan_tinggi'] }}</h5>

                            <div class="pt-meta">
                                <i class="ri-fingerprint-line"></i>
                                <span>Kode: {{ $pt['kode_pt'] }}</span>
                            </div>

                            <div class="pt-action">
                                <i class="ri-eye-line"></i>
                                <span>Lihat</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="report-empty">
                    <i class="ri-folder-search-line display-5 text-muted opacity-50"></i>
                    <h5 class="text-muted fw-bold mt-3">Data tidak ditemukan</h5>
                    <p class="small text-muted mb-3">Coba kata kunci lain atau reset pencarian.</p>
                    <a href="{!! url('admin/laporan') !!}" class="btn btn-primary px-4">Reset</a>
                </div>
            </div>
        @endif
    </div>

    @if($ptCount > 0)
        <div class="table-footer-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="text-muted small fw-bold">
                Menampilkan <span class="text-primary">{{ $pts->firstItem() ?? 0 }}-{{ $pts->lastItem() ?? 0 }}</span>
                dari total <span class="text-dark">{{ $pts->total() }}</span> perguruan tinggi
            </div>
            <div class="sipencak-pager">
                {{ $pts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

@endsection
