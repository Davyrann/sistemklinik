<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $fillable = ['kunjungan_id', 'total', 'diskon', 'total_bayar', 'status'];

    protected function casts(): array
    {
        return ['total' => 'decimal:2', 'diskon' => 'decimal:2', 'total_bayar' => 'decimal:2'];
    }

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(TagihanDetail::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }
}
