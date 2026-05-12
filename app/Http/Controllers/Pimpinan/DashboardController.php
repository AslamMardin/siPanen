<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\LaporanPanen;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun         = $request->get('tahun', date('Y'));
        $musim_tanam   = $request->get('musim_tanam');
        $kecamatan_id  = $request->get('kecamatan_id');

        $query = LaporanPanen::where('status', 'disetujui')
            ->where('tahun', $tahun);

        if ($musim_tanam) {
            $query->where('musim_tanam', $musim_tanam);
        }
        if ($kecamatan_id) {
            $query->where('kecamatan_id', $kecamatan_id);
        }

        $stats = [
            'total_produksi'    => $query->sum('produksi'),
            'total_luas_panen'  => $query->sum('luas_panen'),
            'total_laporan'     => $query->count(),
        ];

        $produksiPerKecamatan = LaporanPanen::with('kecamatan')
            ->where('status', 'disetujui')
            ->where('tahun', $tahun)
            ->selectRaw('kecamatan_id, SUM(produksi) as total_produksi, SUM(luas_panen) as total_luas')
            ->groupBy('kecamatan_id')
            ->get();

        $trenProduksi = LaporanPanen::where('status', 'disetujui')
            ->selectRaw('tahun, musim_tanam, SUM(produksi) as total_produksi')
            ->groupBy('tahun', 'musim_tanam')
            ->orderBy('tahun')
            ->get();

        $kecamatans = Kecamatan::orderBy('nama')->get();
        $tahunList  = LaporanPanen::selectRaw('DISTINCT tahun')->orderBy('tahun', 'desc')->pluck('tahun');

        return view('pimpinan.dashboard', compact(
            'stats', 'produksiPerKecamatan', 'trenProduksi',
            'kecamatans', 'tahunList', 'tahun', 'musim_tanam', 'kecamatan_id'
        ));
    }

    public function exportPdf(Request $request)
    {
        $tahun         = $request->get('tahun', date('Y'));
        $musim_tanam   = $request->get('musim_tanam');
        $kecamatan_id  = $request->get('kecamatan_id');

        $query = LaporanPanen::where('status', 'disetujui')
            ->where('tahun', $tahun);

        if ($musim_tanam) {
            $query->where('musim_tanam', $musim_tanam);
        }
        if ($kecamatan_id) {
            $query->where('kecamatan_id', $kecamatan_id);
        }

        $stats = [
            'total_produksi'    => $query->sum('produksi'),
            'total_luas_panen'  => $query->sum('luas_panen'),
            'total_laporan'     => $query->count(),
        ];

        $produksiPerKecamatan = LaporanPanen::with('kecamatan')
            ->where('status', 'disetujui')
            ->where('tahun', $tahun);

        if ($musim_tanam) {
            $produksiPerKecamatan->where('musim_tanam', $musim_tanam);
        }
        if ($kecamatan_id) {
            $produksiPerKecamatan->where('kecamatan_id', $kecamatan_id);
        }

        $produksiPerKecamatan = $produksiPerKecamatan->selectRaw('kecamatan_id, SUM(produksi) as total_produksi, SUM(luas_panen) as total_luas')
            ->groupBy('kecamatan_id')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.laporan', compact(
            'stats', 'produksiPerKecamatan', 'tahun', 'musim_tanam'
        ));

        return $pdf->download('Laporan_Panen_Pimpinan_'.$tahun.'.pdf');
    }
}
