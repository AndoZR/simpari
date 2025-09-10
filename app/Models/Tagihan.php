<?php

namespace App\Models;

use App\Models\Cicilan;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';
    
    public $timestamps = false;

    protected $fillable = [
        'nop',
        'jumlah',
        'status',
        'sisa_tagihan',
        'uang_dipemungut',
        'uang_didesa',
        'keterangan',
        'tanggal_tagihan',
        'tanggal_lunas',
    ];

    protected $casts = [
        'jumlah' => 'float',
        'sisa_tagihan' => 'float',
        'cicilan' => 'float',
    ];

    public function masyarakat()
    {
        return $this->belongsTo(Masyarakat::class, 'masyarakat_id');
    }

    public function cicilan()
    {
        return $this->hasMany(Cicilan::class, 'tagihan_id');
    }

}
