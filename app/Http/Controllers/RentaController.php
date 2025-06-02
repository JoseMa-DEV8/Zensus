<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProvinciaCodificada;
use App\Models\Municipio;
use App\Models\Renta;

class RentaController extends Controller
{
    public function index(Request $request)
    {
        // 🛡️ Valores por defecto con códigos fiables
        if (empty($request->provincia_id) && empty($request->municipio_id)) {
            $provinciaDefault = ProvinciaCodificada::where('codigo', '30')->first(); // Murcia
            $municipioDefault = Municipio::where('com', '003')->where('provincia_id', $provinciaDefault->id)->first(); // Águilas

            $request->merge([
                'provincia_id' => $provinciaDefault?->id,
                'municipio_id' => $municipioDefault?->id,
            ]);
        }

        // 🗂️ Cargar provincias y municipios según la provincia seleccionada
        $provincias = ProvinciaCodificada::orderBy('nombre')->get();

        $municipios = Municipio::when($request->provincia_id, function ($q) use ($request) {
            $q->where('provincia_id', $request->provincia_id);
        })->orderBy('nombre')->get();

        // 📊 Consultar datos
        $query = Renta::query();

        if ($request->municipio_id) {
            $query->where('municipio_id', $request->municipio_id);
        } elseif ($request->provincia_id) {
            $query->where('provincia_id', $request->provincia_id);
        }

        $datos = $query->get();
        $anios = $datos->pluck('anyo')->unique()->sort()->values();

        $mediaPersonaPorAnio = [];
        $mediaHogarPorAnio = [];

        foreach ($anios as $anio) {
            $grupo = $datos->where('anyo', $anio);
            $mediaPersonaPorAnio[] = round($grupo->where('tipo', 'neta_persona')->avg('valor') ?? 0, 2);
            $mediaHogarPorAnio[] = round($grupo->where('tipo', 'neta_hogar')->avg('valor') ?? 0, 2);
        }

        $ultimoAnio = $anios->last();

        $mediaPersona = round($datos->where('anyo', $ultimoAnio)->where('tipo', 'neta_persona')->avg('valor') ?? 0, 2);
        $mediaHogar = round($datos->where('anyo', $ultimoAnio)->where('tipo', 'neta_hogar')->avg('valor') ?? 0, 2);

        // 📈 Gráfico 1: evolución de todos los municipios
        $evolucionMunicipios = [];
        if ($request->provincia_id && !$request->municipio_id) {
            foreach ($municipios as $muni) {
                $serie = [];
                foreach ($anios as $anio) {
                    $valor = $datos
                        ->where('municipio_id', $muni->id)
                        ->where('anyo', $anio)
                        ->where('tipo', 'neta_persona')
                        ->avg('valor');
                    $serie[] = round($valor ?? 0, 2);
                }
                $evolucionMunicipios[] = [
                    'nombre' => $muni->nombre,
                    'valores' => $serie
                ];
            }
        }

        // 🏆 Gráfico 2: ranking municipios
        $rankingMunicipios = collect();
        if ($request->provincia_id && !$request->municipio_id) {
            $ranking = $datos->where('anyo', $ultimoAnio)
                ->where('tipo', 'neta_persona')
                ->groupBy('municipio_id')
                ->map(function ($items, $municipioId) {
                    return [
                        'municipio' => optional($items->first()->municipio)->nombre,
                        'valor' => round($items->avg('valor'), 2),
                    ];
                })->sortByDesc('valor')->values();

            $rankingMunicipios = $ranking;
        }

        // 📏 Límites del gráfico de barras
        $rankingValores = $rankingMunicipios->pluck('valor');
        $minValor = $rankingValores->isNotEmpty() ? floor($rankingValores->min() / 1000) * 1000 : 0;
        $maxValor = $rankingValores->isNotEmpty() ? ceil($rankingValores->max() / 1000) * 1000 : 20000;

        return view('economia.rentas', compact(
            'provincias',
            'municipios',
            'mediaPersona',
            'mediaHogar',
            'mediaPersonaPorAnio',
            'mediaHogarPorAnio',
            'anios',
            'evolucionMunicipios',
            'rankingMunicipios',
            'minValor',
            'maxValor'
        ));
    }
}
