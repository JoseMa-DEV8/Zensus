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

        // Cargar caché de municipios y provincias
        $municipiosCache = Municipio::all()->keyBy('nombre');
        $provinciasCache = ProvinciaCodificada::all()->keyBy('nombre');

        foreach ($entradas as $registro) {
            $codigoDir = $registro['COD'] ?? null;
            $nombre = $registro['Nombre'] ?? '';
            $data = $registro['Data'] ?? [];

            $provincia_codigo = null;
            $municipio_codigo = null;

            // Buscar municipio
            if (preg_match('/^(.+?)\. Total\. Total de empresas\. Total CNAE\. Empresas\./', $nombre, $match)) {
                $nombreMunicipio = trim($match[1]);
                $municipio = $municipiosCache[$nombreMunicipio] ?? null;
                if ($municipio) {
                    $provincia_codigo = $municipio->cop;
                    $municipio_codigo = $municipio->com;
                }
            }

            // Buscar provincia
            elseif (preg_match('/^Total\. (.+?)\. Total\./', $nombre, $match)) {
                $nombreProvincia = trim($match[1]);
                $provincia = $provinciasCache[$nombreProvincia] ?? null;
                if ($provincia) {
                    $provincia_codigo = $provincia->codigo;
                }
            }

            // Extraer sector
            $sector = 'Total';
            $partes = explode('.', $nombre);
            if (count($partes) >= 2) {
                $sectorCandidato = trim($partes[count($partes) - 2]);
                if (!str_starts_with(strtolower($sectorCandidato), 'total')) {
                    $sector = $sectorCandidato;
                }
            }

            foreach ($data as $dato) {
                $fecha = $dato['Fecha'] ?? null;
                $valor = $dato['Valor'] ?? null;

                if (!$fecha || is_null($valor)) continue;

                $anyo = intval(substr($fecha, 0, 4));
                if ($anyo < 2021) continue;

                NumeroEmpresaSector::updateOrCreate([
                    'codigo_dir' => $codigoDir,
                    'sector'     => $sector,
                    'anyo'       => $anyo,
                ], [
                    'provincia_codigo' => $provincia_codigo,
                    'municipio_codigo' => $municipio_codigo,
                    'valor'            => $valor,
                ]);

                $insertados++;
            }
        }

        $this->info("✅ Importación completada. Registros procesados: {$insertados}");
    }
}
