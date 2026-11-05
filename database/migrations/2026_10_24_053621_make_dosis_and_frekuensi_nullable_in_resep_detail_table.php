<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('resep_detail', function (Blueprint $table) {
            $table->string('dosis', 100)->nullable()->change();
            $table->string('frekuensi', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resep_detail', function (Blueprint $table) {
            $table->string('dosis', 100)->nullable(false)->change();
            $table->string('frekuensi', 100)->nullable(false)->change();
        });
    }
};
