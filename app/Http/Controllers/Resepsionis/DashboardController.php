<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\Tagihan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = now()->toDateString();

        $totalHariIni = Kunjungan::whereDate('tgl_kunjungan', $today)->count();
        $menunggu = Kunjungan::whereDate('tgl_kunjungan', $today)->whereIn('status', ['menunggu', 'dipanggil', 'diperiksa'])->count();
        $selesai = Kunjungan::whereDate('tgl_kunjungan', $today)->where('status', 'selesai')->count();
        $pendapatan = (float) Tagihan::whereHas('kunjungan', fn ($q) => $q->whereDate('tgl_kunjungan', $today))->where('status', 'lunas')->sum('total_bayar');

        $kunjunganHariIni = Kunjungan::with(['pasien', 'dokter.poli', 'poli'])
            ->whereDate('tgl_kunjungan', $today)
            ->orderBy('tgl_kunjungan')
            ->orderBy('no_antrean')
            ->get();

        return view('resepsionis.dashboard', compact('totalHariIni', 'menunggu', 'selesai', 'pendapatan', 'kunjunganHariIni'));
    }
}
