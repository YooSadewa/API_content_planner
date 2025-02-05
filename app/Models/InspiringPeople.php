<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InspiringPeople extends Model
{
    protected $table = 'inspiring_people';

    protected $primaryKey = 'ins_id';

    protected $fillable = [
        'ins_nama',
        'ins_link'
    ];
}
