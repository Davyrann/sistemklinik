<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obat', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama_generik', 100);
            $table->string('nama_dagang', 100)->nullable();
            $table->string('satuan', 20);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(10);
            $table->decimal('harga', 10, 2);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};
