<?php

namespace App\Models;

use App\Models\Cicilan;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    protected $fillable = [
        'jumlah',
        'status',
        'sisa_tagihan',
        'keterangan',
        'tanggal_tagihan',
        'tanggal_lunas',
    ];

    public function cicilan()
    {
        return $this->hasMany(Cicilan::class, 'tagihan_id');
    }

}
