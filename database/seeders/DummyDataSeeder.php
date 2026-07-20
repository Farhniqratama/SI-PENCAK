<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $password = password_hash('password', PASSWORD_BCRYPT);

        $this->command?->info('Mengisi dummy data lengkap SIPENCAK...');

        DB::table('users')->updateOrInsert(
            ['username' => 'operator'],
            [
                'nama' => 'Operator LLDIKTI III',
                'username' => 'operator',
                'password' => $password,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        $operatorId = DB::table('users')->where('username', 'operator')->value('id');

        $pts = [
            ['kode_pt' => '031012', 'perguruan_tinggi' => 'Universitas Bina Nusantara', 'aipt' => 'Unggul', 'admin' => 'admin_binus', 'pj' => 'Nadia Permatasari'],
            ['kode_pt' => '031014', 'perguruan_tinggi' => 'Universitas Trisakti', 'aipt' => 'Unggul', 'admin' => 'admin_trisakti', 'pj' => 'Rangga Pradipta'],
            ['kode_pt' => '031011', 'perguruan_tinggi' => 'Universitas Tarumanagara', 'aipt' => 'Unggul', 'admin' => 'admin_untar', 'pj' => 'Melati Wibisono'],
            ['kode_pt' => '031016', 'perguruan_tinggi' => 'Universitas Pancasila', 'aipt' => 'Baik Sekali', 'admin' => 'admin_pancasila', 'pj' => 'Hendra Wijaya'],
            ['kode_pt' => '031015', 'perguruan_tinggi' => 'Universitas Gunadarma', 'aipt' => 'Unggul', 'admin' => 'admin_gunadarma', 'pj' => 'Kartika Sari'],
            ['kode_pt' => '031020', 'perguruan_tinggi' => 'Universitas Mercu Buana', 'aipt' => 'Baik Sekali', 'admin' => 'admin_mercubuana', 'pj' => 'Dimas Mahendra'],
        ];

        $prodis = [
            ['kode_prodi' => '55201', 'nama_prodi' => 'Teknik Informatika'],
            ['kode_prodi' => '57201', 'nama_prodi' => 'Sistem Informasi'],
            ['kode_prodi' => '61201', 'nama_prodi' => 'Manajemen'],
            ['kode_prodi' => '62201', 'nama_prodi' => 'Akuntansi'],
            ['kode_prodi' => '74201', 'nama_prodi' => 'Ilmu Hukum'],
            ['kode_prodi' => '72201', 'nama_prodi' => 'Ilmu Komunikasi'],
        ];

        $names = [
            'Aulia Rahman', 'Bagas Pratama', 'Citra Lestari', 'Dewi Anggraini', 'Evan Saputra',
            'Farah Nabila', 'Gilang Ramadhan', 'Hana Maharani', 'Iqbal Maulana', 'Jasmine Putri',
            'Kevin Aditya', 'Laras Puspita', 'Mikael Jonathan', 'Nadia Safitri', 'Oscar Wibowo',
            'Putri Amalia', 'Raka Firmansyah', 'Salma Khairunnisa', 'Tegar Nugroho', 'Yasmin Azzahra',
        ];

        $statuses = ['Ajukan Mahasiswa', 'Finalisasi', 'Diproses', 'Selesai', 'Ditolak'];
        $kategoriList = ['Skema Pembiayaan Penuh', 'Skema Biaya Pendidikan'];
        $ptIds = [];

        foreach ($pts as $ptIndex => $pt) {
            DB::table('pts')->updateOrInsert(
                ['kode_pt' => $pt['kode_pt']],
                [
                    'kode_pt' => $pt['kode_pt'],
                    'perguruan_tinggi' => $pt['perguruan_tinggi'],
                    'aipt' => $pt['aipt'],
                ]
            );

            $ptId = DB::table('pts')->where('kode_pt', $pt['kode_pt'])->value('id');
            $ptIds[] = $ptId;

            DB::table('userpts')->updateOrInsert(
                ['username' => $pt['admin']],
                [
                    'username' => $pt['admin'],
                    'password' => $password,
                    'id_pt' => $ptId,
                    'penanggung_jawab' => $pt['pj'],
                    'nip' => '198' . $ptIndex . '061220260' . $ptIndex,
                    'kontak' => '0812' . str_pad((string) ($ptIndex + 32100000), 8, '0', STR_PAD_LEFT),
                    'email' => $pt['admin'] . '@kampus.test',
                    'status' => $ptIndex === 5 ? 'nonaktif' : 'aktif',
                ]
            );

            foreach ($prodis as $prodi) {
                DB::table('prodis')->updateOrInsert(
                    ['id_pt' => $ptId, 'kode_prodi' => $prodi['kode_prodi']],
                    [
                        'id_pt' => $ptId,
                        'kode_prodi' => $prodi['kode_prodi'],
                        'nama_prodi' => $prodi['nama_prodi'],
                    ]
                );
            }

            $prodiIds = DB::table('prodis')->where('id_pt', $ptId)->pluck('id')->values();

            foreach ($statuses as $statusIndex => $status) {
                $kategori = $kategoriList[($ptIndex + $statusIndex) % count($kategoriList)];
                $jumlahMahasiswa = 6 + (($ptIndex + $statusIndex) % 5);
                $nominalPerMahasiswa = $kategori === 'Skema Pembiayaan Penuh' ? 4200000 : 2400000;
                $noSk = 'DUMMY-' . $pt['kode_pt'] . '-' . ($statusIndex + 1) . '-2026';
                $tanggal = now()->subDays(($ptIndex * 5) + $statusIndex + 1)->toDateString();

                DB::table('pencairans')->updateOrInsert(
                    ['no_sk' => $noSk],
                    [
                        'id_pt' => $ptId,
                        'periode' => 'Semester Genap / 2026',
                        'kategori_penerima' => $kategori,
                        'no_sk' => $noSk,
                        'tanggal' => $tanggal,
                        'semester' => 'Genap',
                        'sptjm' => 'dummy/sptjm-' . $pt['kode_pt'] . '-' . ($statusIndex + 1) . '.pdf',
                        'sk_penetapan' => in_array($status, ['Diproses', 'Selesai'], true) ? 'dummy/sk-penetapan-' . $pt['kode_pt'] . '.pdf' : null,
                        'sk_pembatalan' => $status === 'Ditolak' ? 'dummy/sk-pembatalan-' . $pt['kode_pt'] . '.pdf' : null,
                        'berita_acara' => in_array($status, ['Diproses', 'Selesai'], true) ? 'dummy/berita-acara-' . $pt['kode_pt'] . '.pdf' : null,
                        'surat_pengantar' => 'dummy/surat-pengantar-' . $pt['kode_pt'] . '.pdf',
                        'status' => $status,
                        'alasan_tolak' => $status === 'Ditolak' ? 'Dokumen pendukung perlu dilengkapi ulang.' : null,
                        'tanggal_entry' => $tanggal,
                        'tanggal_pengajuan' => $tanggal,
                        'jumlah_mahasiswa' => $jumlahMahasiswa,
                        'nominal_pencairan' => $jumlahMahasiswa * $nominalPerMahasiswa,
                        'jenis_bantuan' => 'KIP Kuliah',
                        'keterangan' => 'Data dummy untuk simulasi ' . strtolower($status) . '.',
                    ]
                );

                $pencairanId = DB::table('pencairans')->where('no_sk', $noSk)->value('id');

                for ($i = 0; $i < $jumlahMahasiswa; $i++) {
                    $nim = 'DUM' . $pt['kode_pt'] . str_pad((string) ($statusIndex + 1), 2, '0', STR_PAD_LEFT) . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT);
                    $statusPengajuan = match ($status) {
                        'Ajukan Mahasiswa' => 'Belum Diajukan',
                        'Finalisasi' => 'Proses Pengajuan',
                        default => 'Diajukan',
                    };

                    DB::table('mahasiswas')->updateOrInsert(
                        ['nim' => $nim],
                        [
                            'id_pt' => $ptId,
                            'id_prodi' => $prodiIds[($i + $statusIndex) % $prodiIds->count()],
                            'id_pencairan' => $status === 'Ajukan Mahasiswa' ? null : $pencairanId,
                            'nim' => $nim,
                            'nama' => $names[($ptIndex + $statusIndex + $i) % count($names)],
                            'jenjang' => 'S1',
                            'angkatan' => (string) (2022 + ($i % 4)),
                            'pembaruan_status' => $i % 9 === 0 ? 'Henti' : 'Tetap',
                            'kategori' => $kategori,
                            'status_pengajuan' => $statusPengajuan,
                        ]
                    );
                }
            }
        }

        foreach (['Semester Genap / 2026', 'Semester Ganjil / 2026', 'Semester Genap / 2025'] as $periode) {
            DB::table('periodes')->updateOrInsert(['periode' => $periode], ['periode' => $periode]);
        }

        $informasis = [
            ['judul' => 'Jadwal Pengajuan Pencairan KIP Kuliah 2026', 'deskripsi' => 'Pengajuan pencairan semester genap dibuka sampai 30 Juni 2026.', 'file' => 'dummy/jadwal-pencairan-2026.pdf', 'tanggal' => '2026-06-01'],
            ['judul' => 'Panduan Unggah Dokumen SPTJM', 'deskripsi' => 'Pastikan dokumen SPTJM ditandatangani dan diunggah dalam format PDF.', 'file' => 'dummy/panduan-sptjm.pdf', 'tanggal' => '2026-06-03'],
            ['judul' => 'Validasi Data Mahasiswa Penerima', 'deskripsi' => 'Perguruan tinggi diminta melakukan pembaruan status mahasiswa sebelum finalisasi.', 'file' => 'dummy/validasi-mahasiswa.pdf', 'tanggal' => '2026-06-06'],
        ];

        foreach ($informasis as $info) {
            DB::table('informasis')->updateOrInsert(['judul' => $info['judul']], $info);
        }

        $notificationRows = [
            ['user_type' => 'operator', 'user_id' => $operatorId, 'title' => 'Pengajuan Baru Masuk', 'message' => 'Beberapa perguruan tinggi mengirim pengajuan pencairan baru.', 'type' => 'info', 'link' => url('pencairan-list'), 'is_read' => false],
            ['user_type' => 'operator', 'user_id' => $operatorId, 'title' => 'Dokumen Perlu Dicek', 'message' => 'Ada pengajuan dengan status finalisasi yang menunggu verifikasi.', 'type' => 'warning', 'link' => url('pencairan-list'), 'is_read' => false],
        ];

        foreach ($ptIds as $ptIndex => $ptId) {
            $adminId = DB::table('userpts')->where('id_pt', $ptId)->value('id');

            if (!$adminId) {
                continue;
            }

            $notificationRows[] = ['user_type' => 'admin', 'user_id' => $adminId, 'title' => 'Status Pengajuan Diperbarui', 'message' => 'Pengajuan pencairan perguruan tinggi Anda sudah masuk antrean verifikasi.', 'type' => 'success', 'link' => url('verifikasi-pembaharuan-status'), 'is_read' => $ptIndex % 2 === 0];
            $notificationRows[] = ['user_type' => 'admin', 'user_id' => $adminId, 'title' => 'Lengkapi Data Mahasiswa', 'message' => 'Silakan cek kembali data mahasiswa sebelum finalisasi pencairan.', 'type' => 'warning', 'link' => url('mahasiswa-list'), 'is_read' => false];
        }

        foreach ($notificationRows as $index => $notification) {
            DB::table('notifications')->updateOrInsert(
                [
                    'user_type' => $notification['user_type'],
                    'user_id' => $notification['user_id'],
                    'title' => $notification['title'],
                ],
                $notification + [
                    'created_at' => now()->subHours($index + 1),
                    'updated_at' => now()->subHours($index + 1),
                ]
            );
        }

        $activityRows = [
            ['user_type' => 'operator', 'user_id' => $operatorId, 'action' => 'seed_dummy_data', 'description' => 'Operator LLDIKTI melihat data dummy lengkap.', 'ip_address' => '127.0.0.1'],
            ['user_type' => 'operator', 'user_id' => $operatorId, 'action' => 'verify_submission', 'description' => 'Simulasi verifikasi pencairan perguruan tinggi.', 'ip_address' => '127.0.0.1'],
        ];

        foreach ($ptIds as $ptId) {
            $adminId = DB::table('userpts')->where('id_pt', $ptId)->value('id');

            if ($adminId) {
                $activityRows[] = ['user_type' => 'admin', 'user_id' => $adminId, 'action' => 'submit_pencairan', 'description' => 'Admin PT mengirim pengajuan pencairan dummy.', 'ip_address' => '127.0.0.1'];
            }
        }

        foreach ($activityRows as $index => $activity) {
            DB::table('activity_logs')->updateOrInsert(
                [
                    'user_type' => $activity['user_type'],
                    'user_id' => $activity['user_id'],
                    'action' => $activity['action'],
                    'description' => $activity['description'],
                ],
                $activity + [
                    'created_at' => now()->subMinutes(($index + 1) * 15),
                    'updated_at' => now()->subMinutes(($index + 1) * 15),
                ]
            );
        }

        foreach ([
            ['user' => 'operator', 'role' => 'operator', 'action' => 'Seed dummy database', 'details' => 'Mengisi data master, pencairan, mahasiswa, notifikasi, dan log.', 'target' => 'database'],
            ['user' => 'admin_binus', 'role' => 'admin', 'action' => 'Review pengajuan', 'details' => 'Simulasi audit untuk pengajuan PT.', 'target' => 'pencairans'],
        ] as $audit) {
            DB::table('audit_logs')->updateOrInsert(
                ['user' => $audit['user'], 'action' => $audit['action']],
                $audit
            );
        }

        $this->command?->info('Dummy data lengkap berhasil disiapkan.');
        $this->command?->line('Login operator: operator / password');
        $this->command?->line('Login admin PT: admin_binus / password, admin_trisakti / password, admin_untar / password');
    }
}
