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
        'user_id', 'onp_platform', 'onp_checkpoint', 'lup_id'
    ];

    // Relasi ke tabel Users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke tabel LinkUploadPlanner
    public function linkUploadPlanner()
    {
        return $this->belongsTo(LinkUploadPlanner::class, 'lup_id', 'lup_id');
    }
}
