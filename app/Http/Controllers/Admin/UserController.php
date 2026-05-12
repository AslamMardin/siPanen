<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('kecamatan')
            ->whereNot('id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $kecamatans = Kecamatan::orderBy('nama')->get();
        return view('admin.users.create', compact('kecamatans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users'],
            'password'      => ['required', 'min:8', 'confirmed'],
            'role'          => ['required', Rule::in(['admin', 'petugas', 'pimpinan'])],
            'kecamatan_id'  => ['nullable', Rule::requiredIf($request->role === 'petugas'), 'exists:kecamatans,id'],
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $kecamatans = Kecamatan::orderBy('nama')->get();
        return view('admin.users.edit', compact('user', 'kecamatans'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'          => ['required', Rule::in(['admin', 'petugas', 'pimpinan'])],
            'kecamatan_id'  => ['nullable', 'exists:kecamatans,id'],
            'is_active'     => ['boolean'],
        ]);

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password berhasil direset.');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Pengguna berhasil {$status}.");
    }
}
