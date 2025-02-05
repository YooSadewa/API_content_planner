<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotes extends Model
{
    protected $table = 'quotes';

    protected $primaryKey = 'qotd_id';

    protected $fillable = [
        'qotd_link',
    ];
}
