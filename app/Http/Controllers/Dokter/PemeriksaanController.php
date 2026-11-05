<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use App\Models\Kunjungan;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\ResepDetail;
use App\Models\TagihanDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PemeriksaanController extends Controller
{
    public function edit(Request $request, Kunjungan $kunjungan): View
    {
        if (in_array($kunjungan->status, ['menunggu', 'dipanggil'], true)) {
            $kunjungan->update(['status' => 'diperiksa']);
        }

        $kunjungan->load([
            'pasien',
            'poli',
            'dokter.poli',
            'tandaVital',
            'rekamMedis.diagnoses',
            'resep.details.obat',
        ]);

        $riwayatKunjungan = Kunjungan::with([
            'dokter.poli',
            'poli',
            'tandaVital',
            'rekamMedis.diagnoses',
            'resep.details.obat',
        ])
            ->where('pasien_id', $kunjungan->pasien_id)
            ->where('id', '!=', $kunjungan->id)
            ->where('status', 'selesai')
            ->orderByDesc('tgl_kunjungan')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $obats = Obat::where('is_aktif', true)
            ->where('stok', '>', 0)
            ->orderBy('nama_generik')
            ->get();

        return view('dokter.pemeriksaan', compact('kunjungan', 'riwayatKunjungan', 'obats'));
    }

    public function panggil(Kunjungan $kunjungan): RedirectResponse
    {
        $kunjungan->update(['status' => 'dipanggil']);

        return back()->with('status', "Pasien antrean {$kunjungan->no_antrean} ({$kunjungan->pasien->nama}) dipanggil.");
    }

    public function update(Request $request, Kunjungan $kunjungan): RedirectResponse
    {
        if ($kunjungan->rekamMedis?->is_locked) {
            return redirect()->route('dokter.pemeriksaan.edit', $kunjungan)
                ->with('error', 'Rekam medis sudah dikunci dan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'td_sistolik' => ['nullable', 'integer', 'min:40', 'max:300'],
            'td_diastolik' => ['nullable', 'integer', 'min:30', 'max:200'],
            'nadi' => ['nullable', 'integer', 'min:30', 'max:250'],
            'suhu' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'rr' => ['nullable', 'integer', 'min:8', 'max:80'],
            'spo2' => ['nullable', 'integer', 'min:50', 'max:100'],
            'bb' => ['nullable', 'numeric', 'min:1', 'max:350'],
            'tb' => ['nullable', 'numeric', 'min:30', 'max:250'],
            'subjective' => ['nullable', 'string'],
            'objective' => ['nullable', 'string'],
            'assessment' => ['nullable', 'string'],
            'plan' => ['nullable', 'string'],
            'diagnoses' => ['nullable', 'array'],
            'diagnoses.*.kode_icd10' => ['nullable', 'string', 'max:20'],
            'diagnoses.*.nama_diagnosis' => ['required_with:diagnoses.*.kode_icd10', 'nullable', 'string', 'max:255'],
            'diagnoses.*.is_primary' => ['nullable'],
            'resep_catatan' => ['nullable', 'string', 'max:500'],
            'resep_items' => ['nullable', 'array'],
            'resep_items.*.obat_id' => ['required', 'exists:obat,id'],
            'resep_items.*.jumlah' => ['required', 'integer', 'min:1'],
            'resep_items.*.dosis' => ['nullable', 'string', 'max:100'],
            'resep_items.*.frekuensi' => ['nullable', 'string', 'max:100'],
            'resep_items.*.aturan_pakai' => ['nullable', 'string', 'max:100'],
            'action' => ['required', 'in:draft,selesai'],
        ]);

        $isLocked = $validated['action'] === 'selesai';

        DB::transaction(function () use ($validated, $kunjungan, $request, $isLocked): void {
            // 1. Tanda Vital & IMT
            $bb = isset($validated['bb']) ? (float) $validated['bb'] : null;
            $tb = isset($validated['tb']) ? (float) $validated['tb'] : null;
            $imt = ($bb && $tb && $tb > 0) ? round($bb / (($tb / 100) ** 2), 1) : null;

            $kunjungan->tandaVital()->updateOrCreate(
                ['kunjungan_id' => $kunjungan->id],
                [
                    'td_sistolik' => $validated['td_sistolik'] ?? null,
                    'td_diastolik' => $validated['td_diastolik'] ?? null,
                    'nadi' => $validated['nadi'] ?? null,
                    'suhu' => $validated['suhu'] ?? null,
                    'rr' => $validated['rr'] ?? null,
                    'spo2' => $validated['spo2'] ?? null,
                    'bb' => $bb,
                    'tb' => $tb,
                    'imt' => $imt,
                    'dicatat_oleh' => $request->user()->id,
                ]
            );

            // 2. Rekam Medis (SOAP)
            $dokterId = $request->user()->dokter?->id ?? $kunjungan->dokter_id;

            $rm = $kunjungan->rekamMedis()->updateOrCreate(
                ['kunjungan_id' => $kunjungan->id],
                [
                    'dokter_id' => $dokterId,
                    'subjective' => $validated['subjective'] ?? null,
                    'objective' => $validated['objective'] ?? null,
                    'assessment' => $validated['assessment'] ?? null,
                    'plan' => $validated['plan'] ?? null,
                    'is_locked' => $isLocked,
                    'locked_at' => $isLocked ? now() : null,
                ]
            );

            // 3. Diagnoses
            $rm->diagnoses()->delete();
            if (! empty($validated['diagnoses'])) {
                foreach ($validated['diagnoses'] as $idx => $d) {
                    if (! empty($d['nama_diagnosis']) || ! empty($d['kode_icd10'])) {
                        Diagnosis::create([
                            'rekam_medis_id' => $rm->id,
                            'kode_icd10' => $d['kode_icd10'] ?? null,
                            'nama_diagnosis' => $d['nama_diagnosis'] ?? $d['kode_icd10'],
                            'is_primary' => ! empty($d['is_primary']) || $idx === 0,
                        ]);
                    }
                }
            }

            // 4. Resep Obat & Tagihan
            if (! empty($validated['resep_items'])) {
                $resep = $kunjungan->resep()->updateOrCreate(
                    ['kunjungan_id' => $kunjungan->id],
                    [
                        'dokter_id' => $dokterId,
                        'status' => $isLocked ? 'diserahkan' : 'draft',
                        'catatan' => $validated['resep_catatan'] ?? null,
                    ]
                );

                $resep->details()->delete();

                foreach ($validated['resep_items'] as $item) {
                    $obat = Obat::find($item['obat_id']);
                    if (! $obat) {
                        continue;
                    }

                    ResepDetail::create([
                        'resep_id' => $resep->id,
                        'obat_id' => $obat->id,
                        'jumlah' => $item['jumlah'],
                        'dosis' => $item['dosis'] ?? null,
                        'frekuensi' => $item['frekuensi'] ?? null,
                        'aturan_pakai' => $item['aturan_pakai'] ?? null,
                    ]);

                    if ($isLocked && $obat->stok >= $item['jumlah']) {
                        $obat->decrement('stok', $item['jumlah']);
                    }
                }

                // Sinkronisasi Tagihan Pasien
                $tagihan = $kunjungan->tagihan;
                if ($tagihan) {
                    $tagihan->details()->where('jenis', 'obat')->delete();

                    foreach ($validated['resep_items'] as $item) {
                        $obat = Obat::find($item['obat_id']);
                        if ($obat) {
                            TagihanDetail::create([
                                'tagihan_id' => $tagihan->id,
                                'deskripsi' => "Obat: {$obat->nama_generik}".($obat->nama_dagang ? " ({$obat->nama_dagang})" : ''),
                                'qty' => $item['jumlah'],
                                'harga_satuan' => $obat->harga,
                                'subtotal' => (float) $obat->harga * (int) $item['jumlah'],
                                'jenis' => 'obat',
                            ]);
                        }
                    }

                    $totalSubtotal = (float) $tagihan->details()->sum('subtotal');
                    $diskon = (float) ($tagihan->diskon ?? 0);
                    $tagihan->update([
                        'total' => $totalSubtotal,
                        'total_bayar' => max(0, $totalSubtotal - $diskon),
                    ]);
                }
            }

            // 5. Update Status Kunjungan
            if ($isLocked) {
                $kunjungan->update(['status' => 'selesai']);
            } else {
                $kunjungan->update(['status' => 'diperiksa']);
            }
        });

        $pesan = $isLocked
            ? "Pemeriksaan pasien {$kunjungan->pasien->nama} selesai dan rekam medis berhasil dikunci."
            : "Draf rekam medis pasien {$kunjungan->pasien->nama} berhasil disimpan.";

        return redirect()->route('dokter.dashboard')->with('status', $pesan);
    }

    public function riwayatPasien(Pasien $pasien): View
    {
        $pasien->load([
            'kunjungans' => function ($q): void {
                $q->with([
                    'dokter.poli',
                    'poli',
                    'tandaVital',
                    'rekamMedis.diagnoses',
                    'resep.details.obat',
                ])
                    ->orderByDesc('tgl_kunjungan')
                    ->orderByDesc('id');
            },
        ]);

        return view('dokter.pasien', compact('pasien'));
    }
}
