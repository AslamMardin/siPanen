@extends('layouts.app')
@section('title', 'Validasi Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Validasi Laporan Panen</h5>
</div>

{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
            <label class="small fw-semibold mb-0">Status:</label>
            <select name="status" class="form-select form-select-sm" style="width:auto">
                <option value="">Semua</option>
                <option value="menunggu_validasi" {{ request('status') === 'menunggu_validasi' ? 'selected' : '' }}>Menunggu Validasi</option>
                <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button class="btn btn-sm btn-outline-success">Filter</button>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Petugas</th>
                        <th>Kecamatan / Desa</th>
                        <th>Musim / Tahun</th>
                        <th>Produksi (ton)</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $item)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td class="small">{{ $item->user->name }}</td>
                        <td class="small">{{ $item->kecamatan->nama }} / {{ $item->desa->nama }}</td>
                        <td class="small">{{ $item->musim_tanam }} {{ $item->tahun }}</td>
                        <td class="fw-semibold">{{ number_format($item->produksi, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $item->status_badge }}">{{ $item->status_label }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.validasi.show', $item) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Tidak ada data laporan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($laporan->hasPages())
    <div class="card-footer bg-white">{{ $laporan->links() }}</div>
    @endif
</div>
@endsection
