<?php

namespace App\Models;

use App\Models\Cicilan;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $fillable = [
        'nop',
        'jumlah',
        'status',
        'sisa_tagihan',
        'keterangan',
        'tanggal_tagihan',
        'tanggal_lunas',
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
