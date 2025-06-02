<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renta extends Model
{
    protected $table = 'rentas';

    protected $fillable = [
        'municipio_id',
        'anyo',
        'valor',
        'tipo'
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}

