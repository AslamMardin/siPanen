<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanPanen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Desa;

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

    public function create()
    {
        $users = User::where('role', 'petugas')->get();
        $desas = Desa::orderBy('nama')->get();

        return view('admin.validasi.create', compact('users', 'desas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'      => ['required', 'exists:users,id'],
            'desa_id'      => ['required', 'exists:desas,id'],
            'musim_tanam'  => ['required', 'in:Musim Hujan,Musim Kemarau'],
            'tahun'        => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'tanggal_panen'=> ['required', 'date', 'before_or_equal:today'],
            'luas_tanam'   => ['required', 'numeric', 'min:0.01'],
            'luas_panen'   => ['required', 'numeric', 'min:0.01'],
            'produksi'     => ['required', 'numeric', 'min:0.01'],
            'varietas_padi'=> ['required', 'string', 'max:100'],
            'keterangan'   => ['nullable', 'string', 'max:500'],
            'action'       => ['required', 'in:draft,kirim'],
        ]);

        $user = User::findOrFail($data['user_id']);
        
        $status = $data['action'] === 'kirim' ? 'disetujui' : 'draft';
        unset($data['action']);

        LaporanPanen::create([
            ...$data,
            'kecamatan_id'  => $user->kecamatan_id,
            'status'        => $status,
            'validated_by'  => $status === 'disetujui' ? auth()->id() : null,
            'validated_at'  => $status === 'disetujui' ? now() : null,
        ]);

        return redirect()->route('admin.validasi.index')
            ->with('success', 'Laporan berhasil ditambahkan.');
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
