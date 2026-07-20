@extends('layouts.app')
@section('content')



<div class="info-page">

    <div class="info-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>Manajemen Informasi</h4>
                <p>Publikasi berita dan panduan KIP-K</p>
            </div>

            <a href="{!! url('informasi-create') !!}" class="btn-info-add">
                <i class="ri-add-line"></i>
                Tambah Informasi
            </a>
        </div>
    </div>

    <div class="card info-main-card mb-4">
        <div class="card-body p-4">

            <div class="info-filter-box">
                <form action="{!! url('informasi-list') !!}" method="get" class="row g-3 align-items-end">

                    <div class="col-lg-5">
                        <label class="form-label">Cari Informasi</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ri-search-line"></i>
                            </span>
                            <input type="text"
                                name="search"
                                class="form-control"
                                placeholder="Cari judul atau deskripsi..."
                                value="{{ $search ?? '' }}">
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date"
                            name="start_date"
                            class="form-control"
                            value="{{ $start_date ?? '' }}">
                    </div>

                    <div class="col-lg-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date"
                            name="end_date"
                            class="form-control"
                            value="{{ $end_date ?? '' }}">
                    </div>

                    <div class="col-lg-1">
                        <button type="submit" class="btn btn-info-filter">
                            <i class="ri-filter-line"></i>
                        </button>
                    </div>

                </form>

                @if(!empty($search) || !empty($start_date) || !empty($end_date))
                    <div class="mt-3 text-end">
                        <a href="{!! url('informasi-list') !!}" class="info-reset-filter">
                            <i class="ri-close-circle-line me-1"></i>
                            Hapus Filter
                        </a>
                    </div>
                @endif
            </div>

            @if($data->count() == 0)

                <div class="info-empty">
                    <i class="ri-search-line"></i>
                    <h5>Data tidak ditemukan</h5>
                    <p>Coba ubah kata kunci atau filter tanggal Anda.</p>
                    <a href="{!! url('informasi-list') !!}" class="btn btn-outline-primary rounded-pill px-4">
                        Reset Filter
                    </a>
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
                                        {{ $row['judul'] }}
                                    </h5>

                                    <p class="info-desc">
                                        {!! \Illuminate\Support\Str::words(strip_tags(html_entity_decode($row['deskripsi'])), 14) !!}
                                    </p>

                                </div>

                                <div class="info-card-footer">

                                    <a href="{!! url('informasi-show/' . $row['id']) !!}" class="btn-info-read">
                                        Baca Selengkapnya
                                    </a>

                                    <a href="{!! url('informasi-edit/' . $row['id']) !!}"
                                        class="btn-info-icon"
                                        title="Edit">
                                        <i class="ri-edit-2-line text-warning"></i>
                                    </a>

                                    <a href="{!! url('informasi-delete/' . $row['id']) !!}"
                                        class="btn-info-icon"
                                        title="Hapus"
                                        onclick="return confirm('Hapus informasi ini?')">
                                        <i class="ri-delete-bin-line text-danger"></i>
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
    </div>

</div>

@endsection
