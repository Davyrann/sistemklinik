<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $dokter = $user->dokter ?? Dokter::where('user_id', $user->id)->first();

        // Admin fallback jika tidak memiliki profil dokter
        if (! $dokter && $user->isAdmin()) {
            $dokter = Dokter::with('poli')->first();
        }

        $tgl = (string) $request->input('tgl', now()->toDateString());

        $kunjungans = Kunjungan::with(['pasien', 'poli', 'dokter', 'tandaVital', 'rekamMedis.diagnoses', 'resep'])
            ->whereDate('tgl_kunjungan', $tgl)
            ->when($dokter, fn ($q) => $q->where('dokter_id', $dokter->id))
            ->orderByRaw("CASE WHEN status = 'diperiksa' THEN 1 WHEN status = 'dipanggil' THEN 2 WHEN status = 'menunggu' THEN 3 WHEN status = 'selesai' THEN 4 ELSE 5 END")
            ->orderBy('no_antrean')
            ->get();

        $totalHariIni = $kunjungans->count();
        $menunggu = $kunjungans->whereIn('status', ['menunggu', 'dipanggil'])->count();
        $diperiksa = $kunjungans->where('status', 'diperiksa')->count();
        $selesai = $kunjungans->where('status', 'selesai')->count();

        return view('dokter.dashboard', compact(
            'dokter',
            'kunjungans',
            'tgl',
            'totalHariIni',
            'menunggu',
            'diperiksa',
            'selesai'
        ));
    }
}
