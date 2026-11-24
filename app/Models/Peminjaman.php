<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $primaryKey = 'id_peminjaman';

    protected $fillable = [
        'kode_peminjaman',
        'id_user',
        'jenis_fasilitas',
        'id_barang',
        'id_ruangan',
        'jumlah',
        'tanggal_pengajuan',
        'tanggal_peminjaman',
        'jam_mulai',
        'jam_selesai',
        'durasi',
        'waktu_pengembalian_rencana',
        'tujuan',
        'status',
        'catatan_penolakan',
        'diserahkan_oleh',
        'diserahkan_pada',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'tanggal_peminjaman' => 'date',
            'waktu_pengembalian_rencana' => 'datetime',
            'diserahkan_pada' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id_ruangan');
    }

    public function diserahkanOleh()
    {
        return $this->belongsTo(User::class, 'diserahkan_oleh', 'id_user');
    }

    public function persetujuan()
    {
        return $this->hasMany(Persetujuan::class, 'id_peminjaman', 'id_peminjaman');
    }

    public function persetujuanTerakhir()
    {
        return $this->hasOne(Persetujuan::class, 'id_peminjaman', 'id_peminjaman')->latestOfMany('id_persetujuan');
    }

    public function qrCodes()
    {
        return $this->hasMany(QRCode::class, 'id_peminjaman', 'id_peminjaman');
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'id_peminjaman', 'id_peminjaman');
    }

    public function getNamaFasilitasAttribute(): string
    {
        if ($this->jenis_fasilitas === 'BARANG') {
            return $this->barang ? $this->barang->nama_barang : 'Barang';
        }
        return $this->ruangan ? $this->ruangan->nama_ruangan : 'Ruangan';
    }

    public function getKodeFasilitasAttribute(): string
    {
        if ($this->jenis_fasilitas === 'BARANG') {
            return $this->barang ? $this->barang->kode_barang : '-';
        }
        return $this->ruangan ? $this->ruangan->kode_ruangan : '-';
    }

    public function getActiveQrCode(string $jenis = 'PEMINJAMAN')
    {
        return $this->qrCodes()
            ->where('jenis_qr', $jenis)
            ->where('status', 'AKTIF')
            ->where('expires_at', '>', now())
            ->latest('id_qr')
            ->first();
    }
}
