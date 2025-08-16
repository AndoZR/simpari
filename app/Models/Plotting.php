<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plotting extends Model
{
    protected $table = 'plotting';

    protected $fillable = [
        'pemungut_id',
        'masyarakat_id',
    ];

    public function pemungut()
    {
        return $this->belongsTo(User::class, 'pemungut_id');
    }

    public function masyarakat()
    {
        return $this->belongsTo(User::class, 'masyarakat_id');
    }
}
