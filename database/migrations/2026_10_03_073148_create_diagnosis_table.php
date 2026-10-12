<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnosis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->cascadeOnDelete();
            $table->string('kode_icd10', 10)->nullable();
            $table->string('nama_diagnosis', 255);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('kode_icd10');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosis');
    }
};
