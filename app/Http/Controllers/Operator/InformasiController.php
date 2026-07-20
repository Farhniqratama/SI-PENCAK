<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;

use App\Controllers\Operator\BaseOperatorController;
use App\Models\Informasi;

class InformasiController extends Controller
{

    public function index()
    {
        $model = new \App\Models\Informasi();

        // Ambil input dari filter form (GET)
        $search    = request('search');
        $startDate = request('start_date');
        $endDate   = request('end_date');

        // Logika Filter Search Box
        if (!empty($search)) {
            $model->where(function($q) use ($search) {
                $q->where('judul', 'like', "%" . $search . "%")
                ->orWhere('deskripsi', 'like', "%" . $search . "%");
            });
        }

        // Logika Filter Rentang Tanggal
        if (!empty($startDate) && !empty($endDate)) {
            $model->where('tanggal >=', $startDate)
                ->where('tanggal <=', $endDate);
        } elseif (!empty($startDate)) {
            $model->where('tanggal', $startDate);
        }

        $data = [
            'data'       => $model->orderBy('tanggal', 'DESC')->paginate(10),
            'pager'      => $model->pager,
            'title'      => 'Manajemen Informasi',
            'search'     => $search,
            'start_date' => $startDate,
            'end_date'   => $endDate
        ];

        return view('operator.informasi_list', $data);
    }

    public function create()
    {
        $data = [
            'btn' => 'add',
            'act' => '/informasi-store',
            'sub' => 'Tambah',
            'title' => 'Tambah Informasi',
        ];
        return view('operator.informasi_form', $data);
    }

    public function store()
    {
        $file = request()->file('file');
        if ($file && $file->isValid()) {
            $newName = $file->hashName();
            $file->move('informasi', $newName);
        } else {
            $newName = null;
        }

        $model = new \App\Models\Informasi();
        $model->save([
            'judul' => request('judul'),
            'deskripsi' => request('deskripsi'),
            'file' => $newName,
            'tanggal' => date('Y-m-d'),
        ]);

        return redirect('informasi-list');
    }

    public function edit($id)
    {
        $model = new \App\Models\Informasi();
        $data = [
            'btn' => 'edit',
            'act' => '/informasi-update/' . $id,
            'sub' => 'Edit',
            'title' => 'Edit Informasi',
            'data' => $model->find($id),
        ];
        return view('operator.informasi_form', $data);
    }

    public function update($id)
    {
        $model = new \App\Models\Informasi();

        $oldData = $model->find($id);
        $oldFile = $oldData['file'];

        $file = request()->file('file');
        $newName = $oldFile;

        if ($file && $file->isValid()) {
            $newName = $file->hashName();
            $file->move('informasi', $newName);
            if ($oldFile && file_exists('informasi/' . $oldFile)) {
                unlink('informasi/' . $oldFile);
            }
        }

        $data = [
            'judul' => request('judul'),
            'deskripsi' => request('deskripsi'),
            'file' => $newName,
        ];

        $model->where('id', $id)->update($data);

        return redirect('informasi-list');
    }

    public function show($id)
    {
        $model = new \App\Models\Informasi();
        $data = [
            'data' => $model->find($id),
            'title' => 'Detail Informasi',
        ];
        return view('operator.informasi_show', $data);
    }

    public function delete($id)
    {
        $model = new \App\Models\Informasi();
        $model->where('id', $id)->delete();
        return redirect('informasi-list');
    }
}
