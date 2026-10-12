<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tanda_vital', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->unique()->constrained('kunjungan')->cascadeOnDelete();
            $table->smallInteger('td_sistolik')->nullable();
            $table->smallInteger('td_diastolik')->nullable();
            $table->smallInteger('nadi')->nullable();
            $table->decimal('suhu', 4, 1)->nullable();
            $table->smallInteger('rr')->nullable();
            $table->smallInteger('spo2')->nullable();
            $table->decimal('bb', 5, 2)->nullable();
            $table->decimal('tb', 5, 2)->nullable();
            $table->decimal('imt', 4, 1)->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanda_vital');
    }
};
