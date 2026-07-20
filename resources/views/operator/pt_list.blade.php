@extends('layouts.app')

@section('content')

<div class="row mt-4 mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Data Perguruan Tinggi</h4>
            <p class="text-muted small mb-0">Wilayah III - Manajemen Data Terpusat</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="ri-file-excel-2-line me-1"></i> Import Excel
            </button>
            <a href="pt-create" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                <i class="ri-add-line me-1"></i> Tambah PT
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body p-4">

                <div class="row mb-3">
                    <div class="col-md-4 ms-auto">
                        <form action="" method="get">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Cari Kode atau Nama PT..." value="{{ $search ?? '' }}">
                                <button class="btn btn-primary" type="submit"><i class="ri-search-line"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0">
                        <thead>
                            <tr>
                                <th width="180" class="ps-3">Kode PT</th>
                                <th>Nama Perguruan Tinggi</th>
                                <th width="120">Status AIPT</th>
                                <th width="120" class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($data))
                                @foreach($data as $row)
                                    <tr>
                                        <td class="ps-3 fw-bold text-primary">{{ $row['kode_pt'] }}</td>
                                        <td class="fw-semibold">{{ $row['perguruan_tinggi'] }}</td>
                                        <td><span class="badge bg-light text-dark border px-2 py-1">{{ $row['aipt'] }}</span></td>
                                        <td class="text-center pe-3">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="pt-edit/{!! $row['id'] !!}" class="btn btn-sm btn-light rounded-pill" title="Edit"><i class="ri-edit-2-line text-warning"></i></a>
                                                <a href="pt-delete/{!! $row['id'] !!}" class="btn btn-sm btn-light rounded-pill" onclick="return confirm('Hapus data ini?')" title="Hapus"><i class="ri-delete-bin-line text-danger"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if(!empty($data))
                    <div class="table-footer-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="text-muted fs-14">
                            Menampilkan <span class="fw-semibold">{{ $data->firstItem() ?? 0 }}-{{ $data->lastItem() ?? 0 }}</span>
                            dari total <span class="fw-semibold">{{ $data->total() }}</span> perguruan tinggi
                        </div>
                        <div class="sipencak-pager">
                            {{ $data->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{!! url('pt-upload') !!}" method="post" enctype="multipart/form-data">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="uploadModalLabel">Import Database</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center border border-dashed rounded p-4 mb-3 position-relative">
                        <input type="file" name="excel_file" id="excelInput" class="position-absolute top-0 start-0 w-100 h-100" accept=".xlsx,.xls,.csv" required>
                        <i class="ri-upload-cloud-2-line text-primary"></i>
                        <h5 class="mt-2" id="fileLabel">Pilih File Excel</h5>
                        <p class="text-muted mb-0">Klik area ini atau tarik file .xlsx, .xls, atau .csv Anda</p>
                    </div>
                    
                    <div class="alert alert-info bg-info-subtle text-info border-0 mb-0" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="ri-information-line fs-3 me-2"></i>
                            <div>
                                <h5 class="alert-heading fs-14 mb-1">Format: kode_pt, perguruan_tinggi, aipt</h5>
                                <a href="{!! url('assets/template/perguruantinggi.xlsx') !!}" class="text-info text-decoration-underline fw-bold">Unduh Template.xlsx</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-primary">Import Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('excelInput');
        const label = document.getElementById('fileLabel');
        input.addEventListener('change', function() {
            if (this.files.length > 0) {
                label.innerHTML = `<span class="text-success">${this.files[0].name}</span>`;
            }
        });
    });
</script>

@endsection
