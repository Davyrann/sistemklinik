<?php

namespace App\Http\Controllers\Resepsionis;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKunjunganRequest;
use App\Models\Dokter;
use App\Models\Kunjungan;
use App\Models\Pasien;
use App\Models\Poli;
use App\Models\Tagihan;
use App\Models\TagihanDetail;
use App\Services\NoGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KunjunganController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));
        $status = $request->input('status');
        $tgl = $request->input('tgl', now()->toDateString());

        $kunjungans = Kunjungan::with(['pasien', 'dokter', 'poli'])
            ->when($q !== '', function ($qry) use ($q): void {
                $qry->whereHas('pasien', fn ($w) => $w->where('nama', 'like', "%{$q}%")->orWhere('no_rm', 'like', "%{$q}%"))
                    ->orWhere('no_antrean', 'like', "%{$q}%");
            })
            ->when($status, fn ($qry) => $qry->where('status', $status))
            ->when($tgl, fn ($qry) => $qry->whereDate('tgl_kunjungan', $tgl))
            ->orderBy('tgl_kunjungan')
            ->orderBy('no_antrean')
            ->get();

        return view('resepsionis.kunjungan.index', compact('kunjungans', 'q', 'status', 'tgl'));
    }

    public function create(Request $request): View
    {
        $pasien = $request->input('pasien_id') ? Pasien::find($request->input('pasien_id')) : null;
        $polis = Poli::where('is_aktif', true)->orderBy('nama')->get();
        $dokters = Dokter::with('poli')->where('is_aktif', true)->orderBy('nama')->get();
        $pasiens = Pasien::orderBy('nama')->limit(100)->get(['id', 'no_rm', 'nama', 'tgl_lahir']);

        return view('resepsionis.kunjungan.form', compact('pasien', 'polis', 'dokters', 'pasiens'));
    }

    public function store(StoreKunjunganRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['no_antrean'] = NoGenerator::nextNoAntrean((int) $data['poli_id'], $data['tgl_kunjungan']);
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'menunggu';

        $kunjungan = DB::transaction(function () use ($data): Kunjungan {
            $k = Kunjungan::create($data);
            $tagihan = Tagihan::create(['kunjungan_id' => $k->id, 'total' => 60000, 'total_bayar' => 60000, 'status' => 'belum']);
            TagihanDetail::create(['tagihan_id' => $tagihan->id, 'deskripsi' => 'Jasa konsultasi dokter', 'qty' => 1, 'harga_satuan' => 50000, 'subtotal' => 50000, 'jenis' => 'jasa']);
            TagihanDetail::create(['tagihan_id' => $tagihan->id, 'deskripsi' => 'Biaya administrasi', 'qty' => 1, 'harga_satuan' => 10000, 'subtotal' => 10000, 'jenis' => 'admin']);

            return $k;
        });

        return redirect()->route('kunjungan.show', $kunjungan)->with('status', "Kunjungan {$kunjungan->no_antrean} dibuat.");
    }

    public function show(Kunjungan $kunjungan): View
    {
        $kunjungan->load(['pasien', 'dokter.poli', 'poli', 'tagihan.details', 'tagihan.pembayarans', 'rekamMedis', 'resep.details.obat']);

        return view('resepsionis.kunjungan.show', compact('kunjungan'));
    }

    public function updateStatus(Request $request, Kunjungan $kunjungan): RedirectResponse
    {
        $request->validate(['status' => ['required', 'in:menunggu,dipanggil,diperiksa,selesai,batal']]);
        $kunjungan->update(['status' => $request->input('status')]);

        return back()->with('status', "Status diubah ke {$kunjungan->status}.");
    }

    public function destroy(Kunjungan $kunjungan): RedirectResponse
    {
        $kunjungan->delete();

        return redirect()->route('kunjungan.index')->with('status', 'Kunjungan dihapus.');
    }
}
