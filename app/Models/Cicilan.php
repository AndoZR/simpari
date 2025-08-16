<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cicilan extends Model
{
    protected $table = 'cicilan_tagihan';

    protected $fillable = [
        'tagihan_id',
        'jumlah_bayar',
        'tanggal_bayar',
        'keterangan',
    ];

    // Relasi ke tagihan
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }
}
