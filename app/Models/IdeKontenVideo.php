<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdeKontenVideo extends Model
{
    protected $table = 'ide_konten_video';
    protected $primaryKey = 'ikv_id';
    protected $fillable = [
        'ikv_tgl',
        'ikv_judul_konten',
        'ikv_ringkasan',
        'ikv_pic',
        'ikv_status',
        'ikv_skrip',
        'ikv_referensi',
        'ikv_upload'
    ];
}
