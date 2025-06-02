<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProvinciaCodificada extends Model
{
    // ✅ Nombre correcto de la tabla
    protected $table = 'provincias_codificadas';

    // ✅ El ID no es autoincremental
    public $incrementing = false;

    // ✅ El ID es un integer (por defecto Laravel espera bigInt)
    protected $keyType = 'int';

    // ✅ Campos que se pueden asignar en masa
    protected $fillable = ['id', 'codigo', 'nombre'];

    // ✅ Relación con municipios
    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'provincia_id');
    }
}

