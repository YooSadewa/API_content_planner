<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetailAccount extends Model
{
    protected $table = 'detail_account';
    protected $primaryKey = 'dacc_id';
    protected $fillable = [
        'dacc_bulan',
        'dacc_tahun',
    ];

    public function platforms(): HasMany
    {
        return $this->hasMany(DetailPlatform::class, 'dacc_id', 'dacc_id');
    }
}
