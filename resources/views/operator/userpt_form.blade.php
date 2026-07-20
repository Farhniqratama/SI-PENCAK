@extends('layouts.app')
@section('content')

<div class="row mt-4 mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">{!! $sub !!} User PT</h4>
            <p class="text-muted small mb-0">Kelola kredensial dan informasi penanggung jawab perguruan tinggi.</p>
        </div>
        <div>
            <a href="{!! url('userpt-list') !!}" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm border">
                <i class="ri-arrow-left-line me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card mb-4">
            <div class="card-body p-4">
                @if(session('errors'))
                    <div class="alert alert-danger" role="alert">
                        <i class="ri-error-warning-line me-1"></i> <strong>Terdapat Kesalahan:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach(session('errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{!! $act !!}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label fw-semibold text-muted">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required
                                value="{!! $btn === 'edit' ? e($data['username']) : '' !!}">
                            <div class="invalid-feedback">Silakan masukkan username.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="id_pt" class="form-label fw-semibold text-muted">Perguruan Tinggi <span class="text-danger">*</span></label>
                            <select name="id_pt" id="id_pt" class="form-select" required>
                                <option value="" disabled selected>- Pilih PT -</option>
                                @foreach($pt as $item)
                                    <option value="{!! $item['id'] !!}" {!! ($btn === 'edit' && $data['id_pt'] == $item['id']) ? 'selected' : '' !!}>
                                        {!! $item['kode_pt'] !!} - {!! $item['perguruan_tinggi'] !!}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Silakan pilih perguruan tinggi.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label fw-semibold text-muted">Password {!! $btn === 'edit' ? '<small class="text-muted">(Kosongkan jika tidak diubah)</small>' : '<span class="text-danger">*</span>' !!}</label>
                            <input type="password" class="form-control" id="password" name="password" {!! $btn === 'add' ? 'required' : '' !!}>
                            @if($btn === 'add')<div class="invalid-feedback">Silakan masukkan password.</div>@endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label fw-semibold text-muted">Konfirmasi Password {!! $btn === 'edit' ? '' : '<span class="text-danger">*</span>' !!}</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" {!! $btn === 'add' ? 'required' : '' !!}>
                            @if($btn === 'add')<div class="invalid-feedback">Silakan konfirmasi password.</div>@endif
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="penanggung_jawab" class="form-label fw-semibold text-muted">Penanggung Jawab</label>
                            <input type="text" class="form-control" id="penanggung_jawab" name="penanggung_jawab" value="{!! $btn === 'edit' ? e($data['penanggung_jawab']) : '' !!}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nip" class="form-label fw-semibold text-muted">NIP / Identitas</label>
                            <input type="text" class="form-control" id="nip" name="nip" value="{!! $btn === 'edit' ? e($data['nip']) : '' !!}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="kontak" class="form-label fw-semibold text-muted">Kontak (WhatsApp/Telp)</label>
                            <input type="text" class="form-control" id="kontak" name="kontak" value="{!! $btn === 'edit' ? e($data['kontak']) : '' !!}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label fw-semibold text-muted">Email Instansi</label>
                            <input type="email" class="form-control" id="email" name="email" value="{!! $btn === 'edit' ? e($data['email']) : '' !!}">
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold text-muted d-block mb-2">Status Akun</label>
                            <div class="d-flex gap-3">
                                <div class="form-check form-radio-success">
                                    <input type="radio" id="statusAktif" name="status" class="form-check-input" value="aktif" {!! ($btn === 'add' || ($btn === 'edit' && $data['status'] === 'aktif')) ? 'checked' : '' !!}>
                                    <label class="form-check-label" for="statusAktif">Aktif</label>
                                </div>
                                <div class="form-check form-radio-danger">
                                    <input type="radio" id="statusNonaktif" name="status" class="form-check-input" value="nonaktif" {!! ($btn === 'edit' && $data['status'] === 'nonaktif') ? 'checked' : '' !!}>
                                    <label class="form-check-label" for="statusNonaktif">Nonaktif</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill py-2 shadow-sm">
                            <i class="ri-save-line me-1"></i> {!! $btn === 'edit' ? 'Perbarui User' : 'Simpan User' !!}
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