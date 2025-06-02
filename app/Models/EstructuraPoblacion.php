<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstructuraPoblacion extends Model
{
    use HasFactory;

    protected $table = 'estructura_poblacion';
    protected $fillable = [
        'municipio_id',
        'anio',
        'grupo_edad',
        'sexo',
        'poblacion',
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}
