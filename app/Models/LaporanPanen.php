<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPanen extends Model
{
    protected $fillable = [
        'user_id',
        'kecamatan_id',
        'desa_id',
        'musim_tanam',
        'tahun',
        'tanggal_panen',
        'luas_tanam',
        'luas_panen',
        'produksi',
        'varietas_padi',
        'keterangan',
        'status',
        'catatan_penolakan',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'tanggal_panen' => 'date',
        'luas_tanam'   => 'decimal:2',
        'luas_panen'   => 'decimal:2',
        'produksi'     => 'decimal:2',
        'produktivitas'=> 'decimal:4',
    ];

    // Status constants
    const STATUS_DRAFT              = 'draft';
    const STATUS_MENUNGGU_VALIDASI  = 'menunggu_validasi';
    const STATUS_DISETUJUI          = 'disetujui';
    const STATUS_DITOLAK            = 'ditolak';

    public static array $statusLabel = [
        'draft'              => 'Draft',
        'menunggu_validasi'  => 'Menunggu Validasi',
        'disetujui'          => 'Disetujui',
        'ditolak'            => 'Ditolak',
    ];

    public static array $statusBadge = [
        'draft'              => 'secondary',
        'menunggu_validasi'  => 'warning',
        'disetujui'          => 'success',
        'ditolak'            => 'danger',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabel[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute(): string
    {
        return self::$statusBadge[$this->status] ?? 'secondary';
    }

    // ─── Computed produktivitas (fallback if stored-as not supported) ─────────

    public function getProduktivitasAttribute(): float
    {
        if ($this->luas_panen > 0) {
            return round($this->produksi / $this->luas_panen, 4);
        }
        return 0;
    }
}
