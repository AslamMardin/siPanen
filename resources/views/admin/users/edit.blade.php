@extends('layouts.app')
@section('title', 'Edit Pengguna')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
    <h5 class="fw-bold mt-1 mb-0">Edit Pengguna – {{ $user->name }}</h5>
</div>

<div class="row g-3" style="max-width:700px">
    {{-- Edit Data --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Data Pengguna</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Role</label>
                        <select name="role" id="roleSelect" class="form-select">
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="petugas" {{ $user->role === 'petugas' ? 'selected' : '' }}>Petugas Kecamatan</option>
                            <option value="pimpinan" {{ $user->role === 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                        </select>
                    </div>

                    <div class="mb-3" id="kecamatanField">
                        <label class="form-label fw-semibold">Kecamatan</label>
                        <select name="kecamatan_id" class="form-select">
                            <option value="">-- Tidak Ditugaskan --</option>
                            @foreach($kecamatans as $kec)
                                <option value="{{ $kec->id }}"
                                    {{ old('kecamatan_id', $user->kecamatan_id) == $kec->id ? 'selected' : '' }}>
                                    {{ $kec->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 form-check form-switch">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                               {{ $user->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="isActive">Akun Aktif</label>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Reset Password --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm border-warning">
            <div class="card-header bg-warning-subtle fw-semibold">Reset Password</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
                    @csrf @method('PATCH')
                    <div class="row g-2">
                        <div class="col-md-5">
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Password baru (min. 8)" required minlength="8">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5">
                            <input type="password" name="password_confirmation" class="form-control"
                                   placeholder="Konfirmasi password" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-warning w-100">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const roleSelect   = document.getElementById('roleSelect');
    const kecamatanDiv = document.getElementById('kecamatanField');
    roleSelect.addEventListener('change', () => {
        kecamatanDiv.style.display = roleSelect.value === 'petugas' ? '' : 'none';
    });
    kecamatanDiv.style.display = roleSelect.value === 'petugas' ? '' : 'none';
</script>
@endpush
