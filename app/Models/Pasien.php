<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    protected $table = 'pasien';

    protected $fillable = ['no_rm', 'nik', 'nama', 'tgl_lahir', 'jk', 'alamat', 'telp', 'gol_darah', 'alergi', 'penjamin'];

    protected function casts(): array
    {
        return ['tgl_lahir' => 'date'];
    }

    public function kunjungans(): HasMany
    {
        return $this->hasMany(Kunjungan::class);
    }
}
