@extends('layouts.app')
@section('content')

<!-- Summernote CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>



<div class="info-form-page">

    <div class="info-form-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>{!! $sub !!} Informasi</h4>
                <p>Lengkapi data informasi yang akan dipublikasikan.</p>
            </div>

            <a href="{!! url('informasi-list') !!}" class="btn-info-back">
                <i class="ri-arrow-left-line"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card info-form-card mb-4">
                <div class="card-body">

                    <div class="info-form-section-title">
                        <div class="icon-box">
                            <i class="ri-article-line"></i>
                        </div>
                        <div>
                            <h5>Form Informasi</h5>
                            <p>Pastikan judul, deskripsi, dan lampiran sudah sesuai sebelum disimpan.</p>
                        </div>
                    </div>

                    <form action="{!! $act !!}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf

                        <div class="mb-4">
                            <label for="judul" class="form-label info-form-label">
                                Judul Informasi <span class="required">*</span>
                            </label>

                            <input type="text"
                                class="form-control info-form-control"
                                id="judul"
                                name="judul"
                                placeholder="Contoh: Pengumuman Seleksi KIP-K 2026"
                                required
                                value="{!! $btn == 'edit' ? e($data['judul']) : '' !!}">

                            <div class="invalid-feedback">
                                Silakan masukkan judul informasi.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="summernote" class="form-label info-form-label">
                                Deskripsi Lengkap <span class="required">*</span>
                            </label>

                            <textarea id="summernote" name="deskripsi" required>{!! $btn == 'edit' ? html_entity_decode($data['deskripsi']) : '' !!}</textarea>

                            <div class="invalid-feedback">
                                Silakan masukkan deskripsi informasi.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label info-form-label">
                                Lampiran Dokumen
                                <span class="text-muted fw-normal">(Opsional)</span>
                            </label>

                            <div class="info-upload-box">
                                <input type="file" name="file" id="fileInput">

                                <i class="ri-upload-cloud-2-line info-upload-icon"></i>

                                <h5 class="info-upload-title" id="fileLabel">
                                    Klik untuk unggah berkas atau tarik ke sini
                                </h5>

                                <p class="info-upload-desc">
                                    Mendukung format PDF, DOCX, JPG, PNG. Maksimal 5MB.
                                </p>

                                @if(!empty($data['file']))
                                    <div class="info-current-file">
                                        <i class="ri-file-text-line text-primary fs-18"></i>

                                        <span>{{ $data['file'] }}</span>

                                        <a href="{!! url('/informasi/' . $data['file']) !!}"
                                            target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            Lihat Berkas
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="info-help-text">
                                Kosongkan bagian ini jika tidak ingin mengganti lampiran yang sudah ada.
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-info-submit">
                                <i class="ri-save-line me-1"></i>
                                Simpan Informasi
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<script>
    $(document).ready(function () {
        $('#summernote').summernote({
            height: 320,
            placeholder: 'Tulis detail informasi di sini...',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onInit: function () {
                    $('.note-editable').css({
                        'font-size': '14px',
                        'line-height': '1.7'
                    });
                }
            }
        });

        $('#fileInput').on('change', function (e) {
            if (e.target.files.length > 0) {
                var fileName = e.target.files[0].name;

                $('#fileLabel').html(
                    '<span class="text-success fw-bold">Terpilih: ' + fileName + '</span>'
                );
            }
        });
    });

    (function () {
        'use strict';

        var forms = document.querySelectorAll('.needs-validation');

        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if ($('#summernote').summernote('isEmpty')) {
                    event.preventDefault();
                    event.stopPropagation();
                    alert('Deskripsi informasi tidak boleh kosong.');
                }

                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

@endsection
