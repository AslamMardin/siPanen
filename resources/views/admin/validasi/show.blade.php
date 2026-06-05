@extends('layouts.app')
@section('title', 'Detail Laporan')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.validasi.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <h5 class="fw-bold mt-1 mb-0">Detail Laporan Panen</h5>
</div>

<div class="row g-3" style="max-width:800px">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Informasi Laporan</span>
                <span class="badge bg-{{ $laporan->status_badge }} fs-6">{{ $laporan->status_label }}</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Petugas</label>
                            <div class="fw-semibold">{{ $laporan->user->name }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Kecamatan</label>
                            <div class="fw-semibold">{{ $laporan->kecamatan->nama }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Desa / Kelurahan</label>
                            <div class="fw-semibold">{{ $laporan->desa->nama }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Musim Tanam</label>
                            <div class="fw-semibold">{{ $laporan->musim_tanam }} {{ $laporan->tahun }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Tanggal Panen</label>
                            <div class="fw-semibold">{{ optional($laporan->tanggal_panen)->isoFormat('D MMMM Y') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Luas Tanam</label>
                            <div class="fw-semibold">{{ number_format($laporan->luas_tanam, 2) }} ha</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Luas Panen</label>
                            <div class="fw-semibold">{{ number_format($laporan->luas_panen, 2) }} ha</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Produksi</label>
                            <div class="fw-semibold">{{ number_format($laporan->produksi, 2) }} ton</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Produktivitas</label>
                            <div class="fw-semibold">{{ number_format($laporan->produktivitas, 2) }} ton/ha</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Varietas Padi</label>
                            <div class="fw-semibold">{{ $laporan->varietas_padi }}</div>
                        </div>
                    </div>
                    @if($laporan->keterangan)
                    <div class="col-12">
                        <label class="form-label text-muted small mb-1">Keterangan</label>
                        <div class="p-2 bg-light rounded">{{ $laporan->keterangan }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Aksi validasi --}}
    @if($laporan->status === 'menunggu_validasi')
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Tindakan Validasi</div>
            <div class="card-body">
                <div class="d-flex gap-2 flex-wrap">
                    <form method="POST" action="{{ route('admin.validasi.setujui', $laporan) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-success"
                                onclick="return confirm('Setujui laporan ini?')">
                            <i class="bi bi-check-lg me-1"></i> Setujui Laporan
                        </button>
                    </form>

                    <button class="btn btn-danger" data-bs-toggle="collapse" data-bs-target="#tolakForm">
                        <i class="bi bi-x-lg me-1"></i> Tolak Laporan
                    </button>
                </div>

                <div class="collapse mt-3" id="tolakForm">
                    <form method="POST" action="{{ route('admin.validasi.tolak', $laporan) }}">
                        @csrf @method('PATCH')
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Catatan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="catatan_penolakan" class="form-control @error('catatan_penolakan') is-invalid @enderror"
                                      rows="3" required placeholder="Tuliskan alasan penolakan..."></textarea>
                            @error('catatan_penolakan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-danger">Kirim Penolakan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Riwayat validasi --}}
    @if($laporan->validator)
    <div class="col-12">
        <div class="alert alert-{{ $laporan->status === 'disetujui' ? 'success' : 'danger' }} mb-0">
            <strong>{{ $laporan->status === 'disetujui' ? 'Disetujui' : 'Ditolak' }}</strong>
            oleh {{ $laporan->validator->name }}
            pada {{ $laporan->validated_at->isoFormat('D MMMM Y, HH:mm') }}
            @if($laporan->catatan_penolakan)
                <br><em>Catatan: {{ $laporan->catatan_penolakan }}</em>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
