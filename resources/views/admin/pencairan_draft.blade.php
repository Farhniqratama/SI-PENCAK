@extends('layouts.app')
@section('content')

<div class="container-fluid px-4 py-5 ">

    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h3 class="fw-bold mb-1">Draft Permohonan</h3>
            <p class="text-muted small mb-0">Kelola antrean permohonan yang belum diajukan ke pusat</p>
        </div>
        <a href="{!! url('verifikasi-pembaharuan-status') !!}" class="btn btn-outline-dark btn-sm fw-bold rounded-pill px-3">
            <i class="ri-arrow-left-line me-2"></i> KEMBALI
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary bg-gradient text-white border-0 p-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-white uppercase"><i class="ri-stack-line me-2"></i> Antrean Draft Aktif</h6>
            <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm small fw-bold">
                Total Keseluruhan: {!! $pager->getTotal() !!} Data
            </span>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-borderless table-hover align-middle mb-0" id="dataTable" data-no-auto-pager="1">
                    <thead>
                        <tr>
                            <th class="ps-3">Tanggal Entri</th>
                            <th>Periode Semester</th>
                            <th>Kategori & Jenis Bantuan</th>
                            <th>Nominal Pencairan</th>
                            <th>Jumlah Mhs</th>
                            <th>Status Draft</th>
                            <th class="text-center pe-3">Opsi Kelola</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($draft->count())
                            @foreach($draft as $item)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{!! tanggal_indonesia($item['tanggal_entry']) !!}</td>
                                    <td>
                                        @php
                                        $semester = $item['semester'] ?? '';
                                        $year = !empty($item['tanggal_entry']) ? date('Y', strtotime($item['tanggal_entry'])) : '';
                                        @endphp
                                        <span class="badge bg-primary bg-gradient text-white border-0 shadow-sm px-2 py-1">{{ $semester }}</span>
                                        @if($year && strpos($semester, $year) === false)
                                            <span class="badge bg-primary-subtle text-primary border-0 shadow-sm px-2 py-1">/ {!! $year !!}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-slate">{{ $item['kategori_penerima'] }}</div>
                                        <div class="small text-muted">{{ $item['jenis_bantuan'] ?? '-' }}</div>
                                    </td>
                                    <td><span class="text-success fw-bold">Rp {{ number_format($item['nominal_pencairan'] ?? 0, 0, ',', '.') }}</span></td>
                                    <td class="fw-bold fs-6">
                                        {!! number_format($item['jumlah_mahasiswa'] ?? 0) !!} <small class="text-muted">Mhs</small>
                                    </td>
                                    <td>
                                        @php $statusClass = strtolower(str_replace(' ', '-', $item['status'])); @endphp
                                        <div class="badge-status badge-{!! $statusClass !!}">
                                            <i class="{!! $item['status'] === 'Ditolak' ? 'ri-close-circle-line' : 'ri-time-line' !!} small"></i>
                                            {{ $item['status'] }}
                                            @if($item['status'] === 'Ditolak' && !empty($item['alasan_tolak']))
                                                <button class="btn-icon-info" data-bs-toggle="modal" data-bs-target="#modalAlasan{!! $item['id'] !!}">
                                                    <i class="ri-information-line ms-1"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center pe-3">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <a href="{!! ($item['status'] === 'Ajukan Mahasiswa') ? "/verifikasi-mahasiswa/{$item['id']}" : "/finalisasi-verifikasi/{$item['id']}" !!}" class="btn btn-success rounded-pill px-3 shadow-sm btn-sm fw-bold">
                                                LANJUTKAN <i class="ri-arrow-right-line"></i>
                                            </a>
                                            <a href="/verifikasi-edit/{!! $item['id'] !!}" class="btn btn-sm btn-warning" title="Edit"><i class="ri-edit-2-line"></i></a>
                                            <a href="/verifikasi-delete/{!! $item['id'] !!}" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus draft ini?')"><i class="ri-delete-bin-line"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="ri-inbox-line fs-1 text-light mb-3"></i>
                                        <p class="text-muted fw-bold">Antrean draft kosong.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        @if($draft->count())
            <div class="draft-card-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="text-muted fw-bold">
                    @php
                    $currentPage = $pager->getCurrentPage('default');
                    $perPage = 10;
                    $totalData = $pager->getTotal('default');
                    $from = ($currentPage - 1) * $perPage + 1;
                    $to = min($from + count($draft) - 1, $totalData);
                    @endphp
                    Menampilkan <span class="text-primary">{!! $from !!}-{!! $to !!}</span> dari total <span class="text-dark">{!! $totalData !!}</span> antrean
                </div>
                <div class="sipencak-pager">
                    {{ $draft->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

@foreach($draft as $item)
    @if($item['status'] === 'Ditolak' && !empty($item['alasan_tolak']))
        <div class="modal fade" id="modalAlasan{!! $item['id'] !!}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 p-4 pb-0 d-flex justify-content-between">
                        <h5 class="fw-bold mb-0 text-danger"><i class="ri-error-warning-line me-2"></i> Feedback Penolakan</h5>
                        <button type="button" class="btn bg-light border-0 rounded-circle" data-bs-dismiss="modal"><i class="ri-close-line"></i></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="p-3 border-start border-warning border-4 rounded-3 bg-light text-dark shadow-sm fw-bold small">
                            {!! nl2br(e($item['alasan_tolak'])) !!}
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-dark w-100 rounded-pill fw-bold" data-bs-dismiss="modal">TUTUP</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // DataTables hanya digunakan untuk Searching & Sorting, Pagination pakai Pager CI4
        $('#dataTable').DataTable({
            "paging": false,
            "info": false,
            "language": {
                "search": "Cari Draft:",
                "emptyTable": "Tidak ada antrean draft saat ini"
            }
        });
    });
</script>
@endsection
