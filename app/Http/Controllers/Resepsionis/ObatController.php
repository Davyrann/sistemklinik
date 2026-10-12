<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ObatController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));

        $obats = Obat::query()
            ->when($q !== '', fn ($qry) => $qry->where('nama_generik', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%")->orWhere('nama_dagang', 'like', "%{$q}%"))
            ->where('is_aktif', true)
            ->orderBy('nama_generik')
            ->get();

        return view('resepsionis.obat.index', compact('obats', 'q'));
    }

    public function show(Obat $obat): View
    {
        return view('resepsionis.obat.show', compact('obat'));
    }
}
