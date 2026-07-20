<?php

namespace App\Models;

use App\Models\Concerns\CodeIgniterModelCompatibility;
use Illuminate\Database\Eloquent\Model;

class Pt extends Model
{
    use CodeIgniterModelCompatibility;

    protected $table = 'pts';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'kode_pt','perguruan_tinggi','aipt'
    ];
}
