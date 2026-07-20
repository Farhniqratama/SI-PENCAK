@extends('layouts.app')
@section('content')

<div class="container-fluid px-4 py-5 ">
    <div class="row align-items-center mb-5 g-4">
        <div class="col-md-7">
            <h2 class="fw-bold mb-2">Manajemen Program Studi</h2>
            <p class="text-muted mb-0">Sistem administrasi data akademik berbasis Elite Dashboard</p>
        </div>
        <div class="col-md-5 d-flex justify-content-md-end gap-3">
            <button type="button" class="btn btn-light" onclick="openUploadModal()">
                <i class="ri-file-excel-2-line text-success "></i> Import Excel
            </button>
            <a href="{!! url('prodi-create') !!}" class="btn btn-primary">
                <i class="ri-add-line "></i> Tambah Prodi
            </a>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-12 d-flex justify-content-md-end">
            <form action="" method="get" class="d-flex gap-2">
                <input type="text" name="keyword" class="form-control"
                    placeholder="Cari Kode atau Nama Prodi..."
                    value="{{ $keyword ?? '' }}">
                <i class="ri-search-line d-none"></i>

                @if(!empty($keyword))
                    <a href="{!! url('prodi-list') !!}" class="btn btn-sm btn-light" title="Bersihkan Pencarian">
                        <i class="ri-close-circle-line"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-striped table-centered mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3 text-center">No</th>
                            <th>Kode Prodi</th>
                            <th>Nama Program Studi</th>
                            <th class="text-center pe-3">Aksi</th>
                        </tr>
                    </thead>
                <tbody>
                    @if(!empty($data))
                        @php
                        $currentPage = $pager->getCurrentPage('default');
                        $perPage = 10;
                        $no = ($currentPage - 1) * $perPage + 1;
                        @endphp
                        @foreach($data as $row)
                            <tr>
                                <td class="ps-3 text-center text-muted fw-bold">{!! $no++ !!}.</td>
                                <td><span class="badge bg-primary-subtle text-primary">{{ $row['kode_prodi'] }}</span></td>
                                <td class="fw-bold">{{ $row['nama_prodi'] }}</td>
                                <td class="text-center pe-3">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="prodi-edit/{!! $row['id'] !!}" class="btn btn-sm btn-warning" title="Edit">
                                            <div class="d-inline-flex align-items-center justify-content-center"><i class="ri-edit-2-line"></i></div>
                                        </a>
                                        <a href="prodi-delete/{!! $row['id'] !!}" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')" title="Hapus">
                                            <div class="d-inline-flex align-items-center justify-content-center"><i class="ri-delete-bin-line"></i></div>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Data program studi tidak ditemukan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            </div>
            <div class="table-footer-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="text-muted small">
                    Menampilkan <strong>{{ $data->firstItem() ?? 0 }}-{{ $data->lastItem() ?? 0 }}</strong>
                    dari total <strong>{{ $data->total() }}</strong> data prodi
                </div>
                <div class="sipencak-pager">
                    @if($pager)
                        {{ $data->links('pagination::bootstrap-5') }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{!! url('prodi-import') !!}" method="post" enctype="multipart/form-data" class="modal-content ">

            <div class="modal-header border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="fw-bold mb-0" id="uploadModalLabel">Import Excel</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
            </div>

            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <a href="{!! url('assets/template/templateprodi.xlsx') !!}" class="text-decoration-none small fw-bold text-primary">
                        <i class="ri-download-cloud-2-line me-1"></i> Unduh Template Excel (.xlsx)
                    </a>
                </div>

                <div class="border rounded p-3 mb-4">
                    <input type="file" name="excel" id="excelInput" class="form-control" accept=".xlsx,.xls,.csv" required>
                    <div class="mb-3">
                        <i class="ri-upload-cloud-2-line fs-1 text-primary opacity-50"></i>
                    </div>
                    <h6 class="fw-bold text-dark">Klik atau Tarik File Anda</h6>
                    <p class="small text-muted mb-0">Mendukung format .xlsx, .xls, dan .csv</p>

                    <div id="fileInfo" class="mt-4 d-none">
                        <div class="p-3 border rounded-3 bg-white d-inline-flex align-items-center gap-3">
                            <i class="ri-checkbox-circle-line text-success"></i>
                            <span class="small fw-bold text-dark" id="nameLabel"></span>
                        </div>
                    </div>
                </div>

                <div class="alert bg-light border-0 d-flex gap-3 p-3">
                    <i class="ri-information-line text-primary fs-5"></i>
                    <div class="small">
                        Kolom wajib di Excel: <br>
                        <code class="text-primary fw-bold">id_pt, kode_prodi, nama_prodi</code>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0 gap-2">
                <button type="button" class="btn btn-light flex-grow-1" data-bs-dismiss="modal" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary flex-grow-1">Proses Import</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUploadModal() {
        const modalId = '#uploadModal';
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const myModal = new bootstrap.Modal(document.querySelector(modalId));
            myModal.show();
        } else if (window.jQuery && typeof jQuery.fn.modal !== 'undefined') {
            $(modalId).modal('show');
        } else {
            const modalEl = document.querySelector(modalId);
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
            document.body.classList.add('modal-open');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('excelInput');
        const fileInfo = document.getElementById('fileInfo');
        const nameLabel = document.getElementById('nameLabel');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                nameLabel.textContent = this.files[0].name;
                fileInfo.classList.remove('d-none');
            }
        });

        // Manual Close Handler (Fallback)
        document.querySelectorAll('[data-bs-dismiss="modal"], [data-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = document.getElementById('uploadModal');
                if (window.jQuery && typeof jQuery.fn.modal !== 'undefined') {
                    $(modal).modal('hide');
                } else {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
            });
        });
    });
</script>

@endsection
