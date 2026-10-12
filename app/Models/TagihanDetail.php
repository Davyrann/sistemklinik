<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagihanDetail extends Model
{
    protected $table = 'tagihan_detail';

    protected $fillable = ['tagihan_id', 'deskripsi', 'qty', 'harga_satuan', 'subtotal', 'jenis'];

    protected function casts(): array
    {
        return ['harga_satuan' => 'decimal:2', 'subtotal' => 'decimal:2'];
    }

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }
}
