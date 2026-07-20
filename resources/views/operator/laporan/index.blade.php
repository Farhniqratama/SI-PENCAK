@extends('layouts.app')

@section('content')
@php
    $laporanItems = $pencairans ?? collect();
    $laporanCount = method_exists($laporanItems, 'count') ? $laporanItems->count() : count($laporanItems);
    $totalMahasiswa = 0;
    $totalNominal = 0;
    $selesaiCount = 0;

    foreach ($laporanItems as $row) {
        $totalMahasiswa += (int) ($row['jumlah_mahasiswa'] ?? 0);
        $totalNominal += (float) ($row['nominal_pencairan'] ?? 0);

        if (strtolower($row['status'] ?? '') === 'selesai') {
            $selesaiCount++;
        }
    }
@endphp



<div class="container-fluid report-page">
    <div class="report-hero mb-3">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
            <div>
                <a href="{!! url('laporan') !!}" class="btn btn-light border mb-3">
                    <i class="ri-arrow-left-line me-1"></i> Kembali
                </a>
                <h4 class="report-title">Monitoring Laporan Pencairan</h4>
                <p class="text-muted small mb-0">Wilayah III | Rekapitulasi data tahun akademik {{ $tahun_terpilih }}</p>
            </div>
            <div class="d-flex flex-wrap align-items-end gap-3">
                <div class="report-stat">
                    <span class="report-stat-label">Laporan</span>
                    <span class="report-stat-value">{!! number_format($laporanCount, 0, ',', '.') !!}</span>
                </div>
                <div class="report-stat">
                    <span class="report-stat-label">Mahasiswa</span>
                    <span class="report-stat-value">{!! number_format($totalMahasiswa, 0, ',', '.') !!}</span>
                </div>
                <div class="report-stat">
                    <span class="report-stat-label">Selesai</span>
                    <span class="report-stat-value text-success">{!! number_format($selesaiCount, 0, ',', '.') !!}</span>
                </div>
            </div>
        </div>
    </div>

    <form method="get" id="filterForm" class="report-filter report-search-card mb-4">
        <div class="row g-3 align-items-end report-filter-grid">
            <div class="col-12 col-lg-7">
                <label class="form-label small text-muted fw-bold text-uppercase">Cari Laporan</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari nama perguruan tinggi atau kode PT..."
                        value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label small text-muted fw-bold text-uppercase">Periode Tahun</label>
                <select name="tahun" id="tahun" class="form-select">
                    @php $tahun_sekarang = date('Y'); @endphp
                    @for($i = $tahun_sekarang; $i >= $tahun_sekarang - 10; $i--)
                        <option value="{!! $i !!}" {!! ($tahun_terpilih == $i) ? 'selected' : '' !!}>{!! $i !!}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-2 d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-filter-line me-1"></i> Terapkan
                </button>
            </div>
        </div>
    </form>

    <div class="row g-4 report-card-grid">
        @if($laporanCount > 0)
            @foreach($pencairans as $p)
                @php
                    $statusText = $p['status'] ?? '-';
                    $statusLower = strtolower($statusText);
                    $statusClass = match (true) {
                        str_contains($statusLower, 'selesai') => 'status-badge--selesai',
                        str_contains($statusLower, 'ditolak') => 'status-badge--ditolak',
                        str_contains($statusLower, 'diajukan') => 'status-badge--diajukan',
                        str_contains($statusLower, 'finalisasi') => 'status-badge--finalisasi',
                        str_contains($statusLower, 'proses'),
                        str_contains($statusLower, 'diproses') => 'status-badge--proses',
                        default => 'status-badge--default',
                    };
                @endphp
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="report-card">
                        <div class="report-card-head">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <span class="report-code">{{ $p['kode_pt'] }}</span>
                                    <h5 class="report-pt-title">{{ $p['perguruan_tinggi'] }}</h5>
                                </div>
                                <span class="status-badge {!! $statusClass !!}">
                                    <i class="{!! str_contains($statusLower, 'selesai') ? 'ri-checkbox-circle-line' : 'ri-time-line' !!}"></i>
                                    {{ $statusText }}
                                </span>
                            </div>
                        </div>
                        <div class="report-card-body">
                            <div class="report-grid mb-3">
                                <div class="report-field">
                                    <span class="report-field-label">Semester</span>
                                    <span class="report-field-value">{{ $p['semester'] ?? '-' }}</span>
                                </div>
                                <div class="report-field">
                                    <span class="report-field-label">Mahasiswa</span>
                                    <span class="report-field-value">{{ number_format($p['jumlah_mahasiswa'] ?? 0, 0, ',', '.') }} Mhs</span>
                                </div>
                                <div class="report-field">
                                    <span class="report-field-label">Kategori</span>
                                    <span class="report-field-value">{{ $p['kategori_penerima'] ?? '-' }}</span>
                                </div>
                                <div class="report-field">
                                    <span class="report-field-label">Nominal</span>
                                    <span class="report-field-value text-success">Rp {{ number_format($p['nominal_pencairan'] ?? 0, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <a href="{!! url('laporan-detail/' . $p['id']) !!}" class="report-detail-btn">
                                <span>Lihat Rincian Laporan</span>
                                <i class="ri-arrow-right-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="report-empty">
                    <i class="ri-folder-open-line display-5 text-muted opacity-50"></i>
                    <h5 class="fw-bold mt-3">Data Tidak Ditemukan</h5>
                    <p class="text-muted mb-0">Tidak ada laporan pencairan untuk kriteria tersebut.</p>
                </div>
            </div>
        @endif
    </div>

    @if($laporanCount > 0)
        <div class="report-pagination table-footer-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="text-muted small fw-bold">
                Menampilkan <span class="text-primary">{{ $pencairans->firstItem() ?? 0 }}-{{ $pencairans->lastItem() ?? 0 }}</span>
                dari total <span class="text-dark">{{ $pencairans->total() }}</span> laporan hasil filter
            </div>
            <div class="sipencak-pager">
                {{ $pencairans->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

@endsection
