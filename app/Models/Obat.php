<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    protected $table = 'obat';

    protected $fillable = ['kode', 'nama_generik', 'nama_dagang', 'satuan', 'stok', 'stok_minimum', 'harga', 'is_aktif'];

    protected function casts(): array
    {
        return ['harga' => 'decimal:2', 'is_aktif' => 'boolean'];
    }

    public function resepDetails(): HasMany
    {
        return $this->hasMany(ResepDetail::class);
    }
}
