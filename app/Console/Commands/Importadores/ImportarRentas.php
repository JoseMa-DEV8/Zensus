<?php

namespace App\Console\Commands\Importadores;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Renta;
use App\Models\Municipio;

class ImportarRentas extends Command
{
    protected $signature = 'importar:rentas';
    protected $description = 'Importa los datos de renta desde la API del INE por municipio';

    public function handle()
    {
        $urls = [
             'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30656?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30833?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30842?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30851?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30860?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30869?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30878?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30887?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30896?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30917?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30926?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30935?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30944?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30953?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30962?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30971?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30980?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30989?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/30995?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31007?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31016?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31025?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31034?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31043?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31052?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31061?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31070?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31079?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31088?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31097?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31106?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31115?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31124?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31133?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31142?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31151?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31160?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31169?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31178?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31187?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31196?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31205?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31214?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31223?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31232?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31241?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31250?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31259?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31268?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31277?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31286?tip=AM&',
'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/31295?tip=AM&'
        ];

        foreach ($urls as $url) {
            $this->info("📥 Procesando: $url");

            try {
                $response = Http::timeout(60)->retry(3, 2000)->get($url);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $this->warn("⏱️ Timeout o error de conexión al intentar acceder a $url. Saltando...");
                continue;
            } catch (\Throwable $e) {
                $this->error("💥 Error inesperado en $url: " . $e->getMessage());
                continue;
            }

            if (!$response->successful()) {
                $this->error("❌ Error HTTP al descargar $url");
                continue;
            }

            $json = $response->json();

            foreach ($json as $entry) {
                if (!isset($entry['MetaData'][0]['Codigo'], $entry['Nombre'], $entry['Data'])) {
                    $this->warn("⚠️ Entrada mal formada, saltando.");
                    continue;
                }

                $codigoOriginal = $entry['MetaData'][0]['Codigo'];
                $municipioCodigo = substr($codigoOriginal, 0, 5);
                $nombreIndicador = $entry['Nombre'];
                $tipo = $this->determinarTipo($nombreIndicador);

                $provinciaPart = substr($municipioCodigo, 0, 2);
                $municipioPart = substr($municipioCodigo, 2, 3);

                $municipio = Municipio::where('cop', $provinciaPart)
                    ->where('com', $municipioPart)
                    ->first();

                if (!$municipio) {
                    $this->warn("⚠️ Municipio con código $municipioCodigo no encontrado (original: $codigoOriginal).");
                    continue;
                }

                if (!$municipio->provincia_id) {
                    $this->warn("⚠️ El municipio {$municipio->nombre} no tiene provincia_id asignado.");
                    continue;
                }

                foreach ($entry['Data'] as $dato) {
                    if (!isset($dato['Anyo'], $dato['Valor'])) continue;
                    if ((int) $dato['Anyo'] < 2020) continue;

                    $renta = Renta::firstOrNew([
                        'municipio_id' => $municipio->id,
                        'anyo' => $dato['Anyo'],
                        'tipo' => $tipo
                    ]);

                    $renta->provincia_id = $municipio->provincia_id;
                    $renta->valor = $dato['Valor'];
                    $renta->save();
                }
            }

            sleep(1); // 💤 No fundimos al INE
        }

        $this->info("✅ Importación completada sin lloros.");
    }

    private function determinarTipo($nombre)
    {
        $nombre = strtolower($nombre);

        return match (true) {
            str_contains($nombre, 'neta') && str_contains($nombre, 'persona') => 'neta_persona',
            str_contains($nombre, 'neta') && str_contains($nombre, 'hogar') => 'neta_hogar',
            str_contains($nombre, 'bruta') && str_contains($nombre, 'persona') => 'bruta_persona',
            str_contains($nombre, 'bruta') && str_contains($nombre, 'hogar') => 'bruta_hogar',
            str_contains($nombre, 'unidad de consumo') && str_contains($nombre, 'media') => 'media_uc',
            str_contains($nombre, 'unidad de consumo') && str_contains($nombre, 'mediana') => 'mediana_uc',
            default => 'otro'
        };
    }
}
