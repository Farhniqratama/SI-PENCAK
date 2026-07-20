<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Memulai generate dummy notifications...');

        // Kita buatkan untuk admin (asumsi admin id=1)
        // Dan untuk operator (asumsi operator pt id=1)

        $notifications = [
            [
                'user_type' => 'admin',
                'user_id' => 1,
                'title' => 'Pengajuan Baru Masuk',
                'message' => 'Universitas Bina Nusantara telah mengirimkan pengajuan pencairan KIP-Kuliah untuk 15 mahasiswa.',
                'type' => 'info',
                'link' => '/admin/verifikasi',
                'is_read' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_type' => 'admin',
                'user_id' => 1,
                'title' => 'Revisi Diperlukan',
                'message' => 'Pengajuan Universitas Trisakti membutuhkan revisi dokumen SPTJM.',
                'type' => 'warning',
                'link' => '/admin/verifikasi',
                'is_read' => 0,
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'user_type' => 'operator',
                'user_id' => 1,
                'title' => 'Pengajuan Disetujui',
                'message' => 'Selamat, pengajuan KIP-Kuliah periode Ganjil 2026 telah disetujui oleh LLDIKTI.',
                'type' => 'success',
                'link' => '/operator/pencairan',
                'is_read' => 0,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'user_type' => 'operator',
                'user_id' => 1,
                'title' => 'Pengingat Batas Waktu',
                'message' => 'Mohon segera unggah dokumen Berita Acara untuk pencairan tahap 2.',
                'type' => 'warning',
                'link' => '/operator/pencairan',
                'is_read' => 1,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ]
        ];

        foreach ($notifications as $notif) {
            DB::table('notifications')->insert($notif);
        }

        $this->command->info('Dummy notifications berhasil di-generate!');
    }
}
