@extends('layouts.app')
@section('content')

<div class="permohonan-page">
    <form action="/permohonan-store" method="POST" enctype="multipart/form-data" class="card mb-4 permohonan-card">
            @csrf

            @php
            $bulan = date('n');
            $tahun = date('Y');
            if ($bulan >= 8) {
                $periodeOtomatis = 'Semester Ganjil / ' . $tahun;
                $activeSemester = 'Semester Ganjil';
            } else {
                $periodeOtomatis = 'Semester Genap / ' . $tahun;
                $activeSemester = 'Semester Genap';
            }
            $selectedPeriode = old('periode') ?? $activeSemester;
            @endphp

            <div class="card-header permohonan-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h4 class="fw-bold mb-1">Permohonan Pencairan</h4>
                    <p class="text-white-50 small mb-0">Lengkapi dokumen syarat pencairan dana beasiswa.</p>
                </div>
                <span class="permohonan-period-badge">
                    <i class="ri-calendar-check-line me-1"></i> {!! $periodeOtomatis !!}
                </span>
            </div>

            <div class="permohonan-body">
                <section class="permohonan-section">
                    <div class="section-title">
                        <i class="ri-user-star-line"></i>
                        <span>Informasi Pengajuan</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="field-label">Kategori Mahasiswa <span class="text-danger">*</span></label>
                            <div class="radio-group-modern">
                                <label class="radio-card">
                                    <input type="radio" name="kategori_penerima" id="kategori1" value="Skema Pembiayaan Penuh" required>
                                    <span class="radio-label-content">Pembiayaan Penuh</span>
                                </label>
                                <label class="radio-card">
                                    <input type="radio" name="kategori_penerima" id="kategori2" value="Skema Biaya Pendidikan">
                                    <span class="radio-label-content">Biaya Pendidikan</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label">Periode Semester <span class="text-danger">*</span></label>
                            <div class="radio-group-modern">
                                <label class="radio-card">
                                    <input type="radio" name="semester" id="sem1" value="Ganjil" required>
                                    <span class="radio-label-content">Semester Ganjil</span>
                                </label>
                                <label class="radio-card">
                                    <input type="radio" name="semester" id="sem2" value="Genap">
                                    <span class="radio-label-content">Semester Genap</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="permohonan-section">
                    <div class="section-title">
                        <i class="ri-money-dollar-circle-line"></i>
                        <span>Detail Bantuan</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="field-label">Jenis Bantuan <span class="text-danger">*</span></label>
                            <select name="jenis_bantuan" class="form-select input-premium" required>
                                <option value="">-- Pilih Jenis Bantuan --</option>
                                <option value="Biaya Pendidikan">Biaya Pendidikan</option>
                                <option value="Biaya Hidup">Biaya Hidup</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label">Nominal Pencairan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group input-premium-group">
                                <span class="input-group-text text-muted fw-semibold">Rp</span>
                                <input type="text" name="nominal_pencairan" id="nominal_pencairan" class="form-control input-premium" placeholder="0" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="field-label">Keterangan / Catatan Tambahan</label>
                            <textarea name="keterangan" class="form-control input-premium" rows="4" placeholder="Tuliskan keterangan tambahan jika ada..."></textarea>
                        </div>
                    </div>
                </section>

                <section class="permohonan-section">
                    <div class="section-title">
                        <i class="ri-folder-upload-line"></i>
                        <span>Surat Pengantar & Berkas SPTJM</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="field-label">Unggah Surat Pengantar (PDF, Max 2MB)</label>
                            <div class="file-upload-box">
                                <i class="ri-mail-send-line text-info"></i>
                                <input type="file" class="form-control" name="surat_pengantar" accept=".pdf" onchange="cekUkuran(this)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label">Unggah Scan SPTJM (PDF, Max 2MB) <span class="text-danger">*</span></label>
                            <div class="file-upload-box">
                                <i class="ri-inbox-archive-line text-info"></i>
                                <input type="file" class="form-control" name="sptjm" accept=".pdf" required onchange="cekUkuran(this)">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="permohonan-section">
                    <div class="section-title">
                        <i class="ri-file-list-3-line"></i>
                        <span>Informasi & Berkas SK</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="field-label">No. SK / Surat Permohonan</label>
                            <input type="text" class="form-control input-premium" name="no_surat_permohonan" placeholder="Contoh: 123/SK/2026" required>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label">Tanggal Surat</label>
                            <input type="date" class="form-control input-premium" name="tanggal" required>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label">Scan SK Penetapan</label>
                            <div class="file-upload-box">
                                <i class="ri-file-text-line text-primary"></i>
                                <input type="file" class="form-control" name="sk_penetapan" accept=".pdf" required onchange="cekUkuran(this)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="field-label">Scan SK Pembatalan</label>
                            <div class="file-upload-box">
                                <i class="ri-file-edit-line text-warning"></i>
                                <input type="file" class="form-control" name="sk_pembatalan" accept=".pdf" required onchange="cekUkuran(this)">
                            </div>
                        </div>
                    </div>
                </section>

                <section class="permohonan-section">
                    <div class="section-title">
                        <i class="ri-clipboard-line"></i>
                        <span>Berita Acara Evaluasi</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="field-label">Unggah Berita Acara Evaluasi</label>
                            <div class="file-upload-box">
                                <i class="ri-clipboard-line text-success"></i>
                                <input type="file" class="form-control" name="berita_acara" accept=".pdf" required onchange="cekUkuran(this)">
                            </div>
                        </div>
                    </div>
                </section>

                <input type="hidden" name="id_pt" value="{!! session('pt') !!}">
                <input type="hidden" name="periode" value="{!! $periode['periode'] !!}">

                <div class="d-none">
                    <input type="radio" name="periode_hidden" value="Semester Ganjil" {!! ($selectedPeriode == 'Semester Ganjil') ? 'checked' : '' !!}>
                    <input type="radio" name="periode_hidden" value="Genap" {!! ($selectedPeriode == 'Genap') ? 'checked' : '' !!}>
                </div>
            </div>

            <div class="card-footer permohonan-footer d-flex flex-column flex-md-row justify-content-between gap-2">
                <a href="/verifikasi-pembaharuan-status" class="btn btn-light px-4 border">
                    <i class="ri-arrow-left-line me-2"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ri-save-line me-2"></i> Simpan Permohonan
                </button>
            </div>
    </form>
</div>

<div id="file-size-alert" class="alert alert-danger d-none permohonan-alert">
    <i class="ri-error-warning-line me-2"></i> Ukuran file tidak boleh lebih dari 2 MB.
</div>

<script>
    function cekUkuran(input) {
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (input.files[0] && input.files[0].size > maxSize) {
            input.value = ""; // kosongkan file
            showAlert(); // tampilkan pesan
        }
    }

    function showAlert() {
        const alertBox = document.getElementById('file-size-alert');
        alertBox.classList.remove('d-none');
        setTimeout(() => {
            alertBox.classList.add('d-none');
        }, 3000); // hilang dalam 3 detik
    }

    // Format Rupiah script
    var rupiah = document.getElementById('nominal_pencairan');
    if (rupiah) {
        rupiah.addEventListener('keyup', function(e){
            rupiah.value = formatRupiah(this.value);
        });
    }

    /* Fungsi formatRupiah */
    function formatRupiah(angka, prefix){
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split   		= number_string.split(','),
        sisa     		= split[0].length % 3,
        rupiah     		= split[0].substr(0, sisa),
        ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

        if(ribuan){
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }
</script>

@endsection
