<?php

namespace App\Console\Commands\Importadores;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\ProvinciaCodificada;
use App\Models\TasaParo;

class ImportarTasaParo extends Command
{
    protected $signature = 'importar:tasaparoprovincia';
    protected $description = 'Importa tasa de paro por provincia desde la API del INE';

    public function handle()
    {
        $this->info("📡 Descargando tasa de paro por provincia desde el INE...");

        $response = Http::timeout(120)->get('https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/3996?tip=AM&');

        if (!$response->ok()) {
            $this->error("❌ Error al obtener los datos de la API");
            return;
        }

        $data = $response->json();

        foreach ($data as $serie) {
            $meta = collect($serie['MetaData']);
            $provMeta = $meta->first(fn($item) => $item['T3_Variable'] === 'Provincias' && isset($item['Codigo']));
            
            if (!$provMeta) {
                $this->info("Metadata válida con código de provincia.");
                continue;
            }

            $codigo = str_pad($provMeta['Codigo'], 2, '0', STR_PAD_LEFT);
            $provincia = ProvinciaCodificada::where('codigo', $codigo)->first();

            if (!$provincia) {
                $this->warn("⚠️ Código no relacionado en la BD: $codigo");
                continue;
            }

            foreach ($serie['Data'] as $entry) {
                $anio = $entry['Anyo'] ?? null;
                $periodo = $entry['T3_Periodo'] ?? null;
                $valor = $entry['Valor'] ?? null;

                if (!$anio || $anio < 2021 || !$periodo || !preg_match('/T([1-4])/', $periodo, $match)) {
                    continue;
                }

                $trimestre = (int) $match[1];

                TasaParo::updateOrCreate(
                    [
                        'provincia_id' => $provincia->id,
                        'anio' => $anio,
                        'trimestre' => $trimestre,
                    ],
                    [
                        'valor' => $valor,
                    ]
                );
            }
        }

        $this->info("✅ Importación de tasa de paro completada.");
    }
}
