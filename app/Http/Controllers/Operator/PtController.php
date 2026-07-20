<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;

use App\Controllers\Operator\BaseOperatorController;
use App\Models\Pt;
use App\Support\SpreadsheetFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

class PtController extends Controller
{
    public function index()
    {
        $model = new \App\Models\Pt();

        // Mengambil input pencarian dari URL
        $search = request('search');

        if (!empty($search)) {
            $model->where(function($q) use ($search) {
                $q->where('kode_pt', 'like', "%" . $search . "%")
                ->orWhere('perguruan_tinggi', 'like', "%" . $search . "%")
                ->orWhere('aipt', 'like', "%" . $search . "%");
            });
        }

        // Menggunakan paginate (6 data per halaman sesuai preferensi)
        $data = [
            'data'   => $model->paginate(10),
            'pager'  => $model->pager,
            'title'  => 'Manajemen Perguruan Tinggi',
            'search' => $search
        ];

        return view('operator.pt_list', $data);
    }

    public function create()
    {
        $data = [
            'btn' => 'add',
            'act' => '/pt-store',
            'sub' => 'Tambah',
            'title' => 'Tambah Perguruan Tinggi',
        ];
        return view('operator.pt_form', $data);
    }

    public function uploadExcel()
    {
        $file = request()->file('excel_file');

        if (!$file || !$file->isValid()) {
            return back()->with('error', 'File tidak valid atau gagal diunggah.');
        }

        try {
            $rows = SpreadsheetFile::rowsFromUpload($file);

            if (empty($rows) || count($rows[0]) < 3) {
                return back()->with('error', 'Format Excel tidak sesuai. Kolom harus: kode_pt, perguruan_tinggi, aipt.');
            }

            $header = array_map('strtolower', $rows[0]);
            $expected = ['kode_pt', 'perguruan_tinggi', 'aipt'];

            if ($header !== $expected) {
                return back()->with('error', 'Header kolom Excel tidak sesuai. Gunakan template sesuai tabel.');
            }

            $inserted = 0;

            foreach ($rows as $index => $row) {
                if ($index === 0) continue;

                $kodePt = trim($row[0] ?? '');
                $nama = trim($row[1] ?? '');
                $aipt = trim($row[2] ?? '');

                if ($kodePt && $nama && $aipt) {
                    \Illuminate\Support\Facades\DB::table('pts')->updateOrInsert(['kode_pt' => $kodePt], [
                        'kode_pt' => $kodePt,
                        'perguruan_tinggi' => $nama,
                        'aipt' => $aipt
                    ]);
                    $inserted++;
                }
            }

            return redirect('pt-list')->with('success', "$inserted data berhasil diimpor.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
    }

    public function store()
    {
        $model = new \App\Models\Pt();
        $model->save([
            'kode_pt'  => request('kode_pt'),
            'perguruan_tinggi' => request('perguruan_tinggi'),
            'aipt' => request('aipt'),
        ]);
        return redirect('pt-list');
    }

    public function edit($id)
    {
        $model = new \App\Models\Pt();
        $data = [
            'btn' => 'edit',
            'act' => '/pt-update/' . $id,
            'sub' => 'Edit',
            'title' => 'Edit Perguruan Tinggi',
            'data' => $model->find($id),
        ];
        return view('operator.pt_form', $data);
    }

    public function update($id)
    {
        $model = new \App\Models\Pt();
        $model->update($id, [
            'kode_pt'  => request('kode_pt'),
            'perguruan_tinggi' => request('perguruan_tinggi'),
            'aipt' => request('aipt'),
        ]);
        return redirect('pt-list');
    }

    public function delete($id)
    {
        $model = new \App\Models\Pt();
        $model->where('id', $id)->delete();
        return redirect('pt-list');
    }
}
