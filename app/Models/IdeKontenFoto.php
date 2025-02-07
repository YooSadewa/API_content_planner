<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdeKontenFoto extends Model
{
    protected $table = 'ide_konten_foto';
    protected $primaryKey = 'ikf_id';
    protected $fillable = [
        'ikf_tgl',
        'ikf_judul_konten',
        'ikf_ringkasan',
        'ikf_referensi'
    ];
}
