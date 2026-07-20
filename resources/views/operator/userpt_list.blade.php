@extends('layouts.app')

@section('content')

<div class="row mt-4 mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Manajemen User</h4>
            <p class="text-muted small mb-0">Kelola data akses dan akun perguruan tinggi</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="ri-file-excel-2-line me-1"></i> Upload Excel
            </button>
            <a href="userpt-create" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                <i class="ri-add-line me-1"></i> Tambah User
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
                                <input type="text" name="search" class="form-control" placeholder="Cari username, nama, atau PT..." value="{{ $search ?? '' }}">
                                <button class="btn btn-primary" type="submit"><i class="ri-search-line"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0">
                        <thead>
                            <tr>
                                <th width="60" class="ps-3">No</th>
                                <th>Username</th>
                                <th>Perguruan Tinggi</th>
                                <th>Penanggung Jawab</th>
                                <th width="120" class="text-center pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($data))
                                @php
                                $currentPage = $pager->getCurrentPage('default');
                        $perPage = method_exists($data, 'perPage') ? $data->perPage() : 10;
                                $no = ($currentPage - 1) * $perPage + 1;
                                foreach ($data as $row): @endphp
                                    <tr>
                                        <td class="ps-3 text-muted">{!! $no++ !!}</td>
                                        <td class="fw-bold text-primary">{{ $row['username'] }}</td>
                                        <td class="fw-semibold">{{ $row['perguruan_tinggi'] }}</td>
                                        <td class="text-muted small">{{ $row['penanggung_jawab'] }}</td>
                                        <td class="text-center pe-3">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="userpt-edit/{!! $row['id'] !!}" class="btn btn-sm btn-light rounded-pill" title="Edit User">
                                                    <i class="ri-edit-2-line text-warning"></i>
                                                </a>
                                                <a href="userpt-delete/{!! $row['id'] !!}" class="btn btn-sm btn-light rounded-pill" title="Hapus User" onclick="return confirm('Hapus user ini?')">
                                                    <i class="ri-delete-bin-line text-danger"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if(!empty($data))
                    <div class="table-footer-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="text-muted fs-14">
                            Menampilkan <span class="fw-semibold">{{ $data->firstItem() ?? 0 }}-{{ $data->lastItem() ?? 0 }}</span>
                            dari total <span class="fw-semibold">{{ $data->total() }}</span> data user
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
            <form action="{!! url('userpt-import') !!}" method="post" enctype="multipart/form-data">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="uploadModalLabel">Import User PT</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-primary bg-primary-subtle text-primary border-0 mb-4" role="alert">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="ri-download-cloud-2-line fs-3 me-2"></i>
                                <div>
                                    <h5 class="alert-heading fs-14 mb-1">Gunakan Format Template</h5>
                                    <p class="mb-0 fs-13">Download file contoh sebelum upload</p>
                                </div>
                            </div>
                            <a href="{!! url('assets/template/user.xlsx') !!}" class="btn btn-sm btn-primary">Unduh</a>
                        </div>
                    </div>

                    <div class="text-center border border-dashed rounded p-4 position-relative">
                        <input type="file" name="excel" id="excelInput" class="position-absolute top-0 start-0 w-100 h-100" accept=".xlsx,.xls,.csv" required>
                        <i class="ri-upload-cloud-2-line text-primary"></i>
                        <h5 class="mt-2" id="fileLabel">Klik atau Tarik File Excel</h5>
                        <p class="text-muted mb-0">Mendukung format .xlsx, .xls, dan .csv</p>
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
        if (input) {
            input.addEventListener('change', function() {
                if (this.files.length > 0) {
                    label.innerHTML = `<span class="text-success fw-bold">${this.files[0].name}</span>`;
                }
            });
        }
    });
</script>

@endsection
