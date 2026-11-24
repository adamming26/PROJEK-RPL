<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    use HasFactory;

    protected $table = 'ruangan';
    protected $primaryKey = 'id_ruangan';

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'kapasitas',
        'lokasi',
        'deskripsi',
        'status',
        'foto',
    ];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_ruangan', 'id_ruangan');
    }
}
