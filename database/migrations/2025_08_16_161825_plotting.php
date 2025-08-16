<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plotting', function (Blueprint $table) {
            $table->id();

            // FK ke user pemungut
            $table->unsignedBigInteger('pemungut_id');
            $table->foreign('pemungut_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            // FK ke user masyarakat
            $table->unsignedBigInteger('masyarakat_id');
            $table->foreign('masyarakat_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->timestamps();

            // optional: cegah duplikasi pemungut-masyarakat
            $table->unique(['pemungut_id', 'masyarakat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plotting');
    }
};
