<?php

namespace App\Models;

use App\Models\Concerns\CodeIgniterModelCompatibility;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Userpt extends Authenticatable
{
    use CodeIgniterModelCompatibility;

    protected $table = 'userpts';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_pt', 'username', 'password', 'penanggung_jawab', 'nip', 'kontak', 'email', 'status'
    ];

    protected $hidden = [
        'password',
    ];

    public function pt()
    {
        return $this->belongsTo(Pt::class, 'id_pt', 'id');
    }

    public function WithPtPager($limit = 10)
    {
        $query = $this->ciBuilder ?? $this->newQuery();
        $this->ciBuilder = null;

        return $this->withPager($query
            ->select(['userpts.*', 'pts.kode_pt', 'pts.perguruan_tinggi'])
            ->leftJoin('pts', 'pts.id', '=', 'userpts.id_pt')
            ->orderBy('userpts.username')
            ->paginate($limit));
    }

    public function findWith($id)
    {
        return $this->newQuery()
            ->select(['userpts.*', 'pts.kode_pt', 'pts.perguruan_tinggi', 'pts.aipt'])
            ->leftJoin('pts', 'pts.id', '=', 'userpts.id_pt')
            ->where('userpts.id', $id)
            ->first();
    }
}
