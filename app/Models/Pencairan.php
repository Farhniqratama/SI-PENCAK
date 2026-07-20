<?php

namespace App\Models;

use App\Models\Concerns\CodeIgniterModelCompatibility;
use Illuminate\Database\Eloquent\Model;

class Pencairan extends Model
{
    use CodeIgniterModelCompatibility;

    protected $table = 'pencairans';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_pt',
        'periode',
        'kategori_penerima',
        'tanggal_entry',
        'no_sk',
        'tanggal',
        'semester',
        'sptjm',
        'sk_penetapan',
        'sk_pembatalan',
        'berita_acara',
        'surat_pengantar',
        'status',
        'jumlah_mahasiswa',
        'alasan_tolak',
        'nominal_pencairan',
        'jenis_bantuan',
        'keterangan'
    ];

    public function histori()
    {
        return $this->newQuery()
            ->select(['pencairans.*', 'pts.kode_pt', 'pts.perguruan_tinggi'])
            ->leftJoin('pts', 'pts.id', '=', 'pencairans.id_pt')
            ->orderByDesc('pencairans.tanggal_entry')
            ->get();
    }

    public function historiPager($limit = 10, $tahun = null, $pt = null, $search = null, $status = null)
    {
        $query = $this->newQuery()
            ->select(['pencairans.*', 'pts.kode_pt', 'pts.perguruan_tinggi'])
            ->leftJoin('pts', 'pts.id', '=', 'pencairans.id_pt');

        if ($status) {
            $query->where('pencairans.status', $status);
        } else {
            $query->whereIn('pencairans.status', ['Ajukan Mahasiswa', 'Finalisasi', 'Diproses', 'Selesai', 'Ditolak']);
        }

        if ($tahun) {
            $query->whereYear('pencairans.tanggal_entry', $tahun);
        }

        if ($pt) {
            $query->where('pencairans.id_pt', $pt);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pts.perguruan_tinggi', 'like', "%{$search}%")
                    ->orWhere('pencairans.no_sk', 'like', "%{$search}%");
            });
        }

        return $this->withPager($query->orderByDesc('pencairans.id')->paginate($limit));
    }

    public function draft()
    {
        $query = $this->ciBuilder ?? $this->newQuery();
        $this->ciBuilder = null;

        return $query
            ->whereNotIn('status', ['Diproses', 'Selesai'])
            ->orderByDesc('id')
            ->get();
    }

    public function detail($id)
    {
        return $this->newQuery()
            ->select(['pencairans.*', 'pts.kode_pt', 'pts.perguruan_tinggi', 'pts.aipt'])
            ->leftJoin('pts', 'pts.id', '=', 'pencairans.id_pt')
            ->where('pencairans.id', $id)
            ->first();
    }
}
