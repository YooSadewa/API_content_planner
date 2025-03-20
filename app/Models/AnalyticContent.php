<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalyticContent extends Model
{
    protected $table = 'analytic_content';
    protected $primaryKey = 'anc_id';
    protected $fillable = [
        'lup_id',
        'anc_tanggal',
        'anc_hari',
    ];

    public function report(): HasMany
    {
        return $this->hasMany(AnalyticContentReport::class, 'anc_id', 'anc_id');
    }

    public function linkContent(): BelongsTo
    {
        return $this->belongsTo(LinkUploadPlanner::class, 'lup_id', 'lup_id');
    }
}
