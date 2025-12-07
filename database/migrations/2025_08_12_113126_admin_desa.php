<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('tagihan', 15)->nullable(); // Total tagihan
            $table->decimal('sisa_tagihan', 15)->nullable();
            $table->decimal('diterima_kec', 15)->nullable();
            $table->string('telepon',13)->unique()->nullable();
            // $table->char('akun_kecamatan',10)->nullable();
            // $table->foreign('akun_kecamatan')->references('id')->on('villages')->onDelete('cascade');
            $table->char('village_id',10)->nullable();
            $table->foreign('village_id')->references('id')->on('villages')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_desa');
    }
};
