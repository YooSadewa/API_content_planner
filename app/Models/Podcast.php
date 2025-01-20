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
        'pmb_id',
        'host_id',
        'pdc_catatan'
    ];

    public function hosts() {
        return $this->belongsTo(Host::class, 'host_id', 'host_id');
    }

    public function pembicaras() {
        return $this->belongsTo(Pembicara::class, 'pmb_id', 'pmb_id' );
    }
}
