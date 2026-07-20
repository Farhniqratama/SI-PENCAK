@extends('layouts.app')
@section('content')

<div class="container-fluid px-4 py-4 ">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="javascript:history.back()" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold shadow-sm me-2">
                <i class="ri-arrow-left-line me-2"></i> Kembali
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm">
                <i class="ri-printer-line me-2"></i> Cetak Rekomendasi
            </button>
        </div>
        <h4 class="fw-bold mb-0 text-dark">Detail Permohonan</h4>
    </div>

    <div class="card mb-4 pencairan-student-table">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-white"><i class="ri-file-search-line me-2"></i> Detail & Tracking Permohonan</h5>
            @php
                $headerStatusText = $data['status'] ?? 'Proses';
                $headerStatusLower = strtolower($headerStatusText);
                $headerStatusClass = match (true) {
                    str_contains($headerStatusLower, 'selesai') => 'header-status-badge--selesai',
                    str_contains($headerStatusLower, 'ditolak') => 'header-status-badge--ditolak',
                    str_contains($headerStatusLower, 'diajukan') => 'header-status-badge--diajukan',
                    str_contains($headerStatusLower, 'finalisasi') => 'header-status-badge--finalisasi',
                    str_contains($headerStatusLower, 'proses'),
                    str_contains($headerStatusLower, 'diproses') => 'header-status-badge--proses',
                    default => 'header-status-badge--default',
                };
                $headerStatusIcon = match (true) {
                    str_contains($headerStatusLower, 'selesai') => 'ri-checkbox-circle-line',
                    str_contains($headerStatusLower, 'ditolak') => 'ri-close-circle-line',
                    str_contains($headerStatusLower, 'diajukan') => 'ri-send-plane-line',
                    default => 'ri-loader-4-line',
                };
            @endphp
            <span class="header-status-badge {!! $headerStatusClass !!}">
                <i class="{!! $headerStatusIcon !!}"></i> {!! strtoupper(e($headerStatusText)) !!}
            </span>
        </div>
        <div class="card-body p-4">
        <!-- VISUAL TRACKING / TIMELINE -->
        <div class="mb-5 px-3">
            <p class="text-muted text-uppercase fs-12 fw-bold mb-3">Status Perjalanan Pengajuan</p>
            <div class="position-relative d-flex justify-content-between align-items-center">
                <div class="progress position-absolute top-50 start-0 w-100 translate-middle-y" style="height: 4px; z-index: 1;">
                    @php
                        $statusProgress = 25;
                        if(in_array($data['status'], ['Ajukan Mahasiswa'])) $statusProgress = 50;
                        if(in_array($data['status'], ['Finalisasi', 'Diproses'])) $statusProgress = 75;
                        if(in_array($data['status'], ['Selesai', 'Ditolak'])) $statusProgress = 100;
                    @endphp
                    <div class="progress-bar {{ $data['status'] == 'Ditolak' ? 'bg-danger' : 'bg-primary' }}" role="progressbar" style="width: {{ $statusProgress }}%"></div>
                </div>

                <!-- Step 1: Draft -->
                <div class="position-relative z-1 d-flex flex-column align-items-center bg-white px-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white mb-2" style="width: 32px; height: 32px;">
                        <i class="ri-check-line"></i>
                    </div>
                    <span class="fs-12 fw-bold text-primary">Draft Dibuat</span>
                </div>

                <!-- Step 2: Diajukan -->
                <div class="position-relative z-1 d-flex flex-column align-items-center bg-white px-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $statusProgress >= 50 ? 'bg-primary text-white' : 'bg-light text-muted border' }} mb-2" style="width: 32px; height: 32px;">
                        <i class="ri-send-plane-line"></i>
                    </div>
                    <span class="fs-12 fw-bold {{ $statusProgress >= 50 ? 'text-primary' : 'text-muted' }}">Diajukan ke LLDIKTI</span>
                </div>

                <!-- Step 3: Verifikasi -->
                <div class="position-relative z-1 d-flex flex-column align-items-center bg-white px-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center {{ $statusProgress >= 75 ? 'bg-primary text-white' : 'bg-light text-muted border' }} mb-2" style="width: 32px; height: 32px;">
                        <i class="ri-file-search-line"></i>
                    </div>
                    <span class="fs-12 fw-bold {{ $statusProgress >= 75 ? 'text-primary' : 'text-muted' }}">Proses Verifikasi</span>
                </div>

                <!-- Step 4: Selesai -->
                <div class="position-relative z-1 d-flex flex-column align-items-center bg-white px-2">
                    @if($data['status'] === 'Ditolak')
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger text-white mb-2" style="width: 32px; height: 32px;">
                            <i class="ri-close-line"></i>
                        </div>
                        <span class="fs-12 fw-bold text-danger">Ditolak</span>
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $statusProgress >= 100 ? 'bg-success text-white' : 'bg-light text-muted border' }} mb-2" style="width: 32px; height: 32px;">
                            <i class="ri-flag-2-fill"></i>
                        </div>
                        <span class="fs-12 fw-bold {{ $statusProgress >= 100 ? 'text-success' : 'text-muted' }}">Selesai</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-4 detail-meta-grid">
            <div class="col-md-4 mb-3">
                <div class="text-muted small d-block">Kategori Penerima</div>
                <div class="fw-semibold text-truncate">{{ $data['kategori_penerima'] }}</div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-muted small d-block">No. SK / Surat</div>
                <div class="fw-semibold text-primary"><i class="ri-file-edit-line me-1"></i> {!! e($data['no_sk']) ?: '-' !!}</div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-muted small d-block">Tanggal Surat</div>
                <div class="fw-semibold">{!! tanggal_indonesia($data['tanggal']) !!}</div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-muted small d-block">Periode</div>
                <div class="fw-semibold">{{ $data['semester'] }} / {!! !empty($data['tanggal_entry']) ? date('Y', strtotime($data['tanggal_entry'])) : '-' !!}</div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-muted small d-block">Status</div>
                <div class="mt-1">
                    @if($data['status'] === 'Selesai')
                        <div class="badge badge-success" data-bs-toggle="modal" data-bs-target="#modalSelesai">
                            <i class="ri-checkbox-circle-line"></i> Selesai <i class="ri-information-line opacity-50"></i>
                        </div>
                    @elseif($data['status'] === 'Ditolak')
                        <div class="badge badge-danger" data-bs-toggle="modal" data-bs-target="#modalTolak">
                            <i class="ri-close-circle-line"></i> Ditolak <i class="ri-information-line opacity-50"></i>
                        </div>
                    @else
                        <span class="badge bg-light text-dark border fw-bold px-3 py-2 rounded-3">{{ $data['status'] }}</span>
                    @endif
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-muted small d-block">Jenis Bantuan</div>
                <div class="fw-semibold">{{ $data['jenis_bantuan'] ?? '-' }}</div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-muted small d-block">Nominal Pencairan</div>
                <div class="fw-semibold text-success">Rp {{ number_format($data['nominal_pencairan'] ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="text-muted small d-block">Keterangan Tambahan</div>
                <div class="fw-semibold">{{ $data['keterangan'] ?? '-' }}</div>
            </div>
        </div>

        <div class="px-4 pb-4 border-top pt-3 mt-1">
            <div class="text-muted small fw-bold mb-2 text-uppercase">Dokumen Pendukung</div>
            <div class="row g-3">
                @foreach(['surat_pengantar' => 'Surat Pengantar', 'sptjm' => 'SPTJM', 'sk_penetapan' => 'SK Penetapan', 'sk_pembatalan' => 'SK Pembatalan', 'berita_acara' => 'Berita Acara'] as $key => $label)
                    <div class="col-md-3 col-6">
                        @if(!empty($data[$key]))
                            <a href="{!! url('file/' . $data[$key]) !!}" target="_blank" class="btn btn-sm btn-light">
                                <span class="text-truncate">{!! $label !!}</span>
                                <i class="ri-external-link-line small opacity-50 ms-2"></i>
                            </a>
                        @else
                            <div class="btn btn-sm btn-light opacity-50 bg-light">
                                <span>{!! $label !!}</span>
                                <i class="ri-close-line small ms-2"></i>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary bg-gradient text-white border-0 p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <h6 class="fw-bold mb-0 text-uppercase">Daftar Mahasiswa</h6>
                </div>
                <div class="col-md-8 d-flex justify-content-md-end gap-3 flex-wrap">
                    <form action="" method="get" class="d-flex gap-2">
                        <i class="ri-search-line d-none"></i>
                        <input type="text" name="keyword" class="form-control"
                            placeholder="Cari Nama atau NIM..."
                            value="{{ $keyword ?? '' }}">
                        @if(!empty($keyword))
                            <a href="{!! current_url('/') !!}" class="btn btn-sm btn-light">
                                <i class="ri-close-circle-line"></i>
                            </a>
                        @endif
                    </form>

                    @if($data['status'] === 'Selesai')
                        <a href="{!! url('admin/pencairan/unduh-mahasiswa/' . $data['id']) !!}" class="btn btn-success btn-sm rounded-pill px-4 fw-bold shadow-sm">
                            <i class="ri-file-excel-2-line me-2"></i> Unduh Data
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="table-responsive pencairan-student-table__wrap">
            <table class="table table-striped table-centered mb-0" data-no-auto-pager="1">
                <thead>
                    <tr>
                        <th width="60" class="text-center">No</th>
                        <th>NIM</th>
                        <th>Nama Lengkap</th>
                        <th>Program Studi</th>
                        <th class="text-center">Jenjang</th>
                        <th class="text-center">Angkatan</th>
                        <th>Kategori</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($mahasiswa))
                        @php
                        $currentPage = method_exists($dataMahasiswa, 'currentPage') ? $dataMahasiswa->currentPage() : $pager->getCurrentPage();
                        $perPage = method_exists($dataMahasiswa, 'perPage') ? $dataMahasiswa->perPage() : 10;
                        $no = 1 + ($perPage * ($currentPage - 1));
                        foreach ($mahasiswa as $m): @endphp
                            <tr>
                                <td class="text-center text-muted fw-bold">{!! $no++ !!}</td>
                                <td class="fw-bold text-primary">{{ $m['nim'] }}</td>
                                <td class="fw-bold text-dark">{{ $m['nama'] }}</td>
                                <td>
                                    <div class="fw-bold small">{{ $m['nama_prodi'] }}</div>
                                    <div class="text-muted">{{ $m['kode_prodi'] }}</div>
                                </td>
                                <td class="text-center fw-medium">{{ $m['jenjang'] }}</td>
                                <td class="text-center">{{ $m['angkatan'] }}</td>
                                <td><span class="badge bg-light text-dark border-0 py-1 px-2">{{ $m['kategori'] }}</span></td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary border-0 fw-bold">
                                        {{ $m['pembaruan_status'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="ri-user-unfollow-line fs-1 mb-3 opacity-25"></i>
                                <p class="mb-0 fw-bold">Mahasiswa tidak ditemukan.</p>
                                @if(!empty($keyword))
                                    <a href="{!! current_url('/') !!}" class="small text-primary text-decoration-none">Reset Pencarian</a>
                                @endif
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="table-footer-pager pencairan-student-table__pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="text-muted fw-bold">
                Menampilkan <strong>{{ $dataMahasiswa->firstItem() ?? 0 }}-{{ $dataMahasiswa->lastItem() ?? 0 }}</strong> dari total <strong>{{ $dataMahasiswa->total() }}</strong> mahasiswa
            </div>
            <div class="">
                {{ $dataMahasiswa->links('pagination::bootstrap-5') }}
            </div>
        </div>
        </div> <!-- Close card-body -->
    </div> <!-- Close card -->
</div> <!-- Close container -->

@if($data['status'] === 'Selesai')
    <div class="modal fade" id="modalSelesai" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white border-0 py-3 px-4">
                    <h6 class="modal-title fw-bold"><i class="ri-checkbox-circle-line me-2"></i> Konfirmasi Sistem</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-5">
                    <i class="ri-upload-cloud-2-line fs-1 text-success mb-4 opacity-50"></i>
                    <p class="text-muted fw-bold small mb-4">Data telah berhasil diverifikasi dan disinkronkan dengan Portal SIMKIP Pusat.</p>
                    <a href="https://kip-kuliah.kemdiktisaintek.go.id/sim/monitoring-pencairan" target="_blank" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                        Buka Monitoring SIMKIP <i class="ri-external-link-line ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

@if($data['status'] === 'Ditolak' && !empty($data['alasan_tolak']))
    <div class="modal fade" id="modalTolak" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white border-0 py-3 px-4">
                    <h6 class="modal-title fw-bold"><i class="ri-error-warning-line me-2"></i> Alasan Penolakan</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="bg-light p-4 rounded-4 border-start border-4 border-danger">
                        <p class="mb-0 text-dark fw-bold small">
                            {!! nl2br(e($data['alasan_tolak'])) !!}
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 justify-content-center">
                    <button type="button" class="btn btn-dark rounded-pill px-5 fw-bold btn-sm shadow-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
