<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProvinciaCodificada as Provincia;
use App\Models\Municipio;
use App\Models\Demografia;
use App\Models\EstructuraPoblacion;
use App\Models\TasaParo;

class DemografiaController extends Controller{

    public function index(Request $request){
        
        // Cargamos todas las provincias para mostrarlas en el select del filtro
        $provincias = Provincia::orderBy('nombre')->get();

        // Variables iniciales vacías
        $provinciaSeleccionada = null;
        $municipioSeleccionado = null;
        $municipios = collect(); // Lista vacía para rellenar según provincia

        // Obtenemos los códigos seleccionados por el usuario
        $provinciaCodigo = $request->input('provincia_codigo');
        $municipioCodigo = $request->input('municipio_codigo');

        // Si hay una provincia seleccionada, la buscamos y traemos sus municipios
        if ($provinciaCodigo) {
            $provinciaSeleccionada = Provincia::where('codigo', $provinciaCodigo)->first();
            if ($provinciaSeleccionada) {
                $municipios = Municipio::where('provincia_id', $provinciaSeleccionada->id)->orderBy('nombre')->get();
            }
        }

        // Si hay un municipio seleccionado, lo buscamos (y validamos que sea de la provincia seleccionada si aplica)
        if ($municipioCodigo) {
            $municipioSeleccionado = Municipio::where('com', $municipioCodigo)
                ->when($provinciaSeleccionada, fn($q) => $q->where('provincia_id', $provinciaSeleccionada->id))
                ->first();
        }

        // === DEMOGRAFÍA ===
        // Construimos la query base de población
        $queryDemografia = Demografia::query()
            ->when($municipioSeleccionado, fn($q) => $q->where('municipio_id', $municipioSeleccionado->id))
            ->when(!$municipioSeleccionado && $provinciaSeleccionada, function ($q) use ($provinciaSeleccionada) {
                $ids = Municipio::where('provincia_id', $provinciaSeleccionada->id)->pluck('id');
                return $q->whereIn('municipio_id', $ids);
            });

        // Buscamos el año más reciente con datos totales
        $anioMasReciente = (clone $queryDemografia)
            ->where('sexo', 'total')
            ->where('tipo', 'total')
            ->max('anio');

        // Agrupamos población total por año (para el gráfico de evolución)
        $valoresPorAnio = (clone $queryDemografia)
            ->where('sexo', 'total')
            ->where('tipo', 'total')
            ->selectRaw('anio, SUM(valor) as total')
            ->groupBy('anio')
            ->orderBy('anio')
            ->pluck('total', 'anio')
            ->toArray();

        // Formato para Chart.js (línea temporal de población)
        $graficoPoblacion = [
            'labels' => array_keys($valoresPorAnio),
            'valores' => array_values($valoresPorAnio),
        ];

        // Totales por sexo en el año más reciente
        $totalHombres = (clone $queryDemografia)
            ->where('anio', $anioMasReciente)
            ->where('sexo', 'hombres')
            ->where('tipo', 'total')
            ->sum('valor');

        $totalMujeres = (clone $queryDemografia)
            ->where('anio', $anioMasReciente)
            ->where('sexo', 'mujeres')
            ->where('tipo', 'total')
            ->sum('valor');

        // === ESTRUCTURA POR EDAD Y SEXO ===
        $estructura = collect();

        // Traemos los datos de estructura de población según el filtro
        if ($municipioSeleccionado) {
            $estructura = EstructuraPoblacion::where('municipio_id', $municipioSeleccionado->id)->get();
        } elseif ($provinciaSeleccionada) {
            $ids = Municipio::where('provincia_id', $provinciaSeleccionada->id)->pluck('id');
            $estructura = EstructuraPoblacion::whereIn('municipio_id', $ids)->get();
        }

        // Inicializamos arrays para los gráficos
        $grupoEdadLabels = [];
        $grupoEdadValores = [];
        $piramideLabels = [];
        $piramideHombres = [];
        $piramideMujeres = [];

        // Si hay datos de estructura, generamos los gráficos
        if ($estructura->count()) {
            $anioEstructura = $estructura->max('anio');

            // === Gráfico de grupos de edad ===
            $grupoEdad = $estructura
                ->where('anio', $anioEstructura)
                ->whereIn('sexo', ['total', 'Total']) // Por si algún listo metió "Total"
                ->groupBy('grupo_edad')
                ->sortKeys();

            $grupoEdadLabels = $grupoEdad->keys()->values();
            $grupoEdadValores = $grupoEdad->map(fn($g) => $g->sum('poblacion'))->values();

            // === Pirámide Poblacional ===
            $piramideLabels = $estructura->where('anio', $anioEstructura)->whereIn('sexo', ['Hombre', 'Mujer'])->pluck('grupo_edad')->unique()->sort()->values();
            $piramideHombres = $estructura->where('anio', $anioEstructura)->where('sexo', 'Hombre')->sortBy('grupo_edad')->pluck('poblacion')->map(fn($v) => -$v)->values();
            $piramideMujeres = $estructura->where('anio', $anioEstructura)->where('sexo', 'Mujer')->sortBy('grupo_edad')->pluck('poblacion')->values();
        }

        // === TASA DE PARO ===
        $tasaLineSeries = [];
        $tasaLineLabels = [];

        if ($provinciaSeleccionada) {
            $tasaParo = TasaParo::where('provincia_id', $provinciaSeleccionada->id)->orderBy('anio')->get();
            if ($tasaParo->count()) {
                $tasaLineLabels = $tasaParo->pluck('anio')->unique()->values();
                $tasaLineSeries = [[
                    'label' => $provinciaSeleccionada->nombre,
                    'data' => $tasaParo->pluck('valor')->values()
                ]];
            }
        }

        // Finalmente, enviamos todo al Blade
        return view('demografia.demografia', compact(
            'provincias',
            'provinciaSeleccionada',
            'municipios',
            'municipioSeleccionado',
            'graficoPoblacion',
            'totalHombres',
            'totalMujeres',
            'anioMasReciente',
            'grupoEdadLabels',
            'grupoEdadValores',
            'piramideLabels',
            'piramideHombres',
            'piramideMujeres',
            'tasaLineLabels',
            'tasaLineSeries'
        ));
    }
}
