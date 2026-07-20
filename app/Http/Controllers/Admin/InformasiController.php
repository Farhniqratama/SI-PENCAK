<?php

namespace App\Http\Controllers\Admin;

use App\Models\Informasi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InformasiController extends Controller
{
    public function index()
    {
        $model = new \App\Models\Informasi();

        $keyword = request('keyword');

        if (!empty($keyword)) {
            $model->where(function($q) use ($keyword) {
                $q->where('judul', 'like', "%" . $keyword . "%")
                ->orWhere('deskripsi', 'like', "%" . $keyword . "%");
            });
        }

        $data = [
            'title'   => 'Papan Informasi',
            // Mengurutkan berdasarkan tanggal terbaru (DESC) agar muncul di page 1
            'data'    => $model->orderBy('tanggal', 'DESC')->paginate(10),
            'pager'   => $model->pager,
            'keyword' => $keyword
        ];

        return view('admin.informasi_list', $data);
    }

    public function show($id)
    {
        $model = new \App\Models\Informasi();
        $data = [
            'data'  => $model->find($id),
            'title' => 'Detail Informasi',
        ];
        return view('admin.informasi_detail', $data);
    }
}
