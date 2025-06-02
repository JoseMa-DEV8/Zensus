<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\Importadores\ImportarTerritorio;
use App\Console\Commands\Importadores\ImportarDemografia;
use App\Console\Commands\Importadores\ImportarTasaParo;
use App\Console\Commands\Importadores\ImportarEmpresasSectores;

class ImportarINE extends Command
{
    protected $signature = 'importar:todo';
    protected $description = 'Ejecuta todos los comandos de importación de golpe';

    public function handle()
    {
        $this->info("🚀 Iniciando importación total...");

        $this->call(ImportarTerritorio::class);
        $this->call(ImportarDemografia::class);
        $this->call(ImportarTasaParo::class);
        $this->call(ImportarEmpresasSectores::class);
        
        $this->info("✅ Todo importado correctamente.");
    }
}
