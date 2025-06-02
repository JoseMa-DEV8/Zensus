<?php

namespace App\Console\Commands\Importadores;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\ProvinciaCodificada;
use App\Models\Municipio;

class ImportarTerritorio extends Command
{
    protected $signature = 'importar:territorio';
    protected $description = 'Importa provincias y municipios desde el Excel oficial del INE';

    public function handle()
    {
        $filePath = storage_path('app/public/municipios_ine.xlsx'); // Coloca el archivo ahí
        $rows = Excel::toArray([], $filePath)[0];
        $importados = 0;

        foreach ($rows as $index => $row) {
            if (!isset($row[1], $row[2], $row[4]) || $index < 2) continue;

            $cop = str_pad((string) $row[1], 2, '0', STR_PAD_LEFT); // CPRO
            $com = str_pad((string) $row[2], 3, '0', STR_PAD_LEFT); // CMUN
            $nombreMunicipio = trim($row[4]);                       // NOMBRE

            // Nombre oficial de la provincia por código
            $nombreProvinciaReal = $this->getNombreProvinciaPorCodigo($cop);

            // Crear provincia si no existe
            $provincia = ProvinciaCodificada::firstOrNew(['codigo' => $cop]);

            if (!$provincia->exists && $nombreProvinciaReal) {
                $provincia->id = (int) $cop;
                $provincia->nombre = $nombreProvinciaReal;
                $provincia->save();

                $this->line("✅ Provincia creada: [$cop] $nombreProvinciaReal");
            }

            // Crear o actualizar municipio
            Municipio::updateOrCreate([
                'cop' => $cop,
                'com' => $com,
            ], [
                'provincia_id' => $provincia->id,
                'nombre' => $nombreMunicipio,
            ]);

            $importados++;
        }

        $this->info("🎯 Importación completada: $importados municipios importados.");
    }

    private function getNombreProvinciaPorCodigo($codigo)
    {
        $provincias = [
            '01' => 'Álava', '02' => 'Albacete', '03' => 'Alicante/Alacant',
            '04' => 'Almería', '05' => 'Ávila', '06' => 'Badajoz',
            '07' => 'Illes Balears', '08' => 'Barcelona', '09' => 'Burgos',
            '10' => 'Cáceres', '11' => 'Cádiz', '12' => 'Castellón/Castelló',
            '13' => 'Ciudad Real', '14' => 'Córdoba', '15' => 'A Coruña',
            '16' => 'Cuenca', '17' => 'Girona', '18' => 'Granada',
            '19' => 'Guadalajara', '20' => 'Gipuzkoa', '21' => 'Huelva',
            '22' => 'Huesca', '23' => 'Jaén', '24' => 'León',
            '25' => 'Lleida', '26' => 'La Rioja', '27' => 'Lugo',
            '28' => 'Madrid', '29' => 'Málaga', '30' => 'Murcia',
            '31' => 'Navarra', '32' => 'Ourense', '33' => 'Asturias',
            '34' => 'Palencia', '35' => 'Las Palmas', '36' => 'Pontevedra',
            '37' => 'Salamanca', '38' => 'Santa Cruz de Tenerife', '39' => 'Cantabria',
            '40' => 'Segovia', '41' => 'Sevilla', '42' => 'Soria',
            '43' => 'Tarragona', '44' => 'Teruel', '45' => 'Toledo',
            '46' => 'València/Valencia', '47' => 'Valladolid', '48' => 'Bizkaia',
            '49' => 'Zamora', '50' => 'Zaragoza', '51' => 'Ceuta', '52' => 'Melilla'
        ];

        return $provincias[$codigo] ?? null;
    }
}
