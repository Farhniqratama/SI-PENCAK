<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;

use App\Controllers\Operator\BaseOperatorController;
use App\Models\Pt;
use App\Models\Pencairan;
use App\Models\Userpt;
use App\Models\Mahasiswa;
use App\Models\Informasi;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        

        $ptModel         = new \App\Models\Pt();
        $pencairanModel  = new \App\Models\Pencairan();
        $userptModel     = new \App\Models\Userpt();
        $mahasiswaModel  = new \App\Models\Mahasiswa();
        $informasiModel  = new \App\Models\Informasi();

        // Semua data PT
        $jumlah_pt = count($ptModel->get());

        // Semua data userpt
        $jumlah_userpt = count($userptModel->get());

        // Semua mahasiswa (tanpa filter id_pt)
        $jumlah_mahasiswa = count($mahasiswaModel->get());

        // Semua pencairan dari semua PT yang berstatus selesai
        $jumlah_pencairan = count(
            $pencairanModel->where('status', 'selesai')->get()
        );

        // Menampilkan 5 informasi terbaru
        $informasi = $informasiModel->orderBy('tanggal', 'DESC')->take(5)->get();

        // Statistik Tambahan
        $total_nominal_selesai = \Illuminate\Support\Facades\DB::table('pencairans')->where('status', 'selesai')->sum('nominal_pencairan');
        $total_pencairan_proses = \Illuminate\Support\Facades\DB::table('pencairans')->where('status', 'proses')->count();
        $total_pencairan_ditolak = \Illuminate\Support\Facades\DB::table('pencairans')->where('status', 'ditolak')->count();

        $kategori_pembiayaan_penuh = \Illuminate\Support\Facades\DB::table('pencairans')->where('kategori_penerima', 'Skema Pembiayaan Penuh')->count();
        $kategori_biaya_pendidikan = \Illuminate\Support\Facades\DB::table('pencairans')->where('kategori_penerima', 'Skema Biaya Pendidikan')->count();

        $tren_bulanan = \Illuminate\Support\Facades\DB::table('pencairans')
            ->selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->whereYear('tanggal', date('Y'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
        
        $chart_bulanan = array_fill(1, 12, 0);
        foreach ($tren_bulanan as $row) {
            $chart_bulanan[$row->bulan] = $row->total;
        }

        $pencairan_terbaru = \Illuminate\Support\Facades\DB::table('pencairans')
            ->leftJoin('pts', 'pencairans.id_pt', '=', 'pts.id')
            ->select('pencairans.*', 'pts.kode_pt', 'pts.perguruan_tinggi')
            ->orderBy('pencairans.id', 'DESC')
            ->take(5)
            ->get();

        $data = [
            'title'                     => 'PT - Dashboard',
            'jumlah_pt'                 => $jumlah_pt,
            'jumlah_mahasiswa'          => $jumlah_mahasiswa,
            'jumlah_pencairan'          => $jumlah_pencairan,
            'jumlah_userpt'             => $jumlah_userpt,
            'informasi'                 => $informasi,
            'total_nominal_selesai'     => $total_nominal_selesai,
            'total_pencairan_proses'    => $total_pencairan_proses,
            'total_pencairan_ditolak'   => $total_pencairan_ditolak,
            'kategori_pembiayaan_penuh' => $kategori_pembiayaan_penuh,
            'kategori_biaya_pendidikan' => $kategori_biaya_pendidikan,
            'chart_bulanan'             => array_values($chart_bulanan),
            'pencairan_terbaru'         => $pencairan_terbaru
        ];

        return view('operator.index', $data);
    }

    public function update($id)
    {
        $data = [
            'password' => password_hash(request('password'), PASSWORD_DEFAULT),
        ];
        $model = new \App\Models\User();
        $model->where('id', $id)->update($data);

        return redirect('dashboard');
    }
}
