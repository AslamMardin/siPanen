<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $fillable = ['kecamatan_id', 'nama'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function laporanPanens()
    {
        return $this->hasMany(LaporanPanen::class);
    }
}
