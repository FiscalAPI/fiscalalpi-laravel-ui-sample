<?php

namespace App\Console\Commands;

use App\Models\Person;
use Fiscalapi\Services\FiscalApiClient;
use Illuminate\Console\Command;

class TestFiscalApiPersonCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiscalapi:test-person-creation
                            {--id= : ID de la persona local a usar como base}
                            {--minimal : Probar solo con campos mínimos requeridos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar diferentes combinaciones de campos para crear personas en FiscalAPI';

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

        $this->info('🧪 Probando creación de personas en FiscalAPI...');
        $this->newLine();

        try {
            if ($this->option('id')) {
                $person = Person::find($this->option('id'));
                if (!$person) {
                    $this->error("❌ No se encontró la persona con ID: {$this->option('id')}");
                    return 1;
                }
                $this->testPersonCreation($person);
            } else {
                $this->error("❌ Debe especificar un ID de persona con --id");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error durante las pruebas: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Probar diferentes combinaciones de campos.
     */
    protected function testPersonCreation(Person $person)
    {
        $this->info("🔍 Probando con persona: {$person->legalName} (ID: {$person->id})");
        $this->newLine();

        // Test 1: Solo campos básicos
        $this->testBasicFields($person);

        // Test 2: Con códigos SAT
        $this->testWithSatCodes($person);

        // Test 3: Con todos los campos
        $this->testWithAllFields($person);

        // Test 4: Campos mínimos (si se especifica)
        if ($this->option('minimal')) {
            $this->testMinimalFields($person);
        }
    }

    /**
     * Test 1: Solo campos básicos.
     */
    protected function testBasicFields(Person $person)
    {
        $this->info('📋 Test 1: Solo campos básicos');

        $data = [
            'legalName' => $person->legalName,
            'email' => $person->email,
            'password' => 'Admin12345!', // Contraseña estándar hardcodeada
        ];

        $this->testApiCall('create', $data, 'Solo campos básicos');
    }

    /**
     * Test 2: Con códigos SAT.
     */
    protected function testWithSatCodes(Person $person)
    {
        $this->info('📋 Test 2: Con códigos SAT');

        $data = [
            'legalName' => $person->legalName,
            'email' => $person->email,
            'password' => 'Admin12345!', // Contraseña estándar hardcodeada
            'taxPassword' => '12345678a', // Contraseña fiscal estándar hardcodeada
            'satTaxRegimeId' => $person->satTaxRegimeId,
            'satCfdiUseId' => $person->satCfdiUseId,
        ];

        $this->testApiCall('create', $data, 'Con códigos SAT');
    }

    /**
     * Test 3: Con todos los campos.
     */
    protected function testWithAllFields(Person $person)
    {
        $this->info('📋 Test 3: Con todos los campos');

        $data = [
            'legalName' => $person->legalName,
            'email' => $person->email,
            'password' => 'Admin12345!', // Contraseña estándar hardcodeada
            'taxPassword' => '12345678a', // Contraseña fiscal estándar hardcodeada
            'capitalRegime' => $person->capitalRegime,
            'satTaxRegimeId' => $person->satTaxRegimeId,
            'satCfdiUseId' => $person->satCfdiUseId,
            'tin' => $person->tin,
            'zipCode' => $person->zipCode,
        ];

        // Remover campos null y vacíos
        $data = array_filter($data, function ($value) {
            return $value !== null && $value !== '';
        });

        $this->testApiCall('create', $data, 'Con todos los campos');
    }

    /**
     * Test 4: Campos mínimos.
     */
    protected function testMinimalFields(Person $person)
    {
        $this->info('📋 Test 4: Campos mínimos');

        $data = [
            'legalName' => $person->legalName,
            'password' => 'Admin12345!', // Contraseña estándar hardcodeada
        ];

        $this->testApiCall('create', $data, 'Solo legalName y password');
    }

    /**
     * Probar llamada a la API.
     */
    protected function testApiCall(string $method, array $data, string $description)
    {
        try {
            $this->line("  📤 Probando: {$description}");
            $this->line("  📋 Datos: " . json_encode($data, JSON_PRETTY_PRINT));

            $apiResponse = $this->fiscalApi->getPersonService()->$method($data);
            $responseData = $apiResponse->getJson();

            if ($responseData['succeeded']) {
                $this->info("  ✅ ÉXITO: {$description}");
                $this->line("  📊 Respuesta: " . json_encode($responseData, JSON_PRETTY_PRINT));

                // Si es create, mostrar el ID
                if (isset($responseData['data']['id'])) {
                    $this->line("  🆔 ID creado: {$responseData['data']['id']}");
                }
            } else {
                $this->error("  ❌ FALLO: {$description}");
                $this->line("  📊 Respuesta: " . json_encode($responseData, JSON_PRETTY_PRINT));
            }

        } catch (\Exception $e) {
            $this->error("  ❌ ERROR: {$description} - " . $e->getMessage());
        }

        $this->newLine();
    }
}
