<?php

namespace App\Console\Commands\Importadores;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\NumeroEmpresaSector;
use App\Models\Municipio;
use App\Models\ProvinciaCodificada;

class ImportarEmpresasSectores extends Command
{
    protected $signature = 'importar:empresas-sectores';
    protected $description = 'Importa los datos del INE (tabla 4721) de empresas por sector desde 2021 hasta el año actual';

    public function handle()
    {
        $this->info("📥 Descargando datos desde el INE...");
        $url = 'https://servicios.ine.es/wstempus/jsCache/es/DATOS_TABLA/4721?tip=AM&';

        $response = Http::timeout(120)->retry(3, 5000)->get($url);

        if (!$response->ok()) {
            $this->error("❌ No se pudo acceder al INE.");
            return;
        }

        $json = $response->json();
        $entradas = $json ?? [];
        $insertados = 0;

        // 📌 Mapeo rápido DIRxxxxx -> cop/com
        $codigosMunicipios = Municipio::all()->mapWithKeys(function ($m) {
            $dir = 'DIR' . $m->cop . str_pad($m->com, 3, '0', STR_PAD_LEFT);
            return [$dir => ['cop' => $m->cop, 'com' => $m->com]];
        });

        // 📌 Mapeo rápido DIRxx -> cop (provincias)
        $codigosProvincias = ProvinciaCodificada::all()->mapWithKeys(function ($p) {
            $dir = 'DIR' . $p->codigo;
            return [$dir => ['cop' => $p->codigo]];
        });

        foreach ($entradas as $registro) {
            $codigoDir = $registro['COD'] ?? null;
            $nombre = $registro['Nombre'] ?? '';
            $data = $registro['Data'] ?? [];

            // Detectar cop y com desde COD
            $cop = null;
            $com = null;

            if (isset($codigosMunicipios[$codigoDir])) {
                $cop = $codigosMunicipios[$codigoDir]['cop'];
                $com = $codigosMunicipios[$codigoDir]['com'];
            } elseif (isset($codigosProvincias[$codigoDir])) {
                $cop = $codigosProvincias[$codigoDir]['cop'];
            }

            // Sector desde el texto
            $sector = 'Total';
            if (preg_match('/\.\s*([^\.]+)\s*\.\s*Empresas/i', $nombre, $match)) {
                $sector = trim($match[1]);
            }

            foreach ($data as $dato) {
                $fecha = $dato['Fecha'] ?? null;
                $valor = $dato['Valor'] ?? null;

                if (!$fecha || is_null($valor)) {
                    continue;
                }

                $anyo = intval(substr($fecha, 0, 4));
                if ($anyo < 2021) {
                    continue;
                }

                NumeroEmpresaSector::updateOrCreate([
                    'codigo_dir' => $codigoDir,
                    'sector'     => $sector,
                    'anyo'       => $anyo,
                ], [
                    'cop'        => $cop,
                    'com'        => $com,
                    'valor'      => $valor,
                ]);

                $insertados++;
            }
        }

        $this->info("✅ Importación completada. Registros procesados: {$insertados}");
    }
}
