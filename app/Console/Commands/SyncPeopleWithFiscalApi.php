<?php

namespace App\Console\Commands;

use App\Models\Person;
use Fiscalapi\Services\FiscalApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;

class SyncPeopleWithFiscalApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'people:sync-fiscalapi
                            {--id= : Sincronizar una persona específica por ID local}
                            {--fiscalapi-id= : Sincronizar una persona específica por ID de FiscalAPI}
                            {--all : Sincronizar todas las personas}
                            {--from-fiscalapi : Sincronizar desde FiscalAPI hacia Laravel}
                            {--to-fiscalapi : Sincronizar desde Laravel hacia FiscalAPI}
                            {--force : Forzar sincronización incluso si hay errores}
                            {--debug : Mostrar datos detallados de la sincronización}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizar personas entre Laravel y FiscalAPI';

    /**
     * The FiscalAPI client instance.
     *
     * @var FiscalApiClient
     */
    protected $fiscalApi;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->fiscalApi = app(FiscalApiClient::class);

        $this->info('🔄 Iniciando sincronización de personas con FiscalAPI...');

        try {
            if ($this->option('id')) {
                $this->syncSpecificPerson($this->option('id'));
            } elseif ($this->option('fiscalapi-id')) {
                $this->syncFromFiscalApiId($this->option('fiscalapi-id'));
            } elseif ($this->option('all')) {
                $this->syncAllPeople();
            } else {
                $this->showUsage();
            }
        } catch (Exception $e) {
            $this->error('❌ Error durante la sincronización: ' . $e->getMessage());
            Log::error('Error en comando de sincronización de personas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        $this->info('✅ Sincronización completada.');
        return 0;
    }

    /**
     * Sincronizar una persona específica por ID local.
     */
    protected function syncSpecificPerson(string $id)
    {
        $person = Person::find($id);

        if (!$person) {
            $this->error("❌ No se encontró la persona con ID: {$id}");
            return;
        }

        $this->info("🔄 Sincronizando persona: {$person->legalName} (ID: {$id})");

        if ($this->option('from-fiscalapi')) {
            $this->syncFromFiscalApi($person);
        } elseif ($this->option('to-fiscalapi')) {
            $this->syncToFiscalApi($person);
        } else {
            // Sincronización bidireccional
            $this->syncBidirectional($person);
        }
    }

    /**
     * Sincronizar desde un ID de FiscalAPI.
     */
    protected function syncFromFiscalApiId(string $fiscalApiId)
    {
        $this->info("🔄 Sincronizando desde FiscalAPI ID: {$fiscalApiId}");

        try {
            $person = $this->syncFromFiscalApi(null, $fiscalApiId);
            if ($person) {
                $this->info("✅ Persona sincronizada: {$person->legalName}");
            }
        } catch (Exception $e) {
            $this->error("❌ Error al sincronizar desde FiscalAPI: " . $e->getMessage());
        }
    }

    /**
     * Sincronizar todas las personas.
     */
    protected function syncAllPeople()
    {
        $people = Person::all();
        $total = $people->count();

        $this->info("🔄 Sincronizando {$total} personas...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $synced = 0;
        $errors = 0;

        foreach ($people as $person) {
            try {
                if ($this->option('from-fiscalapi')) {
                    $this->syncFromFiscalApi($person);
                } elseif ($this->option('to-fiscalapi')) {
                    $this->syncToFiscalApi($person);
                } else {
                    $this->syncBidirectional($person);
                }
                $synced++;
            } catch (Exception $e) {
                $errors++;
                Log::error('Error al sincronizar persona', [
                    'person_id' => $person->id,
                    'error' => $e->getMessage()
                ]);

                if (!$this->option('force')) {
                    $this->error("\n❌ Error en persona {$person->id}: " . $e->getMessage());
                    break;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Sincronización completada: {$synced} exitosas, {$errors} errores");
    }

    /**
     * Sincronización bidireccional de una persona.
     */
    protected function syncBidirectional(Person $person)
    {
        $this->info("  📤 Sincronizando hacia FiscalAPI...");
        $this->syncToFiscalApi($person);

        $this->info("  📥 Sincronizando desde FiscalAPI...");
        $this->syncFromFiscalApi($person);
    }

    /**
     * Sincronizar persona hacia FiscalAPI.
     */
    protected function syncToFiscalApi(Person $person)
    {
        try {
            if (!$person->fiscalapiId) {
                // Crear en FiscalAPI
                $apiData = $this->prepareApiData($person);

                // Debug: mostrar datos que se envían
                if ($this->option('debug')) {
                    $this->line("    📤 Datos a enviar a FiscalAPI:");
                    foreach ($apiData as $key => $value) {
                        $this->line("      - {$key}: " . (is_null($value) ? 'NULL' : $value));
                    }
                }

                $apiResponse = $this->fiscalApi->getPersonService()->create($apiData);
                $responseData = $apiResponse->getJson();

                if ($responseData['succeeded']) {
                    $person->update(['fiscalapiId' => $responseData['data']['id']]);
                    $this->info("    ✅ Creada en FiscalAPI con ID: {$responseData['data']['id']}");
                } else {
                    // Mostrar información detallada del error
                    $errorMessage = $responseData['message'] ?? 'Error desconocido';
                    $errorDetails = $responseData['details'] ?? 'Sin detalles';
                    $httpStatus = $responseData['httpStatusCode'] ?? 'N/A';

                    $this->error("    ❌ Error al crear en FiscalAPI:");
                    $this->line("      - Mensaje: {$errorMessage}");
                    $this->line("      - Detalles: {$errorDetails}");
                    $this->line("      - HTTP Status: {$httpStatus}");

                    // Mostrar datos enviados para debugging
                    $this->line("      - Datos enviados:");
                    foreach ($apiData as $key => $value) {
                        $this->line("        * {$key}: " . (is_null($value) ? 'NULL' : $value));
                    }

                    throw new Exception("Error al crear en FiscalAPI: {$errorMessage}. Detalles: {$errorDetails}");
                }
            } else {
                // Actualizar en FiscalAPI
                $apiData = $this->prepareApiData($person);
                $apiData['id'] = $person->fiscalapiId;

                // Debug: mostrar datos que se envían
                if ($this->option('debug')) {
                    $this->line("    📤 Datos a enviar a FiscalAPI:");
                    foreach ($apiData as $key => $value) {
                        $this->line("      - {$key}: " . (is_null($value) ? 'NULL' : $value));
                    }
                }

                $apiResponse = $this->fiscalApi->getPersonService()->update($apiData);
                $responseData = $apiResponse->getJson();

                if ($responseData['succeeded']) {
                    $this->info("    ✅ Actualizada en FiscalAPI");
                } else {
                    // Mostrar información detallada del error
                    $errorMessage = $responseData['message'] ?? 'Error desconocido';
                    $errorDetails = $responseData['details'] ?? 'Sin detalles';
                    $httpStatus = $responseData['httpStatusCode'] ?? 'N/A';

                    $this->error("    ❌ Error al actualizar en FiscalAPI:");
                    $this->line("      - Mensaje: {$errorMessage}");
                    $this->line("      - Detalles: {$errorDetails}");
                    $this->line("      - HTTP Status: {$httpStatus}");

                    // Mostrar datos enviados para debugging
                    $this->line("      - Datos enviados:");
                    foreach ($apiData as $key => $value) {
                        $this->line("        * {$key}: " . (is_null($value) ? 'NULL' : $value));
                    }

                    throw new Exception("Error al actualizar en FiscalAPI: {$errorMessage}. Detalles: {$errorDetails}");
                }
            }
        } catch (Exception $e) {
            $this->error("    ❌ Error al sincronizar hacia FiscalAPI: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sincronizar persona desde FiscalAPI.
     */
    protected function syncFromFiscalApi(?Person $person = null, ?string $fiscalApiId = null)
    {
        try {
            $targetFiscalApiId = $fiscalApiId ?? $person->fiscalapiId;

            if (!$targetFiscalApiId) {
                throw new Exception('No se puede sincronizar sin fiscalapiId');
            }

            $apiResponse = $this->fiscalApi->getPersonService()->get($targetFiscalApiId);
            $responseData = $apiResponse->getJson();

            if (!$responseData['succeeded']) {
                throw new Exception('Error al obtener de FiscalAPI: ' . ($responseData['message'] ?? 'Error desconocido'));
            }

            $fiscalApiPerson = $responseData['data'];

            if ($person) {
                // Actualizar persona existente
                $person->update([
                    'legalName' => $fiscalApiPerson['legalName'] ?? $person->legalName,
                    'email' => $fiscalApiPerson['email'] ?? $person->email,
                    'capitalRegime' => $fiscalApiPerson['capitalRegime'] ?? $person->capitalRegime,
                    'satTaxRegimeId' => $fiscalApiPerson['satTaxRegimeId'] ?? $person->satTaxRegimeId,
                    'satCfdiUseId' => $fiscalApiPerson['satCfdiUseId'] ?? $person->satCfdiUseId,
                    'tin' => $fiscalApiPerson['tin'] ?? $person->tin,
                    'zipCode' => $fiscalApiPerson['zipCode'] ?? $person->zipCode,
                ]);

                $this->info("    ✅ Actualizada desde FiscalAPI");
                return $person;
            } else {
                // Crear nueva persona
                $newPerson = Person::create([
                    'fiscalapiId' => $targetFiscalApiId,
                    'legalName' => $fiscalApiPerson['legalName'],
                    'email' => $fiscalApiPerson['email'],
                    'capitalRegime' => $fiscalApiPerson['capitalRegime'] ?? null,
                    'satTaxRegimeId' => $fiscalApiPerson['satTaxRegimeId'] ?? null,
                    'satCfdiUseId' => $fiscalApiPerson['satCfdiUseId'] ?? null,
                    'tin' => $fiscalApiPerson['tin'] ?? null,
                    'zipCode' => $fiscalApiPerson['zipCode'] ?? null,
                    'password' => bcrypt('temp_password_' . time()), // Contraseña temporal
                ]);

                $this->info("    ✅ Creada desde FiscalAPI con ID local: {$newPerson->id}");
                return $newPerson;
            }
        } catch (Exception $e) {
            $this->error("    ❌ Error al sincronizar desde FiscalAPI: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Preparar datos para la API de FiscalAPI.
     */
    protected function prepareApiData(Person $person): array
    {
        // Mapear campos locales a los esperados por FiscalAPI
        $apiData = [
            'legalName' => $person->legalName,
            'email' => $person->email,
            'password' => 'Admin12345!', // Contraseña hardcodeada estándar
            'taxPassword' => '12345678a', // Contraseña fiscal hardcodeada estándar
            'capitalRegime' => $person->capitalRegime,
            'satTaxRegimeId' => $person->satTaxRegimeId,
            'satCfdiUseId' => $person->satCfdiUseId,
            'tin' => $person->tin,
            'zipCode' => $person->zipCode,
        ];

        // Remover campos null y vacíos
        $filteredData = array_filter($apiData, function ($value) {
            return $value !== null && $value !== '';
        });

        // Debug: mostrar qué campos se están enviando
        if ($this->option('debug')) {
            $this->line("    🔍 Campos disponibles en la persona:");
            foreach ($apiData as $key => $value) {
                $status = ($value !== null && $value !== '') ? '✅' : '❌';
                $this->line("      {$status} {$key}: " . (is_null($value) ? 'NULL' : $value));
            }

            $this->line("    📤 Campos que se enviarán a FiscalAPI:");
            foreach ($filteredData as $key => $value) {
                if ($key === 'password') {
                    $this->line("      ✅ {$key}: ***Admin12345!");
                } elseif ($key === 'taxPassword') {
                    $this->line("      ✅ {$key}: ***12345678a");
                } else {
                    $this->line("      ✅ {$key}: {$value}");
                }
            }
        }

        return $filteredData;
    }



    /**
     * Mostrar uso del comando.
     */
    protected function showUsage()
    {
        $this->info('📖 Uso del comando de sincronización:');
        $this->line('');
        $this->line('  # Sincronizar una persona específica por ID local');
        $this->line('  php artisan people:sync-fiscalapi --id=1');
        $this->line('');
        $this->line('  # Sincronizar una persona específica por ID de FiscalAPI');
        $this->line('  php artisan people:sync-fiscalapi --fiscalapi-id=uuid-here');
        $this->line('');
        $this->line('  # Sincronizar todas las personas');
        $this->line('  php artisan people:sync-fiscalapi --all');
        $this->line('');
        $this->line('  # Sincronizar solo desde FiscalAPI hacia Laravel');
        $this->line('  php artisan people:sync-fiscalapi --all --from-fiscalapi');
        $this->line('');
        $this->line('  # Sincronizar solo desde Laravel hacia FiscalAPI');
        $this->line('  php artisan people:sync-fiscalapi --all --to-fiscalapi');
        $this->line('');
        $this->line('  # Forzar sincronización incluso con errores');
        $this->line('  php artisan people:sync-fiscalapi --all --force');
        $this->line('');
        $this->line('  # Mostrar datos detallados de la sincronización');
        $this->line('  php artisan people:sync-fiscalapi --all --debug');
        $this->line('');
    }
}
