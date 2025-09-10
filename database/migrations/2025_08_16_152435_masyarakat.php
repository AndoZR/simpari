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
        Schema::create('masyarakat', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('telepon',13)->unique()->nullable();
            $table->string('alamat');
            $table->boolean('status_lunas')->default(false); // ini next setelah uji coba 11 sep happus aja gak penting
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pemungut_id')->nullable()->constrained('pemungut')->nullOnDelete();
            $table->char('village_id',10)->nullable();
            $table->foreign('village_id')->references('id')->on('villages')->onDelete('cascade');
            $table->timestamps();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masyarakat');
    }
};
