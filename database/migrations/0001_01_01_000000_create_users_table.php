<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $path = database_path('migrations/indonesia.sql');
        $sql = file_get_contents($path);
        DB::unprepared($sql);

        // 1. Hapus desa di luar Bondowoso
        DB::table('villages')->whereNotIn('district_id', function($q) {
            $q->select('id')->from('districts')->where('regency_id', '3511');
        })->delete();

        // 2. Hapus kecamatan di luar Bondowoso
        DB::table('districts')->where('regency_id', '!=', '3511')->delete();

        // 3. Hapus kabupaten/kota selain Bondowoso
        DB::table('regencies')->where('id', '!=', '3511')->delete();

        // 4. Hapus provinsi selain Jawa Timur
        DB::table('provinces')->where('id', '!=', '35')->delete();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->char('nik',16)->unique();
            
            // $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['masyarakat', 'pemungut', 'admin_kecamatan', 'admin_desa']);
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
