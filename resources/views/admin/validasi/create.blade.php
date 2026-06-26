@extends('layouts.app')
@section('title', 'Buat Laporan Panen (Admin)')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.validasi.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <h5 class="fw-bold mt-1 mb-0">Formulir Laporan Hasil Panen (Oleh Admin)</h5>
</div>

<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.validasi.store') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Petugas <span class="text-danger">*</span></label>
                    <select name="user_id" id="userId" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Petugas --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" data-kecamatan="{{ $user->kecamatan_id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Desa / Kelurahan <span class="text-danger">*</span></label>
                    <select name="desa_id" id="desaId" class="form-select @error('desa_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Desa --</option>
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}" data-kecamatan="{{ $desa->kecamatan_id }}" {{ old('desa_id') == $desa->id ? 'selected' : '' }}>
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
                    <label class="form-label fw-semibold">Tanggal Panen <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_panen" class="form-control @error('tanggal_panen') is-invalid @enderror"
                           value="{{ old('tanggal_panen', date('Y-m-d')) }}" required>
                    @error('tanggal_panen')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                    <i class="bi bi-send me-1"></i> Kirim & Validasi (Sesuai)
                </button>
                <a href="{{ route('admin.validasi.index') }}" class="btn btn-link text-muted">Batal</a>
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
    
    document.getElementById('userId').addEventListener('change', function() {
        const selectedKecamatan = this.options[this.selectedIndex].getAttribute('data-kecamatan');
        const desaSelect = document.getElementById('desaId');
        Array.from(desaSelect.options).forEach(option => {
            if (option.value === "") return;
            if (option.getAttribute('data-kecamatan') === selectedKecamatan) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });
        desaSelect.value = '';
    });
</script>
@endpush
