@extends('layouts.app')
@section('title', 'Dashboard Petugas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Dashboard Petugas</h5>
        <small class="text-muted">
            Wilayah: <strong>{{ auth()->user()->kecamatan->nama ?? 'Belum ditugaskan' }}</strong>
        </small>
    </div>
    <a href="{{ route('petugas.laporan.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1"></i> Buat Laporan
    </a>
</div>

<div class="row g-3 mb-4">
    @php
        $statItems = [
            ['label' => 'Draft',             'key' => 'draft',             'icon' => 'file-earmark',        'color' => 'secondary'],
            ['label' => 'Menunggu Validasi', 'key' => 'menunggu_validasi', 'icon' => 'clock-history',       'color' => 'warning'],
            ['label' => 'Disetujui',         'key' => 'disetujui',         'icon' => 'check-circle-fill',   'color' => 'success'],
            ['label' => 'Ditolak',           'key' => 'ditolak',           'icon' => 'x-circle-fill',       'color' => 'danger'],
        ];
    @endphp
    @foreach($statItems as $s)
    <div class="col-6 col-md-3">
        <div class="card stat-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="icon-wrap bg-{{ $s['color'] }}-subtle text-{{ $s['color'] }}">
                    <i class="bi bi-{{ $s['icon'] }}"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold">{{ $stats[$s['key']] }}</div>
                    <div class="small text-muted">{{ $s['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between">
        <span class="fw-semibold">Laporan Terbaru</span>
        <a href="{{ route('petugas.laporan.index') }}" class="btn btn-sm btn-outline-success">Semua Laporan</a>
    </div>
    <div class="card-body p-0">
        @forelse($laporanTerbaru as $laporan)
        <div class="d-flex align-items-center px-3 py-2 border-bottom gap-3">
            <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold small">{{ $laporan->desa->nama }} – {{ $laporan->kecamatan->nama }}</div>
                <div class="text-muted" style="font-size:.78rem;">
                    {{ $laporan->musim_tanam }} {{ $laporan->tahun }} &bull;
                    {{ number_format($laporan->produksi, 2) }} ton
                </div>
            </div>
            <span class="badge bg-{{ $laporan->status_badge }}">{{ $laporan->status_label }}</span>
            <a href="{{ route('petugas.laporan.show', $laporan) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-eye"></i>
            </a>
        </div>
        @empty
        <div class="text-center text-muted py-4">
            <i class="bi bi-inbox fs-2 d-block mb-1"></i>
            Belum ada laporan. <a href="{{ route('petugas.laporan.create') }}">Buat sekarang</a>.
        </div>
        @endforelse
    </div>
</div>
@endsection
