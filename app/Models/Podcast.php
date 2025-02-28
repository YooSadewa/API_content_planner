<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Podcast extends Model
{
    protected $table = 'podcasts';

    protected $primaryKey = 'pdc_id';

    protected $fillable = [
        'pdc_jadwal_shoot',
        'pdc_jadwal_upload',
        'pdc_tema',
        'pdc_abstrak',
        'pdc_host',
        'pdc_speaker',
        'pdc_link',
        'pdc_catatan'
    ];
}
