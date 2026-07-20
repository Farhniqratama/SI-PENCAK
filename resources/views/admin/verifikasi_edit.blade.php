@extends('layouts.app')
@section('content')

<div class="container py-5 ">
    <div class="container-fluid">

        <form action="/verifikasi-update/{!! $data['id'] !!}" method="POST" enctype="multipart/form-data" class="card mb-4">
            @csrf

            <div class="card-header bg-primary bg-gradient text-white border-0 p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Edit Permohonan Pencairan</h4>
                    <p class="text-white-50 small mb-0">ID Record: #{!! $data['id'] !!} | Status: <span class="badge bg-info text-white">{{ $data['status'] }}</span></p>
                </div>
                <span class="badge bg-white rounded-pill px-3 py-2 fs-13" style="color: #0b3f88 !important;">
                    <i class="ri-calendar-check-line me-1"></i> {{ $data['periode'] ?? $periode['periode'] }}
                </span>
            </div>

            <div class="p-5">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="field-label">Kategori Mahasiswa <span class="text-danger">*</span></label>
                        <div class="radio-group-modern">
                            <label class="radio-card">
                                <input type="radio" name="kategori_penerima" value="Skema Pembiayaan Penuh" {!! old('kategori_penerima', $data['kategori_penerima']) == 'Skema Pembiayaan Penuh' ? 'checked' : '' !!} required>
                                <span class="radio-label-content">Pembiayaan Penuh</span>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="kategori_penerima" value="Skema Biaya Pendidikan" {!! old('kategori_penerima', $data['kategori_penerima']) == 'Skema Biaya Pendidikan' ? 'checked' : '' !!}>
                                <span class="radio-label-content">Biaya Pendidikan</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="field-label">Periode Semester <span class="text-danger">*</span></label>
                        <div class="radio-group-modern">
                            <label class="radio-card">
                                <input type="radio" name="semester" value="Ganjil" {!! old('semester', $data['semester']) == 'Ganjil' ? 'checked' : '' !!} required>
                                <span class="radio-label-content">Semester Ganjil</span>
                            </label>
                            <label class="radio-card">
                                <input type="radio" name="semester" value="Genap" {!! old('semester', $data['semester']) == 'Genap' ? 'checked' : '' !!}>
                                <span class="radio-label-content">Semester Genap</span>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <label class="field-label">No. SK / Surat Permohonan</label>
                        <input type="text" class="input-premium" name="no_surat_permohonan"
                            value="{!! old('no_surat_permohonan', e($data['no_sk'] ?? '')) !!}"
                            placeholder="Contoh: 123/SK/2026" required>
                    </div>

                    <div class="col-md-4">
                        <label class="field-label">Tanggal Surat</label>
                        <input type="date" class="input-premium" name="tanggal"
                            value="{!! old('tanggal', isset($data['tanggal']) ? date('Y-m-d', strtotime($data['tanggal'])) : '') !!}" required>
                    </div>
                </div>

                <div class="h5 mt-4 mb-3">Detail Bantuan</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="field-label">Jenis Bantuan <span class="text-danger">*</span></label>
                        <select name="jenis_bantuan" class="form-select input-premium" required>
                            <option value="">-- Pilih Jenis Bantuan --</option>
                            <option value="Biaya Pendidikan" {!! old('jenis_bantuan', $data['jenis_bantuan']) == 'Biaya Pendidikan' ? 'selected' : '' !!}>Biaya Pendidikan</option>
                            <option value="Biaya Hidup" {!! old('jenis_bantuan', $data['jenis_bantuan']) == 'Biaya Hidup' ? 'selected' : '' !!}>Biaya Hidup</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="field-label">Nominal Pencairan (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted fw-semibold">Rp</span>
                            <input type="text" name="nominal_pencairan" id="nominal_pencairan" class="form-control input-premium border-start-0 ps-0" value="{!! old('nominal_pencairan', number_format($data['nominal_pencairan'] ?? 0, 0, ',', '.')) !!}" placeholder="0" required>
                        </div>
                    </div>
                    <div class="col-md-12 mt-3">
                        <label class="field-label">Keterangan / Catatan Tambahan</label>
                        <textarea name="keterangan" class="form-control input-premium" rows="3" placeholder="Tuliskan keterangan tambahan jika ada...">{!! old('keterangan', $data['keterangan']) !!}</textarea>
                    </div>
                </div>

                <div class="h5 mb-3">Surat Pengantar & Berkas SPTJM</div>
                <div class="border rounded p-3 bg-light mb-4">
                    @if(!empty($data['surat_pengantar']))
                        <div class="current-file-badge mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-mail-send-line text-info fs-5"></i>
                                <span class="fw-bold small text-dark">{{ $data['surat_pengantar'] }}</span>
                            </div>
                            <a href="{!! url('file/' . $data['surat_pengantar']) !!}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">LIHAT FILE SAAT INI</a>
                        </div>
                    @endif
                    <label class="field-label">Update Surat Pengantar (PDF, Kosongkan jika tetap)</label>
                    <input type="file" class="form-control input-premium" name="surat_pengantar" accept=".pdf" onchange="cekUkuran(this)">
                </div>

                <div class="border rounded p-3 bg-light mb-4">
                    @if(!empty($data['sptjm']))
                        <div class="current-file-badge">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-file-pdf-2-line text-danger fs-5"></i>
                                <span class="fw-bold small text-dark">{{ $data['sptjm'] }}</span>
                            </div>
                            <a href="{!! url('file/' . $data['sptjm']) !!}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">LIHAT FILE SAAT INI</a>
                        </div>
                    @endif
                    <label class="field-label">Update SPTJM (PDF, Kosongkan jika tetap)</label>
                    <input type="file" class="form-control input-premium" name="sptjm" accept=".pdf" onchange="cekUkuran(this)">
                </div>

                <div class="h5 mb-3">Informasi & Berkas SK</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3 bg-light">
                            @if(!empty($data['sk_penetapan']))
                                <div class="current-file-badge">
                                    <i class="ri-file-text-line text-primary"></i>
                                    <span class="fw-bold small text-truncate ms-2 me-auto">{{ $data['sk_penetapan'] }}</span>
                                    <a href="{!! url('file/' . $data['sk_penetapan']) !!}" target="_blank" class="text-primary"><i class="ri-external-link-line"></i></a>
                                </div>
                            @endif
                            <label class="field-label">Update SK Penetapan</label>
                            <input type="file" class="form-control input-premium" name="sk_penetapan" accept=".pdf" onchange="cekUkuran(this)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 bg-light">
                            @if(!empty($data['sk_pembatalan']))
                                <div class="current-file-badge">
                                    <i class="ri-file-edit-line text-warning"></i>
                                    <span class="fw-bold small text-truncate ms-2 me-auto">{{ $data['sk_pembatalan'] }}</span>
                                    <a href="{!! url('file/' . $data['sk_pembatalan']) !!}" target="_blank" class="text-warning"><i class="ri-external-link-line"></i></a>
                                </div>
                            @endif
                            <label class="field-label">Update SK Pembatalan</label>
                            <input type="file" class="form-control input-premium" name="sk_pembatalan" accept=".pdf" onchange="cekUkuran(this)">
                        </div>
                    </div>
                </div>

                <div class="h5 mb-3">Berita Acara Evaluasi</div>
                <div class="border rounded p-3 bg-light">
                    @if(!empty($data['berita_acara']))
                        <div class="current-file-badge">
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-clipboard-line text-success fs-5"></i>
                                <span class="fw-bold small text-dark">{{ $data['berita_acara'] }}</span>
                            </div>
                            <a href="{!! url('file/' . $data['berita_acara']) !!}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">LIHAT FILE SAAT INI</a>
                        </div>
                    @endif
                    <label class="field-label">Update Berita Acara</label>
                    <input type="file" class="form-control input-premium" name="berita_acara" accept=".pdf" onchange="cekUkuran(this)">
                </div>

                <input type="hidden" name="id_pt" value="{!! old('id_pt', $data['id_pt'] ?? session('pt')) !!}">
                <input type="hidden" name="periode" value="{!! old('periode', $data['periode'] ?? $periode['periode']) !!}">
            </div>

            <div class="card-footer bg-light p-4 d-flex justify-content-between">
                <a href="/verifikasi-pembaharuan-status" class="btn btn-light btn-sm rounded-pill px-4 shadow-sm border">
                    <i class="ri-arrow-left-line me-2"></i> Batal & Kembali
                </a>
                <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold rounded-pill shadow-sm">
                    <i class="ri-save-line me-2"></i> Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>

<div id="file-size-alert" class="alert alert-danger d-none">
    <i class="ri-error-warning-line me-2"></i> Ukuran file tidak boleh lebih dari 2 MB.
</div>

<script>
    function cekUkuran(input) {
        const maxSize = 2 * 1024 * 1024;
        if (input.files[0] && input.files[0].size > maxSize) {
            input.value = "";
            const alertBox = document.getElementById('file-size-alert');
            alertBox.classList.remove('d-none');
            setTimeout(() => {
                alertBox.classList.add('d-none');
            }, 3000);
        }
    }

    // Format Rupiah script
    var rupiah = document.getElementById('nominal_pencairan');
    if (rupiah) {
        rupiah.addEventListener('keyup', function(e){
            rupiah.value = formatRupiah(this.value);
        });
    }

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
