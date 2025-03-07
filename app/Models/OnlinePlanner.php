<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlinePlanner extends Model
{
    use HasFactory;

    protected $table = 'online_planners';
    protected $primaryKey = 'onp_id'; 

    protected $fillable = [
        'onp_tanggal', 'onp_hari', 'onp_topik_konten',
        'onp_admin', 'onp_platform', 'onp_checkpoint'
    ];

    // Relasi ke tabel LinkUploadPlanner
    public function linkUploadPlanner()
    {
        return $this->belongsTo(LinkUploadPlanner::class, 'onp_id', 'onp_id');
    }
}
