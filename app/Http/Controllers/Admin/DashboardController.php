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

        // ─── Data Analisis & Perbandingan ─────────────────────────────
        $tahunIni = date('Y');
        $tahunLalu = $tahunIni - 1;

        // Total Tahun Ini & Tahun Lalu untuk YoY
        $totalTahunIni = LaporanPanen::where('status', 'disetujui')->where('tahun', $tahunIni)->sum('produksi');
        $totalTahunLalu = LaporanPanen::where('status', 'disetujui')->where('tahun', $tahunLalu)->sum('produksi');
        
        $growth = 0;
        if ($totalTahunLalu > 0) {
            $growth = (($totalTahunIni - $totalTahunLalu) / $totalTahunLalu) * 100;
        } elseif ($totalTahunIni > 0) {
            $growth = 100; // jika sebelumnya 0 dan sekarang ada
        }

        // Kontribusi Musim Terakhir di Tahun Ini
        $laporanTerakhir = LaporanPanen::where('status', 'disetujui')->where('tahun', $tahunIni)->latest('tanggal_panen')->first();
        $musimBerjalan = $laporanTerakhir ? $laporanTerakhir->musim_tanam : 'Musim Hujan';
        
        $produksiMusimIni = LaporanPanen::where('status', 'disetujui')
            ->where('tahun', $tahunIni)
            ->where('musim_tanam', $musimBerjalan)
            ->sum('produksi');
            
        $persentaseMusimIni = $totalTahunIni > 0 ? ($produksiMusimIni / $totalTahunIni) * 100 : 0;

        // Data Chart (Musim vs Tahunan)
        $rawChartData = LaporanPanen::where('status', 'disetujui')
            ->selectRaw('tahun, musim_tanam, SUM(produksi) as total')
            ->groupBy('tahun', 'musim_tanam')
            ->orderBy('tahun')
            ->get();

        $chartTahunan = [];
        $chartHujan = [];
        $chartKemarau = [];
        $listTahun = $rawChartData->pluck('tahun')->unique()->values()->toArray();
        if(empty($listTahun)) {
            $listTahun = [$tahunIni];
        }

        foreach ($listTahun as $t) {
            $hujan = $rawChartData->where('tahun', $t)->where('musim_tanam', 'Musim Hujan')->sum('total');
            $kemarau = $rawChartData->where('tahun', $t)->where('musim_tanam', 'Musim Kemarau')->sum('total');
            $chartHujan[] = $hujan;
            $chartKemarau[] = $kemarau;
            $chartTahunan[] = $hujan + $kemarau;
        }
        
        // Data Doughnut Chart Tahun Ini
        $doughnutHujan = $rawChartData->where('tahun', $tahunIni)->where('musim_tanam', 'Musim Hujan')->sum('total');
        $doughnutKemarau = $rawChartData->where('tahun', $tahunIni)->where('musim_tanam', 'Musim Kemarau')->sum('total');

        // YTD Table
        $ytdTable = [];
        foreach ($listTahun as $i => $t) {
            $ytdTable[] = [
                'tahun' => $t,
                'hujan' => $chartHujan[$i],
                'kemarau' => $chartKemarau[$i],
                'total' => $chartTahunan[$i]
            ];
        }
        $ytdTable = array_reverse($ytdTable); // terbaru di atas

        return view('admin.dashboard', compact(
            'stats', 'laporanTerbaru', 'produksiPerKecamatan',
            'tahunIni', 'totalTahunIni', 'growth', 'musimBerjalan', 'produksiMusimIni', 'persentaseMusimIni',
            'listTahun', 'chartHujan', 'chartKemarau', 'chartTahunan',
            'doughnutHujan', 'doughnutKemarau', 'ytdTable'
        ));
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

        return $pdf->stream('Laporan_Panen_Admin_'.$tahun.'.pdf');
    }
}
