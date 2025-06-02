<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    // ✅ Si usas una tabla llamada "municipios", Laravel la reconoce automáticamente. No necesitas $table.

    // ✅ Campos que puedes asignar en masa (fillables)
    protected $fillable = ['provincia_id', 'cop', 'com', 'nombre'];

    // ✅ Relación con provincia
    public function provincia()
{
    return $this->belongsTo(\App\Models\ProvinciaCodificada::class, 'provincia_id');
}


    // ✅ (Opcional) getter para el código completo tipo "01023"
    public function getCodigoCompletoAttribute()
    {
        return $this->cop . $this->com;
    }
}
