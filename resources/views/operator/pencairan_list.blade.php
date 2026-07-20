@extends('layouts.app')

@section('content')

<div class="row mt-4 mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Permohonan Pencairan</h4>
            <p class="text-muted small">Histori pengajuan dan verifikasi status wilayah</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm bg-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas">
                <i class="ri-filter-3-line me-1"></i> Filter Lanjutan
            </button>
            <a href="{!! url('operator/pencairan/unduh-excel') !!}" class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm">
                <i class="ri-file-excel-2-line me-1"></i> Unduh Excel
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body p-4">



                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0">
                        <thead>
                            <tr>
                                <th width="100" class="ps-3">Kode PT</th>
                                <th>Perguruan Tinggi</th>
                                <th>Periode</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Kategori & Jenis Bantuan</th>
                                <th class="text-end">Nominal Pencairan</th>
                                <th class="text-center">Mhs</th>
                                <th>Status</th>
                                <th width="140" class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($histori))
                                @foreach($histori as $item)
                                    <tr>
                                        <td class="ps-3 fw-semibold text-dark">{{ $item['kode_pt'] }}</td>
                                        <td>
                                            <div class="fw-bold text-primary">{{ $item['perguruan_tinggi'] }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-gradient text-white border-0 shadow-sm px-2 py-1">{{ $item['semester'] }} / {!! date('Y', strtotime($item['tanggal_entry'])) !!}</span>
                                        </td>
                                        <td class="text-muted small"><i class="ri-calendar-event-line me-1"></i>{!! date('d M Y', strtotime($item['tanggal_entry'])) !!}</td>
                                        <td>
                                            <div class="small fw-bold text-dark">{{ $item['kategori_penerima'] }}</div>
                                            <div class="small text-muted">{{ $item['jenis_bantuan'] ?? '-' }}</div>
                                        </td>
                                        <td class="text-end money-cell"><span class="text-success fw-bold">Rp {{ number_format($item['nominal_pencairan'] ?? 0, 0, ',', '.') }}</span></td>
                                        <td class="fw-bold text-center text-primary">{{ $item['jumlah_mahasiswa'] }}</td>
                                        <td>
                                            @php
                                                $statusText = $item['status'] ?? '-';
                                                $statusLower = strtolower($statusText);
                                                $statusClass = match (true) {
                                                    str_contains($statusLower, 'selesai') => 'status-badge--selesai',
                                                    str_contains($statusLower, 'ditolak') => 'status-badge--ditolak',
                                                    $statusLower === 'diajukan' => 'status-badge--diajukan',
                                                    str_contains($statusLower, 'finalisasi') => 'status-badge--finalisasi',
                                                    str_contains($statusLower, 'proses'),
                                                    str_contains($statusLower, 'diproses') => 'status-badge--proses',
                                                    default => 'status-badge--default',
                                                };
                                            @endphp
                                            @if($item['status'] === 'Selesai')
                                                <span class="status-badge {!! $statusClass !!}" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalSelesai{!! $item['id'] !!}">
                                                    <i class="ri-checkbox-circle-line"></i> {{ $statusText }}
                                                </span>
                                            @elseif($item['status'] === 'Ditolak')
                                                <span class="status-badge {!! $statusClass !!}" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalAlasan{!! $item['id'] !!}">
                                                    <i class="ri-close-circle-line"></i> {{ $statusText }}
                                                </span>
                                            @else
                                                <span class="status-badge {!! $statusClass !!}">
                                                    <i class="ri-time-line"></i> {{ $statusText }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center pe-3">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="/pencairan-detail/{!! $item['id'] !!}" class="btn btn-sm btn-light rounded-pill" title="Detail Pengajuan"><i class="ri-eye-line text-primary"></i></a>
                                                @if($item['status'] === 'Diproses' && session('role') === 'operator')
                                                    <form action="{!! url('pencairan/selesai/' . $item['id']) !!}" method="post" class="d-inline" onsubmit="return confirm('Selesaikan pengajuan?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-light rounded-pill" title="Selesai"><i class="ri-check-line text-success"></i></button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-light rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTolak{!! $item['id'] !!}" title="Tolak"><i class="ri-close-line text-danger"></i></button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">Tidak ada data ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if(!empty($histori))
                    <div class="table-footer-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="text-muted fs-14">
                            Menampilkan <span class="fw-semibold">{{ $histori->firstItem() ?? 0 }}-{{ $histori->lastItem() ?? 0 }}</span>
                            dari total <span class="fw-semibold">{{ $histori->total() }}</span> data pengajuan
                        </div>
                        <div class="sipencak-pager">
                            {{ $histori->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(!empty($histori))
    @foreach($histori as $item)
    <div class="modal fade" id="modalAlasan{!! $item['id'] !!}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning-subtle text-warning border-0">
                    <h5 class="modal-title mb-0"><i class="ri-error-warning-line me-1"></i> Alasan Penolakan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="bg-light p-3 rounded border">
                        <p class="mb-0 text-dark">{!! nl2br(e($item['alasan_tolak'])) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSelesai{!! $item['id'] !!}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success-subtle text-success border-0">
                    <h5 class="modal-title mb-0"><i class="ri-checkbox-circle-line me-1"></i> Pengajuan Selesai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="text-muted mb-4">Verifikasi telah diajukan ke kementerian. Silakan cek portal monitoring SIMKIP:</p>
                    <a href="https://kip-kuliah.kemdiktisaintek.go.id/sim/monitoring-pencairan" target="_blank" class="btn btn-primary">
                        <i class="ri-external-link-line me-1"></i> Buka SIMKIP Portal
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTolak{!! $item['id'] !!}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{!! url('pencairan/ditolak/' . $item['id']) !!}" method="post">
                    @csrf
                    <div class="modal-header bg-light">
                        <h5 class="modal-title mb-0">Input Alasan Tolak</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Berikan alasan penolakan untuk dikirim ke PT:</label>
                            <textarea name="alasan" class="form-control" rows="4" required placeholder="Masukkan alasan yang spesifik..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endif

<!-- Offcanvas Filter -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas" aria-labelledby="filterOffcanvasLabel">
  <div class="offcanvas-header bg-light border-bottom">
    <h5 class="offcanvas-title fw-bold" id="filterOffcanvasLabel"><i class="ri-filter-3-line me-2"></i>Filter Lanjutan</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <form action="" method="get" id="filterForm">
        <div class="mb-3">
            <label class="form-label fw-bold text-muted text-uppercase fs-12 mb-1">Pencarian Data</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                <input type="text" name="search" class="form-control" placeholder="No. SK atau Nama PT..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold text-muted text-uppercase fs-12 mb-1">Institusi Perguruan Tinggi</label>
            <select name="pt" class="form-select">
                <option value="">Semua Perguruan Tinggi</option>
                @foreach($daftar_pt as $ptItem)
                    <option value="{!! $ptItem['id'] !!}" {!! (request('pt') == $ptItem['id']) ? 'selected' : '' !!}>
                        {{ $ptItem['perguruan_tinggi'] }}
                    </option>
                @endforeach
            </select>
        </div>
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

@endsection
