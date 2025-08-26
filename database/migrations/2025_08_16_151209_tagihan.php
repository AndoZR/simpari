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
            $table->string('nop',30)->unique()->nullable();
            $table->decimal('jumlah', 15); // Total tagihan
            $table->decimal('sisa_tagihan', 15); // Sisa setelah dicicil
            $table->enum('status', ['belum', 'cicilan', 'didesa', 'dipemungut', 'lunas'])->default('belum');
            $table->decimal('cicilan',15)->nullable();
            $table->text('keterangan')->nullable();

            $table->date('tanggal_tagihan'); 
            $table->date('tanggal_lunas')->nullable(); // opsional

            $table->timestamps(); // created_at & updated_at

            // Relasi ke tabel users
            $table->foreign('masyarakat_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

        // Schema::create('cicilan_tagihan', function (Blueprint $table) {
        //     $table->bigIncrements('id'); // PK

        //     $table->unsignedBigInteger('tagihan_id'); // FK ke tagihan
        //     $table->decimal('jumlah_bayar', 15, 2); // nominal cicilan
        //     $table->date('tanggal_bayar'); // kapan dibayar
        //     $table->text('keterangan')->nullable(); // opsional

        //     $table->timestamps();

        //     // Relasi ke tabel tagihan
        //     $table->foreign('tagihan_id')
        //         ->references('id')->on('tagihan')
        //         ->onDelete('cascade');
        // });
    }
};
