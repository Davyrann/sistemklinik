<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = ['tagihan_id', 'metode', 'jumlah', 'tgl_bayar', 'kasir_id', 'keterangan'];

    protected function casts(): array
    {
        return ['jumlah' => 'decimal:2', 'tgl_bayar' => 'datetime'];
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }
}
