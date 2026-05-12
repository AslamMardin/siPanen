<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanPanen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValidasiController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanPanen::with(['user', 'kecamatan', 'desa'])
            ->whereIn('status', ['menunggu_validasi', 'disetujui', 'ditolak']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $laporan = $query->latest()->paginate(15);

        return view('admin.validasi.index', compact('laporan'));
    }

    public function show(LaporanPanen $laporan)
    {
        $laporan->load(['user', 'kecamatan', 'desa', 'validator']);
        return view('admin.validasi.show', compact('laporan'));
    }

    public function setujui(LaporanPanen $laporan)
    {
        if ($laporan->status !== 'menunggu_validasi') {
            return back()->with('error', 'Laporan tidak dalam status menunggu validasi.');
        }

        $laporan->update([
            'status'           => 'disetujui',
            'catatan_penolakan'=> null,
            'validated_by'     => auth()->id(),
            'validated_at'     => now(),
        ]);

        return redirect()->route('admin.validasi.index')
            ->with('success', 'Laporan berhasil disetujui.');
    }

    public function tolak(Request $request, LaporanPanen $laporan)
    {
        $request->validate([
            'catatan_penolakan' => ['required', 'string', 'max:500'],
        ]);

        if ($laporan->status !== 'menunggu_validasi') {
            return back()->with('error', 'Laporan tidak dalam status menunggu validasi.');
        }

        $laporan->update([
            'status'            => 'ditolak',
            'catatan_penolakan' => $request->catatan_penolakan,
            'validated_by'      => auth()->id(),
            'validated_at'      => now(),
        ]);

        return redirect()->route('admin.validasi.index')
            ->with('success', 'Laporan berhasil ditolak.');
    }
}
