<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalyticContentReport extends Model
{
    protected $table = 'analytic_content_report';
    protected $primaryKey = 'acr_id';
    protected $fillable = [
        'anc_id',
        'acr_platform',
        'acr_reach',
        'acr_like',
        'acr_comment',
        'acr_share',
        'acr_save',
    ];

    public function analytic(): BelongsTo
    {
        return $this->belongsTo(AnalyticContent::class, 'anc_id', 'anc_id');
    }
}
