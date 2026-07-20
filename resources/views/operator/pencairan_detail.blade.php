@extends('layouts.app')

@section('content')

<div class="row mt-4 mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Rincian Laporan Pencairan</h4>
            <p class="text-muted small mb-0">Detail pengajuan dan daftar mahasiswa</p>
        </div>
        <div class="d-flex gap-2">
            @if(strtolower($data['status'] ?? '') === 'diproses')
                <form action="{{ url('pencairan/selesai/' . $data['id']) }}" method="post" onsubmit="return confirm('Terima dan selesaikan pengajuan ini?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                        <i class="ri-checkbox-circle-line me-1"></i> Terima
                    </button>
                </form>
                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTolakDetail">
                    <i class="ri-close-circle-line me-1"></i> Tolak
                </button>
            @endif
            <button onclick="window.print()" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm border">
                <i class="ri-printer-line me-1"></i> Cetak Bukti
            </button>
            <a href="javascript:history.back()" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm border">
                <i class="ri-arrow-left-line me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-4 pencairan-student-table">
            <div class="card-header bg-primary bg-gradient text-white border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0">Informasi Pengajuan</h5>
                    <p class="text-white-50 fs-13 mb-0 mt-1">Reference ID: <span class="fw-bold text-white">#{{ $data['id'] }}</span></p>
                </div>
                <div>
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

                <div class="row g-4 bg-light-subtle p-3 rounded border">
                    <div class="col-md-4 col-sm-6">
                        <p class="text-muted text-uppercase fs-12 fw-bold mb-1">Institusi</p>
                        <h5 class="text-primary fs-15 mt-0 mb-0">{{ $data['kode_pt'] }} - {{ $data['perguruan_tinggi'] }}</h5>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <p class="text-muted text-uppercase fs-12 fw-bold mb-1">Kategori Bantuan</p>
                        <h5 class="fs-15 mt-0 mb-0">{{ $data['kategori_penerima'] }}</h5>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <p class="text-muted text-uppercase fs-12 fw-bold mb-1">Nomor Dokumen</p>
                        <h5 class="fs-15 mt-0 mb-0">{{ $data['no_sk'] ?? '-' }}</h5>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <p class="text-muted text-uppercase fs-12 fw-bold mb-1">Tanggal Pengajuan</p>
                        <h5 class="fs-15 mt-0 mb-0">{!! tanggal_indonesia($data['tanggal']) !!}</h5>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <p class="text-muted text-uppercase fs-12 fw-bold mb-1">Periode Akademik</p>
                        <h5 class="fs-15 mt-0 mb-0">{{ $data['semester'] }} / {!! date('Y', strtotime($data['tanggal_entry'])) !!}</h5>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <p class="text-muted text-uppercase fs-12 fw-bold mb-1">Jenis Bantuan</p>
                        <h5 class="fs-15 mt-0 mb-0">{{ $data['jenis_bantuan'] ?? '-' }}</h5>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <p class="text-muted text-uppercase fs-12 fw-bold mb-1">Nominal Pencairan</p>
                        <h5 class="text-success fs-15 mt-0 mb-0">Rp {{ number_format($data['nominal_pencairan'] ?? 0, 0, ',', '.') }}</h5>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <p class="text-muted text-uppercase fs-12 fw-bold mb-1">Keterangan Tambahan</p>
                        <h5 class="fs-15 mt-0 mb-0">{{ $data['keterangan'] ?? '-' }}</h5>
                    </div>
                    <div class="col-md-12">
                        <p class="text-muted text-uppercase fs-12 fw-bold mb-1">Lampiran Berkas</p>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            @foreach(['surat_pengantar' => 'Surat Pengantar', 'sptjm' => 'SPTJM', 'sk_penetapan' => 'SK Penetapan', 'sk_pembatalan' => 'Pembatalan', 'berita_acara' => 'Berita Acara'] as $key => $label)
                                @if(!empty($data[$key]))
                                    <a href="{!! url('file/' . $data[$key]) !!}" target="_blank" class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-12 text-decoration-none hover-bg-primary">
                                        <i class="ri-file-pdf-line"></i> {!! $label !!}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Daftar Mahasiswa Penerima</h5>
                    <div class="d-flex gap-2">
                        <form action="" method="get">
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Cari Nama atau NIM..." value="{{ $search ?? '' }}">
                                <button class="btn btn-primary" type="submit"><i class="ri-search-line"></i></button>
                            </div>
                        </form>
                        <a href="{!! url('operator/pencairan/unduh-mahasiswa/' . $data['id']) !!}" class="btn btn-sm btn-success text-nowrap rounded-pill px-3 shadow-sm">
                            <i class="ri-file-excel-2-line me-1"></i> Export Excel
                        </a>
                    </div>
                </div>

                <div class="table-responsive pencairan-student-table__wrap">
                    <table class="table table-striped table-centered mb-0" data-no-auto-pager="1">
                        <thead>
                            <tr>
                                <th width="60" class="text-center">No</th>
                                <th width="150">NIM</th>
                                <th>Nama Lengkap</th>
                                <th>Program Studi</th>
                                <th class="text-center">Angkatan</th>
                                <th>Status Perubahan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($mahasiswa))
                                @php
                                $currentPage = method_exists($dataMahasiswa, 'currentPage') ? $dataMahasiswa->currentPage() : $pager->getCurrentPage('default');
                                $perPage = method_exists($dataMahasiswa, 'perPage') ? $dataMahasiswa->perPage() : 10;
                                $no = 1 + ($perPage * ($currentPage - 1));
                                foreach ($mahasiswa as $mhs):
                                @endphp
                                    <tr>
                                        <td class="text-center text-muted">{!! $no++ !!}</td>
                                        <td class="text-primary fw-semibold">{{ $mhs['nim'] }}</td>
                                        <td>{{ $mhs['nama'] }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $mhs['prodi'] ?? '-' }}</div>
                                            <div class="text-muted fs-12">{{ $mhs['jenjang'] ?? 'S1' }}</div>
                                        </td>
                                        <td class="text-center fw-semibold">{{ $mhs['angkatan'] }}</td>
                                        <td>
                                            <span class="text-primary">{{ $mhs['pembaruan_status'] ?? '-' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Data mahasiswa tidak ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if(!empty($mahasiswa))
                    <div class="table-footer-pager pencairan-student-table__pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="text-muted fs-14">
                            Menampilkan <span class="fw-semibold">{{ $dataMahasiswa->firstItem() ?? 0 }}-{{ $dataMahasiswa->lastItem() ?? 0 }}</span> dari total <span class="fw-semibold">{{ $dataMahasiswa->total() }}</span> mahasiswa
                        </div>
                        <div>
                            {{ $dataMahasiswa->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(strtolower($data['status'] ?? '') === 'diproses')
    <div class="modal fade" id="modalTolakDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ url('pencairan/ditolak/' . $data['id']) }}" method="post" class="modal-content border-0 shadow-lg">
                @csrf
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title fw-bold"><i class="ri-close-circle-line me-2"></i> Tolak Pengajuan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-bold">Alasan Penolakan</label>
                    <textarea name="alasan" class="form-control" rows="4" placeholder="Tuliskan alasan agar user PT dapat memperbaiki pengajuan..." required></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-send-plane-line me-1"></i> Kirim Penolakan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

@endsection
