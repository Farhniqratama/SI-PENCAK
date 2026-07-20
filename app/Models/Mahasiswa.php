<?php

namespace App\Models;

use App\Models\Concerns\CodeIgniterModelCompatibility;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use CodeIgniterModelCompatibility;

    protected $table = 'mahasiswas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_pt',
        'id_prodi',
        'id_pencairan',
        'nim',
        'nama',
        'jenjang',
        'angkatan',
        'pembaruan_status',
        'status_pengajuan',
        'kategori'
    ];

    public function universitas($idPt, $idPencairan = null, $limit = 10)
    {
        $query = $this->ciBuilder ?? $this->newQuery();
        $this->ciBuilder = null;

        return $this->withPager($query
            ->select(['mahasiswas.*', 'prodis.nama_prodi', 'prodis.kode_prodi'])
            ->leftJoin('prodis', 'prodis.id', '=', 'mahasiswas.id_prodi')
            ->where('mahasiswas.id_pt', $idPt)
            ->where(function ($q) use ($idPencairan) {
                $q->whereNull('mahasiswas.id_pencairan');

                if ($idPencairan) {
                    $q->orWhere('mahasiswas.id_pencairan', $idPencairan);
                }
            })
            ->orderBy('mahasiswas.nama')
            ->paginate($limit));
    }

    public function verifikasi($idPencairan, $keyword = null, $limit = 10)
    {
        $query = $this->newQuery()
            ->select(['mahasiswas.*', 'prodis.nama_prodi', 'prodis.kode_prodi'])
            ->leftJoin('prodis', 'prodis.id', '=', 'mahasiswas.id_prodi')
            ->where('mahasiswas.id_pencairan', $idPencairan);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('mahasiswas.nama', 'like', "%{$keyword}%")
                    ->orWhere('mahasiswas.nim', 'like', "%{$keyword}%");
            });
        }

        return $this->withPager($query->orderBy('mahasiswas.nama')->paginate($limit));
    }

    public function pencairan($idPencairan, $keyword = null, $limit = 10)
    {
        $query = $this->newQuery()
            ->select(['mahasiswas.*', 'prodis.kode_prodi', 'prodis.nama_prodi', 'pts.kode_pt', 'pts.perguruan_tinggi'])
            ->leftJoin('prodis', 'prodis.id', '=', 'mahasiswas.id_prodi')
            ->leftJoin('pts', 'pts.id', '=', 'mahasiswas.id_pt')
            ->where('mahasiswas.id_pencairan', $idPencairan);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('mahasiswas.nama', 'like', "%{$keyword}%")
                    ->orWhere('mahasiswas.nim', 'like', "%{$keyword}%");
            });
        }

        $query->orderBy('mahasiswas.nama');

        if (func_num_args() > 1) {
            return $this->withPager($query->paginate($limit));
        }

        return $query->get();
    }
}
