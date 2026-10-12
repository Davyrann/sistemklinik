<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RekamMedis extends Model
{
    protected $table = 'rekam_medis';

    protected $fillable = ['kunjungan_id', 'dokter_id', 'subjective', 'objective', 'assessment', 'plan', 'is_locked', 'locked_at'];

    protected function casts(): array
    {
        return ['is_locked' => 'boolean', 'locked_at' => 'datetime'];
    }

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class);
    }
}
