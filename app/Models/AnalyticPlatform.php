<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticPlatform extends Model
{
    protected $table = 'analytic_platforms';
    protected $primaryKey = 'anp_id';
    protected $fillable = [
        'anp_name',
    ];
}
