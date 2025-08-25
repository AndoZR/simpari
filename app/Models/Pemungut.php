<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemungut extends Model
{
    protected $table = 'pemungut';

    protected $fillable = [
        'nama',
        'telepon',
        'alamat',
    ];

    // Relasi balik ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function masyarakat()
    {
        return $this->hasMany(Masyarakat::class, 'pemungut_id');
    }
}
