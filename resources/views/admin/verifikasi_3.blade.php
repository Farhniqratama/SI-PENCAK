@extends('layouts.app')
@section('content')

<div class="finalisasi-page">
    <div class="card overflow-hidden mb-4 finalisasi-card">
        <div class="summary-box">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <span class="badge bg-primary mb-3 px-3 py-2 rounded-pill shadow-sm">TAHAP FINALISASI</span>
                    <h2 class="fw-bold mb-1">Verifikasi Berhasil Disusun</h2>
                    <p class="text-muted mb-0 small">
                        <i class="ri-bank-line me-1"></i> {{ $pt['kode_pt'] }} &mdash; {{ $pt['perguruan_tinggi'] }}
                    </p>
                </div>
                <div class="col-md-5 text-md-end mt-4 mt-md-0">
                    <p class="text-muted small fw-bold mb-1 text-uppercase">Total Dana Diajukan (Estimasi)</p>
                    <div class="count-display text-success">Rp {!! number_format(($pencairan['nominal_pencairan'] ?? 0) * $jumlah, 0, ',', '.') !!}</div>
                    <p class="text-muted small fw-bold mt-2 mb-1 text-uppercase">Total Mahasiswa Terdaftar</p>
                    <div class="count-display fs-4">{!! number_format($jumlah, 0, ',', '.') !!} Mhs</div>
                </div>
            </div>
        </div>

        <div class="bg-light-subtle p-3 border-bottom d-flex gap-4">
            <div>
                <div class="small text-muted fw-bold text-uppercase">Kategori Bantuan</div>
                <div class="fw-bold text-dark">{!! $pencairan['kategori_penerima'] !!}</div>
            </div>
            <div>
                <div class="small text-muted fw-bold text-uppercase">Jenis Bantuan</div>
                <div class="fw-bold text-dark">{!! $pencairan['jenis_bantuan'] ?? '-' !!}</div>
            </div>
            <div>
                <div class="small text-muted fw-bold text-uppercase">No. SK</div>
                <div class="fw-bold text-dark">{!! $pencairan['no_sk'] ?? '-' !!}</div>
            </div>
        </div>

        <div class="finalisasi-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="ri-list-check-3 me-2 text-primary"></i>Daftar Mahasiswa Diajukan
                </h6>

                <div class="d-flex flex-column align-items-md-end gap-2">
                    <a href="{!! url('export-mahasiswa/' . $id_pencairan) !!}" class="btn btn-success">
                        <i class="ri-file-excel-2-line"></i> Export Excel
                    </a>
                    <form action="" method="get" class="d-flex gap-2">
                        <i class="ri-search-line"></i>
                        <input type="text" name="keyword" value="{{ $keyword ?? '' }}"
                            class="form-control" placeholder="Cari Nama atau NIM...">
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-centered mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Program Studi</th>
                            <th class="text-center">Jenjang</th>
                            <th class="text-center">Angkatan</th>
                            <th>Kategori</th>
                            <th class="text-center pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($mahasiswa))
                            @foreach($mahasiswa as $mhs)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">{{ $mhs['nim'] }}</td>
                                    <td class="fw-bold text-dark">{{ $mhs['nama'] }}</td>
                                    <td>
                                        <div class="fw-medium">{{ $mhs['nama_prodi'] }}</div>
                                        <div class="text-muted small">{{ $mhs['kode_prodi'] }}</div>
                                    </td>
                                    <td class="text-center">{{ $mhs['jenjang'] }}</td>
                                    <td class="text-center">{{ $mhs['angkatan'] }}</td>
                                    <td><span class="text-uppercase small fw-semibold text-muted">{{ $mhs['kategori'] }}</span></td>
                                    <td class="text-center pe-4"><span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">{{ $mhs['pembaruan_status'] }}</span></td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted italic">Data mahasiswa tidak ditemukan.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="table-footer-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="text-muted">
                    Menampilkan <strong>{{ $mahasiswa->firstItem() ?? 0 }}-{{ $mahasiswa->lastItem() ?? 0 }}</strong>
                    dari total <strong>{{ $mahasiswa->total() }}</strong> mahasiswa
                </div>
                <div class="sipencak-pager">
                    {{ $mahasiswa->links('pagination::bootstrap-5') }}
                </div>
            </div>

            <div class="alert alert-warning my-4">
                <div class="d-flex gap-3 align-items-start">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="ri-alert-line"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Konfirmasi Finalisasi Data</h6>
                        <p class="mb-0 small">
                            Periksa kembali data di atas. Klik <strong>Kirim Hasil Verifikasi</strong> untuk menyetujui sejumlah <strong>{!! $jumlah !!}</strong> mahasiswa. Setelah dikirim, data akan dikunci secara otomatis oleh sistem.
                        </p>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 pt-4 border-top">
                <a href="{!! url('verifikasi-mahasiswa/' . $id_pencairan) !!}" class="btn btn-light">
                    <i class="ri-arrow-left-line"></i> Tahap Sebelumnya
                </a>

                <div class="d-flex gap-2 w-100 w-md-auto justify-content-md-end">
                    <a href="{!! url('admin/pencairan/draft') !!}" class="btn btn-light border-warning text-warning">
                        <i class="ri-file-list-3-line"></i> Simpan ke Draft
                    </a>

                    <a href="{!! url('verifikasi-final/' . $id_pencairan) !!}"
                        class="btn btn-primary shadow-sm"
                        onclick="return confirm('Apakah Anda yakin data sudah valid? Tindakan ini tidak dapat dibatalkan.')">
                        Kirim Hasil Verifikasi <i class="ri-send-plane-line ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
