<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Poli;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PoliController extends Controller
{
    public function index(): View
    {
        $polis = Poli::with(['dokters.user'])
            ->withCount(['dokters', 'kunjungans'])
            ->orderBy('nama')
            ->get();

        $totalPoli = $polis->count();
        $poliAktif = $polis->where('is_aktif', true)->count();
        $totalDokter = Dokter::where('is_aktif', true)->count();
        $totalKunjungan = $polis->sum('kunjungans_count');

        return view('admin.poli.index', compact(
            'polis',
            'totalPoli',
            'poliAktif',
            'totalDokter',
            'totalKunjungan'
        ));
    }

    public function create(): View
    {
        return view('admin.poli.form', [
            'poli' => new Poli,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:poli,kode'],
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        $data['kode'] = strtoupper(trim($data['kode']));
        $data['is_aktif'] = $request->boolean('is_aktif', true);

        Poli::create($data);

        return redirect()->route('admin.poli.index')->with('status', 'Unit poli berhasil ditambahkan.');
    }

    public function show(Poli $poli): View
    {
        $poli->load(['dokters.user'])->loadCount('kunjungans');

        return view('admin.poli.show', compact('poli'));
    }

    public function edit(Poli $poli): View
    {
        return view('admin.poli.form', compact('poli'));
    }

    public function update(Request $request, Poli $poli): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:20', Rule::unique('poli', 'kode')->ignore($poli->id)],
            'nama' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'is_aktif' => ['nullable', 'boolean'],
        ]);

        $data['kode'] = strtoupper(trim($data['kode']));
        $data['is_aktif'] = $request->boolean('is_aktif', true);

        $poli->update($data);

        return redirect()->route('admin.poli.index')->with('status', "Data poli {$poli->nama} diperbarui.");
    }

    public function destroy(Poli $poli): RedirectResponse
    {
        // Deactivate instead of hard deleting per clinical safety guidelines (DESIGN.md:515)
        $poli->update(['is_aktif' => false]);

        return redirect()->route('admin.poli.index')->with('status', "Poli {$poli->nama} dinonaktifkan.");
    }
}
