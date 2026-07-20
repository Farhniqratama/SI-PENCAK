@extends('layouts.app')

@section('content')
<div class="row mt-4 mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Audit Trail & Log Sistem</h4>
            <p class="text-muted small">Monitoring seluruh aktivitas pengguna pada SIPENCAK.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body p-4">
                
                <div class="bg-light-subtle border border-light p-3 rounded mb-4">
                    <form action="" method="get" class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-muted text-uppercase fs-12 mb-1">Aksi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="ri-search-line"></i></span>
                                <input type="text" name="action" class="form-control" placeholder="Cari nama aksi atau deskripsi..." value="{{ request('action') }}">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-muted text-uppercase fs-12 mb-1">Tipe Pengguna</label>
                            <select name="user_type" class="form-select">
                                <option value="">Semua Tipe</option>
                                <option value="operator" {{ request('user_type') === 'operator' ? 'selected' : '' }}>Operator LLDIKTI</option>
                                <option value="admin" {{ request('user_type') === 'admin' ? 'selected' : '' }}>Admin PT</option>
                            </select>
                        </div>
                        <div class="col-md-2 mt-auto">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-centered mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3">Waktu</th>
                                <th>Tipe User</th>
                                <th>Aksi</th>
                                <th>Deskripsi Detail</th>
                                <th class="pe-3">IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td class="ps-3 fw-semibold text-muted">
                                        <div class="small"><i class="ri-calendar-event-line me-1"></i>{{ $log->created_at->format('d M Y') }}</div>
                                        <div class="small mt-1"><i class="ri-time-line me-1"></i>{{ $log->created_at->format('H:i:s') }}</div>
                                    </td>
                                    <td>
                                        @if($log->user_type === 'operator')
                                            <span class="badge bg-primary-subtle text-primary px-2 py-1"><i class="ri-shield-user-line me-1"></i> Operator LLDIKTI</span>
                                        @else
                                            <span class="badge bg-info-subtle text-info px-2 py-1"><i class="ri-user-settings-line me-1"></i> Admin PT</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $log->action }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $log->description }}</span>
                                    </td>
                                    <td class="pe-3">
                                        <span class="font-monospace text-muted small bg-light px-1 rounded">{{ $log->ip_address }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Belum ada log aktivitas yang tercatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->count())
                    <div class="table-footer-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="text-muted small">
                            Menampilkan <strong>{{ $logs->firstItem() ?? 0 }}-{{ $logs->lastItem() ?? 0 }}</strong>
                            dari total <strong>{{ $logs->total() }}</strong> log aktivitas
                        </div>
                        <div class="sipencak-pager">
                        {{ $logs->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</div>
@endsection
