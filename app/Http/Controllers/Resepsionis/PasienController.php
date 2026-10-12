<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePasienRequest;
use App\Http\Requests\UpdatePasienRequest;
use App\Models\Pasien;
use App\Services\NoGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasienController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $q = trim((string) $request->input('q', ''));

        $query = Pasien::query()->when($q !== '', function ($qry) use ($q): void {
            $qry->where(function ($w) use ($q): void {
                $w->where('no_rm', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%");
            });
        })->orderBy('nama');

        if ($q !== '') {
            $count = (clone $query)->count();
            if ($count === 1) {
                return redirect()->route('pasien.show', $query->firstOrFail());
            }
        }

        $pasiens = $query->get();

        return view('resepsionis.pasien.index', compact('pasiens', 'q'));
    }

    public function create(): View
    {
        return view('resepsionis.pasien.form', ['pasien' => new Pasien]);
    }

    public function store(StorePasienRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['no_rm'] = NoGenerator::nextNoRm();
        $pasien = Pasien::create($data);

        return redirect()->route('pasien.show', $pasien)->with('status', 'Pasien berhasil ditambahkan.');
    }

    public function show(Pasien $pasien): View
    {
        $pasien->load(['kunjungans.dokter.poli', 'kunjungans.poli', 'kunjungans.tagihan']);

        return view('resepsionis.pasien.show', compact('pasien'));
    }

    public function edit(Pasien $pasien): View
    {
        return view('resepsionis.pasien.form', compact('pasien'));
    }

    public function update(UpdatePasienRequest $request, Pasien $pasien): RedirectResponse
    {
        $pasien->update($request->validated());

        return redirect()->route('pasien.show', $pasien)->with('status', 'Data pasien diperbarui.');
    }

    public function destroy(Pasien $pasien): RedirectResponse
    {
        $nama = $pasien->nama;
        $pasien->delete();

        return redirect()->route('pasien.index')->with('status', "Pasien {$nama} dihapus beserta kunjungannya.");
    }
}
