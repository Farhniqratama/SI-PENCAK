<?php

namespace App\Models;

use App\Models\Concerns\CodeIgniterModelCompatibility;
use Illuminate\Database\Eloquent\Model;

class Informasi extends Model
{
    use CodeIgniterModelCompatibility;

    protected $table = 'informasis';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'judul','deskripsi','file','tanggal'
    ];
}
