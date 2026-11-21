<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.user.index', [
            'users' => User::with('dokter.poli')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $polis = Poli::where('is_aktif', true)->orderBy('nama')->get();

        return view('admin.user.form', [
            'user' => new User,
            'polis' => $polis,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'resepsionis', 'dokter', 'apoteker'])],
            'poli_id' => ['nullable', 'exists:poli,id'],
            'spesialisasi' => ['nullable', 'string', 'max:100'],
            'nip' => ['nullable', 'string', 'max:30'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
        ]);

        if ($data['role'] === 'dokter') {
            $poliId = $data['poli_id'] ?? Poli::where('is_aktif', true)->value('id');
            if ($poliId) {
                Dokter::create([
                    'user_id' => $user->id,
                    'poli_id' => $poliId,
                    'nama' => $user->name,
                    'nip' => $data['nip'] ?? null,
                    'spesialisasi' => $data['spesialisasi'] ?? 'Dokter Umum',
                    'no_hp' => $data['no_hp'] ?? null,
                    'is_aktif' => true,
                ]);
            }
        }

        return redirect()->route('admin.user.index')->with('status', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $user->load('dokter.poli');
        $polis = Poli::where('is_aktif', true)->orderBy('nama')->get();

        return view('admin.user.form', compact('user', 'polis'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'resepsionis', 'dokter', 'apoteker'])],
            'poli_id' => ['nullable', 'exists:poli,id'],
            'spesialisasi' => ['nullable', 'string', 'max:100'],
            'nip' => ['nullable', 'string', 'max:30'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        if ($data['role'] === 'dokter') {
            $poliId = $data['poli_id'] ?? $user->dokter?->poli_id ?? Poli::where('is_aktif', true)->value('id');
            if ($poliId) {
                Dokter::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'poli_id' => $poliId,
                        'nama' => $user->name,
                        'nip' => $data['nip'] ?? $user->dokter?->nip,
                        'spesialisasi' => $data['spesialisasi'] ?? ($user->dokter?->spesialisasi ?? 'Dokter Umum'),
                        'no_hp' => $data['no_hp'] ?? $user->dokter?->no_hp,
                        'is_aktif' => true,
                    ]
                );
            }
        }

        return redirect()->route('admin.user.index')->with('status', "Data {$user->name} diperbarui.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('admin.user.index')->with('status', 'Tidak bisa menghapus akun sendiri.');
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->route('admin.user.index')->with('status', "Pengguna {$nama} dihapus.");
    }
}
