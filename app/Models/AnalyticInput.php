<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticInput extends Model
{
    protected $table = 'analytic_content_input';
    protected $primaryKey = 'anc_id';
    protected $fillable = [
        'anc_tgl',
        'anc_hari',
        'lup_id',
        'anf_id',
        'value'
    ];

    public function topikKonten(): BelongsTo
    {
        return $this->belongsTo(LinkUploadPlanner::class, 'dacc_id', 'dacc_id');
    }

    public function fieldPlatform(): BelongsTo
    {
        return $this->belongsTo(AnalyticField::class, 'dacc_id', 'dacc_id');
    }
}
