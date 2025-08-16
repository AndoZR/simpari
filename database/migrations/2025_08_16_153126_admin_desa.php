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
            $table->unsignedBigInteger('desa_id'); // bisa FK ke tabel desa kalau ada
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_desa');
    }
};
