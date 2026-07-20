@extends('layouts.app')
@section('content')

<div class="container-fluid px-4 py-4 ">
    <div class="row mb-4 align-items-end">
        <div class="col-md-7">
            <h2 class="fw-bold mb-1">Ajukan Mahasiswa</h2>
            <p class="text-muted mb-0">Seleksi data mahasiswa untuk proses pengajuan pencairan dana periode aktif.</p>
        </div>
        <div class="col-md-5 d-flex justify-content-md-end gap-2 mt-3 mt-md-0">
            <button type="button" class="btn btn-danger shadow-sm" id="btn-cancel-top">
                <i class="ri-close-line"></i> Batal
            </button>
            <button type="button" class="btn btn-primary shadow-sm" id="btn-save-top">
                <i class="ri-save-line"></i> Simpan Draft
            </button>
        </div>
    </div>

    <!-- SUMMARY CONTEXT CARD -->
    <div class="card mb-4 bg-light-subtle border-start border-4 border-primary">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase">No. SK / Dokumen</div>
                    <div class="fw-bold text-dark">{!! $pencairan['no_sk'] ?? '-' !!}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase">Kategori Bantuan</div>
                    <div class="fw-bold text-dark">{!! $pencairan['kategori_penerima'] !!}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase">Jenis Bantuan</div>
                    <div class="fw-bold text-dark">{!! $pencairan['jenis_bantuan'] ?? '-' !!}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase">Nominal / Mhs</div>
                    <div class="fw-bold text-success">Rp {!! number_format($pencairan['nominal_pencairan'] ?? 0, 0, ',', '.') !!}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-md-end">
            <form action="" method="get" class="d-flex gap-2">
                <input type="text" name="keyword" class="form-control"
                    placeholder="Cari NIM atau Nama Mahasiswa..."
                    value="{{ $keyword ?? '' }}">
                <i class="ri-search-line d-none"></i>

                @if(!empty($keyword))
                    <a href="{!! url('verifikasi-mahasiswa/' . $id_pencairan) !!}" class="btn btn-sm btn-light" title="Reset">
                        <i class="ri-close-circle-line text-danger"></i>
                    </a>
                @endif
            </form>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const idPencairan = {!! $id_pencairan !!};
                const checkAll = document.getElementById('checkAll');

                // Fungsi Kirim Data ke Server (AJAX)
                const syncStatus = (ids, isChecked) => {
                    fetch("{!! url('ajukan-mahasiswa-sync') !!}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                selected_ids: ids,
                                id_pencairan: idPencairan,
                                checked: isChecked
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) {
                                alert("Gagal sinkronisasi data.");
                                location.reload(); // Refresh jika gagal agar UI sinkron kembali
                            }
                        })
                        .catch(err => console.error("Error:", err));
                };

                // Event untuk Checkbox Satuan
                document.querySelectorAll('.check-item').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        syncStatus([this.value], this.checked);
                    });
                });

                // Event untuk Check All (Semua di halaman ini)
                checkAll.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.check-item');
                    const ids = Array.from(checkboxes).map(cb => cb.value);

                    checkboxes.forEach(cb => cb.checked = this.checked);
                    syncStatus(ids, this.checked);
                });
            });
        </script>
    </div>

    <div class="card overflow-hidden mb-4">
        <div class="table-responsive">
                <table class="table table-borderless table-hover align-middle mb-0" id="dataTable">
                    <thead>
                        <tr>
                            <th width="40" class="ps-3"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                            <th>NIM</th>
                            <th>Status</th>
                            <th>Nama Lengkap</th>
                            <th>Kode Prodi</th>
                            <th>Nama Prodi</th>
                            <th>Jenjang</th>
                            <th>Angkatan</th>
                            <th>Kategori</th>
                            <th width="120" class="pe-3">Pembaruan</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(!empty($mahasiswa))
                        @foreach($mahasiswa as $mhs)
                            <tr>
                                <td class="ps-3">
                                    <input type="checkbox"
                                        class="check-item form-check-input"
                                        {!! ($mhs['status_pengajuan'] == 'Proses Pengajuan' || $mhs['id_pencairan'] == $id_pencairan) ? 'checked' : '' !!}
                                        value="{!! $mhs['id'] !!}"
                                        data-nim="{!! $mhs['nim'] !!}">
                                </td>
                                <td class="fw-bold text-primary">{{ $mhs['nim'] }}</td>
                                <td>
                                    @php
                                        $statusText = $mhs['status_pengajuan'] ?? '-';
                                        $statusLower = strtolower($statusText);
                                        $statusClass = match (true) {
                                            str_contains($statusLower, 'selesai') => 'status-badge--selesai',
                                            str_contains($statusLower, 'ditolak') => 'status-badge--ditolak',
                                            $statusLower === 'diajukan' => 'status-badge--diajukan',
                                            str_contains($statusLower, 'finalisasi') => 'status-badge--finalisasi',
                                            str_contains($statusLower, 'proses'),
                                            str_contains($statusLower, 'diproses') => 'status-badge--proses',
                                            default => 'status-badge--default',
                                        };
                                    @endphp
                                    <span class="status-badge {!! $statusClass !!}">{{ $statusText }}</span>
                                </td>
                                <td class="fw-bold">{{ $mhs['nama'] }}</td>
                                <td>{{ $mhs['kode_prodi'] }}</td>
                                <td>{{ $mhs['nama_prodi'] }}</td>
                                <td>{{ $mhs['jenjang'] }}</td>
                                <td class="text-center">{{ $mhs['angkatan'] }}</td>
                                <td><small class="fw-medium text-uppercase">{{ $mhs['kategori'] }}</small></td>
                                <td class="pe-3">
                                    <select class="form-select form-select-sm pembaruan-status w-100" data-id="{!! $mhs['id'] !!}">
                                        <option value="Tetap" {!! $mhs['pembaruan_status'] == 'Tetap' ? 'selected' : '' !!}>Tetap</option>
                                        <option value="Henti" {!! $mhs['pembaruan_status'] == 'Henti' ? 'selected' : '' !!}>Henti</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="table-footer-pager d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="text-muted fw-bold">
                Menampilkan <strong>{{ $mahasiswa->firstItem() ?? 0 }}-{{ $mahasiswa->lastItem() ?? 0 }}</strong>
                dari total <strong>{{ $mahasiswa->total() }}</strong> mahasiswa
            </div>
            <div class="sipencak-pager">
                @if($pager)
                    {{ $mahasiswa->links('pagination::bootstrap-5') }}
                @endif
            </div>
        </div>

        <div class="p-4 bg-light border-top d-flex justify-content-between align-items-center">
            <a href="{!! url('verifikasi-pembaharuan-status') !!}" class="btn btn-light">
                <i class="ri-arrow-left-line"></i> Kembali
            </a>
            <button type="button" class="btn btn-success shadow" id="btn-ajukan">
                <i class="ri-send-plane-line"></i> Selesai & Ajukan Sekarang
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        const idPencairan = {!! $id_pencairan !!};

        // Fungsi dinamis untuk mengambil checkbox yang ada di halaman pager saat ini
        const getCheckItems = () => document.querySelectorAll('.check-item');

        checkAll.addEventListener('change', function() {
            getCheckItems().forEach(cb => cb.checked = this.checked);
        });

        const handleAction = (isSubmit = false, isCancel = false) => {
            const items = getCheckItems();
            const selected = Array.from(items).filter(cb => cb.checked).map(cb => cb.value);
            const all = Array.from(items).map(cb => cb.value);

            if (isSubmit && selected.length === 0) {
                alert("Pilih mahasiswa terlebih dahulu.");
                return;
            }

            if (isCancel && !confirm("Batalkan semua pengajuan di halaman ini?")) return;
            if (isSubmit && !confirm("Ajukan mahasiswa yang dipilih untuk diproses?")) return;

            fetch("{!! url('ajukan-mahasiswa') !!}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        selected: isCancel ? [] : selected,
                        all: all,
                        id_pencairan: idPencairan
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect || "{!! url('verifikasi-pembaharuan-status') !!}";
                    } else {
                        alert(data.message || "Gagal memproses data.");
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Terjadi kesalahan sistem.");
                });
        };

        document.getElementById('btn-save-top').addEventListener('click', () => handleAction(false, false));
        document.getElementById('btn-cancel-top').addEventListener('click', () => handleAction(false, true));
        document.getElementById('btn-ajukan').addEventListener('click', () => handleAction(true, false));

        document.querySelectorAll('.pembaruan-status').forEach(select => {
            select.addEventListener('change', function() {
                fetch("{!! url('mahasiswa/updateStatus') !!}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            id: this.dataset.id,
                            pembaruan_status: this.value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) alert("Gagal memperbarui status.");
                    })
                    .catch(error => console.error(error));
            });
        });
    });
</script>

@endsection
