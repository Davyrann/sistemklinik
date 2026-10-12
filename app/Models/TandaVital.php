<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TandaVital extends Model
{
    protected $table = 'tanda_vital';

    protected $fillable = ['kunjungan_id', 'td_sistolik', 'td_diastolik', 'nadi', 'suhu', 'rr', 'spo2', 'bb', 'tb', 'imt', 'dicatat_oleh'];

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }
}
