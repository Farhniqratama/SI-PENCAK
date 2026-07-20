<?php

namespace App\Models;

use App\Models\Concerns\CodeIgniterModelCompatibility;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use CodeIgniterModelCompatibility;

    protected $table = 'prodis';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_pt','kode_prodi','nama_prodi'
    ];

    public function Cari($idPt)
    {
        return $this->newQuery()
            ->where('id_pt', $idPt)
            ->orderBy('nama_prodi')
            ->get();
    }
}
