<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihan')->cascadeOnDelete();
            $table->enum('metode', ['tunai', 'transfer', 'qris', 'kartu', 'penjamin'])->default('tunai');
            $table->decimal('jumlah', 12, 2);
            $table->timestamp('tgl_bayar')->useCurrent();
            $table->foreignId('kasir_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
