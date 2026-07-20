<?php

namespace App\Http\Controllers\Operator;

use App\Models\Periode;
use App\Models\Mahasiswa;
use App\Models\Pencairan;
use App\Support\SpreadsheetFile;
use App\Support\NotificationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PencairanController extends Controller
{
    public function index()
    {
        $model = new \App\Models\PencairanModel();
        $periode = new \App\Models\PeriodeModel();
        $ptModel = new \App\Models\PtModel();

        // Ambil input filter dan search dari GET
        $tahun = request('tahun');
        $pt = request('pt');
        $search = request('search');

        $status = request('status');

        /**
         * Menggunakan historiPager dari model yang menangani:
         * 1. Pagination (Limit 6 sesuai preferensi user_context)
         * 2. Filter Tahun (dari kolom tanggal_entry)
         * 3. Filter Perguruan Tinggi (berdasarkan ID PT)
         * 4. Search Box (berdasarkan Nama PT atau No SK)
         * 5. Filter Status
         */
        $histori = $model->historiPager(10, $tahun, $pt, $search, $status);

        // Ambil daftar seluruh Perguruan Tinggi untuk dropdown filter
        $daftar_pt = $ptModel->orderBy('perguruan_tinggi', 'ASC')->get();

        // Kirim data ke view
        $data = [
            'draft'         => $model->draft(),
            'histori'       => $histori,
            'pager'         => $model->pager,
            'title'         => 'Verifikasi Pembaharuan Status',
            'periode'       => $periode->periode(),
            'filter_tahun'  => $tahun,
            'filter_pt'     => $pt,
            'search'        => $search,
            'daftar_pt'     => $daftar_pt
        ];

        return view('operator.pencairan_list', $data);
    }

    public function detail($id)
    {
        $model = new \App\Models\PencairanModel();
        $mahasiswaModel = new \App\Models\MahasiswaModel();

        $search = request('search');

        $data = $model->detail($id);

        if (!$data) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        $builder = $mahasiswaModel->where('id_pencairan', $id);

        if ($search) {
            $builder->where(function($q) use ($search) {
                $q->where('nama', 'like', "%" . $search . "%")
                ->orWhere('nim', 'like', "%" . $search . "%");
            });
        }

        $dataMahasiswa = $builder->paginate(10);

        return view('operator.pencairan_detail', [
            'data'      => $data,
            'title'     => 'Detail Pencairan',
            'mahasiswa' => $dataMahasiswa,
            'dataMahasiswa' => $dataMahasiswa,
            'pager'     => $mahasiswaModel->pager,
            'search'    => $search,
            'id'        => $id
        ]);
    }

    public function markSelesai($id)
    {
        $model = new \App\Models\PencairanModel();
        $data = $model->find($id);

        if (!$data) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        if (strtolower($data['status']) === 'diproses') {
            $model->update($id, ['status' => 'Selesai']);

            NotificationService::notifyAdminPt(
                (int) $data['id_pt'],
                'Pencairan diterima',
                "Permohonan pencairan {$this->pencairanSummaryText($data['periode'] ?? null, $data['semester'] ?? null, $data['jenis_bantuan'] ?? null)} telah diterima dan statusnya menjadi Selesai.",
                'success',
                url('verifikasi-detail/' . $id)
            );

            return back()->with('success', 'Status berhasil diubah menjadi Selesai.');
        }

        return back()->with('warning', 'Status tidak dapat diubah.');
    }

    public function laporan()
    {
        $model = new \App\Models\PencairanModel();
        $tahun = request('tahun') ?? date('Y');
        $search = request('search');

        $builder = $model
            ->select('pencairans.*, pts.kode_pt, pts.perguruan_tinggi')
            ->join('pts', 'pts.id = pencairans.id_pt', 'left')
            ->where("YEAR(pencairans.tanggal_entry)", $tahun);

        if ($search) {
            $builder->where(function($q) use ($search) {
                $q->where('pts.perguruan_tinggi', 'like', "%" . $search . "%")
                ->orWhere('pts.kode_pt', 'like', "%" . $search . "%");
            });
        }

        $data = [
            'title'          => 'Laporan Pencairan',
            'pencairans'     => $builder->orderBy('pencairans.id', 'DESC')->paginate(10),
            'pager'          => $model->pager,
            'tahun_terpilih' => $tahun,
            'search'         => $search
        ];

        return view('operator.laporan/index', $data);
    }

    public function laporanHome()
    {
        $model = new \App\Models\PencairanModel();
        $search = request('search');

        $builder = $model
            ->select('pts.id, pts.kode_pt, pts.perguruan_tinggi, userpts.status')
            ->join('pts', 'pts.id = pencairans.id_pt')
            ->join('userpts', 'userpts.id_pt = pts.id', 'left')
            ->distinct();

        if ($search) {
            $builder->where(function($q) use ($search) {
                $q->where('pts.perguruan_tinggi', 'like', "%" . $search . "%")
                ->orWhere('pts.kode_pt', 'like', "%" . $search . "%");
            });
        }

        $data = [
            'title'  => 'Laporan Perguruan Tinggi',
            'pts'    => $builder->orderBy('pts.kode_pt', 'ASC')->paginate(10),
            'pager'  => $model->pager,
            'search' => $search
        ];

        return view('operator.laporan_home', $data);
    }

    public function laporanByPt($id_pt)
    {
        $model = new \App\Models\PencairanModel();
        $tahun = request('tahun') ?? date('Y');
        $search = request('search');

        $builder = $model
            ->select('pencairans.*, pts.kode_pt, pts.perguruan_tinggi')
            ->join('pts', 'pts.id = pencairans.id_pt', 'left')
            ->where('pencairans.id_pt', $id_pt)
            ->where("YEAR(pencairans.tanggal_entry)", $tahun);

        if ($search) {
            $builder->where(function($q) use ($search) {
                $q->where('pencairans.semester', 'like', "%" . $search . "%")
                ->orWhere('pencairans.kategori_penerima', 'like', "%" . $search . "%");
            });
        }

        $data = [
            'title'          => 'Laporan Pencairan',
            'pencairans'     => $builder->orderBy('pencairans.id', 'DESC')->paginate(10),
            'pager'          => $model->pager,
            'tahun_terpilih' => $tahun,
            'search'         => $search,
            'id_pt'          => $id_pt
        ];

        return view('operator.laporan/index', $data);
    }

    public function unduhLaporan()
    {
        $model = new \App\Models\Pencairan();

        // Ambil histori lalu filter hanya status Selesai
        $histori = array_filter($model->histori()->toArray(), function ($item) {
            return strtolower($item['status']) === 'selesai';
        });

        if (!class_exists(Spreadsheet::class)) {
            $filename = 'laporan_pencairan_' . date('Ymd_His') . '.csv';
            $rows = [];

            foreach ($histori as $item) {
                $periode = $item['semester'] . ' / ' . date('Y', strtotime($item['tanggal_entry']));
                $tanggal = date('d-m-Y', strtotime($item['tanggal_entry']));

                $rows[] = [
                    $item['kode_pt'],
                    $item['perguruan_tinggi'],
                    $periode,
                    $tanggal,
                    $item['kategori_penerima'],
                    $item['jenis_bantuan'] ?? '-',
                    $item['nominal_pencairan'] ?? 0,
                    $item['jumlah_mahasiswa'],
                    $item['status'],
                ];
            }

            return SpreadsheetFile::streamCsv($filename, [
                'Kode PT',
                'Perguruan Tinggi',
                'Periode',
                'Tanggal Pengajuan',
                'Kategori',
                'Jenis Bantuan',
                'Nominal (Rp)',
                'Jumlah Mahasiswa',
                'Status',
            ], $rows);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $header = [
            'A1' => 'Kode PT',
            'B1' => 'Perguruan Tinggi',
            'C1' => 'Periode',
            'D1' => 'Tanggal Pengajuan',
            'E1' => 'Kategori',
            'F1' => 'Jenis Bantuan',
            'G1' => 'Nominal (Rp)',
            'H1' => 'Jumlah Mahasiswa',
            'I1' => 'Status'
        ];

        foreach ($header as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Isi Data
        $row = 2;
        foreach ($histori as $item) {
            $periode = $item['semester'] . ' / ' . date('Y', strtotime($item['tanggal_entry']));
            $tanggal = date('d-m-Y', strtotime($item['tanggal_entry']));

            $sheet->setCellValue("A{$row}", $item['kode_pt']);
            $sheet->setCellValue("B{$row}", $item['perguruan_tinggi']);
            $sheet->setCellValue("C{$row}", $periode);
            $sheet->setCellValue("D{$row}", $tanggal);
            $sheet->setCellValue("E{$row}", $item['kategori_penerima']);
            $sheet->setCellValue("F{$row}", $item['jenis_bantuan'] ?? '-');
            $sheet->setCellValue("G{$row}", $item['nominal_pencairan'] ?? 0);
            $sheet->setCellValue("H{$row}", $item['jumlah_mahasiswa']);
            $sheet->setCellValue("I{$row}", $item['status']);
            $row++;
        }

        $lastRow = $row - 1;

        // Style border
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ];
        $sheet->getStyle("A1:I{$lastRow}")->applyFromArray($styleArray);

        // Auto-size kolom
        foreach (range('A', 'I') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Nama file
        $filename = 'laporan_pencairan_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function unduhExcel()
    {
        $mahasiswaModel = new \App\Models\MahasiswaModel();

        // Ambil data mahasiswa dengan pencairan status 'Selesai'
        $data = $mahasiswaModel
            ->select('mahasiswas.*, prodis.nama_prodi, prodis.kode_prodi, pts.perguruan_tinggi, pts.kode_pt')
            ->join('prodis', 'prodis.id = mahasiswas.id_prodi', 'left')
            ->join('pts', 'pts.id = mahasiswas.id_pt', 'left')
            ->join('pencairans', 'pencairans.id = mahasiswas.id_pencairan', 'left')
            ->where('pencairans.status', 'Selesai')
            ->get();

        if (!class_exists(Spreadsheet::class)) {
            $filename = 'mahasiswa_selesai_' . date('Ymd_His') . '.csv';

            return response()->streamDownload(function () use ($data) {
                $output = fopen('php://output', 'w');
                fputcsv($output, ['NIM', 'Nama', 'Program Studi', 'Kode Prodi', 'Perguruan Tinggi', 'Jenis Bantuan', 'Nominal (Rp)', 'Jenjang', 'Angkatan', 'Pembaruan Status', 'Status Pengajuan']);

                foreach ($data as $mhs) {
                    fputcsv($output, [
                        $mhs['nim'],
                        $mhs['nama'],
                        $mhs['nama_prodi'] ?? '-',
                        $mhs['kode_prodi'] ?? '-',
                        ($mhs['kode_pt'] ?? '-') . ' - ' . ($mhs['perguruan_tinggi'] ?? '-'),
                        $mhs['jenis_bantuan'] ?? '-',
                        $mhs['nominal_pencairan'] ?? 0,
                        $mhs['jenjang'],
                        $mhs['angkatan'],
                        $mhs['pembaruan_status'],
                        $mhs['status_pengajuan'],
                    ]);
                }

                fclose($output);
            }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'A1' => 'NIM',
            'B1' => 'Nama',
            'C1' => 'Program Studi',
            'D1' => 'Kode Prodi',
            'E1' => 'Perguruan Tinggi',
            'F1' => 'Jenis Bantuan',
            'G1' => 'Nominal (Rp)',
            'H1' => 'Jenjang',
            'I1' => 'Angkatan',
            'J1' => 'Pembaruan Status',
            'K1' => 'Status Pengajuan'
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        // Isi data
        $row = 2;
        foreach ($data as $mhs) {
            $sheet->setCellValue('A' . $row, $mhs['nim']);
            $sheet->setCellValue('B' . $row, $mhs['nama']);
            $sheet->setCellValue('C' . $row, $mhs['nama_prodi'] ?? '-');
            $sheet->setCellValue('D' . $row, $mhs['kode_prodi'] ?? '-');
            $sheet->setCellValue('E' . $row, ($mhs['kode_pt'] ?? '-') . ' - ' . ($mhs['perguruan_tinggi'] ?? '-'));
            $sheet->setCellValue('F' . $row, $mhs['jenis_bantuan'] ?? '-');
            $sheet->setCellValue('G' . $row, $mhs['nominal_pencairan'] ?? 0);
            $sheet->setCellValue('H' . $row, $mhs['jenjang']);
            $sheet->setCellValue('I' . $row, $mhs['angkatan']);
            $sheet->setCellValue('J' . $row, $mhs['pembaruan_status']);
            $sheet->setCellValue('K' . $row, $mhs['status_pengajuan']);
            $row++;
        }

        $lastRow = $row - 1;

        // Tambahkan border untuk semua data + header
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ];
        $sheet->getStyle("A1:K{$lastRow}")->applyFromArray($styleArray);

        // Auto-size semua kolom
        foreach (range('A', 'K') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Nama file
        $filename = 'mahasiswa_selesai_' . date('Ymd_His') . '.xlsx';

        // Output untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function unduhMahasiswa($id_pencairan)
    {
        $mahasiswaModel = new \App\Models\MahasiswaModel();
        $table = $mahasiswaModel->table;

        $mahasiswaList = $mahasiswaModel
            ->select("$table.*, prodis.nama_prodi")
            ->join('prodis', "prodis.id = $table.id_prodi", 'left')
            ->where("$table.id_pencairan", $id_pencairan)
            ->get();

        if (!class_exists(Spreadsheet::class)) {
            $filename = 'mahasiswa_pencairan_' . $id_pencairan . '_' . date('Ymd_His') . '.csv';
            $rows = [];

            foreach ($mahasiswaList as $mhs) {
                $rows[] = [
                    $mhs['nim'],
                    $mhs['nama'],
                    $mhs['nama_prodi'] ?? '-',
                    $mhs['jenjang'],
                    $mhs['angkatan'],
                    $mhs['pembaruan_status'],
                ];
            }

            return SpreadsheetFile::streamCsv($filename, ['NIM', 'Nama', 'Program Studi', 'Jenjang', 'Angkatan', 'Pembaruan Status'], $rows);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $headers = [
            'A1' => 'NIM',
            'B1' => 'Nama',
            'C1' => 'Program Studi',
            'D1' => 'Jenjang',
            'E1' => 'Angkatan',
            'F1' => 'Pembaruan Status'
        ];

        foreach ($headers as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        // Isi data
        $row = 2;
        foreach ($mahasiswaList as $mhs) {
            $sheet->setCellValue("A$row", $mhs['nim']);
            $sheet->setCellValue("B$row", $mhs['nama']);
            $sheet->setCellValue("C$row", $mhs['nama_prodi'] ?? '-');
            $sheet->setCellValue("D$row", $mhs['jenjang']);
            $sheet->setCellValue("E$row", $mhs['angkatan']);
            $sheet->setCellValue("F$row", $mhs['pembaruan_status']);
            $row++;
        }

        $lastRow = $row - 1;

        // Tambahkan border & alignment ke seluruh sel yang terisi
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
            ],
        ];
        $sheet->getStyle("A1:F{$lastRow}")->applyFromArray($styleArray);

        // Auto-size kolom agar konten tidak terpotong
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Nama file
        $filename = 'mahasiswa_pencairan_' . $id_pencairan . '_' . date('Ymd_His') . '.xlsx';

        // Output file Excel untuk didownload
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function ditolak($id)
    {
        $alasan = request('alasan');
        $model = new \App\Models\Pencairan();
        $data = $model->find($id);

        $model->update($id, [
            'status' => 'Ditolak',
            'alasan_tolak' => $alasan
        ]);

        if ($data) {
            NotificationService::notifyAdminPt(
                (int) $data['id_pt'],
                'Pencairan ditolak',
                "Permohonan pencairan {$this->pencairanSummaryText($data['periode'] ?? null, $data['semester'] ?? null, $data['jenis_bantuan'] ?? null)} ditolak." . ($alasan ? " Alasan: {$alasan}" : ''),
                'danger',
                url('verifikasi-detail/' . $id)
            );
        }

        return back()->with('success', 'Permohonan berhasil ditolak dengan alasan.');
    }

    public function markDitolak($id)
    {
        $model = new \App\Models\PencairanModel();
        $mahasiswaModel = new \App\Models\MahasiswaModel();

        $data = $model->find($id);

        if (!$data) {
            return back()->with('error', 'Data tidak ditemukan.');
        }

        // Tangkap alasan dari form
        $alasan = request('alasan');

        if (strtolower($data['status']) === 'diproses') {
            // Update mahasiswa yang terkait: reset status dan hapus id_pencairan
            $mahasiswaModel->where('id_pencairan', $id)
                ->set([
                    'status_pengajuan' => 'Belum Diajukan',
                    'id_pencairan' => null,
                ])
                ->update();

            // Update status pencairan dan simpan alasan (jika ada kolom alasan_tolak)
            $updateData = ['status' => 'Ditolak'];

            // Hanya jika kolom `alasan_tolak` tersedia di tabel pencairans
            if ($model->allowedFields && in_array('alasan_tolak', $model->allowedFields)) {
                $updateData['alasan_tolak'] = $alasan;
            }

            $model->where('id', $id)->update($updateData);

            NotificationService::notifyAdminPt(
                (int) $data['id_pt'],
                'Pencairan ditolak',
                "Permohonan pencairan {$this->pencairanSummaryText($data['periode'] ?? null, $data['semester'] ?? null, $data['jenis_bantuan'] ?? null)} ditolak." . ($alasan ? " Alasan: {$alasan}" : ''),
                'danger',
                url('verifikasi-detail/' . $id)
            );

            return back()->with('success', 'Status berhasil diubah menjadi Ditolak. Mahasiswa dikembalikan ke status Belum Diajukan.');
        }

        return back()->with('warning', 'Status tidak dapat diubah.');
    }

    private function pencairanSummaryText(?string $periode, ?string $semester, ?string $jenisBantuan): string
    {
        return collect([$semester, $periode, $jenisBantuan])
            ->filter()
            ->implode(' - ') ?: 'periode pencairan terkait';
    }
}
