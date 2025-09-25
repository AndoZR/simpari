<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id(); // PK

            // FK ke users.id (role masyarakat)
            $table->unsignedBigInteger('masyarakat_id');
            $table->string('nop',30)->unique();
            $table->bigInteger('jumlah')->default(0); // Total tagihan
            $table->bigInteger('sisa_tagihan')->default(0); // Sisa setelah dicicil
            $table->bigInteger('uang_dipemungut')->default(0);
            $table->bigInteger('uang_didesa')->default(0);
            $table->enum('status', ['belum', 'cicilan', 'lunas', 'didesa', 'dikecamatan'])->default('belum');
            $table->text('keterangan')->nullable();

            $table->date('tanggal_tagihan')->nullable(); 
            $table->date('tanggal_lunas')->nullable(); // opsional

            // Relasi ke tabel users
            $table->foreign('masyarakat_id')
                ->references('id')->on('masyarakat')
                ->onDelete('cascade');
        });

        Schema::create('cicilan_tagihan', function (Blueprint $table) {
            $table->bigIncrements('id'); // PK
            $table->unsignedBigInteger('tagihan_id'); // FK ke tagihan
            $table->bigInteger('total_cicilan_now'); // nominal cicilan
            $table->timestamps();

            // Relasi ke tabel tagihan
            $table->foreign('tagihan_id')
                ->references('id')->on('tagihan')
                ->onDelete('cascade');
        });
    }
};
