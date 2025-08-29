<?php

namespace App\Console\Commands;

use Fiscalapi\Services\FiscalApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestFiscalApiPeopleConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiscalapi:test-people
                            {--detailed : Mostrar información detallada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar conexión con FiscalAPI para el servicio de personas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Probando conexión con FiscalAPI para personas...');
        $this->newLine();

        try {
            $fiscalApi = app(FiscalApiClient::class);

            // Test 1: Listar personas (página 1, 1 persona)
            $this->info('📋 Test 1: Listar personas...');
            $this->testListPeople($fiscalApi);

            // Test 2: Verificar configuración
            $this->info('⚙️ Test 2: Verificar configuración...');
            $this->testConfiguration();

            // Test 3: Probar servicio de personas
            $this->info('🔧 Test 3: Probar servicio de personas...');
            $this->testPersonService($fiscalApi);

            $this->newLine();
            $this->info('✅ Todas las pruebas completadas exitosamente!');

        } catch (\Exception $e) {
            $this->error('❌ Error durante las pruebas: ' . $e->getMessage());
            Log::error('Error en prueba de conexión de personas con FiscalAPI', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }

    /**
     * Test 1: Listar personas.
     */
    protected function testListPeople(FiscalApiClient $fiscalApi)
    {
        try {
            $this->line('  📤 Enviando request a getPersonService()->list(1, 1)...');

            $apiResponse = $fiscalApi->getPersonService()->list(1, 1);
            $responseData = $apiResponse->getJson();

            if ($responseData['succeeded']) {
                $this->info('  ✅ Lista de personas obtenida exitosamente');

                if ($this->option('detailed')) {
                    $this->line('  📊 Respuesta:');
                    $this->line('    - Succeeded: ' . ($responseData['succeeded'] ? 'true' : 'false'));
                    $this->line('    - HTTP Status: ' . ($responseData['httpStatusCode'] ?? 'N/A'));
                    $this->line('    - Total de personas en página: ' . count($responseData['data']['items'] ?? []));

                    if (isset($responseData['data']['totalCount'])) {
                        $this->line('    - Total de personas en sistema: ' . $responseData['data']['totalCount']);
                    }

                    if (isset($responseData['data']['hasNextPage'])) {
                        $this->line('    - Tiene siguiente página: ' . ($responseData['data']['hasNextPage'] ? 'Sí' : 'No'));
                    }
                }
            } else {
                $this->warn('  ⚠️ Lista obtenida pero con errores: ' . ($responseData['message'] ?? 'Error desconocido'));
            }

        } catch (\Exception $e) {
            $this->error('  ❌ Error al listar personas: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Test 2: Verificar configuración.
     */
    protected function testConfiguration()
    {
        $config = config('fiscalapi');

        $this->line('  📋 Configuración actual:');
        $this->line('    - API URL: ' . ($config['apiUrl'] ?? 'No configurado'));
        $this->line('    - API Key: ' . (strlen($config['apiKey'] ?? '') > 0 ? 'Configurada (' . substr($config['apiKey'], 0, 8) . '...)' : 'No configurada'));
        $this->line('    - Tenant: ' . ($config['tenant'] ?? 'No configurado'));
        $this->line('    - API Version: ' . ($config['apiVersion'] ?? 'No configurado'));
        $this->line('    - Timezone: ' . ($config['timeZone'] ?? 'No configurado'));
        $this->line('    - Debug: ' . ($config['debug'] ? 'Activado' : 'Desactivado'));
        $this->line('    - Verify SSL: ' . ($config['verifySsl'] ? 'Sí' : 'No'));

        // Verificar variables de entorno
        $this->line('  🔍 Variables de entorno:');
        $this->line('    - FISCALAPI_URL: ' . (env('FISCALAPI_URL') ?: 'No configurada'));
        $this->line('    - FISCALAPI_KEY: ' . (env('FISCALAPI_KEY') ? 'Configurada' : 'No configurada'));
        $this->line('    - FISCALAPI_TENANT: ' . (env('FISCALAPI_TENANT') ?: 'No configurada'));

        $this->info('  ✅ Configuración verificada');
    }

    /**
     * Test 3: Probar servicio de personas.
     */
    protected function testPersonService(FiscalApiClient $fiscalApi)
    {
        try {
            $this->line('  🔧 Probando métodos del servicio de personas...');

            // Verificar que el servicio existe
            $personService = $fiscalApi->getPersonService();
            $this->line('    ✅ getPersonService() disponible');

            // Verificar métodos disponibles
            $methods = get_class_methods($personService);
            $expectedMethods = ['list', 'get', 'create', 'update', 'delete'];

            $this->line('    📋 Métodos disponibles:');
            foreach ($expectedMethods as $method) {
                if (in_array($method, $methods)) {
                    $this->line('      ✅ ' . $method . '()');
                } else {
                    $this->line('      ❌ ' . $method . '() - NO DISPONIBLE');
                }
            }

            // Verificar que todos los métodos esperados estén disponibles
            $missingMethods = array_diff($expectedMethods, $methods);
            if (empty($missingMethods)) {
                $this->info('    ✅ Todos los métodos esperados están disponibles');
            } else {
                $this->warn('    ⚠️ Faltan métodos: ' . implode(', ', $missingMethods));
            }

        } catch (\Exception $e) {
            $this->error('  ❌ Error al probar servicio de personas: ' . $e->getMessage());
            throw $e;
        }
    }
}
