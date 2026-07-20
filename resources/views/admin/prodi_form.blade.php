@extends('layouts.app')
@section('content')

<div class="container py-5 page-animate">
    <div class="container-fluid">

        <form action="{!! $act !!}" method="POST" class="card mb-4">
            @csrf

            <div class="card-header bg-primary bg-gradient text-white border-0 p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="ri-book-open-line fs-4"></i>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2">
                        <i class="ri-bank-line me-1"></i> {!! strtoupper($sub) !!} DATA
                    </span>
                </div>
                <h4 class="fw-bold mb-1">
                    {!! $btn == 'edit' ? 'Perbarui' : 'Tambah' !!} Program Studi
                </h4>
                <p class="text-muted small mb-0">Kelola identitas program studi untuk sinkronisasi data mahasiswa.</p>
            </div>

            <div class="p-5">
                <div class="row">
                    <div class="col-md-12">
                        <div class="field-group">
                            <label class="field-label">Kode Program Studi</label>
                            <input type="text" class="input-premium" name="kode_prodi" placeholder="Contoh: INF-01" required
                                value="{!! $btn == 'edit' ? e($data['kode_prodi']) : '' !!}">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="field-group">
                            <label class="field-label">Nama Lengkap Prodi</label>
                            <input type="text" class="input-premium" name="nama_prodi" placeholder="Masukkan nama program studi" required
                                value="{!! $btn == 'edit' ? e($data['nama_prodi']) : '' !!}">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="id_pt" value="{!! session('pt') !!}">
            </div>

            <div class="card-footer bg-light p-4 d-flex justify-content-between">
                <a href="/prodi-list" class="btn btn-light rounded-pill px-4 shadow-sm border">
                    <i class="ri-arrow-left-line me-2"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="ri-save-line me-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>

    </div>
</div>

@endsection
