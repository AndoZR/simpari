<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Masyarakat extends Model
{
    protected $table = 'masyarakat';

    protected $fillable = [
        'nama',
        'nop',
        'telepon',
        'alamat',
        'status_lunas',
    ];

    protected $casts = [
        'status_lunas' => 'boolean', // ✅ ini akan selalu cast ke 0/1 atau true/false
    ];

    // Relasi balik ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 1 masyarakat bisa punya banyak tagihan
    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'masyarakat_id');
    }

    public function pemungut()
    {
        return $this->belongsTo(Pemungut::class, 'pemungut_id');
    }
}
