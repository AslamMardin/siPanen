<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanPanen;
use App\Models\User;
use App\Models\Kecamatan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_laporan'          => LaporanPanen::count(),
            'menunggu_validasi'      => LaporanPanen::where('status', 'menunggu_validasi')->count(),
            'disetujui'              => LaporanPanen::where('status', 'disetujui')->count(),
            'ditolak'                => LaporanPanen::where('status', 'ditolak')->count(),
            'total_petugas'          => User::where('role', 'petugas')->count(),
            'total_produksi'         => LaporanPanen::where('status', 'disetujui')->sum('produksi'),
            'total_luas_panen'       => LaporanPanen::where('status', 'disetujui')->sum('luas_panen'),
        ];

        $laporanTerbaru = LaporanPanen::with(['user', 'kecamatan', 'desa'])
            ->where('status', 'menunggu_validasi')
            ->latest()
            ->take(5)
            ->get();

        $produksiPerKecamatan = LaporanPanen::with('kecamatan')
            ->where('status', 'disetujui')
            ->selectRaw('kecamatan_id, SUM(produksi) as total_produksi')
            ->groupBy('kecamatan_id')
            ->get();

        return view('admin.dashboard', compact('stats', 'laporanTerbaru', 'produksiPerKecamatan'));
    }

    public function exportPdf()
    {
        $stats = [
            'total_laporan'          => LaporanPanen::count(),
            'menunggu_validasi'      => LaporanPanen::where('status', 'menunggu_validasi')->count(),
            'disetujui'              => LaporanPanen::where('status', 'disetujui')->count(),
            'ditolak'                => LaporanPanen::where('status', 'ditolak')->count(),
            'total_petugas'          => User::where('role', 'petugas')->count(),
            'total_produksi'         => LaporanPanen::where('status', 'disetujui')->sum('produksi'),
            'total_luas_panen'       => LaporanPanen::where('status', 'disetujui')->sum('luas_panen'),
        ];

        $produksiPerKecamatan = LaporanPanen::with('kecamatan')
            ->where('status', 'disetujui')
            ->selectRaw('kecamatan_id, SUM(produksi) as total_produksi, SUM(luas_panen) as total_luas')
            ->groupBy('kecamatan_id')
            ->get();

        $tahun = date('Y');
        $musim_tanam = 'Keseluruhan';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.laporan', compact(
            'stats', 'produksiPerKecamatan', 'tahun', 'musim_tanam'
        ));

        return $pdf->download('Laporan_Panen_Admin_'.$tahun.'.pdf');
    }
}
