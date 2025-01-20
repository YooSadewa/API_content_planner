<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembicara extends Model
{
    protected $table = 'pembicaras';
    protected $primaryKey = 'pmb_id';

    protected $fillable = [
        'pmb_nama',
    ];

    public function podcasts() {
        return $this->hasMany('App\Models\Podcast', 'pmb_id', 'pmb_id' );
    }
}

