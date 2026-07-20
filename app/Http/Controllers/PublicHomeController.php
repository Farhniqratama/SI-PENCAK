<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicHomeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('q')) {
            return redirect()->route('public.search', ['q' => $request->query('q')]);
        }

        return view('public.home', [
            'stats' => $this->stats(),
        ]);
    }

    public function search(Request $request)
    {
        $keyword = trim((string) $request->query('q', ''));

        $query = $this->mahasiswaQuery($keyword);

        // Jika keyword kosong, jangan tampilkan data apa pun
        if ($keyword === '') {
            $query->whereRaw('1 = 0');
        }

        $mahasiswas = $query->orderBy('mahasiswas.nama')
            ->paginate(10)
            ->withQueryString();

        return view('public.search', [
            'keyword' => $keyword,
            'mahasiswas' => $mahasiswas,
            'stats' => $this->stats(),
        ]);
    }

    public function detail(int $mahasiswa)
    {
        $data = $this->mahasiswaQuery('')
            ->where('mahasiswas.id', $mahasiswa)
            ->first();

        abort_if(!$data, 404);

        return view('public.student-detail', [
            'mahasiswa' => $data,
            'stats' => $this->stats(),
        ]);
    }

    private function mahasiswaQuery(string $keyword)
    {
        $query = DB::table('mahasiswas')
            ->leftJoin('prodis', 'prodis.id', '=', 'mahasiswas.id_prodi')
            ->leftJoin('pts', 'pts.id', '=', 'mahasiswas.id_pt')
            ->leftJoin('pencairans', 'pencairans.id', '=', 'mahasiswas.id_pencairan')
            ->select([
                'mahasiswas.id',
                'mahasiswas.id_pencairan',
                'mahasiswas.nim',
                'mahasiswas.nama',
                'mahasiswas.jenjang',
                'mahasiswas.angkatan',
                'mahasiswas.kategori',
                'mahasiswas.pembaruan_status',
                'mahasiswas.status_pengajuan',
                'prodis.nama_prodi',
                'prodis.kode_prodi',
                'pts.perguruan_tinggi',
                'pts.kode_pt',
                'pencairans.id as pencairan_id',
                'pencairans.periode as pencairan_periode',
                'pencairans.semester as pencairan_semester',
                'pencairans.kategori_penerima as pencairan_kategori_penerima',
                'pencairans.jenis_bantuan as pencairan_jenis_bantuan',
                'pencairans.nominal_pencairan as pencairan_nominal',
                'pencairans.jumlah_mahasiswa as pencairan_jumlah_mahasiswa',
                'pencairans.no_sk as pencairan_no_sk',
                'pencairans.tanggal as pencairan_tanggal_surat',
                'pencairans.tanggal_entry as pencairan_tanggal_entry',
                'pencairans.status as pencairan_status',
                'pencairans.alasan_tolak as pencairan_alasan_tolak',
                'pencairans.keterangan as pencairan_keterangan',
            ]);

        if ($keyword !== '') {
            $query->where(function ($query) use ($keyword) {
                $query->where('mahasiswas.nama', 'like', "%{$keyword}%")
                    ->orWhere('mahasiswas.nim', 'like', "%{$keyword}%")
                    ->orWhere('prodis.nama_prodi', 'like', "%{$keyword}%")
                    ->orWhere('pts.perguruan_tinggi', 'like', "%{$keyword}%");
            });
        }

        return $query;
    }

    private function stats(): array
    {
        return [
            'mahasiswa' => DB::table('mahasiswas')->count(),
            'perguruan_tinggi' => DB::table('pts')->count(),
            'program_studi' => DB::table('prodis')->count(),
            'pencairan' => DB::table('pencairans')->count(),
        ];
    }
}
