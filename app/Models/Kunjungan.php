<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kunjungan extends Model
{
    protected $table = 'kunjungan';

    protected $fillable = ['pasien_id', 'dokter_id', 'poli_id', 'user_id', 'tgl_kunjungan', 'no_antrean', 'status', 'keluhan_utama', 'penjamin'];

    protected function casts(): array
    {
        return ['tgl_kunjungan' => 'date'];
    }

    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tandaVital(): HasOne
    {
        return $this->hasOne(TandaVital::class);
    }

    public function rekamMedis(): HasOne
    {
        return $this->hasOne(RekamMedis::class);
    }

    public function resep(): HasOne
    {
        return $this->hasOne(Resep::class);
    }

    public function tagihan(): HasOne
    {
        return $this->hasOne(Tagihan::class);
    }
}
