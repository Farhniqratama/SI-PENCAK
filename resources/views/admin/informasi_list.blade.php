@extends('layouts.app')
@section('content')

<div class="info-page">
    <div class="info-header">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
            <div>
                <h4>Papan Informasi</h4>
                <p>Update terkini mengenai kegiatan di lingkungan LLDIKTI III.</p>
            </div>

            <form action="{!! current_url('/') !!}" method="get" class="d-flex flex-column flex-sm-row gap-2">
                <div class="input-group">
                    <span class="input-group-text"><i class="ri-search-line"></i></span>
                    <input type="text" name="keyword" class="form-control" placeholder="Cari informasi..." value="{{ $keyword ?? '' }}">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-search-line me-1"></i> Cari
                </button>
                @if(!empty($keyword))
                    <a href="{!! url('papan-informasi') !!}" class="btn btn-light">Reset</a>
                @endif
            </form>
        </div>
    </div>

    @if(empty($data) || count($data) === 0)
        <div class="info-empty text-center py-5">
            <i class="ri-search-line display-4 text-muted"></i>
            <h5 class="mt-3">Informasi tidak ditemukan.</h5>
            <p class="text-muted small mb-3">Coba kata kunci lain atau reset pencarian.</p>
            <a href="{!! url('papan-informasi') !!}" class="btn btn-primary">Reset</a>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 info-grid">
            @foreach($data as $row)
                <div class="col">
                    <div class="info-card">
                        <div class="info-thumb">
                            <span class="info-badge">Info</span>
                            <img src="{!! url('assets/img/lldikti3.png') !!}" alt="Thumbnail">
                        </div>

                        <div class="info-card-body">
                            <div class="info-date">
                                <i class="ri-calendar-line"></i>
                                <span>{!! date('d M Y', strtotime($row['tanggal'])) !!}</span>
                            </div>

                            <h5 class="info-title">
                                <a href="{!! url('informasi-detail/' . $row['id']) !!}">
                                    {{ $row['judul'] }}
                                </a>
                            </h5>

                            <p class="info-desc">
                                {!! \Illuminate\Support\Str::words(strip_tags($row['deskripsi']), 18) !!}
                            </p>
                        </div>

                        <div class="info-card-footer">
                            <a href="{!! url('informasi-detail/' . $row['id']) !!}" class="btn-info-read">
                                Baca Selengkapnya <i class="ri-arrow-right-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="table-footer-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4">
            <div class="text-muted small fw-bold">
                Menampilkan <span class="text-primary">{{ $data->firstItem() ?? 0 }}-{{ $data->lastItem() ?? 0 }}</span>
                dari total <span class="text-dark">{{ $data->total() }}</span> informasi
            </div>
            <div class="sipencak-pager">
                {{ $data->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

@endsection
