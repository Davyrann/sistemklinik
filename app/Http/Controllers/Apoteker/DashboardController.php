<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use App\Models\Resep;
use App\Models\Tagihan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $resepMenungguCount = Resep::whereIn('status', ['menunggu', 'draft'])->count();
        $resepDiserahkanHariIni = Resep::where('status', 'diserahkan')
            ->whereDate('updated_at', today())
            ->count();
        $tagihanBelumLunasCount = Tagihan::where('status', 'belum')->count();
        $obatMenipisCount = Obat::where('is_aktif', true)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->count();

        // Resep terlama menunggu di atas (DESIGN.md R-26 / 9.3)
        $resepMenunggu = Resep::with(['kunjungan.pasien', 'kunjungan.poli', 'dokter', 'details.obat'])
            ->whereIn('status', ['menunggu', 'draft', 'diproses'])
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        $tagihanBelumLunas = Tagihan::with(['kunjungan.pasien', 'kunjungan.poli'])
            ->where('status', 'belum')
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        $obatPerluPerhatian = Obat::where('is_aktif', true)
            ->whereColumn('stok', '<=', 'stok_minimum')
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get();

        return view('apoteker.dashboard', compact(
            'resepMenungguCount',
            'resepDiserahkanHariIni',
            'tagihanBelumLunasCount',
            'obatMenipisCount',
            'resepMenunggu',
            'tagihanBelumLunas',
            'obatPerluPerhatian'
        ));
    }
}
