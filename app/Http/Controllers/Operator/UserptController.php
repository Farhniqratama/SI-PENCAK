<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;

use App\Controllers\Operator\BaseOperatorController;
use App\Models\Pt;
use App\Models\Userpt;
use App\Support\SpreadsheetFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserptController extends Controller
{
    public function index()
    {
        $model = new \App\Models\Userpt();
        $search = request('search');

        if ($search) {
            $model->where(function($q) use ($search) {
                $q->where('username', 'like', "%" . $search . "%")
                ->orWhere('perguruan_tinggi', 'like', "%" . $search . "%");
            });
        }

        $data = [
            'data'   => $model->WithPtPager(6), // Menggunakan pager dengan limit 6 sesuai konfigurasi Anda
            'pager'  => $model->pager,
            'title'  => 'Manajemen User',
            'search' => $search
        ];

        return view('operator.userpt_list', $data);
    }

    public function create()
    {
        $pt = new \App\Models\Pt();
        $data = [
            'btn' => 'add',
            'act' => '/userpt-store',
            'sub' => 'Tambah',
            'title' => 'Tambah User',
            'pt' => $pt->get(),
        ];
        return view('operator.userpt_form', $data);
    }

    public function store()
    {
        if (
            !$this->validate([
                'password' => 'required|min_length[6]|matches[password_confirm]',
                'password_confirm' => 'required',
            ])
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new \App\Models\Userpt();
        $model->save([
            'id_pt' => request('id_pt'),
            'username' => request('username'),
            'password' => password_hash(request('password'), PASSWORD_DEFAULT),
            'penanggung_jawab' => request('penanggung_jawab'),
            'nip' => request('nip'),
            'kontak' => request('kontak'),
            'email' => request('email'),
            'status' => request('status'),
        ]);
        return redirect('userpt-list');
    }

    public function edit($id)
    {
        $pt = new \App\Models\Pt();
        $model = new \App\Models\Userpt();
        $data = [
            'btn' => 'edit',
            'act' => '/userpt-update/' . $id,
            'sub' => 'Edit',
            'title' => 'Edit User',
            'data' => $model->find($id),
            'pt' => $pt->get(),
        ];
        return view('operator.userpt_form', $data);
    }

    public function import()
    {
        $file = request()->file('excel');

        if ($file && $file->isValid()) {
            try {
                $sheet = SpreadsheetFile::rowsFromUpload($file);
            } catch (\Throwable $e) {
                return redirect('userpt-list')->with('error', 'Gagal membaca file: ' . $e->getMessage());
            }

            $ptModel = new \App\Models\Pt();

            $total = 0;
            foreach (array_slice($sheet, 1) as $row) {
                $kodePt = trim($row[0] ?? '');
                $username = trim($row[1] ?? '');
                $pt = $ptModel->where('kode_pt', $kodePt)->first();

                if ($pt && $username) {
                    \Illuminate\Support\Facades\DB::table('userpts')->updateOrInsert(['username' => $username], [
                        'id_pt' => $pt['id'],
                        'username' => $username,
                        'password' => password_hash($row[2] ?? 'password', PASSWORD_DEFAULT),
                        'penanggung_jawab' => trim($row[3] ?? ''),
                        'nip' => trim($row[4] ?? '-'),
                        'kontak' => trim($row[5] ?? '-'),
                        'email' => trim($row[6] ?? ''),
                        'status' => strtolower(trim($row[7] ?? 'aktif')) === 'aktif' ? 'aktif' : 'nonaktif',
                    ]);
                    $total++;
                }
            }

            // Notifikasi sesuai hasil
            if ($total > 0) {
                return redirect('userpt-list')->with('success', "$total user berhasil diimpor dari Excel.");
            } else {
                return redirect('userpt-list')->with('error', 'Tidak ada data yang berhasil diimpor. Pastikan kode_pt cocok dengan database.');
            }
        }

        // Gagal upload file
        return redirect('userpt-list')->with('error', 'Gagal mengunggah file. Pastikan file Excel valid dan belum dipindahkan.');
    }

    public function update($id)
    {
        $password = request('password');
        $password_confirm = request('password_confirm');

        if (!empty($password)) {
            if (
                !$this->validate([
                    'password' => 'required|min_length[6]|matches[password_confirm]',
                    'password_confirm' => 'required',
                ])
            ) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }
        }

        $data = [
            'id_pt' => request('id_pt'),
            'username' => request('username'),
            'penanggung_jawab' => request('penanggung_jawab'),
            'nip' => request('nip'),
            'kontak' => request('kontak'),
            'email' => request('email'),
            'status' => request('status'),
        ];

        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $model = new \App\Models\Userpt();
        $model->where('id', $id)->update($data);

        return redirect('userpt-list');
    }

    public function show($id)
    {
        $model = new \App\Models\Userpt();
        $data = [
            'data' => $model->findWith($id),
            'title' => 'Detail User',
        ];
        return view('operator.userpt_show', $data);
    }

    public function delete($id)
    {
        $model = new \App\Models\Userpt();
        $model->where('id', $id)->delete();
        return redirect('userpt-list');
    }
}
