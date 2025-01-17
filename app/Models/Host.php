<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Host extends Model
{
    protected $table = 'hosts';
    protected $primaryKey = 'host_id';

    protected $fillable = [
        'host_nama',
    ];
}
