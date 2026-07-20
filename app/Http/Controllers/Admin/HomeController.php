<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Userpt;
use App\Models\Mahasiswa;
use App\Models\Pencairan;
use App\Models\Pt;
use App\Models\Informasi;

class HomeController extends Controller
{
    public function index()
    {
        $userModel       = new \App\Models\Userpt();
        $mahasiswaModel  = new \App\Models\Mahasiswa();
        $pencairanModel  = new \App\Models\Pencairan();
        $ptModel         = new \App\Models\Pt();
        $informasiModel  = new \App\Models\Informasi();

        // Statistik keseluruhan (TANPA dibatasi id_pt)
        $jumlah_pt         = $ptModel->countAll();
        $jumlah_userpt     = $userModel->countAll();
        $jumlah_mahasiswa  = $mahasiswaModel->countAll();
        $jumlah_pencairan  = $pencairanModel->where('status', 'selesai')->countAllResults();

        // Ambil 6 informasi terbaru (semua PT)
        $informasi = $informasiModel->orderBy('tanggal', 'DESC')->take(6)->get();

        $id_pt = session('pt');
        
        $total_nominal_selesai = \Illuminate\Support\Facades\DB::table('pencairans')->where('id_pt', $id_pt)->where('status', 'selesai')->sum('nominal_pencairan');
        $total_pencairan_proses = \Illuminate\Support\Facades\DB::table('pencairans')->where('id_pt', $id_pt)->where('status', 'proses')->count();
        $total_pencairan_ditolak = \Illuminate\Support\Facades\DB::table('pencairans')->where('id_pt', $id_pt)->where('status', 'ditolak')->count();

        $kategori_pembiayaan_penuh = \Illuminate\Support\Facades\DB::table('pencairans')->where('id_pt', $id_pt)->where('kategori_penerima', 'Skema Pembiayaan Penuh')->count();
        $kategori_biaya_pendidikan = \Illuminate\Support\Facades\DB::table('pencairans')->where('id_pt', $id_pt)->where('kategori_penerima', 'Skema Biaya Pendidikan')->count();

        $tren_bulanan = \Illuminate\Support\Facades\DB::table('pencairans')
            ->selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
            ->where('id_pt', $id_pt)
            ->whereYear('tanggal', date('Y'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();
        
        $chart_bulanan = array_fill(1, 12, 0);
        foreach ($tren_bulanan as $row) {
            $chart_bulanan[$row->bulan] = $row->total;
        }

        $pencairan_terbaru = \Illuminate\Support\Facades\DB::table('pencairans')
            ->where('id_pt', $id_pt)
            ->orderBy('id', 'DESC')
            ->take(5)
            ->get();

        $data = [
            'title'                     => 'Admin PT - Dashboard',
            'jumlah_pt'                 => $jumlah_pt,
            'jumlah_mahasiswa'          => $jumlah_mahasiswa,
            'jumlah_userpt'             => $jumlah_userpt,
            'jumlah_pencairan'          => $jumlah_pencairan,
            'informasi'                 => $informasi,
            'total_nominal_selesai'     => $total_nominal_selesai,
            'total_pencairan_proses'    => $total_pencairan_proses,
            'total_pencairan_ditolak'   => $total_pencairan_ditolak,
            'kategori_pembiayaan_penuh' => $kategori_pembiayaan_penuh,
            'kategori_biaya_pendidikan' => $kategori_biaya_pendidikan,
            'chart_bulanan'             => array_values($chart_bulanan),
            'pencairan_terbaru'         => $pencairan_terbaru
        ];

        return view('admin.index', $data);
    }

    public function update($id)
    {
        $password = request('password');

        if (empty($password)) {
            return back()->with('error', 'Password tidak boleh kosong.');
        }

        $model = new \App\Models\Userpt();
        $data = [
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];

        $model->where('id', $id)->update($data);

        return redirect(base_url('home'))->with('success', 'Password berhasil diperbarui.');
    }
}
