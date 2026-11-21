<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ObatController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'semua');
        $search = $request->query('q');

        $query = Obat::query();

        if ($filter === 'menipis') {
            $query->whereColumn('stok', '<=', 'stok_minimum')->where('stok', '>', 0);
        } elseif ($filter === 'habis') {
            $query->where('stok', '<=', 0);
        }

        if ($search) {
            $query->where(function ($q) use ($search): void {
                $q->where('nama_generik', 'like', "%{$search}%")
                    ->orWhere('nama_dagang', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }

        $obats = $query->orderBy('nama_generik')->paginate(15)->withQueryString();

        $totalObat = Obat::count();
        $menipisCount = Obat::whereColumn('stok', '<=', 'stok_minimum')->where('stok', '>', 0)->count();
        $habisCount = Obat::where('stok', '<=', 0)->count();

        return view('apoteker.obat.index', compact('obats', 'filter', 'search', 'totalObat', 'menipisCount', 'habisCount'));
    }

    public function adjustStock(Request $request, Obat $obat): RedirectResponse
    {
        $validated = $request->validate([
            'tipe' => ['required', Rule::in(['masuk', 'keluar', 'koreksi'])],
            'jumlah' => ['required', 'integer', 'min:1'],
            'alasan' => ['required', 'string', 'max:255'],
        ]);

        $tipe = $validated['tipe'];
        $jumlah = (int) $validated['jumlah'];
        $alasan = $validated['alasan'];
        $stokAwal = $obat->stok;

        if ($tipe === 'masuk') {
            $obat->increment('stok', $jumlah);
            $stokAkhir = $stokAwal + $jumlah;
            $msg = "Penambahan stok {$obat->nama_generik} sebanyak {$jumlah} {$obat->satuan} berhasil (Alasan: {$alasan}).";
        } elseif ($tipe === 'keluar') {
            $kurang = min($stokAwal, $jumlah);
            $obat->decrement('stok', $kurang);
            $stokAkhir = $stokAwal - $kurang;
            $msg = "Pengurangan stok {$obat->nama_generik} sebanyak {$kurang} {$obat->satuan} berhasil (Alasan: {$alasan}).";
        } else { // koreksi
            $obat->update(['stok' => $jumlah]);
            $stokAkhir = $jumlah;
            $msg = "Koreksi stok {$obat->nama_generik} menjadi {$jumlah} {$obat->satuan} berhasil (Alasan: {$alasan}).";
        }

        return back()->with('status', $msg);
    }
}
