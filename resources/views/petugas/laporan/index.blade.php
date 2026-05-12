@extends('layouts.app')
@section('title', 'Riwayat Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">Riwayat Laporan Panen</h5>
    <a href="{{ route('petugas.laporan.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i> Buat Laporan
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Desa</th>
                        <th>Musim / Tahun</th>
                        <th>Luas Panen (ha)</th>
                        <th>Produksi (ton)</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($laporan as $item)
                    <tr>
                        <td class="text-muted small">{{ $loop->iteration }}</td>
                        <td class="small fw-semibold">{{ $item->desa->nama }}</td>
                        <td class="small">{{ $item->musim_tanam }} {{ $item->tahun }}</td>
                        <td>{{ number_format($item->luas_panen, 2) }}</td>
                        <td class="fw-semibold">{{ number_format($item->produksi, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $item->status_badge }}">{{ $item->status_label }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('petugas.laporan.show', $item) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array($item->status, ['draft', 'ditolak']))
                                <a href="{{ route('petugas.laporan.edit', $item) }}"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada laporan.</td>
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
