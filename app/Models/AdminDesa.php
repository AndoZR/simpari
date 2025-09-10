<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminDesa extends Model
{
    protected $table = "admin_desa";

    protected $fillable = [
        'telepon',
        'tagihan',
        'sisa_tagihan'
    ];
}
