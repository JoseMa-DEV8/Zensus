<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumeroEmpresaSector extends Model
{
    use HasFactory;

    protected $table = 'numero_empresas_sectores';

    protected $fillable = [
    'provincia_codigo',
    'municipio_codigo',
    'codigo_dir',
    'sector',
    'anyo',
    'valor',
];

public function provincia()
{
    return $this->belongsTo(ProvinciaCodificada::class, 'provincia_codigo', 'codigo');
}

public function municipio()
{
    return $this->belongsTo(Municipio::class, function ($q) {
        $q->whereColumn('municipios.cop', 'numero_empresas_sectores.provincia_codigo')
          ->whereColumn('municipios.com', 'numero_empresas_sectores.municipio_codigo');
    });
}

}