<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkUploadPlanner extends Model
{
    protected $table = 'link_upload_planners';
    protected $primaryKey = 'lup_id';
    protected $fillable = [
        'lup_instagram',
        'lup_facebook',
        'lup_twitter',
        'lup_youtube',
        'lup_website',
        'lup_tiktok',
    ];

    public function platforms(): BelongsTo
    {
        return $this->belongsTo(OnlinePlanner::class, 'lup_id', 'lup_id');
    }
}
