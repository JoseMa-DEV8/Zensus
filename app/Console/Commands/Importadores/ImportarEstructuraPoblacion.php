<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Municipio;
use App\Models\EstructuraPoblacion;

class ImportarEstructuraPoblacion extends Command
{
    protected $signature = 'importar:estructura-poblacion';
    protected $description = 'Importa datos de población por edad, sexo y municipio desde API jsCache del INE usando cop + com';

    public function handle()
    {
        $urls = [
            'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33576?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33584?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33645?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33686?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33692?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33698?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33704?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33710?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33716?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33722?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33728?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33734?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33740?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33746?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33752?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33758?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33764?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33770?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33776?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33782?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33788?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33794?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33800?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33806?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33812?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33818?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33824?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33830?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33836?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33842?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33848?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33854?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33860?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33866?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33872?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33878?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33884?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33890?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33896?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33902?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33908?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33914?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33920?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33926?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33932?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33938?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33944?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33950?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33956?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33962?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33968?tip=AM&',
               'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/33974?tip=AM&'
        ];

        foreach ($urls as $url) {
            $this->info("📥 Procesando: $url");

            $response = Http::get($url);

            if (!$response->successful()) {
                $this->error("❌ Error al obtener datos de $url");
                continue;
            }

            $json = $response->json();

            if (!is_array($json)) {
                $this->warn("⚠️ La respuesta no es un array de entradas");
                continue;
            }

            foreach ($json as $entry) {
                if (!isset($entry['MetaData']) || !isset($entry['Data'])) {
                    $this->warn("⚠️ Registro mal formado o incompleto.");
                    continue;
                }

                $meta = collect($entry['MetaData']);

                $sexo = optional($meta->firstWhere('T3_Variable', 'Sexo'))['Nombre'] ?? 'Total';
                $sexo = match(strtolower($sexo)) {
                    'hombres' => 'Hombre',
                    'mujeres' => 'Mujer',
                    'total' => 'Total',
                    default => $sexo
                };

                $grupoEdad = optional(
                    $meta->first(fn ($m) => str_contains(strtolower($m['T3_Variable']), 'edad'))
                )['Nombre'] ?? 'Desconocido';

                $municipioMeta = $meta->firstWhere('T3_Variable', 'Municipios');
                if (!$municipioMeta) {
                    $this->warn("❗ No hay metadato de municipio");
                    continue;
                }

                if (isset($municipioMeta['Codigo']) && strlen($municipioMeta['Codigo']) === 5) {
                    $codigoCOM = str_pad($municipioMeta['Codigo'], 5, '0', STR_PAD_LEFT);
                    $cop = substr($codigoCOM, 0, 2);
                    $com = substr($codigoCOM, 2, 3);

                    $municipio = Municipio::where('cop', $cop)->where('com', $com)->first();
                } else {
                    $nombreMunicipio = trim($municipioMeta['Nombre']);
                    $municipio = Municipio::whereRaw('LOWER(nombre) = ?', [strtolower($nombreMunicipio)])->first();
                }

                if (!$municipio) {
                    $this->warn("❗ Municipio no encontrado: " . ($municipioMeta['Nombre'] ?? 'desconocido'));
                    continue;
                }

                $valoresData = collect($entry['Data'])->filter(fn($d) => isset($d['Anyo']) && isset($d['Valor']));

                if ($valoresData->isEmpty()) {
                    continue;
                }

                $ultimoAnyo = $valoresData->max('Anyo');

                foreach ($valoresData as $data) {
                    if ((int)$data['Anyo'] !== (int)$ultimoAnyo) {
                        continue;
                    }

                    if ($grupoEdad === 'Desconocido') {
                        $this->warn("⚠️ Grupo edad desconocido en municipio {$municipio->nombre} – omitido");
                        continue;
                    }

                    if ($sexo === 'Total' && $grupoEdad === 'Todas las edades') {
                        continue;
                    }

                    EstructuraPoblacion::updateOrCreate(
                        [
                            'municipio_id' => $municipio->id,
                            'anio' => $data['Anyo'],
                            'grupo_edad' => $grupoEdad,
                            'sexo' => $sexo,
                        ],
                        ['poblacion' => $data['Valor']]
                    );
                }
            }

            $this->info("✅ Datos importados desde $url");
        }

        $this->info("🏁 Importación completada.");
    }
}
