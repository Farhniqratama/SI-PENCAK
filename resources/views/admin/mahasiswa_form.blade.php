@extends('layouts.app')
@section('content')

<div class="container py-5 page-animate">
    <div class="container-fluid">

        <form action="{!! $act !!}" method="POST" class="card mb-4">
            @csrf

            <div class="card-header bg-primary bg-gradient text-white border-0 p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="icon-box bg-primary-subtle text-primary rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="ri-user-star-line fs-4"></i>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2">
                        <i class="ri-lock-line me-1"></i> {!! strtoupper($btn) !!} MODE
                    </span>
                </div>
                <h4 class="fw-bold mb-1">
                    {!! $btn == 'edit' ? 'Update Data' : 'Tambah Mahasiswa' !!}
                </h4>
                <p class="text-muted small mb-0">Lengkapi berkas akademik mahasiswa dengan data valid PDDIKTI.</p>
            </div>

            <div class="p-5">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="field-group">
                            <label class="field-label">Nomor Induk Mahasiswa</label>
                            <input type="text" class="input-premium" name="nim" placeholder="Masukkan NIM" required
                                value="{!! $btn == 'edit' ? e($data['nim']) : '' !!}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="field-group">
                            <label class="field-label">Nama Mahasiswa</label>
                            <input type="text" class="input-premium" name="nama" placeholder="Nama Lengkap" required
                                value="{!! $btn == 'edit' ? e($data['nama']) : '' !!}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="field-group">
                            <label class="field-label">Jenjang Pendidikan</label>
                            <input type="text" class="input-premium" name="jenjang" placeholder="S1 / D3" required
                                value="{!! $btn == 'edit' ? e($data['jenjang']) : '' !!}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="field-group">
                            <label class="field-label">Kategori Penerima</label>
                            <select name="kategori" class="input-premium" required>
                                <option value="" disabled selected>Pilih Kategori</option>
                                <option value="Skema Pembiayaan Penuh" {!! $btn == 'edit' && $data['kategori'] == 'Skema Pembiayaan Penuh' ? 'selected' : '' !!}>Skema Pembiayaan Penuh</option>
                                <option value="Skema Biaya Pendidikan" {!! $btn == 'edit' && $data['kategori'] == 'Skema Biaya Pendidikan' ? 'selected' : '' !!}>Skema Biaya Pendidikan</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="field-group">
                            <label class="field-label">Program Studi</label>
                            <select name="id_prodi" class="input-premium" required>
                                <option value="" disabled selected>Pilih Program Studi</option>
                                @foreach($prodi as $p)
                                    <option value="{!! $p['id'] !!}" {!! $btn == 'edit' && $p['id'] == $data['id_prodi'] ? 'selected' : '' !!}>
                                        {!! $p['kode_prodi'] !!} - {!! $p['nama_prodi'] !!}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="field-group">
                            <label class="field-label">Tahun Angkatan</label>
                            <input type="number" class="input-premium" name="angkatan" placeholder="Contoh: 2024" required
                                value="{!! $btn == 'edit' ? e($data['angkatan']) : '' !!}">
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="id_pt" value="{!! session('pt') !!}">

            <div class="card-footer bg-light p-4 d-flex justify-content-between">
                <a href="/mahasiswa-list" class="btn btn-light rounded-pill px-4 shadow-sm border">
                    <i class="ri-arrow-left-line me-2"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                    <i class="ri-checkbox-circle-line me-2"></i> Konfirmasi & Simpan
                </button>
            </div>
        </form>

    </div>
</div>

@endsection