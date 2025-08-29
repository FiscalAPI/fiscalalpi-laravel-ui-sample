<?php

namespace App\Console\Commands;

use App\Models\Person;
use Fiscalapi\Services\FiscalApiClient;
use Illuminate\Console\Command;

class ValidatePersonForFiscalApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'people:validate-fiscalapi
                            {--id= : Validar una persona específica por ID local}
                            {--all : Validar todas las personas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validar datos de personas antes de enviar a FiscalAPI';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Iniciando validación de personas para FiscalAPI...');
        $this->newLine();

        if ($this->option('id')) {
            $this->validateSpecificPerson($this->option('id'));
        } elseif ($this->option('all')) {
            $this->validateAllPeople();
        } else {
            $this->showUsage();
        }

        $this->info('✅ Validación completada.');
        return 0;
    }

    /**
     * Validar una persona específica.
     */
    protected function validateSpecificPerson(string $id)
    {
        $person = Person::find($id);

        if (!$person) {
            $this->error("❌ No se encontró la persona con ID: {$id}");
            return;
        }

        $this->info("🔍 Validando persona: {$person->legalName} (ID: {$id})");
        $this->validatePersonData($person);
    }

    /**
     * Validar todas las personas.
     */
    protected function validateAllPeople()
    {
        $people = Person::all();
        $total = $people->count();

        $this->info("🔍 Validando {$total} personas...");

        $validCount = 0;
        $invalidCount = 0;

        foreach ($people as $person) {
            $this->line("  📋 Validando: {$person->legalName} (ID: {$person->id})");

            if ($this->validatePersonData($person, false)) {
                $validCount++;
            } else {
                $invalidCount++;
            }

            $this->newLine();
        }

        $this->info("📊 RESUMEN DE VALIDACIÓN:");
        $this->line("  Total personas: {$total}");
        $this->line("  Válidas: {$validCount}");
        $this->line("  Inválidas: {$invalidCount}");
    }

    /**
     * Validar datos de una persona.
     */
    protected function validatePersonData(Person $person, bool $showDetails = true): bool
    {
        $isValid = true;
        $errors = [];
        $warnings = [];

        // Validar campos requeridos
        if (empty($person->legalName)) {
            $errors[] = 'legalName es requerido';
            $isValid = false;
        }

        if (empty($person->email)) {
            $errors[] = 'email es requerido';
            $isValid = false;
        }

        if (empty($person->tin)) {
            $errors[] = 'tin (RFC) es requerido';
            $isValid = false;
        }

        // Validar formato de email
        if (!empty($person->email) && !filter_var($person->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'email tiene formato inválido';
            $isValid = false;
        }

        // Validar formato de RFC (básico)
        if (!empty($person->tin) && strlen($person->tin) < 10) {
            $warnings[] = 'tin (RFC) parece ser muy corto';
        }

        // Validar códigos SAT
        if (!empty($person->satTaxRegimeId)) {
            $satTaxRegime = $person->satTaxRegime;
            if (!$satTaxRegime) {
                $warnings[] = 'satTaxRegimeId no existe en la tabla de códigos SAT';
            }
        }

        if (!empty($person->satCfdiUseId)) {
            $satCfdiUse = $person->satCfdiUse;
            if (!$satCfdiUse) {
                $warnings[] = 'satCfdiUseId no existe en la tabla de códigos SAT';
            }
        }

        // Validar zipCode
        if (!empty($person->zipCode) && strlen($person->zipCode) !== 5) {
            $warnings[] = 'zipCode debe tener 5 dígitos';
        }

        // Mostrar resultados
        if ($showDetails) {
            $this->line("  📋 Datos de la persona:");
            $this->line("    - ID: {$person->id}");
            $this->line("    - Nombre: {$person->legalName}");
            $this->line("    - Email: {$person->email}");
            $this->line("    - RFC: {$person->tin}");
            $this->line("    - Código Postal: {$person->zipCode}");
            $this->line("    - Régimen Fiscal: {$person->satTaxRegimeId}");
            $this->line("    - Uso CFDI: {$person->satCfdiUseId}");
            $this->line("    - Régimen Capital: {$person->capitalRegime}");
            $this->line("    - FiscalAPI ID: " . ($person->fiscalapiId ?: 'No asignado'));

            $this->newLine();
        }

        // Mostrar errores
        if (!empty($errors)) {
            $this->error("  ❌ Errores de validación:");
            foreach ($errors as $error) {
                $this->line("    - {$error}");
            }
        }

        // Mostrar advertencias
        if (!empty($warnings)) {
            $this->warn("  ⚠️ Advertencias:");
            foreach ($warnings as $warning) {
                $this->line("    - {$warning}");
            }
        }

        // Mostrar estado final
        if ($isValid) {
            if (empty($warnings)) {
                $this->info("  ✅ Persona válida para FiscalAPI");
            } else {
                $this->warn("  ⚠️ Persona válida pero con advertencias");
            }
        } else {
            $this->error("  ❌ Persona NO válida para FiscalAPI");
        }

        return $isValid;
    }

    /**
     * Mostrar uso del comando.
     */
    protected function showUsage()
    {
        $this->info('📖 Uso del comando de validación:');
        $this->line('');
        $this->line('  # Validar una persona específica por ID local');
        $this->line('  php artisan people:validate-fiscalapi --id=1');
        $this->line('');
        $this->line('  # Validar todas las personas');
        $this->line('  php artisan people:validate-fiscalapi --all');
        $this->line('');
    }
}
