<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $fillable = ['nama', 'kode'];

    public function desas()
    {
        return $this->hasMany(Desa::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function laporanPanens()
    {
        return $this->hasMany(LaporanPanen::class);
    }
}
