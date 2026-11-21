<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePembayaranRequest;
use App\Models\Tagihan;
use Illuminate\Http\JsonResponse;
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

    public function bayar(StorePembayaranRequest $request, Tagihan $tagihan): RedirectResponse|JsonResponse
    {
        if ($tagihan->status === 'lunas') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tagihan sudah lunas.'], 422);
            }

            return back()->withErrors(['jumlah' => 'Tagihan sudah lunas.']);
        }

        $data = $request->validated();
        $pembayaran = $tagihan->pembayarans()->create([
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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dicatat.',
                'status' => $tagihan->fresh()->status,
                'status_label' => ucfirst($tagihan->fresh()->status),
                'terbayar' => $totalBayar,
                'terbayar_formatted' => number_format($totalBayar, 0, ',', '.'),
                'sisa' => max(0, (float) $tagihan->total_bayar - $totalBayar),
                'sisa_formatted' => number_format(max(0, (float) $tagihan->total_bayar - $totalBayar), 0, ',', '.'),
                'kuitansi_url' => route('tagihan.kuitansi', $tagihan),
                'pembayaran' => [
                    'tgl_bayar' => $pembayaran->tgl_bayar->format('d M Y H:i'),
                    'metode' => strtoupper($pembayaran->metode),
                    'kasir' => $request->user()->name,
                    'keterangan' => $pembayaran->keterangan ?? '-',
                    'jumlah' => number_format((float) $pembayaran->jumlah, 0, ',', '.'),
                ],
            ]);
        }

        return back()->with('status', 'Pembayaran dicatat.');
    }

    public function kuitansi(Tagihan $tagihan): View
    {
        $tagihan->load(['kunjungan.pasien', 'details', 'pembayarans']);

        return view('resepsionis.tagihan.kuitansi', compact('tagihan'));
    }
}
