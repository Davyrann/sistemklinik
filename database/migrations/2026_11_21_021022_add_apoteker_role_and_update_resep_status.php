<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->enum('role', ['admin', 'resepsionis', 'dokter', 'apoteker'])->default('resepsionis')->change();
        });

        Schema::table('resep', function (Blueprint $table): void {
            $table->enum('status', ['draft', 'menunggu', 'diproses', 'diserahkan', 'batal'])->default('menunggu')->change();
        });
    }

    public function down(): void
    {
        Schema::table('resep', function (Blueprint $table): void {
            $table->enum('status', ['draft', 'diserahkan'])->default('draft')->change();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->enum('role', ['admin', 'resepsionis', 'dokter'])->default('resepsionis')->change();
        });
    }
};
