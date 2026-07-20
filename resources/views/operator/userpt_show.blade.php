@extends('layouts.app')
@section('content')

<div class="row mt-4 mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Detail User</h4>
            <p class="text-muted small mb-0">Rincian informasi akun perguruan tinggi</p>
        </div>
        <div>
            <a href="{!! url('userpt-list') !!}" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm border">
                <i class="ri-arrow-left-line me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0">
                        <tbody>
                        <tr>
                            <td class="text-muted fw-semibold" width="30%">Username</td>
                            <td class="fw-bold text-primary">{{ $data['username'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Perguruan Tinggi</td>
                            <td class="fw-semibold">{{ $data['perguruan_tinggi'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Penanggung Jawab</td>
                            <td>{{ $data['penanggung_jawab'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">NIP</td>
                            <td>{{ $data['nip'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Kontak</td>
                            <td>{{ $data['kontak'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold">Email</td>
                            <td>{{ $data['email'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-semibold" width="30%">Status</td>
                            <td>
                                @if($data['status'] === 'aktif')
                                    <span class="badge bg-success-subtle text-success fs-13 px-3 py-2 rounded-pill"><i class="ri-checkbox-circle-line me-1"></i> AKTIF</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger fs-13 px-3 py-2 rounded-pill"><i class="ri-close-circle-line me-1"></i> NONAKTIF</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection