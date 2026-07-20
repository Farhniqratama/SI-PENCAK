@extends('layouts.app')
@section('content')

<div class="row mt-4 mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">{!! $sub !!} Perguruan Tinggi</h4>
            <p class="text-muted small mb-0">Lengkapi informasi detail perguruan tinggi di bawah ini.</p>
        </div>
        <div>
            <a href="{!! url('pt-list') !!}" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm border">
                <i class="ri-arrow-left-line me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-white"><i class="ri-building-line me-2"></i> Form {!! $sub !!} Perguruan Tinggi</h5>
            </div>
            <div class="card-body">
                <form action="{!! $act !!}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="kode_pt" class="form-label fw-semibold text-muted">Kode Perguruan Tinggi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode_pt" name="kode_pt" placeholder="Contoh: 031001" required
                                value="{!! $btn == 'edit' ? e($data['kode_pt']) : '' !!}">
                            <div class="invalid-feedback">
                                Silakan masukkan kode perguruan tinggi.
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="aipt" class="form-label fw-semibold text-muted">Akreditasi (AIPT)</label>
                            <select name="aipt" id="aipt" class="form-select">
                                <option value="">- Pilih AIPT -</option>
                                <option value="A (Unggul)" {!! ($btn == 'edit' && $data['aipt'] == 'A (Unggul)') ? 'selected' : '' !!}>A (Unggul)</option>
                                <option value="B (Baik Sekali)" {!! ($btn == 'edit' && $data['aipt'] == 'B (Baik Sekali)') ? 'selected' : '' !!}>B (Baik Sekali)</option>
                                <option value="C (Baik)" {!! ($btn == 'edit' && $data['aipt'] == 'C (Baik)') ? 'selected' : '' !!}>C (Baik)</option>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="perguruan_tinggi" class="form-label fw-semibold text-muted">Nama Perguruan Tinggi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="perguruan_tinggi" name="perguruan_tinggi" placeholder="Masukkan nama resmi perguruan tinggi" required
                                value="{!! $btn == 'edit' ? e($data['perguruan_tinggi']) : '' !!}">
                            <div class="invalid-feedback">
                                Silakan masukkan nama perguruan tinggi.
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill py-2 shadow-sm">
                            <i class="ri-save-line me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation script
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>

@endsection