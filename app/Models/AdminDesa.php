<?php

namespace App\Models;

use App\Models\Desa;
use Illuminate\Database\Eloquent\Model;

class AdminDesa extends Model
{
    protected $table = "admin_desa";

    protected $fillable = [
        'telepon',
        'tagihan',
        'sisa_tagihan',
        'diterima_kec',
        'village_id'
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class, 'village_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
