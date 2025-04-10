<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalyticField extends Model
{
    protected $table = 'analytic_fields';
    protected $primaryKey = 'anf_id';
    protected $fillable = [
        'anp_id',
        'anf_name',
        'anf_required'
    ];

    public function platforms(): HasMany
    {
        return $this->hasMany(AnalyticPlatform::class, 'anp_id', 'anp_id');
    }
}
