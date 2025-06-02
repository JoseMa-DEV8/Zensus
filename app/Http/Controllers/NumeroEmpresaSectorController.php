<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProvinciaCodificada;
use App\Models\Municipio;
use App\Models\NumeroEmpresaSector;

class NumeroEmpresaSectorController extends Controller{
    
    public function index(Request $request){
        // Obtener todas las provincias ordenadas alfabéticamente
        $provincias = ProvinciaCodificada::orderBy('nombre')->get();

        // Obtener el código de provincia seleccionado (por defecto la primera del listado)
        $provinciaCodigo = $request->input('provincia_codigo', $provincias->first()?->codigo);

        // Buscar el objeto de la provincia seleccionada
        $provinciaSeleccionada = $provincias->firstWhere('codigo', $provinciaCodigo);

        // Obtener todos los municipios pertenecientes a la provincia seleccionada
        $municipios = Municipio::where('cop', $provinciaCodigo)->orderBy('nombre')->get();

        // Obtener el código del municipio seleccionado (por defecto el primero del listado)
        $municipioCodigo = $request->input('municipio_codigo', $municipios->first()?->com);

        // Buscar el objeto del municipio seleccionado
        $municipioSeleccionado = $municipios->firstWhere('com', $municipioCodigo);

        // Obtener la evolución de empresas para ese municipio
        $datos = NumeroEmpresaSector::where('provincia_codigo', $provinciaCodigo)
            ->where('municipio_codigo', $municipioCodigo)
            ->orderBy('anyo')
            ->get(['anyo', 'valor']);

        // Calcular el año más reciente con datos disponibles
        $ultimoAnyo = NumeroEmpresaSector::max('anyo');

        // Obtener los 5 municipios con más empresas de la provincia seleccionada en el último año
        $topMunicipiosRaw = NumeroEmpresaSector::where('provincia_codigo', $provinciaCodigo)
            ->where('anyo', $ultimoAnyo)
            ->whereNotNull('municipio_codigo')
            ->selectRaw('municipio_codigo, SUM(valor) as total')
            ->groupBy('municipio_codigo')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Asociar cada resultado crudo con su municipio correspondiente
        $topMunicipios = $topMunicipiosRaw->map(function ($item) use ($provinciaCodigo) {
            $item->municipio = Municipio::where('cop', $provinciaCodigo)
                ->where('com', $item->municipio_codigo)
                ->first();
            return $item;
        })->filter(fn($item) => $item->municipio !== null)->values();

        // -------- COMPARACIÓN ENTRE PROVINCIAS -------- //

        // Capturar los códigos de las dos provincias a comparar
        $provinciaA = $request->input('provincia_a');
        $provinciaB = $request->input('provincia_b');

        // Inicializar las colecciones de datos vacías
        $datosComparacionA = collect();
        $datosComparacionB = collect();

        // Si se seleccionó Provincia A, obtener sus datos agregados por año
        if ($provinciaA) {
            $datosComparacionA = NumeroEmpresaSector::where('provincia_codigo', $provinciaA)
                ->whereNotNull('municipio_codigo')
                ->selectRaw('anyo, SUM(valor) as valor')
                ->groupBy('anyo')
                ->orderBy('anyo')
                ->get();
        }

        // Si se seleccionó Provincia B, obtener sus datos agregados por año
        if ($provinciaB) {
            $datosComparacionB = NumeroEmpresaSector::where('provincia_codigo', $provinciaB)
                ->whereNotNull('municipio_codigo')
                ->selectRaw('anyo, SUM(valor) as valor')
                ->groupBy('anyo')
                ->orderBy('anyo')
                ->get();
        }

        // Obtener los nombres de las provincias seleccionadas para usarlos en la leyenda del gráfico
        $nombreProvinciaA = ProvinciaCodificada::where('codigo', $provinciaA)->value('nombre');
        $nombreProvinciaB = ProvinciaCodificada::where('codigo', $provinciaB)->value('nombre');

        // Enviar todos los datos necesarios a la vista Blade
        return view('economia.empresas_sectores', compact(
            'provincias',
            'provinciaSeleccionada',
            'municipios',
            'municipioSeleccionado',
            'datos',
            'topMunicipios',
            'ultimoAnyo',
            'datosComparacionA',
            'datosComparacionB',
            'nombreProvinciaA',
            'nombreProvinciaB'
        ));
    }
}
