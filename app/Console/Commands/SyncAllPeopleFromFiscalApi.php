<?php

namespace App\Console\Commands;

use App\Models\Person;
use Fiscalapi\Services\FiscalApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Exception;

class SyncAllPeopleFromFiscalApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'people:sync-all-from-fiscalapi
                            {--page-size=50 : Número de personas por página}
                            {--force : Forzar sincronización incluso si hay errores}
                            {--update-existing : Actualizar personas existentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizar todas las personas desde FiscalAPI hacia Laravel';

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

        $this->info('🔄 Iniciando sincronización masiva de personas desde FiscalAPI...');

        try {
            $this->syncAllPeopleFromFiscalApi();
        } catch (Exception $e) {
            $this->error('❌ Error durante la sincronización: ' . $e->getMessage());
            Log::error('Error en comando de sincronización masiva de personas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        $this->info('✅ Sincronización masiva completada.');
        return 0;
    }

    /**
     * Sincronizar todas las personas desde FiscalAPI.
     */
    protected function syncAllPeopleFromFiscalApi()
    {
        $pageSize = (int) $this->option('page-size');
        $page = 1;
        $totalSynced = 0;
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;

        $this->info("📊 Configuración: {$pageSize} personas por página");

        do {
            $this->info("📄 Procesando página {$page}...");

            try {
                $apiResponse = $this->fiscalApi->getPersonService()->list($page, $pageSize);
                $responseData = $apiResponse->getJson();

                // Debug: Mostrar respuesta completa de la API
                $this->info("🔍 Respuesta de la API (página {$page}):");
                $this->line("  - Succeeded: " . ($responseData['succeeded'] ? 'true' : 'false'));
                $this->line("  - Message: " . ($responseData['message'] ?? 'N/A'));
                $this->line("  - Data keys: " . implode(', ', array_keys($responseData['data'] ?? [])));

                if (isset($responseData['data']['items'])) {
                    $this->line("  - Items count: " . count($responseData['data']['items']));
                }

                if (!$responseData['succeeded']) {
                    $this->error("❌ Error al obtener página {$page}: " . ($responseData['message'] ?? 'Error desconocido'));
                    Log::error('Error en respuesta de API', [
                        'page' => $page,
                        'response' => $responseData
                    ]);
                    break;
                }

                $people = $responseData['data']['items'] ?? [];
                $totalInPage = count($people);

                if ($totalInPage === 0) {
                    $this->info("📄 Página {$page} está vacía, terminando sincronización.");
                    $this->warn("⚠️  La API no devolvió personas. Verifica:");
                    $this->warn("   - Configuración de API Key en .env");
                    $this->warn("   - Endpoint correcto de la API");
                    $this->warn("   - Permisos de la cuenta de FiscalAPI");
                    break;
                }

                $this->info("📄 Procesando {$totalInPage} personas en la página {$page}...");

                $bar = $this->output->createProgressBar($totalInPage);
                $bar->start();

                foreach ($people as $fiscalApiPerson) {
                    try {
                        $result = $this->syncPersonFromFiscalApi($fiscalApiPerson);

                        if ($result === 'created') {
                            $totalCreated++;
                        } elseif ($result === 'updated') {
                            $totalUpdated++;
                        }

                        $totalSynced++;
                    } catch (Exception $e) {
                        $totalErrors++;
                        Log::error('Error al sincronizar persona desde FiscalAPI', [
                            'fiscalapi_id' => $fiscalApiPerson['id'] ?? 'unknown',
                            'error' => $e->getMessage()
                        ]);

                        if (!$this->option('force')) {
                            $this->error("\n❌ Error en persona {$fiscalApiPerson['id']}: " . $e->getMessage());
                            break 2; // Salir de ambos bucles
                        }
                    }

                    $bar->advance();
                }

                $bar->finish();
                $this->newLine();

                $this->info("📄 Página {$page} completada: {$totalSynced} total, {$totalCreated} creadas, {$totalUpdated} actualizadas, {$totalErrors} errores");

                // Verificar si hay más páginas
                $hasNextPage = $responseData['data']['hasNextPage'] ?? false;

                if ($hasNextPage) {
                    $page++;
                    $this->newLine();
                } else {
                    $this->info("📄 No hay más páginas, sincronización completada.");
                    break;
                }

            } catch (Exception $e) {
                $this->error("❌ Error al procesar página {$page}: " . $e->getMessage());
                Log::error('Error al procesar página de personas', [
                    'page' => $page,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                if (!$this->option('force')) {
                    break;
                }

                $page++;
            }

        } while (true);

        // Resumen final
        $this->newLine(2);
        $this->info('📊 RESUMEN DE SINCRONIZACIÓN:');
        $this->line("  Total sincronizadas: {$totalSynced}");
        $this->line("  Total creadas: {$totalCreated}");
        $this->line("  Total actualizadas: {$totalUpdated}");
        $this->line("  Total errores: {$totalErrors}");
        $this->line("  Páginas procesadas: " . ($page - 1));

        if ($totalSynced === 0) {
            $this->warn("⚠️  No se sincronizó ninguna persona. Verifica la configuración de FiscalAPI.");
        }
    }

    /**
     * Sincronizar una persona específica desde FiscalAPI.
     */
    protected function syncPersonFromFiscalApi(array $fiscalApiPerson): string
    {
        $fiscalApiId = $fiscalApiPerson['id'];
        Log::info('FiscalAPI ID: ' . $fiscalApiId);
        Log::info('FiscalAPI Person: ' . json_encode($fiscalApiPerson));

        //if id length is difernet that 36, then skip it.
        if (strlen($fiscalApiId) !== 36) {
            return 'skipped';
        }
        // Buscar si la persona ya existe localmente
        $existingPerson = Person::where('fiscalapiId', $fiscalApiId)->first();

        if ($existingPerson) {
            // Persona existe, actualizar si se solicita
            if ($this->option('update-existing')) {
                $existingPerson->update([
                    'legalName' => $fiscalApiPerson['legalName'] ?? $existingPerson->legalName,
                    'email' => $fiscalApiPerson['email'] ?? $existingPerson->email,
                    'capitalRegime' => $fiscalApiPerson['capitalRegime'] ?? $existingPerson->capitalRegime,
                    'satTaxRegimeId' => $fiscalApiPerson['satTaxRegimeId'] ?? $existingPerson->satTaxRegimeId,
                    'satCfdiUseId' => $fiscalApiPerson['satCfdiUseId'] ?? $existingPerson->satCfdiUseId,
                    'tin' => $fiscalApiPerson['tin'] ?? $existingPerson->tin,
                    'zipCode' => $fiscalApiPerson['zipCode'] ?? $existingPerson->zipCode,
                ]);

                return 'updated';
            } else {
                return 'skipped';
            }
        } else {
            // Persona no existe, crear nueva
            $newPerson = Person::create([
                'fiscalapiId' => $fiscalApiId,
                'legalName' => $fiscalApiPerson['legalName'],
                'email' => $fiscalApiPerson['email'],
                'capitalRegime' => $fiscalApiPerson['capitalRegime'] ?? null,
                'satTaxRegimeId' => $fiscalApiPerson['satTaxRegimeId'] ?? null,
                'satCfdiUseId' => $fiscalApiPerson['satCfdiUseId'] ?? null,
                'tin' => $fiscalApiPerson['tin'] ?? null,
                'zipCode' => $fiscalApiPerson['zipCode'] ?? null,
                'password' => bcrypt('Admin12345!'), // Contraseña estándar hardcodeada
                'taxPassword' => bcrypt('12345678a'), // Contraseña fiscal estándar hardcodeada
            ]);

            return 'created';
        }
    }
}
