<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePembayaranRequest;
use App\Models\Tagihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagihanController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));
        $status = $request->input('status');

        $tagihans = Tagihan::with(['kunjungan.pasien', 'kunjungan.dokter', 'details'])
            ->when($q !== '', fn ($qry) => $qry->whereHas('kunjungan.pasien', fn ($w) => $w->where('nama', 'like', "%{$q}%")->orWhere('no_rm', 'like', "%{$q}%")))
            ->when($status, fn ($qry) => $qry->where('status', $status))
            ->latest('id')
            ->get();

        return view('resepsionis.tagihan.index', compact('tagihans', 'q', 'status'));
    }

    public function show(Tagihan $tagihan): View
    {
        $tagihan->load(['kunjungan.pasien', 'kunjungan.dokter.poli', 'kunjungan.poli', 'details', 'pembayarans.kasir']);

        return view('resepsionis.tagihan.show', compact('tagihan'));
    }

    public function bayar(StorePembayaranRequest $request, Tagihan $tagihan): RedirectResponse
    {
        if ($tagihan->status === 'lunas') {
            return back()->withErrors(['jumlah' => 'Tagihan sudah lunas.']);
        }

        $data = $request->validated();
        $tagihan->pembayarans()->create([
            'metode' => $data['metode'],
            'jumlah' => $data['jumlah'],
            'keterangan' => $data['keterangan'] ?? null,
            'kasir_id' => $request->user()->id,
            'tgl_bayar' => now(),
        ]);

        $totalBayar = (float) $tagihan->pembayarans()->sum('jumlah');
        if ($totalBayar >= (float) $tagihan->total_bayar) {
            $tagihan->update(['status' => 'lunas']);
        }

        return back()->with('status', 'Pembayaran dicatat.');
    }

    public function kuitansi(Tagihan $tagihan): View
    {
        $tagihan->load(['kunjungan.pasien', 'details', 'pembayarans']);

        return view('resepsionis.tagihan.kuitansi', compact('tagihan'));
    }
}
