<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Pencairan;
use App\Models\Periode;
use App\Support\SpreadsheetFile;
use App\Support\NotificationService;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PencairanController extends Controller
{
    public function index()
    {
        $model = new \App\Models\Pencairan();
        $periodeModel = new \App\Models\Periode();
        $pt = session('pt');

        // Ambil keyword dari input GET
        $keyword = request('keyword');
        $tahun = request('tahun');
        $status = request('status');

        // Query Dasar
        $model->where('id_pt', $pt);
        
        if ($status) {
            $model->where('status', $status);
        } else {
            $model->whereIn('status', ['Ajukan Mahasiswa', 'Finalisasi', 'Diproses', 'Selesai', 'Ditolak']);
        }

        if ($tahun) {
            $model->whereYear('tanggal_entry', $tahun);
        }

        // Logika Pencarian
        if ($keyword) {
            $model->where(function($q) use ($keyword) {
                $q->where('kategori_penerima', 'like', "%" . $keyword . "%")
                ->orWhere('semester', 'like', "%" . $keyword . "%")
                ->orWhere('status', 'like', "%" . $keyword . "%");
            });
        }

        $data = [
            'histori' => $model->orderBy('tanggal_entry', 'DESC')->paginate(10),
            'pager'   => $model->pager,
            'draft'   => $model->where('id_pt', $pt)->draft(),
            'title'   => 'Verifikasi Pembaharuan Status',
            'periode' => $periodeModel->periode(),
            'keyword' => $keyword // Kirim balik ke view
        ];

        return view('admin.verifikasi_list', $data);
    }

    public function permohonan()
    {
        $periode = new \App\Models\Periode();
        $data = [
            'title' => 'Permohonan Pencairan',
            'periode' => $periode->periode(),
        ];
        return view('admin.verifikasi_1', $data);
    }

    public function unduhExcel()
    {
        $mahasiswaModel = new \App\Models\MahasiswaModel();

        $data = $mahasiswaModel
            ->select('mahasiswas.*, prodis.nama_prodi, prodis.kode_prodi, pts.perguruan_tinggi, pts.kode_pt')
            ->join('prodis', 'prodis.id = mahasiswas.id_prodi', 'left')
            ->join('pts', 'pts.id = mahasiswas.id_pt', 'left')
            ->join('pencairans', 'pencairans.id = mahasiswas.id_pencairan', 'left')
            ->where('pencairans.status', 'Selesai')
            ->get();

        if (!class_exists(Spreadsheet::class)) {
            $filename = 'mahasiswa_selesai_admin_' . date('Ymd_His') . '.csv';

            return response()->streamDownload(function () use ($data) {
                $output = fopen('php://output', 'w');
                fputcsv($output, ['NIM', 'Nama', 'Program Studi', 'Kode Prodi', 'Perguruan Tinggi', 'Jenis Bantuan', 'Nominal', 'Jenjang', 'Angkatan', 'Pembaruan Status', 'Status Pengajuan']);

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

        // Header
        $headers = ['NIM', 'Nama', 'Program Studi', 'Kode Prodi', 'Perguruan Tinggi', 'Jenis Bantuan', 'Nominal', 'Jenjang', 'Angkatan', 'Pembaruan Status', 'Status Pengajuan'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Data
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

        // Tambahkan border ke semua sel yang digunakan
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'wrapText' => true,
            ],
        ];

        $sheet->getStyle('A1:K' . $lastRow)->applyFromArray($styleArray);

        // Set auto width per kolom
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Nama file
        $filename = 'mahasiswa_selesai_admin_' . date('Ymd_His') . '.xlsx';

        // Header untuk download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function unduhMahasiswa($id_pencairan)
    {
        $pencairanModel = new \App\Models\PencairanModel();
        $mahasiswaModel = new \App\Models\MahasiswaModel();

        $pencairan = $pencairanModel->find($id_pencairan);

        if (!$pencairan || $pencairan['status'] !== 'Selesai') {
            return back()->with('error', 'Data tidak ditemukan atau status belum selesai.');
        }

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
        $headers = ['NIM', 'Nama', 'Program Studi', 'Jenjang', 'Angkatan', 'Pembaruan Status'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Data
        $row = 2;
        foreach ($mahasiswaList as $mhs) {
            $sheet->setCellValue('A' . $row, $mhs['nim']);
            $sheet->setCellValue('B' . $row, $mhs['nama']);
            $sheet->setCellValue('C' . $row, $mhs['nama_prodi'] ?? '-');
            $sheet->setCellValue('D' . $row, $mhs['jenjang']);
            $sheet->setCellValue('E' . $row, $mhs['angkatan']);
            $sheet->setCellValue('F' . $row, $mhs['pembaruan_status']);
            $row++;
        }

        $lastRow = $row - 1;

        // Style border dan perataan
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'wrapText' => true,
            ],
        ];
        $sheet->getStyle('A1:F' . $lastRow)->applyFromArray($styleArray);

        // Bold dan rata tengah header
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFDDDDDD']
            ]
        ]);

        // Set kolom auto width
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Nama file
        $filename = 'mahasiswa_pencairan_' . $id_pencairan . '_' . date('Ymd_His') . '.xlsx';

        // Header unduh
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$filename\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function store()
    {
        $maxSize = 2097152; // 2MB dalam byte
        $folder = 'file/';

        // === SPTJM ===
        $sptjm = request()->file('sptjm');
        if ($sptjm && $sptjm->isValid()) {
            if ($sptjm->getSize() > $maxSize) {
                return back()->with('error', 'Ukuran file SPTJM melebihi 2MB.');
            }
            $newsptjm = $sptjm->hashName();
            $sptjm->move($folder, $newsptjm);
        } else {
            $newsptjm = null;
        }

        // === SK Penetapan ===
        $sk_penetapan = request()->file('sk_penetapan');
        if ($sk_penetapan && $sk_penetapan->isValid()) {
            if ($sk_penetapan->getSize() > $maxSize) {
                return back()->with('error', 'Ukuran file SK Penetapan melebihi 2MB.');
            }
            $newSk_penetapan = $sk_penetapan->hashName();
            $sk_penetapan->move($folder, $newSk_penetapan);
        } else {
            $newSk_penetapan = null;
        }

        // === SK Pembatalan ===
        $sk_pembatalan = request()->file('sk_pembatalan');
        if ($sk_pembatalan && $sk_pembatalan->isValid()) {
            if ($sk_pembatalan->getSize() > $maxSize) {
                return back()->with('error', 'Ukuran file SK Pembatalan melebihi 2MB.');
            }
            $newSk_pembatalan = $sk_pembatalan->hashName();
            $sk_pembatalan->move($folder, $newSk_pembatalan);
        } else {
            $newSk_pembatalan = null;
        }

        // === Berita Acara ===
        $berita_acara = request()->file('berita_acara');
        if ($berita_acara && $berita_acara->isValid()) {
            if ($berita_acara->getSize() > $maxSize) {
                return back()->with('error', 'Ukuran file Berita Acara melebihi 2MB.');
            }
            $newBerita_acara = $berita_acara->hashName();
            $berita_acara->move($folder, $newBerita_acara);
        } else {
            $newBerita_acara = null;
        }

        // === Surat Pengantar ===
        $surat_pengantar = request()->file('surat_pengantar');
        if ($surat_pengantar && $surat_pengantar->isValid()) {
            if ($surat_pengantar->getSize() > $maxSize) {
                return back()->with('error', 'Ukuran file Surat Pengantar melebihi 2MB.');
            }
            $newSurat_pengantar = $surat_pengantar->hashName();
            $surat_pengantar->move($folder, $newSurat_pengantar);
        } else {
            $newSurat_pengantar = null;
        }

        // Simpan ke database
        $idPt = (int) (request('id_pt') ?: session('pt'));
        $model = new \App\Models\Pencairan();
        $model->save([
            'id_pt' => $idPt,
            'periode' => request('periode'),
            'kategori_penerima' => request('kategori_penerima'),
            'tanggal_entry' => date('Y-m-d'),
            'no_sk' => request('no_surat_permohonan'),
            'tanggal' => request('tanggal'), // Ambil dari input form, bukan date('Y-m-d')
            'semester' => request('semester'),
            'status' => 'Ajukan Mahasiswa',
            'jumlah_mahasiswa' => 0,
            'sptjm' => $newsptjm,
            'sk_penetapan' => $newSk_penetapan,
            'sk_pembatalan' => $newSk_pembatalan,
            'berita_acara' => $newBerita_acara,
            'surat_pengantar' => $newSurat_pengantar,
            'nominal_pencairan' => request('nominal_pencairan') ? str_replace(['Rp', '.', ',', ' '], '', request('nominal_pencairan')) : null,
            'jenis_bantuan' => request('jenis_bantuan'),
            'keterangan' => request('keterangan'),
        ]);

        $pencairanId = $model->getInsertID();
        $ptName = DB::table('pts')->where('id', $idPt)->value('perguruan_tinggi') ?? 'Perguruan tinggi';

        NotificationService::notifyOperators(
            'Pencairan baru dibuat',
            "{$ptName} membuat draft permohonan pencairan untuk {$this->pencairanSummaryText(request('periode'), request('semester'), request('jenis_bantuan'))}.",
            'info',
            url('pencairan-detail/' . $pencairanId)
        );

        return redirect('verifikasi-mahasiswa/' . $pencairanId);
    }

    public function laporanHome()
    {
        $model = new \App\Models\PencairanModel();
        $pt = session('pt');

        // Ambil keyword dari input search di View
        $keyword = request('keyword');

        // Inisialisasi Query
        $query = $model->select('pts.id, pts.kode_pt, pts.perguruan_tinggi, userpts.status')
            ->join('pts', 'pts.id = pencairans.id_pt')
            ->join('userpts', 'userpts.id_pt = pts.id', 'left')
            ->where('pencairans.id_pt', $pt)
            ->distinct();

        // Logika Search
        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('pts.perguruan_tinggi', 'like', "%" . $keyword . "%")
                ->orWhere('pts.kode_pt', 'like', "%" . $keyword . "%");
            });
        }

        $data = [
            'title'   => 'Laporan Perguruan Tinggi',
            'keyword' => $keyword,
            'pts'     => $query->orderBy('pts.kode_pt', 'ASC')->paginate(10),
            'pager'   => $model->pager,
        ];

        return view('admin.laporan/home', $data);
    }

    public function laporan()
    {
        $model = new \App\Models\PencairanModel();

        // Ambil tahun dari GET atau default ke tahun sekarang
        $tahun = request('tahun') ?? date('Y');

        $builder = $model
            ->select('pencairans.*, pts.kode_pt, pts.perguruan_tinggi')
            ->join('pts', 'pts.id = pencairans.id_pt', 'left')
            ->where("YEAR(pencairans.tanggal_entry)", $tahun);

        $data = [
            'title' => 'Semua Laporan Pencairan',
            'pencairans' => $builder->orderBy('pencairans.id', 'DESC')->get(),
            'tahun_terpilih' => $tahun,
        ];

        return view('admin.laporan/list', $data);
    }

    public function laporanByPt($id_pt)
    {
        $model = new \App\Models\PencairanModel();
        $tahun = request('tahun');

        // Gunakan model langsung untuk inisiasi query agar paginate() bekerja maksimal
        $model->select('pencairans.*, pts.kode_pt, pts.perguruan_tinggi')
            ->join('pts', 'pts.id = pencairans.id_pt', 'left')
            ->where('pencairans.id_pt', $id_pt);

        if ($tahun) {
            $model->where("YEAR(pencairans.tanggal_entry)", $tahun);
        }

        $data = [
            'title'          => 'Laporan Pencairan PT',
            // Menggunakan paginate(10) untuk melimit 10 data per halaman
            'pencairans'     => $model->orderBy('pencairans.id', 'DESC')->paginate(10),
            // Mengambil objek pager untuk dikirim ke view
            'pager'          => $model->pager,
            'tahun_terpilih' => $tahun,
        ];

        return view('admin.laporan/list_by_pt', $data);
    }

    public function edit($id)
    {
        $model = new \App\Models\Pencairan();
        $periode = new \App\Models\Periode();
        $data = [
            'title' => 'Permohonan Pencairan',
            'data' => $model->find($id),
            'periode' => $periode->periode(),
        ];
        return view('admin.verifikasi_edit', $data);
    }

    public function draft()
    {
        $pt = session('pt');
        $pencairanModel = new \App\Models\PencairanModel();

        $data = [
            'title' => 'Draft Permohonan Pencairan',
            // Menggunakan paginate(10) untuk limit 10 data per halaman
            'draft' => $pencairanModel
                ->where('id_pt', $pt)
                ->where('status !=', 'Selesai')  // Saring agar 'Selesai' tidak muncul
                ->where('status !=', 'Diproses') // Saring agar 'Diproses' juga tidak muncul
                ->orderBy('id', 'DESC')
                ->paginate(10),
            'pager' => $pencairanModel->pager,
        ];

        return view('admin.pencairan_draft', $data);
    }

    public function update($id)
    {
        $model = new \App\Models\Pencairan();
        $dataLama = $model->find($id);

        $folder = 'file/';
        $maxSize = 2097152; // 2MB

        // === SPTJM ===
        $sptjm = request()->file('sptjm');
        if ($sptjm && $sptjm->isValid()) {
            if ($sptjm->getSize() > $maxSize) {
                return back()->with('error', 'Ukuran file SPTJM melebihi 2MB.');
            }
            if (!empty($dataLama['sptjm']) && file_exists($folder . $dataLama['sptjm'])) {
                unlink($folder . $dataLama['sptjm']);
            }
            $newsptjm = $sptjm->hashName();
            $sptjm->move($folder, $newsptjm);
        } else {
            $newsptjm = $dataLama['sptjm'];
        }

        // === SK PENETAPAN ===
        $sk_penetapan = request()->file('sk_penetapan');
        if ($sk_penetapan && $sk_penetapan->isValid()) {
            if ($sk_penetapan->getSize() > $maxSize) {
                return back()->with('error', 'Ukuran file SK Penetapan melebihi 2MB.');
            }
            if (!empty($dataLama['sk_penetapan']) && file_exists($folder . $dataLama['sk_penetapan'])) {
                unlink($folder . $dataLama['sk_penetapan']);
            }
            $newSk_penetapan = $sk_penetapan->hashName();
            $sk_penetapan->move($folder, $newSk_penetapan);
        } else {
            $newSk_penetapan = $dataLama['sk_penetapan'];
        }

        // === SK PEMBATALAN ===
        $sk_pembatalan = request()->file('sk_pembatalan');
        if ($sk_pembatalan && $sk_pembatalan->isValid()) {
            if ($sk_pembatalan->getSize() > $maxSize) {
                return back()->with('error', 'Ukuran file SK Pembatalan melebihi 2MB.');
            }
            if (!empty($dataLama['sk_pembatalan']) && file_exists($folder . $dataLama['sk_pembatalan'])) {
                unlink($folder . $dataLama['sk_pembatalan']);
            }
            $newSk_pembatalan = $sk_pembatalan->hashName();
            $sk_pembatalan->move($folder, $newSk_pembatalan);
        } else {
            $newSk_pembatalan = $dataLama['sk_pembatalan'];
        }

        // === BERITA ACARA ===
        $berita_acara = request()->file('berita_acara');
        if ($berita_acara && $berita_acara->isValid()) {
            if ($berita_acara->getSize() > $maxSize) {
                return back()->with('error', 'Ukuran file Berita Acara melebihi 2MB.');
            }
            if (!empty($dataLama['berita_acara']) && file_exists($folder . $dataLama['berita_acara'])) {
                unlink($folder . $dataLama['berita_acara']);
            }
            $newBerita_acara = $berita_acara->hashName();
            $berita_acara->move($folder, $newBerita_acara);
        } else {
            $newBerita_acara = $dataLama['berita_acara'];
        }

        // === SURAT PENGANTAR ===
        $surat_pengantar = request()->file('surat_pengantar');
        if ($surat_pengantar && $surat_pengantar->isValid()) {
            if ($surat_pengantar->getSize() > $maxSize) {
                return back()->with('error', 'Ukuran file Surat Pengantar melebihi 2MB.');
            }
            if (!empty($dataLama['surat_pengantar']) && file_exists($folder . $dataLama['surat_pengantar'])) {
                unlink($folder . $dataLama['surat_pengantar']);
            }
            $newSurat_pengantar = $surat_pengantar->hashName();
            $surat_pengantar->move($folder, $newSurat_pengantar);
        } else {
            $newSurat_pengantar = $dataLama['surat_pengantar'];
        }

        // Simpan ke database
        $model->update($id, [
            'kategori_penerima' => request('kategori_penerima'),
            'no_sk' => request('no_surat_permohonan'),
            'tanggal' => request('tanggal'), // Ambil dari input form
            'semester' => request('semester'),
            'sptjm' => $newsptjm,
            'sk_penetapan' => $newSk_penetapan,
            'sk_pembatalan' => $newSk_pembatalan,
            'berita_acara' => $newBerita_acara,
            'surat_pengantar' => $newSurat_pengantar,
            'nominal_pencairan' => request('nominal_pencairan') ? str_replace(['Rp', '.', ',', ' '], '', request('nominal_pencairan')) : null,
            'jenis_bantuan' => request('jenis_bantuan'),
            'keterangan' => request('keterangan'),
        ]);

        $ptName = DB::table('pts')->where('id', session('pt'))->value('perguruan_tinggi') ?? 'Perguruan tinggi';

        NotificationService::notifyOperators(
            'Permohonan pencairan diperbarui',
            "{$ptName} memperbarui data pencairan {$this->pencairanSummaryText($dataLama['periode'] ?? null, request('semester'), request('jenis_bantuan'))}.",
            'warning',
            url('pencairan-detail/' . $id)
        );

        return redirect('verifikasi-pembaharuan-status');
    }

    public function verifikasi_mahasiswa($id)
    {
        $pt = session('pt');
        $mahasiswaModel = new \App\Models\MahasiswaModel();

        // Ambil keyword pencarian
        $keyword = request('keyword');

        // Pastikan fungsi universitas di model Anda sudah mendukung parameter keyword
        // Jika belum, Anda bisa menambahkan filter LIKE di sini sebelum paginate
        if ($keyword) {
            $mahasiswaModel->where(function($q) use ($keyword) {
                $q->where('mahasiswas.nama', 'like', "%" . $keyword . "%")
                ->orWhere('mahasiswas.nim', 'like', "%" . $keyword . "%");
            });
        }

        $pencairanModel = new \App\Models\PencairanModel();
        $pencairan = $pencairanModel->find($id);

        $listMahasiswa = $mahasiswaModel->universitas($pt, $id, 10);

        $data = [
            'title'         => 'Ajukan Mahasiswa',
            'id_pencairan'  => $id,
            'pencairan'     => $pencairan,
            'mahasiswa'     => $listMahasiswa,
            'pager'         => $mahasiswaModel->pager,
            'keyword'       => $keyword // Kirim ke view agar input tetap terisi
        ];

        return view('admin.verifikasi_2', $data);
    }

    public function sync_mahasiswa()
    {
        $json = $this->request->getJSON();
        $model = new \App\Models\MahasiswaModel();

        $ids = $json->selected_ids;
        $idPencairan = $json->id_pencairan;
        $isChecked = $json->checked;

        if (!empty($ids)) {
            $dataUpdate = [
                'id_pencairan'     => $isChecked ? $idPencairan : null,
                // Perubahan di sini: jika false, set ke 'Belum Diajukan'
                'status_pengajuan' => $isChecked ? 'Proses Pengajuan' : 'Belum Diajukan'
            ];

            // Update database secara massal untuk ID yang dikirim
            $model->whereIn('id', $ids)->set($dataUpdate)->update();
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function ajukanMahasiswa()
    {
        if ($this->request->isAJAX()) {
            $data = $this->request->getJSON();
            $selected = $data->selected ?? [];
            $all = $data->all ?? [];
            $idPencairan = $data->id_pencairan ?? null;

            if (!$idPencairan) {
                return $this->response->setJSON(['success' => false, 'message' => 'ID Pencairan tidak ditemukan.']);
            }

            $model = new \App\Models\Mahasiswa();
            DB::beginTransaction();

            // 1. Validasi Ganda
            if (!empty($selected)) {
                $isUsed = $model->whereIn('id', $selected)
                    ->where('status_pengajuan', 'Diajukan')
                    ->first();
                if ($isUsed) {
                    DB::rollBack();
                    return $this->response->setJSON(['success' => false, 'message' => 'Salah satu mahasiswa sudah terdaftar di pengajuan Final.']);
                }
            }

            // 2. Reset status mahasiswa yang batal dicentang
            if (!empty($all)) {
                $notSelected = array_diff($all, $selected);
                if (!empty($notSelected)) {
                    $model->whereIn('id', $notSelected)
                        ->where('id_pencairan', $idPencairan)
                        ->set([
                            'status_pengajuan' => 'Belum Diajukan', // Memastikan kembali ke status awal
                            'id_pencairan' => null
                        ])
                        ->update();
                }
            }

            // 3. Update mahasiswa yang terpilih
            if (!empty($selected)) {
                $model->whereIn('id', $selected)
                    ->set(['status_pengajuan' => 'Proses Pengajuan', 'id_pencairan' => $idPencairan])
                    ->update();
            }

            // 4 & 5. Hitung ulang dan Update Tabel Pencairan
            $jumlahRiil = $model->where('id_pencairan', $idPencairan)->countAllResults();
            $pencairanModel = new \App\Models\PencairanModel();
            $pencairanModel->update($idPencairan, [
                'status' => 'Finalisasi',
                'jumlah_mahasiswa' => $jumlahRiil
            ]);

            DB::commit();

            $pencairan = $pencairanModel->find($idPencairan);
            $ptName = DB::table('pts')->where('id', session('pt'))->value('perguruan_tinggi') ?? 'Perguruan tinggi';

            NotificationService::notifyOperators(
                'Daftar mahasiswa pencairan diperbarui',
                "{$ptName} mengajukan {$jumlahRiil} mahasiswa pada permohonan pencairan {$this->pencairanSummaryText($pencairan['periode'] ?? null, $pencairan['semester'] ?? null, $pencairan['jenis_bantuan'] ?? null)}.",
                'warning',
                url('pencairan-detail/' . $idPencairan)
            );

            return $this->response->setJSON([
                'success' => true,
                'redirect' => base_url('finalisasi-verifikasi/' . $idPencairan),
            ]);
        }
    }

    public function finalisasi_verifikasi($id)
    {
        $mahasiswaModel = new \App\Models\Mahasiswa();

        // Ambil keyword dari input search
        $keyword = $this->request->getVar('keyword');

        $pt = (array) DB::table('pts')
            ->where('id', session('pt'))
            ->first();

        $jumlahTotal = DB::table('mahasiswas')->where('id_pencairan', $id)->count();

        $pencairanModel = new \App\Models\PencairanModel();
        $pencairan = $pencairanModel->find($id);

        $data = [
            'mahasiswa'    => $mahasiswaModel->verifikasi($id, $keyword),
            'title'        => 'Finalisasi Verifikasi',
            'id_pencairan' => $id,
            'pencairan'    => $pencairan,
            'pager'        => $mahasiswaModel->pager,
            'jumlah'       => $jumlahTotal,
            'pt'           => $pt,
            'keyword'      => $keyword // Kirim ke view agar input tidak kosong setelah refresh
        ];

        return view('admin.verifikasi_3', $data);
    }

    public function verifikasi_final($id)
    {
        DB::beginTransaction();

        $pencairanModel = new \App\Models\PencairanModel();
        $mahasiswaModel = new \App\Models\Mahasiswa();

        // Hitung ulang sebelum benar-benar diproses
        $jumlah = $mahasiswaModel->where('id_pencairan', $id)->countAllResults();

        if ($jumlah <= 0) {
            DB::rollBack();
            return back()->with('error', 'Gagal: Jumlah mahasiswa 0. Silakan pilih mahasiswa kembali.');
        }

        // Update status mahasiswa secara massal
        $mahasiswaModel->where('id_pencairan', $id)
            ->set(['status_pengajuan' => 'Diajukan'])
            ->update();

        // Finalisasi status pencairan
        $pencairanModel->update($id, [
            'status' => 'Diproses',
            'jumlah_mahasiswa' => $jumlah
        ]);

        DB::commit();

        $pencairan = $pencairanModel->find($id);
        $ptName = DB::table('pts')->where('id', session('pt'))->value('perguruan_tinggi') ?? 'Perguruan tinggi';

        NotificationService::notifyOperators(
            'Permohonan siap diverifikasi',
            "{$ptName} mengirim finalisasi pencairan dengan {$jumlah} mahasiswa untuk {$this->pencairanSummaryText($pencairan['periode'] ?? null, $pencairan['semester'] ?? null, $pencairan['jenis_bantuan'] ?? null)}.",
            'primary',
            url('pencairan-detail/' . $id)
        );

        return redirect('verifikasi-pembaharuan-status')->with('success', 'Berhasil diajukan ke pusat.');
    }

    private function pencairanSummaryText(?string $periode, ?string $semester, ?string $jenisBantuan): string
    {
        return collect([$semester, $periode, $jenisBantuan])
            ->filter()
            ->implode(' - ') ?: 'periode pencairan terkait';
    }

    public function delete($id)
    {
        $model = new \App\Models\Pencairan();
        $mahasiswaModel = new \App\Models\MahasiswaModel();

        // 1. Ambil data pencairan untuk menghapus file fisik
        $data = $model->find($id);

        if ($data) {
            DB::beginTransaction(); // Gunakan transaksi agar proses reset & hapus sinkron

            // 2. RESET RELASI MAHASISWA
            // Mengembalikan status mahasiswa agar bisa dipilih kembali di pengajuan baru
            $mahasiswaModel->where('id_pencairan', $id)
                ->set([
                    'id_pencairan'     => null,
                    'status_pengajuan' => 'Belum Diajukan'
                ])
                ->update();

            // 3. HAPUS FILE FISIK
            $folder = 'file/';
            $fields = ['sptjm', 'sk_penetapan', 'sk_pembatalan', 'berita_acara', 'surat_pengantar'];

            foreach ($fields as $field) {
                if (!empty($data[$field]) && file_exists($folder . $data[$field])) {
                    unlink($folder . $data[$field]);
                }
            }

            // 4. HAPUS DATA PENCAIRAN DARI DATABASE
            $model->where('id', $id)->delete();

            DB::commit();

            $ptName = DB::table('pts')->where('id', $data['id_pt'])->value('perguruan_tinggi') ?? 'Perguruan tinggi';

            NotificationService::notifyOperators(
                'Draft pencairan dihapus',
                "{$ptName} menghapus draft permohonan pencairan {$this->pencairanSummaryText($data['periode'] ?? null, $data['semester'] ?? null, $data['jenis_bantuan'] ?? null)}.",
                'danger',
                url('pencairan-list')
            );
        }

        return redirect('verifikasi-pembaharuan-status')->with('success', 'Draft dihapus dan mahasiswa dikembalikan ke daftar.');
    }

    public function export_mahasiswa($id)
    {
        $model = new \App\Models\Mahasiswa();
        $data = $model->pencairan($id);

        if (!class_exists(Spreadsheet::class)) {
            $filename = 'data_mahasiswa_' . date('Ymd_His') . '.csv';
            $rows = [];
            $no = 1;

            foreach ($data as $item) {
                $rows[] = [
                    $no,
                    $item['kode_pt'],
                    $item['perguruan_tinggi'],
                    $item['nim'],
                    $item['nama'],
                    $item['kode_prodi'],
                    $item['nama_prodi'],
                    $item['jenjang'],
                    $item['angkatan'],
                    $item['pembaruan_status'],
                ];
                $no++;
            }

            return SpreadsheetFile::streamCsv($filename, [
                'NO',
                'KODE PTS',
                'PTS',
                'NIM',
                'NAMA',
                'KODE PRODI',
                'NAMA PRODI',
                'JENJANG',
                'ANGKATAN',
                'PEMBARUAN STATUS',
            ], $rows);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'NO');
        $sheet->setCellValue('B1', 'KODE PTS');
        $sheet->setCellValue('C1', 'PTS');
        $sheet->setCellValue('D1', 'NIM');
        $sheet->setCellValue('E1', 'NAMA');
        $sheet->setCellValue('F1', 'KODE PRODI');
        $sheet->setCellValue('G1', 'NAMA PRODI');
        $sheet->setCellValue('H1', 'JENJANG');
        $sheet->setCellValue('I1', 'ANGKATAN');
        $sheet->setCellValue('J1', 'PEMBARUAN STATUS');
        $row = 2;
        $no = 1;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item['kode_pt']);
            $sheet->setCellValue('C' . $row, $item['perguruan_tinggi']);
            $sheet->setCellValue('D' . $row, $item['nim']);
            $sheet->setCellValue('E' . $row, $item['nama']);
            $sheet->setCellValue('F' . $row, $item['kode_prodi']);
            $sheet->setCellValue('G' . $row, $item['nama_prodi']);
            $sheet->setCellValue('H' . $row, $item['jenjang']);
            $sheet->setCellValue('I' . $row, $item['angkatan']);
            $sheet->setCellValue('J' . $row, $item['pembaruan_status']);
            $row++;
            $no++;
        }

        $filename = 'data_mahasiswa.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }

    public function detail($id)
    {
        $model = new \App\Models\Pencairan();
        $data = $model->find($id);

        // Tangkap keyword dari query string (?keyword=...)
        $keyword = request('keyword');

        $mahasiswaModel = new \App\Models\Mahasiswa();
        $dataMahasiswa = $mahasiswaModel->pencairan($id, $keyword);

        $dataView = [
            'data'      => $data,
            'title'     => 'Detail Pencairan',
            'keyword'   => $keyword,
            // Kirim keyword ke method pencairan di model
            'mahasiswa' => $dataMahasiswa,
            'dataMahasiswa' => $dataMahasiswa,
            'pager'     => $mahasiswaModel->pager,
            'jumlah'    => $mahasiswaModel->where('id_pencairan', $id)->countAllResults()
        ];

        return view('Admin.verifikasi_detail', $dataView);
    }
}
