<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QRCode extends Model
{
    use HasFactory;

    protected $table = 'qr_code';
    protected $primaryKey = 'id_qr';

    protected $fillable = [
        'id_peminjaman',
        'token',
        'jenis_qr',
        'expires_at',
        'status',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman', 'id_peminjaman');
    }

    public function isExpired(): bool
    {
        if ($this->status === 'KEDALUWARSA') {
            return true;
        }

        if ($this->status === 'AKTIF' && Carbon::now()->greaterThan($this->expires_at)) {
            $this->update(['status' => 'KEDALUWARSA']);
            return true;
        }

        return false;
    }

    public function isValid(): bool
    {
        return $this->status === 'AKTIF' && Carbon::now()->lessThanOrEqualTo($this->expires_at);
    }

    public function getRemainingSecondsAttribute(): int
    {
        if (!$this->isValid()) {
            return 0;
        }
        return max(0, Carbon::now()->diffInSeconds($this->expires_at, false));
    }
}
