<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prodi;
use App\Models\Pt;
use App\Support\SpreadsheetFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProdiController extends Controller
{
    public function index()
    {
        $model = new \App\Models\Prodi();
        $id_pt = session('pt');

        // Ambil kata kunci pencarian dari URL (GET)
        $keyword = request('keyword');

        if ($keyword) {
            $model->where(function($q) use ($keyword) {
                $q->where('kode_prodi', 'like', "%" . $keyword . "%")
                ->orWhere('nama_prodi', 'like', "%" . $keyword . "%");
            });
        }

        $data = [
            'data'    => $model->where('id_pt', $id_pt)->paginate(10),
            'pager'   => $model->pager,
            'title'   => 'Manajemen Prodi',
            'keyword' => $keyword // Kirim balik ke view untuk mengisi value input
        ];

        return view('admin.prodi_list', $data);
    }

    public function create()
    {
        $data = [
            'btn' => 'add',
            'act' => '/prodi-store',
            'sub' => 'Tambah',
            'title' => 'Tambah Prodi',
        ];
        return view('admin.prodi_form', $data);
    }

    public function store()
    {
        $model = new \App\Models\Prodi();
        $model->save([
            'kode_prodi'  => request('kode_prodi'),
            'nama_prodi'  => request('nama_prodi'),
            'id_pt'       => request('id_pt'),
        ]);
        return redirect('prodi-list');
    }

    public function edit($id)
    {
        $model = new \App\Models\Prodi();
        $data = [
            'btn'   => 'edit',
            'act'   => '/prodi-update/' . $id,
            'sub'   => 'Edit',
            'title' => 'Edit Prodi',
            'data'  => $model->find($id),
        ];
        return view('admin.prodi_form', $data);
    }

    public function update($id)
    {
        $model = new \App\Models\Prodi();
        $model->update($id, [
            'kode_prodi'  => request('kode_prodi'),
            'nama_prodi'  => request('nama_prodi'),
        ]);
        return redirect('prodi-list');
    }

    public function delete($id)
    {
        $model = new \App\Models\Prodi();
        $model->where('id', $id)->delete();
        return redirect('prodi-list');
    }

    public function import()
    {
        $file = request()->file('excel');

        if ($file && $file->isValid()) {
            try {
                $sheet = SpreadsheetFile::rowsFromUpload($file);

                $ptModel    = new \App\Models\Pt();

                $imported = 0;
                $skipped  = 0;

                foreach (array_slice($sheet, 1) as $row) {
                    $kode_pt    = trim($row[0] ?? '');
                    $kode_prodi = trim($row[1] ?? '');
                    $nama_prodi = trim($row[2] ?? '');

                    if ($kode_pt && $kode_prodi && $nama_prodi) {
                        $pt = $ptModel->where('kode_pt', $kode_pt)->first();

                        if ($pt) {
                            \Illuminate\Support\Facades\DB::table('prodis')->updateOrInsert([
                                'id_pt' => $pt['id'],
                                'kode_prodi' => $kode_prodi,
                            ], [
                                'id_pt'       => $pt['id'],
                                'kode_prodi'  => $kode_prodi,
                                'nama_prodi'  => $nama_prodi,
                            ]);
                            $imported++;
                        } else {
                            $skipped++;
                        }
                    }
                }

                $message = "$imported Prodi berhasil diimpor.";
                if ($skipped > 0) {
                    $message .= " $skipped baris dilewati karena kode_pt tidak ditemukan.";
                }

                return redirect('prodi-list')->with('success', $message);
            } catch (\Exception $e) {
                return redirect('prodi-list')->with('error', 'Gagal memproses file: ' . $e->getMessage());
            }
        }

        return redirect('prodi-list')->with('error', 'File tidak valid atau gagal diunggah.');
    }
}
