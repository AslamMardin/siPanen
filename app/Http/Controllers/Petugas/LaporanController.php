<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\LaporanPanen;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $laporan = LaporanPanen::where('user_id', auth()->id())
            ->with(['kecamatan', 'desa'])
            ->latest()
            ->paginate(15);

        return view('petugas.laporan.index', compact('laporan'));
    }

    public function create()
    {
        $user  = auth()->user();
        $desas = Desa::where('kecamatan_id', $user->kecamatan_id)->orderBy('nama')->get();

        return view('petugas.laporan.create', compact('desas', 'user'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
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

        $status = $data['action'] === 'kirim' ? 'menunggu_validasi' : 'draft';
        unset($data['action']);

        LaporanPanen::create([
            ...$data,
            'user_id'       => $user->id,
            'kecamatan_id'  => $user->kecamatan_id,
            'status'        => $status,
        ]);

        $msg = $status === 'draft'
            ? 'Laporan disimpan sebagai draft.'
            : 'Laporan berhasil dikirim untuk divalidasi.';

        return redirect()->route('petugas.laporan.index')->with('success', $msg);
    }

    public function show(LaporanPanen $laporan)
    {
        $this->authorizeOwner($laporan);
        $laporan->load(['kecamatan', 'desa', 'validator']);

        return view('petugas.laporan.show', compact('laporan'));
    }

    public function edit(LaporanPanen $laporan)
    {
        $this->authorizeOwner($laporan);

        if (!in_array($laporan->status, ['draft', 'ditolak'])) {
            return back()->with('error', 'Laporan tidak dapat diedit.');
        }

        $user  = auth()->user();
        $desas = Desa::where('kecamatan_id', $user->kecamatan_id)->orderBy('nama')->get();

        return view('petugas.laporan.edit', compact('laporan', 'desas', 'user'));
    }

    public function update(Request $request, LaporanPanen $laporan)
    {
        $this->authorizeOwner($laporan);

        if (!in_array($laporan->status, ['draft', 'ditolak'])) {
            return back()->with('error', 'Laporan tidak dapat diedit.');
        }

        $data = $request->validate([
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

        $status = $data['action'] === 'kirim' ? 'menunggu_validasi' : 'draft';
        unset($data['action']);

        $laporan->update([
            ...$data,
            'status'            => $status,
            'catatan_penolakan' => null,
        ]);

        $msg = $status === 'draft'
            ? 'Laporan diperbarui dan disimpan sebagai draft.'
            : 'Laporan berhasil dikirim ulang untuk divalidasi.';

        return redirect()->route('petugas.laporan.index')->with('success', $msg);
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function authorizeOwner(LaporanPanen $laporan): void
    {
        if ($laporan->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
