<?php

namespace App\Services;

use App\Models\Kunjungan;
use App\Models\Pasien;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class NoGenerator
{
    public static function nextNoRm(): string
    {
        $year = now()->format('Y');
        $max = Pasien::where('no_rm', 'like', "RM-{$year}-%")->max('no_rm');
        $num = $max ? (int) substr($max, -4) + 1 : 1;

        return sprintf('RM-%s-%04d', $year, $num);
    }

    public static function nextNoAntrean(int $poliId, string $tgl): string
    {
        $kode = DB::table('poli')->where('id', $poliId)->value('kode') ?? 'UMUM';
        $date = Carbon::parse($tgl)->toDateString();
        $max = Kunjungan::where('poli_id', $poliId)->whereDate('tgl_kunjungan', $date)->max('no_antrean');
        $num = $max ? (int) substr($max, -3) : 0;

        return sprintf('%s-%03d', $kode, $num + 1);
    }
}
