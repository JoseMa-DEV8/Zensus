<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TasaParo extends Model
{
    protected $table = 'tasas_paro';

    protected $fillable = [
        'provincia_id',
        'anio',
        'trimestre',
        'valor'
    ];

    public function provincia()
    {
        return $this->belongsTo(ProvinciaCodificada::class, 'provincia_id');
    }
}
