<?php

namespace App\Http\Controllers\Apoteker;

use App\Http\Controllers\Controller;
use App\Models\Resep;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResepController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('q');

        $query = Resep::with([
            'kunjungan.pasien',
            'kunjungan.poli',
            'kunjungan.tagihan',
            'dokter',
            'details.obat',
        ]);

        if ($status && in_array($status, ['menunggu', 'draft', 'diproses', 'diserahkan', 'batal'], true)) {
            if ($status === 'menunggu') {
                $query->whereIn('status', ['menunggu', 'draft']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($search) {
            $query->whereHas('kunjungan.pasien', function ($q) use ($search): void {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('no_rm', 'like', "%{$search}%");
            })->orWhereHas('dokter', function ($q) use ($search): void {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        // Urutkan terlama menunggu di atas untuk status aktif, atau terbaru untuk riwayat selesai
        if ($status === 'diserahkan') {
            $query->orderByDesc('updated_at');
        } else {
            $query->orderBy('created_at', 'asc');
        }

        $reseps = $query->paginate(15)->withQueryString();

        return view('apoteker.resep.index', compact('reseps', 'status', 'search'));
    }

    public function show(Resep $resep): View
    {
        $resep->load([
            'kunjungan.pasien',
            'kunjungan.poli',
            'kunjungan.tagihan.details',
            'dokter.poli',
            'details.obat',
        ]);

        $pasien = $resep->kunjungan->pasien;
        $alergi = strtolower(trim((string) $pasien->alergi));

        // Analisis peringatan alergi per item obat
        $itemsWithWarning = $resep->details->map(function ($detail) use ($alergi) {
            $hasAllergyWarning = false;
            if ($alergi !== '' && $alergi !== 'tidak ada' && $alergi !== 'tidak ada alergi') {
                $generik = strtolower($detail->obat?->nama_generik ?? '');
                $dagang = strtolower($detail->obat?->nama_dagang ?? '');

                if (
                    str_contains($alergi, $generik) ||
                    ($generik !== '' && str_contains($generik, $alergi)) ||
                    ($dagang !== '' && (str_contains($alergi, $dagang) || str_contains($dagang, $alergi)))
                ) {
                    $hasAllergyWarning = true;
                }
            }

            return [
                'detail' => $detail,
                'has_allergy_warning' => $hasAllergyWarning,
                'stok_cukup' => ($detail->obat?->stok ?? 0) >= $detail->jumlah,
            ];
        });

        return view('apoteker.resep.show', compact('resep', 'pasien', 'itemsWithWarning'));
    }

    public function updateStatus(Request $request, Resep $resep): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['menunggu', 'diproses', 'diserahkan', 'batal'])],
        ]);

        $oldStatus = $resep->status;
        $newStatus = $validated['status'];

        if ($oldStatus !== 'batal' && $newStatus === 'batal') {
            // Pulihkan stok obat bila resep dibatalkan
            foreach ($resep->details as $detail) {
                if ($detail->obat) {
                    $detail->obat->increment('stok', $detail->jumlah);
                }
            }
        }

        $resep->update(['status' => $newStatus]);

        $message = match ($newStatus) {
            'diproses' => 'Resep sedang diproses/disiapkan oleh apoteker.',
            'diserahkan' => 'Obat telah berhasil diserahkan kepada pasien.',
            'batal' => 'Resep telah dibatalkan.',
            default => 'Status resep berhasil diperbarui.',
        };

        return back()->with('status', $message);
    }

    public function etiket(Resep $resep): View
    {
        $resep->load([
            'kunjungan.pasien',
            'kunjungan.poli',
            'dokter',
            'details.obat',
        ]);

        return view('apoteker.resep.etiket', compact('resep'));
    }
}
