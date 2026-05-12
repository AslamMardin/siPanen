<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\LaporanPanen;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $stats = [
            'draft'              => LaporanPanen::where('user_id', $user->id)->where('status', 'draft')->count(),
            'menunggu_validasi'  => LaporanPanen::where('user_id', $user->id)->where('status', 'menunggu_validasi')->count(),
            'disetujui'          => LaporanPanen::where('user_id', $user->id)->where('status', 'disetujui')->count(),
            'ditolak'            => LaporanPanen::where('user_id', $user->id)->where('status', 'ditolak')->count(),
        ];

        $laporanTerbaru = LaporanPanen::where('user_id', $user->id)
            ->with(['kecamatan', 'desa'])
            ->latest()
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact('stats', 'laporanTerbaru'));
    }
}
