<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProvinciaCodificada;
use App\Models\TasaParo;

class TasaParoController extends Controller{

    public function index(Request $request){
        
        // Cargamos todas las provincias ordenadas alfabéticamente para el selector
        $provincias = ProvinciaCodificada::orderBy('nombre')->get();

        // Iniciamos la consulta base de tasas de paro, con la relación 'provincia' ya cargada (Eager Loading)
        $query = TasaParo::with('provincia');

        // Si el usuario ha filtrado por provincia, añadimos el filtro
        if ($request->filled('provincia_id')) {
            $query->where('provincia_id', $request->provincia_id);
        }

        // Si el usuario ha filtrado por año, aplicamos filtro por año
        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }

        // Si el usuario ha filtrado por trimestre, aplicamos ese filtro también
        if ($request->filled('trimestre')) {
            $query->where('trimestre', $request->trimestre);
        }

        // Ordenamos los resultados de más reciente a más antiguo y paginamos de 20 en 20
        $tasas = $query->orderByDesc('anio')
                       ->orderByDesc('trimestre')
                       ->paginate(20);

        // Retornamos la vista con los resultados y la lista de provincias para los filtros
        return view('demografia.tasa_paro', compact('tasas', 'provincias'));
    }
}
