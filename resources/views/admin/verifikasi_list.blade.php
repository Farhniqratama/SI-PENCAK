@extends('layouts.app')
@section('content')

<div class="verifikasi-status-page">
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <a href="/permohonan-pencairan" class="verifikasi-action-card verifikasi-action-card--primary">
                <span class="verifikasi-action-icon">
                    <i class="ri-play-circle-line"></i>
                </span>
                <div class="verifikasi-action-copy">
                    <span class="verifikasi-action-title">Mulai Proses Pencairan</span>
                    <span class="verifikasi-action-subtitle">Periode aktif: {!! $periode['periode'] !!}</span>
                </div>
                <span class="verifikasi-action-cta">Mulai <i class="ri-arrow-right-line"></i></span>
            </a>
        </div>
        @if(session('role') === 'admin')
            <div class="col-md-6">
                <a href="/admin/pencairan/draft" class="verifikasi-action-card verifikasi-action-card--secondary">
                    <span class="verifikasi-action-icon">
                        <i class="ri-draft-line"></i>
                    </span>
                    <div class="verifikasi-action-copy">
                        <span class="verifikasi-action-title">Lihat Semua Draft</span>
                        <span class="verifikasi-action-subtitle">Sistem antrean draft permohonan</span>
                    </div>
                    <span class="verifikasi-action-cta">Buka <i class="ri-arrow-right-line"></i></span>
                </a>
            </div>
        @endif
    </div>

    <div class="verifikasi-toolbar mb-3">
        <div>
            <h3 class="verifikasi-title">Histori Permohonan Pencairan</h3>
            <p class="text-muted small mb-0">Arsip pengajuan dan status verifikasi pencairan dana</p>
        </div>
        <div class="verifikasi-toolbar-actions">
            <a href="{!! url('admin/pencairan/unduh-excel') !!}" class="btn btn-light verifikasi-export-btn">
                <i class="ri-file-excel-2-line text-success"></i> Unduh Semua Data
            </a>
        </div>
    </div>

    <div class="verifikasi-filter-row mb-3">
            <div class="verifikasi-filter-group">
                <button class="btn btn-outline-primary px-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                    <i class="ri-filter-3-line me-1"></i> Filter Lanjutan
                </button>
                <form action="" method="get" class="verifikasi-search-form">
                    <input type="text" name="keyword" class="form-control"
                        placeholder="Cari Periode, Kategori..."
                        value="{{ request('keyword') }}">

                    @if(!empty(request('keyword')) || !empty(request('tahun')) || !empty(request('status')))
                        <a href="{!! url('verifikasi-pembaharuan-status') !!}" class="btn btn-light" title="Bersihkan">
                            <i class="ri-close-circle-line"></i>
                        </a>
                    @endif
                </form>
            </div>
    </div>

    <div class="card mb-4 verifikasi-history-card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless table-hover align-middle mb-0" id="dataTable" data-no-auto-pager="1">
                    <thead>
                        <tr>
                            <th class="ps-3">Tanggal Pengajuan</th>
                        <th>Periode Semester</th>
                        <th>Kategori & Jenis Bantuan</th>
                        <th class="text-end">Nominal Pencairan</th>
                        <th class="text-center">Jumlah Mhs</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($histori->count())
                        @php
                        // Ambil halaman saat ini
                        $currentPage = method_exists($histori, 'currentPage') ? $histori->currentPage() : $pager->getCurrentPage('default');
                        // Karena Anda ingin 10 per halaman:
                        $perPage = method_exists($histori, 'perPage') ? $histori->perPage() : 10;
                        $no = ($currentPage - 1) * $perPage + 1;
                        @endphp
                        @foreach($histori as $item)
                            <tr>
                                <td class="ps-3 fw-semibold">{!! tanggal_indonesia($item['tanggal_entry']) !!}</td>
                                <td>
                                    @php
                                    $semester = $item['semester'] ?? '';
                                    $year = !empty($item['tanggal_entry']) ? date('Y', strtotime($item['tanggal_entry'])) : '';
                                    @endphp
                                    <span class="badge bg-primary bg-gradient text-white border-0 shadow-sm px-2 py-1">{{ $semester }}</span>
                                    @if($year && strpos($semester, $year) === false)
                                        <span class="badge badge-blue-muted px-2 py-1">/ {!! $year !!}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{!! $item['kategori_penerima'] !!}</div>
                                    <div class="text-muted small">{!! $item['jenis_bantuan'] ?? '-' !!}</div>
                                </td>
                                <td class="text-end money-cell"><span class="text-success fw-bold">Rp {!! number_format($item['nominal_pencairan'] ?? 0, 0, ',', '.') !!}</span></td>
                                <td class="fw-bold text-primary text-center">{!! $item['jumlah_mahasiswa'] !!}</td>
                                <td>
                                    @php
                                        $statusText = $item['status'] ?? '-';
                                        $statusLower = strtolower($statusText);
                                        $statusClass = match (true) {
                                            str_contains($statusLower, 'selesai') => 'status-badge--selesai',
                                            str_contains($statusLower, 'ditolak') => 'status-badge--ditolak',
                                            str_contains($statusLower, 'ajukan') || $statusLower === 'diajukan' => 'status-badge--diajukan',
                                            str_contains($statusLower, 'finalisasi') => 'status-badge--finalisasi',
                                            str_contains($statusLower, 'proses'),
                                            str_contains($statusLower, 'diproses') => 'status-badge--proses',
                                            default => 'status-badge--default',
                                        };
                                    @endphp
                                    @if($item['status'] === 'Selesai')
                                        <span class="status-badge {!! $statusClass !!}"
                                            data-bs-toggle="modal" data-bs-target="#modalSelesai{!! $item['id'] !!}">
                                            <i class="ri-checkbox-circle-line"></i> {!! $statusText !!}
                                        </span>
                                    @elseif($item['status'] === 'Ditolak')
                                        <span class="status-badge {!! $statusClass !!}"
                                            data-bs-toggle="modal" data-bs-target="#modalAlasan{!! $item['id'] !!}">
                                            <i class="ri-close-circle-line"></i> {!! $statusText !!}
                                        </span>
                                    @else
                                        <span class="status-badge {!! $statusClass !!}">
                                            <i class="ri-time-line"></i> {!! $statusText !!}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    <a href="/verifikasi-detail/{!! $item['id'] !!}" class="btn btn-sm btn-light rounded-pill px-3" title="Detail">
                                        <i class="ri-eye-line me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Belum ada riwayat permohonan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            </div>

            @if($histori->count())
                <div class="verifikasi-card-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="text-muted small fw-bold">
                        Menampilkan <span class="text-primary">{{ $histori->firstItem() }}</span>-<span class="text-primary">{{ $histori->lastItem() }}</span>
                        dari total <span class="text-primary">{{ $histori->total() }}</span> data
                    </div>
                    <div class="sipencak-pager">
                        {{ $histori->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@foreach($histori as $item)
    @if($item['status'] === 'Selesai')
        <div class="modal fade" id="modalSelesai{!! $item['id'] !!}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content ">
                    <div class="modal-header border-0 p-4 pb-0">
                        <h5 class="fw-bold mb-0 text-success"><i class="ri-checkbox-circle-line me-2"></i> Verifikasi Selesai</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-line"></i></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <div class="mb-4">
                            <i class="ri-send-plane-line fs-1 text-success opacity-25"></i>
                        </div>
                        <p class="text-dark fw-bold mb-1">Hasil verifikasi telah berhasil diajukan.</p>
                        <p class="small text-muted mb-4">Silakan pantau status pencairan di Portal resmi.</p>

                        <a href="https://kip-kuliah.kemdiktisaintek.go.id/sim/monitoring-pencairan"
                            target="_blank" class="btn btn-primary w-100">
                            <i class="ri-external-link-line me-2"></i> Buka Portal SIMKIP
                        </a>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($item['status'] === 'Ditolak')
        <div class="modal fade" id="modalAlasan{!! $item['id'] !!}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content ">
                    <div class="modal-header border-0 p-4 pb-0">
                        <h5 class="fw-bold mb-0 text-danger"><i class="ri-error-warning-line me-2"></i> Alasan Penolakan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="ri-close-line"></i></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="p-3 border rounded-4 bg-light text-dark shadow-sm">
                            {!! nl2br(e($item['alasan_tolak'])) !!}
                        </div>
                        <p class="small text-muted mt-3 mb-0 text-center"><i class="ri-information-line me-1"></i> Silakan perbaiki data sesuai alasan di atas.</p>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        </div>
    @endif
@endforeach

<!-- Offcanvas Filter -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
  <div class="offcanvas-header bg-light border-bottom">
    <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel"><i class="ri-filter-3-line me-2"></i>Filter Lanjutan</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form action="" method="get" id="filterForm">
        <div class="mb-3">
            <label class="form-label fw-bold text-muted text-uppercase fs-12 mb-1">Tahun Angkatan</label>
            <select name="tahun" class="form-select">
                <option value="">Semua Tahun</option>
                @for($i = date('Y'); $i >= 2020; $i--)
                    <option value="{!! $i !!}" {!! (request('tahun') == $i) ? 'selected' : '' !!}>{!! $i !!}</option>
                @endfor
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold text-muted text-uppercase fs-12 mb-1">Status Pencairan</label>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="Ajukan Mahasiswa" {!! request('status') == 'Ajukan Mahasiswa' ? 'selected' : '' !!}>Ajukan Mahasiswa</option>
                <option value="Finalisasi" {!! request('status') == 'Finalisasi' ? 'selected' : '' !!}>Finalisasi</option>
                <option value="Diproses" {!! request('status') == 'Diproses' ? 'selected' : '' !!}>Diproses</option>
                <option value="Selesai" {!! request('status') == 'Selesai' ? 'selected' : '' !!}>Selesai</option>
                <option value="Ditolak" {!! request('status') == 'Ditolak' ? 'selected' : '' !!}>Ditolak</option>
            </select>
        </div>
        <div class="mt-5 pt-3 border-top">
            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold mb-2">Terapkan Filter</button>
            <a href="?" class="btn btn-light border w-100 rounded-pill fw-bold text-dark">Reset Pencarian</a>
        </div>
    </form>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fallback manual close modal jika data-bs-dismiss bermasalah
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modalEl = btn.closest('.modal');
                if (window.jQuery && typeof jQuery.fn.modal !== 'undefined') {
                    $(modalEl).modal('hide');
                } else {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
            });
        });
    });
</script>

@endsection
