<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemungut', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('telepon')->nullable();
            $table->string('alamat')->nullable();
            // RELASI admin_desa
            $table->foreignId('admin_desa_id')->nullable()->constrained('admin_desa')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemungut');
    }
};
