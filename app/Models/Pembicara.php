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
}

