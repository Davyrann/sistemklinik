<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasien', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm', 20)->unique();
            $table->char('nik', 16)->nullable()->unique();
            $table->string('nama', 150);
            $table->date('tgl_lahir');
            $table->enum('jk', ['L', 'P']);
            $table->text('alamat')->nullable();
            $table->string('telp', 20)->nullable();
            $table->enum('gol_darah', ['A', 'B', 'AB', 'O'])->nullable();
            $table->text('alergi')->nullable();
            $table->enum('penjamin', ['umum', 'bpjs', 'asuransi'])->default('umum');
            $table->timestamps();

            $table->index(['nama', 'tgl_lahir']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasien');
    }
};
