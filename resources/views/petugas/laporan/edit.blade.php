@extends('layouts.app')
@section('title', 'Edit Laporan')

@section('content')
<div class="mb-3">
    <a href="{{ route('petugas.laporan.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <h5 class="fw-bold mt-1 mb-0">Edit Laporan Panen</h5>
    @if($laporan->status === 'ditolak')
        <div class="alert alert-warning mt-2 mb-0 py-2">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>
            <strong>Ditolak:</strong> {{ $laporan->catatan_penolakan }}
        </div>
    @endif
</div>

<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-body">
        <form method="POST" action="{{ route('petugas.laporan.update', $laporan) }}">
            @csrf @method('PUT')

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Desa / Kelurahan</label>
                    <select name="desa_id" class="form-select @error('desa_id') is-invalid @enderror" required>
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}"
                                {{ old('desa_id', $laporan->desa_id) == $desa->id ? 'selected' : '' }}>
                                {{ $desa->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('desa_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Musim Tanam</label>
                    <select name="musim_tanam" class="form-select" required>
                        <option value="Musim Hujan" {{ old('musim_tanam', $laporan->musim_tanam) === 'Musim Hujan' ? 'selected' : '' }}>Musim Hujan</option>
                        <option value="Musim Kemarau" {{ old('musim_tanam', $laporan->musim_tanam) === 'Musim Kemarau' ? 'selected' : '' }}>Musim Kemarau</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tahun</label>
                    <input type="number" name="tahun" class="form-control"
                           value="{{ old('tahun', $laporan->tahun) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Panen</label>
                    <input type="date" name="tanggal_panen" class="form-control @error('tanggal_panen') is-invalid @enderror"
                           value="{{ old('tanggal_panen', $laporan->tanggal_panen?->format('Y-m-d')) }}" required>
                    @error('tanggal_panen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Luas Tanam (ha)</label>
                    <input type="number" name="luas_tanam" class="form-control"
                           value="{{ old('luas_tanam', $laporan->luas_tanam) }}" step="0.01" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Luas Panen (ha)</label>
                    <input type="number" name="luas_panen" id="luasPanen" class="form-control"
                           value="{{ old('luas_panen', $laporan->luas_panen) }}" step="0.01" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Produksi (ton)</label>
                    <input type="number" name="produksi" id="produksi" class="form-control"
                           value="{{ old('produksi', $laporan->produksi) }}" step="0.01" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Produktivitas (ton/ha)</label>
                    <input type="text" id="produktivitas" class="form-control bg-light" readonly
                           value="{{ number_format($laporan->produktivitas, 4) }} ton/ha">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Varietas Padi</label>
                    <input type="text" name="varietas_padi" class="form-control"
                           value="{{ old('varietas_padi', $laporan->varietas_padi) }}"
                           list="varietasList" required>
                    <datalist id="varietasList">
                        <option value="Ciherang">
                        <option value="IR64">
                        <option value="Inpari 32">
                        <option value="Mekongga">
                    </datalist>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $laporan->keterangan) }}</textarea>
                </div>
            </div>

            <hr class="my-3">
            <div class="d-flex gap-2">
                <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
                    <i class="bi bi-floppy me-1"></i> Simpan Draft
                </button>
                <button type="submit" name="action" value="kirim" class="btn btn-success">
                    <i class="bi bi-send me-1"></i> Kirim untuk Validasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function hitungProduktivitas() {
        const lp = parseFloat(document.getElementById('luasPanen').value) || 0;
        const pr = parseFloat(document.getElementById('produksi').value) || 0;
        document.getElementById('produktivitas').value = lp > 0
            ? (pr / lp).toFixed(4) + ' ton/ha' : '';
    }
    document.getElementById('luasPanen').addEventListener('input', hitungProduktivitas);
    document.getElementById('produksi').addEventListener('input', hitungProduktivitas);
</script>
@endpush
