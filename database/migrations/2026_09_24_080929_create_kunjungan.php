<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien')->cascadeOnDelete();
            $table->foreignId('dokter_id')->nullable()->constrained('dokter')->nullOnDelete();
            $table->foreignId('poli_id')->constrained('poli')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tgl_kunjungan');
            $table->string('no_antrean', 20);
            $table->enum('status', ['menunggu', 'dipanggil', 'diperiksa', 'selesai', 'batal'])->default('menunggu');
            $table->text('keluhan_utama')->nullable();
            $table->enum('penjamin', ['umum', 'bpjs', 'asuransi'])->default('umum');
            $table->timestamps();

            $table->unique(['poli_id', 'tgl_kunjungan', 'no_antrean']);
            $table->index(['dokter_id', 'status', 'tgl_kunjungan']);
            $table->index(['status', 'tgl_kunjungan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};
