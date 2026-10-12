<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResepDetail extends Model
{
    protected $table = 'resep_detail';

    protected $fillable = ['resep_id', 'obat_id', 'dosis', 'frekuensi', 'jumlah', 'aturan_pakai'];

    public function resep(): BelongsTo
    {
        return $this->belongsTo(Resep::class);
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }
}
