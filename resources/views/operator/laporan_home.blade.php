@extends('layouts.app')
@section('content')

@php
    $ptItems = $pts ?? collect();
    $ptCount = method_exists($ptItems, 'count') ? $ptItems->count() : count($ptItems);
    $aktifCount = 0;

    foreach ($ptItems as $pt) {
        if (($pt['status'] ?? '') === 'aktif') {
            $aktifCount++;
        }
    }
@endphp



<div class="container-fluid report-home">
    <div class="report-panel mb-3">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <h4 class="report-title">{{ $title }}</h4>
                <p class="text-muted small mb-0">Manajemen laporan pencairan berdasarkan perguruan tinggi</p>
            </div>
            <div class="d-flex flex-wrap align-items-end gap-3">
                <div class="report-stat">
                    <span class="report-stat-label">Entitas</span>
                    <span class="report-stat-value">{!! number_format($ptCount, 0, ',', '.') !!}</span>
                </div>
                <div class="report-stat">
                    <span class="report-stat-label">Aktif</span>
                    <span class="report-stat-value text-success">{!! number_format($aktifCount, 0, ',', '.') !!}</span>
                </div>
                <a href="{!! url('Operator/pencairan/unduh-laporan') !!}" class="btn btn-success">
                    <i class="ri-file-excel-2-line me-1"></i> Unduh Laporan
                </a>
            </div>
        </div>
    </div>

    <form action="" method="get" class="report-filter report-search-card mb-4">
        <div class="row g-3 align-items-end report-filter-grid">
            <div class="col-12 col-lg-9 col-xl-10">
                <label class="form-label small text-muted fw-bold text-uppercase">Cari Perguruan Tinggi</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari kode atau nama PT..." value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-12 col-lg-3 col-xl-2 d-grid">
                <button class="btn btn-primary" type="submit">
                    <i class="ri-search-line me-1"></i> Cari
                </button>
            </div>
        </div>
    </form>

    <div class="report-table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="140" class="ps-3">Kode PT</th>
                        <th>Nama Perguruan Tinggi</th>
                        <th width="160">Status</th>
                        <th width="160" class="text-center pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($ptCount > 0)
                        @foreach($pts as $pt)
                            <tr>
                                <td class="ps-3 fw-bold text-primary">{{ $pt['kode_pt'] }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $pt['perguruan_tinggi'] }}</div>
                                    <div class="small text-muted">Arsip laporan pencairan perguruan tinggi</div>
                                </td>
                                <td>
                                    @if(isset($pt['status']) && $pt['status'] == 'aktif')
                                        <span class="status-badge status-badge--selesai">Aktif</span>
                                    @else
                                        <span class="status-badge status-badge--default">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    <a href="{!! url('laporan-list/' . $pt['id']) !!}" class="report-action" title="Lihat Laporan">
                                        <i class="ri-eye-line"></i>
                                        <span>Lihat</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Data perguruan tinggi tidak ditemukan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @if($ptCount > 0)
        <div class="table-footer-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="text-muted small fw-bold">
                Menampilkan <span class="text-primary">{{ $pts->firstItem() ?? 0 }}-{{ $pts->lastItem() ?? 0 }}</span>
                dari total <span class="text-dark">{{ $pts->total() }}</span> entitas
            </div>
            <div class="sipencak-pager">
                {{ $pts->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

@endsection
