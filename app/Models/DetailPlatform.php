<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPlatform extends Model
{
    protected $table = 'detail_platform';
    protected $primaryKey = 'dpl_id';
    protected $fillable = [
        'dacc_id',
        'dpl_platform',
        'dpl_total_konten',
        'dpl_pengikut',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(DetailAccount::class, 'dacc_id', 'dacc_id');
    }
}
