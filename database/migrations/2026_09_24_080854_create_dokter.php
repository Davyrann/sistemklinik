<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('poli_id')->constrained('poli')->restrictOnDelete();
            $table->string('nama', 100);
            $table->string('nip', 30)->nullable()->unique();
            $table->string('spesialisasi', 100)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index(['poli_id', 'is_aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokter');
    }
};
