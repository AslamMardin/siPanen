@extends('layouts.app')
@section('title', 'Detail Laporan')

@section('content')
<div class="mb-3">
    <a href="{{ route('petugas.laporan.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <h5 class="fw-bold mt-1 mb-0">Detail Laporan Panen</h5>
</div>

<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">{{ $laporan->desa->nama }} – {{ $laporan->kecamatan->nama }}</span>
        <span class="badge bg-{{ $laporan->status_badge }} fs-6">{{ $laporan->status_label }}</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="text-muted small mb-1">Musim Tanam / Tahun</div>
                <div class="fw-semibold">{{ $laporan->musim_tanam }} {{ $laporan->tahun }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small mb-1">Tanggal Panen</div>
                <div class="fw-semibold">{{ optional($laporan->tanggal_panen)->isoFormat('D MMMM Y') }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small mb-1">Varietas Padi</div>
                <div class="fw-semibold">{{ $laporan->varietas_padi }}</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small mb-1">Luas Tanam</div>
                <div class="fw-semibold">{{ number_format($laporan->luas_tanam, 2) }} ha</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small mb-1">Luas Panen</div>
                <div class="fw-semibold">{{ number_format($laporan->luas_panen, 2) }} ha</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small mb-1">Produksi</div>
                <div class="fw-semibold">{{ number_format($laporan->produksi, 2) }} ton</div>
            </div>
            <div class="col-md-3">
                <div class="text-muted small mb-1">Produktivitas</div>
                <div class="fw-semibold">{{ number_format($laporan->produktivitas, 2) }} ton/ha</div>
            </div>
            @if($laporan->keterangan)
            <div class="col-12">
                <div class="text-muted small mb-1">Keterangan</div>
                <div class="p-2 bg-light rounded">{{ $laporan->keterangan }}</div>
            </div>
            @endif
        </div>

        @if($laporan->status === 'ditolak' && $laporan->catatan_penolakan)
        <div class="alert alert-danger mt-3 mb-0">
            <strong><i class="bi bi-exclamation-triangle-fill me-1"></i>Laporan Ditolak</strong>
            <div>{{ $laporan->catatan_penolakan }}</div>
            <div class="small mt-1 text-muted">
                oleh {{ $laporan->validator->name ?? '-' }}
                pada {{ optional($laporan->validated_at)->isoFormat('D MMMM Y') }}
            </div>
        </div>
        @endif
    </div>

    @if(in_array($laporan->status, ['draft', 'ditolak']))
    <div class="card-footer bg-white">
        <a href="{{ route('petugas.laporan.edit', $laporan) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>
            {{ $laporan->status === 'ditolak' ? 'Perbaiki & Kirim Ulang' : 'Edit Draft' }}
        </a>
    </div>
    @endif
</div>
@endsection
