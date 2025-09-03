<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cicilan extends Model
{
    protected $table = 'cicilan_tagihan';

    public $timestamps = false;

    protected $fillable = [
        'tagihan_id',
        'total_cicilan_now',
    ];

    // Relasi ke tagihan
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }
}
