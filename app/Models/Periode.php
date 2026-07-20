<?php

namespace App\Models;

use App\Models\Concerns\CodeIgniterModelCompatibility;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use CodeIgniterModelCompatibility;

    protected $table = 'periodes';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        
    ];

    public function periode()
    {
        $periode = $this->newQuery()->orderByDesc('id')->first();

        if ($periode) {
            return $periode;
        }

        $tahun = date('Y');
        $semester = ((int) date('n')) >= 7 ? 'Semester Ganjil' : 'Semester Genap';

        return ['periode' => "{$semester} / {$tahun}"];
    }
}
