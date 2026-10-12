<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')->constrained('resep')->cascadeOnDelete();
            $table->foreignId('obat_id')->constrained('obat')->restrictOnDelete();
            $table->string('dosis', 100);
            $table->string('frekuensi', 100);
            $table->integer('jumlah');
            $table->string('aturan_pakai', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_detail');
    }
};
