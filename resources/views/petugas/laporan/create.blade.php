@extends('layouts.app')
@section('title', 'Buat Laporan Panen')

@section('content')
<div class="mb-3">
    <a href="{{ route('petugas.laporan.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <h5 class="fw-bold mt-1 mb-0">Formulir Laporan Hasil Panen</h5>
    <small class="text-muted">Kecamatan: <strong>{{ $user->kecamatan->nama ?? '-' }}</strong></small>
</div>

<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-body">
        <form method="POST" action="{{ route('petugas.laporan.store') }}" id="laporanForm">
            @csrf

            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Desa / Kelurahan <span class="text-danger">*</span></label>
                    <select name="desa_id" class="form-select @error('desa_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Desa --</option>
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}" {{ old('desa_id') == $desa->id ? 'selected' : '' }}>
                                {{ $desa->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('desa_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Musim Tanam <span class="text-danger">*</span></label>
                    <select name="musim_tanam" class="form-select @error('musim_tanam') is-invalid @enderror" required>
                        <option value="">-- Pilih Musim --</option>
                        <option value="Musim Hujan" {{ old('musim_tanam') === 'Musim Hujan' ? 'selected' : '' }}>Musim Hujan</option>
                        <option value="Musim Kemarau" {{ old('musim_tanam') === 'Musim Kemarau' ? 'selected' : '' }}>Musim Kemarau</option>
                    </select>
                    @error('musim_tanam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                    <input type="number" name="tahun" class="form-control @error('tahun') is-invalid @enderror"
                           value="{{ old('tahun', date('Y')) }}" min="2000" max="{{ date('Y') + 1 }}" required>
                    @error('tahun')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Luas Tanam (ha) <span class="text-danger">*</span></label>
                    <input type="number" name="luas_tanam" class="form-control @error('luas_tanam') is-invalid @enderror"
                           value="{{ old('luas_tanam') }}" step="0.01" min="0.01" required>
                    @error('luas_tanam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Luas Panen (ha) <span class="text-danger">*</span></label>
                    <input type="number" name="luas_panen" id="luasPanen"
                           class="form-control @error('luas_panen') is-invalid @enderror"
                           value="{{ old('luas_panen') }}" step="0.01" min="0.01" required>
                    @error('luas_panen')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Produksi (ton) <span class="text-danger">*</span></label>
                    <input type="number" name="produksi" id="produksi"
                           class="form-control @error('produksi') is-invalid @enderror"
                           value="{{ old('produksi') }}" step="0.01" min="0.01" required>
                    @error('produksi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Produktivitas (ton/ha)</label>
                    <input type="text" id="produktivitas" class="form-control bg-light" readonly
                           placeholder="Dihitung otomatis">
                    <div class="form-text">Hasil bagi produksi ÷ luas panen</div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Varietas Padi <span class="text-danger">*</span></label>
                    <input type="text" name="varietas_padi"
                           class="form-control @error('varietas_padi') is-invalid @enderror"
                           value="{{ old('varietas_padi') }}" placeholder="Contoh: Ciherang, IR64, Inpari 32"
                           list="varietasList" required>
                    <datalist id="varietasList">
                        <option value="Ciherang">
                        <option value="IR64">
                        <option value="Inpari 32">
                        <option value="Mekongga">
                        <option value="Cisantana">
                    </datalist>
                    @error('varietas_padi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Keterangan (opsional)</label>
                    <textarea name="keterangan" class="form-control" rows="2"
                              placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
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
                <a href="{{ route('petugas.laporan.index') }}" class="btn btn-link text-muted">Batal</a>
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
            ? (pr / lp).toFixed(4) + ' ton/ha'
            : '';
    }
    document.getElementById('luasPanen').addEventListener('input', hitungProduktivitas);
    document.getElementById('produksi').addEventListener('input', hitungProduktivitas);
</script>
@endpush
