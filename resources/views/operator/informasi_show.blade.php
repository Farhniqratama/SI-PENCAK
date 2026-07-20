@extends('layouts.app')
@section('content')

<div class="row mt-4 mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Detail Informasi</h4>
            <p class="text-muted small mb-0">Lihat rincian lengkap informasi</p>
        </div>
        <div>
            <a href="#" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{!! url('/informasi-list') !!}';" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm border">
                <i class="ri-arrow-left-line me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card overflow-hidden mb-4">
            <div class="card-body p-0">
                <div class="bg-light-subtle p-4 border-bottom">
                    <span class="badge bg-primary px-3 py-1 mb-3 fs-12 text-uppercase">Informasi Publik</span>
                    <h2 class="text-dark fw-bold mb-3">{{ $data['judul'] }}</h2>
                    
                    <div class="d-flex flex-wrap align-items-center gap-4 text-muted fs-14">
                        <div class="d-flex align-items-center">
                            <i class="ri-calendar-line fs-16 me-2 text-primary"></i>
                            <span>{!! date('d F Y', strtotime($data['tanggal'])) !!}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="ri-time-line fs-16 me-2 text-primary"></i>
                            <span>09:00 WIB</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="ri-user-line fs-16 me-2 text-primary"></i>
                            <span>Administrator</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 p-md-5">
                    @if(!empty($data['file']))
                        <div class="alert alert-info bg-info-subtle text-info border-0 d-flex align-items-center justify-content-between p-4 mb-5" role="alert">
                            <div class="d-flex align-items-center">
                                <div class="bg-white rounded d-flex align-items-center justify-content-center me-3 shadow-sm">
                                    <i class="ri-file-text-line fs-24 text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading fs-15 mb-1 text-dark">Lampiran Dokumen</h5>
                                    <p class="mb-0 fs-13 text-muted">{{ $data['file'] }}</p>
                                </div>
                            </div>
                            <a href="{!! url('/informasi/' . $data['file']); !!}" class="btn btn-primary rounded-pill px-3" download>
                                <i class="ri-download-cloud-2-line me-1"></i> Unduh Berkas
                            </a>
                        </div>
                    @endif

                    @php
                    $decoded = html_entity_decode($data['deskripsi']);
                    $strip = strip_tags($decoded);
                    $limit = 600;
                    $isLong = strlen($strip) > $limit;

                    function smart_truncate($html, $limit)
                    {
                        $decoded = html_entity_decode($html);
                        if (strlen(strip_tags($decoded)) <= $limit) return $decoded;
                        return substr($decoded, 0, strpos($decoded, ' ', $limit)) . '...';
                    }
                    @endphp

                    <div class="text-dark fs-15">
                        <div id="desc-short">
                            {!! smart_truncate($data['deskripsi'], $limit) !!}
                            @if($isLong)
                                <div class="text-center mt-4">
                                    <button class="btn btn-outline-primary rounded-pill px-4" onclick="toggleDeskripsi()">Baca Selengkapnya</button>
                                </div>
                            @endif
                        </div>

                        <div id="desc-full">
                            {!! $decoded !!}
                            @if($isLong)
                                <div class="text-center mt-4">
                                    <button class="btn btn-outline-primary rounded-pill px-4" onclick="toggleDeskripsi()">Sembunyikan</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDeskripsi() {
        const short = document.getElementById('desc-short');
        const full = document.getElementById('desc-full');
        const isShortVisible = short.style.display !== 'none';

        short.style.display = isShortVisible ? 'none' : '';
        full.style.display = isShortVisible ? 'block' : 'none';

        if (isShortVisible) {
            full.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }
    }
</script>

@endsection
