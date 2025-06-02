<?php

namespace App\Console\Commands\Importadores;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ProvinciaCodificada;
use App\Models\Municipio;
use App\Models\Demografia;

class ImportarDemografia extends Command
{
    protected $signature = 'importar:demografia';
    protected $description = 'Importa datos demográficos por municipio desde la API del INE';

    public function handle()
    {
        ini_set('memory_limit', '2048M');

        $urls = [
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2855?tip=AM&',    //ALBACETE
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2856?tip=AM&',    //ALICANTE
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2857?tip=AM&',    //ALMERIA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2854?tip=AM&',    //ALAVA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2886?tip=AM&',    //ASTURIAS  
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2858?tip=AM&',    //AVILA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2859?tip=AM&',    //BADAJOZ
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2860?tip=AM&',    //BALEARES
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2861?tip=AM&',    //BARCELONA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2905?tip=AM&',    //BIZKAIA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2862?tip=AM&',    //BURGOS
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2863?tip=AM&',    //CACERES
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2864?tip=AM&',    //CADIZ
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2893?tip=AM&',    //CANTABRIA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2865?tip=AM&',    //CASTELLON
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2866?tip=AM&',    //CIUDAD REAL
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2901?tip=AM&',    //CORDOBA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2868?tip=AM&',    //CORUÑA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2869?tip=AM&',    //CUENCA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2873?tip=AM&',    //GIPUZKOA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2870?tip=AM&',    //GIRONA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2871?tip=AM&',    //GRANADA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2872?tip=AM&',    //GUADALAJARA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2874?tip=AM&',    //HUELVA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2875?tip=AM&',    //HUESCA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2876?tip=AM&',    //JAEN
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2877?tip=AM&',    //LEON
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2878?tip=AM&',    //LLEIDA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2880?tip=AM&',    //LUGO
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2881?tip=AM&',    //MADRID
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2882?tip=AM&',    //MALAGA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2883?tip=AM&',    //MURCIA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2884?tip=AM&',    //NAVARRA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2885?tip=AM&',    //OURENSE
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2888?tip=AM&',    //PALENCIA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2889?tip=AM&',    //LAS PALMAS
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2890?tip=AM&',    //PONTEVEDRA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2879?tip=AM&',    //LA RIOJA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2891?tip=AM&',    //SALAMANCA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2892?tip=AM&',    //SANTA CRUZ DE TENERIFE
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2894?tip=AM&',    //SEGOVIA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2895?tip=AM&',    //SEVILLA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2896?tip=AM&',    //SORIA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2900?tip=AM&',    //TARRAGONA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2899?tip=AM&',    //TERUEL
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2902?tip=AM&',    //TOLEDO
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2903?tip=AM&',    //VALENCIA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2904?tip=AM&',    //VALLADOLID
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2906?tip=AM&',    //ZAMORA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2907?tip=AM&',    //ZARAGOZA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2908?tip=AM&',    //CEUTA
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/2909?tip=AM&',    //MELILLA
            
        ];

        foreach ($urls as $url) {
            $this->info("📡 Importando desde: $url");

            $response = Http::get($url);
            if (!$response->ok()) {
                $this->error("❌ Error al obtener datos desde $url");
                continue;
            }

            $data = $response->json();

            foreach ($data as $serie) {
                $meta = collect($serie['MetaData']);
                $municipioMeta = $meta->firstWhere('T3_Variable', 'Municipios');

                $municipioNombre = $municipioMeta['Nombre'] ?? null;
                $codigoCompleto = $municipioMeta['Codigo'] ?? null;

                if (empty($codigoCompleto) || strtolower($municipioNombre) === 'total') {
                    continue;
                }

                // 💡 Extraer COP y COM directamente del código INE
                $cop = substr($codigoCompleto, 0, 2);
                $com = substr($codigoCompleto, 2, 3);

                $municipio = Municipio::where('cop', $cop)
                                      ->where('com', $com)
                                      ->first();

                if (!$municipio) {
                    $this->error("❌ Municipio no encontrado: $codigoCompleto - $municipioNombre");
                    continue;
                }

                $sexoMeta = $meta->firstWhere('T3_Variable', 'Sexo');
                $sexo = strtolower($sexoMeta['Nombre'] ?? 'total');

                $tipo = 'total';
                if (str_contains($serie['Nombre'], 'España')) {
                    $tipo = 'españoles';
                } elseif (str_contains($serie['Nombre'], 'Extranj')) {
                    $tipo = 'extranjeros';
                }

                foreach ($serie['Data'] as $entry) {
                    if (!isset($entry['Anyo']) || $entry['Anyo'] < 2021) continue;

                    Demografia::updateOrCreate(
                        [
                            'municipio_id' => $municipio->id,
                            'sexo' => $sexo,
                            'tipo' => $tipo,
                            'anio' => $entry['Anyo'],
                        ],
                        [
                            'valor' => $entry['Valor'] ?? null
                        ]
                    );
                }
            }
        }

        $this->info("✅ Importación completada para años desde 2021.");
    }
}
