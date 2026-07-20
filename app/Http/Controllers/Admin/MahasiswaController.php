<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\Prodi;
use App\Models\Mahasiswa;
use App\Support\SpreadsheetFile;

class MahasiswaController extends Controller
{
    public function index()
    {
        $model = new \App\Models\Mahasiswa();
        $idPpt = session('pt');

        // Ambil input keyword dari pencarian
        $keyword = request('keyword');

        $model->select('mahasiswas.*, prodis.nama_prodi')
            ->join('prodis', 'prodis.id = mahasiswas.id_prodi', 'left')
            ->where('mahasiswas.id_pt', $idPpt);

        // Tambahkan logika pencarian jika keyword ada
        if ($keyword) {
            $model->where(function($q) use ($keyword) {
                $q->where('mahasiswas.nim', 'like', "%" . $keyword . "%")
                ->orWhere('mahasiswas.nama', 'like', "%" . $keyword . "%")
                ->orWhere('prodis.nama_prodi', 'like', "%" . $keyword . "%");
            });
        }

        $data = [
            'data'    => $model->paginate(10),
            'pager'   => $model->pager,
            'title'   => 'Manajemen Mahasiswa',
            'keyword' => $keyword // Kirim balik ke view
        ];

        return view('admin.mahasiswa_list', $data);
    }

    // --- Method create, store, edit, update tetap sama ---

    public function create()
    {
        $prodi = new \App\Models\Prodi();
        $data = [
            'btn' => 'add',
            'act' => '/mahasiswa-store',
            'sub' => 'Tambah',
            'title' => 'Tambah Mahasiswa',
            'prodi' => $prodi->Cari(session('pt')),
        ];
        return view('admin.mahasiswa_form', $data);
    }

    public function store()
    {
        $model = new \App\Models\Mahasiswa();
        $model->save([
            'id_pt' => request('id_pt'),
            'id_prodi' => request('id_prodi'),
            'nim' => request('nim'),
            'nama' => request('nama'),
            'jenjang' => request('jenjang'),
            'angkatan' => request('angkatan'),
            'kategori' => request('kategori'),
            'pembaruan_status' => 'Tetap',
            'status_pengajuan' => 'Belum Diajukan',
        ]);
        return redirect('mahasiswa-list');
    }

    public function edit($id)
    {
        $prodi = new \App\Models\Prodi();
        $model = new \App\Models\Mahasiswa();
        $data = [
            'btn' => 'edit',
            'act' => '/mahasiswa-update/' . $id,
            'sub' => 'Edit',
            'title' => 'Edit mahasiswa',
            'data' => $model->find($id),
            'prodi' => $prodi->Cari(session('pt')),
        ];
        return view('admin.mahasiswa_form', $data);
    }

    public function update($id)
    {
        $model = new \App\Models\Mahasiswa();
        $model->update($id, [
            'id_pt' => request('id_pt'),
            'id_prodi' => request('id_prodi'),
            'nim' => request('nim'),
            'nama' => request('nama'),
            'jenjang' => request('jenjang'),
            'angkatan' => request('angkatan'),
            'kategori' => request('kategori'),
            'pembaruan_status' => 'Tetap',
            'status_pengajuan' => 'Belum Diajukan',
        ]);
        return redirect('mahasiswa-list');
    }

    public function show($id)
    {
        $model = new \App\Models\MahasiswaModel();

        // Sesuaikan pts.nama_pt menjadi pts.perguruan_tinggi berdasarkan PtModel Anda
        $mahasiswa = $model->select('mahasiswas.*, prodis.nama_prodi, prodis.kode_prodi, pts.perguruan_tinggi')
            ->join('prodis', 'prodis.id = mahasiswas.id_prodi', 'left')
            ->join('pts', 'pts.id = mahasiswas.id_pt', 'left')
            ->find($id);

        if (!$mahasiswa) {
            return redirect('mahasiswa-list')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $data = [
            'title' => 'Detail Mahasiswa - ' . $mahasiswa['nama'],
            'data'  => $mahasiswa,
        ];

        return view('admin.mahasiswa_show', $data);
    }

    public function import()
    {
        $file = request()->file('excel');
        if ($file && $file->isValid()) {
            try {
                $sheet = SpreadsheetFile::rowsFromUpload($file);

                $prodiModel = new \App\Models\Prodi();
                $total = 0;
                $gagal = 0;

                foreach (array_slice($sheet, 1) as $row) {
                    $kodeProdi = trim($row[0] ?? '');
                    $nim = trim($row[1] ?? '');
                    $prodi = $prodiModel->where('kode_prodi', $kodeProdi)->where('id_pt', session('pt'))->first();

                    if ($prodi && $nim) {
                        \Illuminate\Support\Facades\DB::table('mahasiswas')->updateOrInsert(['nim' => $nim], [
                            'id_pt' => session('pt'),
                            'id_prodi' => $prodi['id'],
                            'nim' => $nim,
                            'nama' => trim($row[2] ?? ''),
                            'jenjang' => trim($row[3] ?? 'S1'),
                            'angkatan' => trim($row[4] ?? date('Y')),
                            'kategori' => trim($row[5] ?? 'Skema Biaya Pendidikan'),
                            'pembaruan_status' => 'Tetap',
                            'status_pengajuan' => 'Belum Diajukan',
                        ]);
                        $total++;
                    } else {
                        $gagal++;
                    }
                }
                return redirect('mahasiswa-list')->with('success', "$total mahasiswa berhasil diimpor.");
            } catch (\Throwable $e) {
                return redirect('mahasiswa-list')->with('error', 'Gagal: ' . $e->getMessage());
            }
        }
        return redirect('mahasiswa-list')->with('error', 'File tidak valid.');
    }

    public function updateStatus()
    {
        if ($this->request->isAJAX()) {
            $data = $this->request->getJSON();
            $model = new \App\Models\Mahasiswa();
            $updated = $model->update($data->id, [
                'pembaruan_status' => $data->pembaruan_status,
            ]);
            return $this->response->setJSON(['success' => $updated]);
        }
        return $this->response->setStatusCode(400);
    }

    public function delete($id)
    {
        $model = new \App\Models\Mahasiswa();
        $model->where('id', $id)->delete();
        return redirect('mahasiswa-list');
    }
}
