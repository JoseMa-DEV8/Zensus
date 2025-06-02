<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demografia extends Model
{
    protected $fillable = [
        'municipio_id',
        'sexo',
        'tipo',
        'anio',
        'valor',
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}
