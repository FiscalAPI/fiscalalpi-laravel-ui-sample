<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\SatTaxRegimeCode;
use App\Models\SatCfdiUseCode;
use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use Fiscalapi\Services\FiscalApiClient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class PersonController extends Controller
{
    private $fiscalApi;

    public function __construct(FiscalApiClient $fiscalApi)
    {
        $this->fiscalApi = $fiscalApi;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $people = Person::with([
            'satTaxRegime',
            'satCfdiUse'
        ])->get();

        return view('components.people.index', compact('people'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $satTaxRegimes = SatTaxRegimeCode::all();
        $satCfdiUses = SatCfdiUseCode::all();

        return view('components.people.create', compact(
            'satTaxRegimes',
            'satCfdiUses'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // 1. Preparar datos para FiscalAPI (sin hashear contraseñas)
            $apiData = $this->prepareApiData($validatedData);

            // 2. Llamar a la API de FiscalAPI
            $apiResponse = $this->fiscalApi->getPersonService()->create($apiData);

            // 3. Verificar éxito y proceder
            $responseData = $apiResponse->getJson();
            if ($responseData['succeeded']) {
                $fiscalapiId = $responseData['data']['id'];

                // 4. Preparar datos para la BD local (con ID de fiscalapi y contraseñas hasheadas)
                $localData = $validatedData;
                $localData['fiscalapiId'] = $fiscalapiId;
                $localData['password'] = Hash::make($validatedData['password']);

                if (isset($validatedData['taxPassword'])) {
                    $localData['taxPassword'] = Hash::make($validatedData['taxPassword']);
                }

                // 5. Guardar en la base de datos local
                Person::create($localData);

                return redirect()->route('people.index')->with('success', 'Persona creada y sincronizada correctamente');
            } else {
                // 6. Manejar el error de la API
                Log::error('Error al crear persona en FiscalAPI', [
                    'response' => $apiResponse->getJson(),
                    'data' => $apiData
                ]);

                return back()->withErrors(['fiscalapi' => 'No se pudo crear la persona en el servicio externo. Error: ' . ($apiResponse->getJson()['message'] ?? 'Error desconocido')]);
            }
        } catch (Exception $e) {
            Log::error('Excepción al crear persona', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['fiscalapi' => 'Error interno del sistema: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Person $person)
    {
        $person->load([
            'satTaxRegime',
            'satCfdiUse'
        ]);

        return view('components.people.show', compact('person'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Person $person)
    {
        $satTaxRegimes = SatTaxRegimeCode::all();
        $satCfdiUses = SatCfdiUseCode::all();

        return view('components.people.edit', compact(
            'person',
            'satTaxRegimes',
            'satCfdiUses'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonRequest $request, Person $person)
    {
        try {
            // Verificar que la persona tenga fiscalapiId
            if (!$person->fiscalapiId) {
                return back()->withErrors(['fiscalapi' => 'Esta persona no está sincronizada con el servicio externo.']);
            }

            $validatedData = $request->validated();

            // 1. Preparar datos para FiscalAPI (sin hashear contraseñas)
            $apiData = $this->prepareApiData($validatedData);
            $apiData['id'] = $person->fiscalapiId;

            // 2. Llamar a la API de FiscalAPI
            $apiResponse = $this->fiscalApi->getPersonService()->update($apiData);

            // 3. Verificar éxito y proceder
            $responseData = $apiResponse->getJson();
            if ($responseData['succeeded']) {
                // 4. Preparar datos para la BD local (con contraseñas hasheadas si se proporcionan)
                $localData = $validatedData;

                // Solo actualizar password si se proporciona uno nuevo
                if (isset($validatedData['password']) && !empty($validatedData['password'])) {
                    $localData['password'] = Hash::make($validatedData['password']);
                } else {
                    unset($localData['password']); // Remover el campo si está vacío
                }

                // Solo actualizar taxPassword si se proporciona uno nuevo
                if (isset($validatedData['taxPassword']) && !empty($validatedData['taxPassword'])) {
                    $localData['taxPassword'] = Hash::make($validatedData['taxPassword']);
                } else {
                    unset($localData['taxPassword']); // Remover el campo si está vacío
                }

                // 5. Actualizar en la base de datos local
                $person->update($localData);

                return redirect()->route('people.index')->with('success', 'Persona actualizada y sincronizada correctamente');
            } else {
                // 6. Manejar el error de la API
                Log::error('Error al actualizar persona en FiscalAPI', [
                    'response' => $apiResponse->getJson(),
                    'data' => $apiData,
                    'person_id' => $person->id
                ]);

                return back()->withErrors(['fiscalapi' => 'No se pudo actualizar la persona en el servicio externo. Error: ' . ($apiResponse->getJson()['message'] ?? 'Error desconocido')]);
            }
        } catch (Exception $e) {
            Log::error('Excepción al actualizar persona', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'person_id' => $person->id
            ]);

            return back()->withErrors(['fiscalapi' => 'Error interno del sistema: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        try {
            // Verificar que la persona tenga fiscalapiId
            if (!$person->fiscalapiId) {
                return back()->withErrors(['fiscalapi' => 'Esta persona no está sincronizada con el servicio externo.']);
            }

            // 1. Llamar a la API de FiscalAPI para eliminar
            $apiResponse = $this->fiscalApi->getPersonService()->delete($person->fiscalapiId);

            // 2. Verificar éxito y proceder
            $responseData = $apiResponse->getJson();
            if ($responseData['succeeded']) {
                // 3. Eliminar de la base de datos local
                $person->delete();

                return redirect()->route('people.index')->with('success', 'Persona eliminada y sincronizada correctamente');
            } else {
                // 4. Manejar el error de la API
                Log::error('Error al eliminar persona en FiscalAPI', [
                    'response' => $apiResponse->getJson(),
                    'person_id' => $person->id,
                    'fiscalapi_id' => $person->fiscalapiId
                ]);

                return back()->withErrors(['fiscalapi' => 'No se pudo eliminar la persona en el servicio externo. Error: ' . ($apiResponse->getJson()['message'] ?? 'Error desconocido')]);
            }
        } catch (Exception $e) {
            Log::error('Excepción al eliminar persona', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'person_id' => $person->id
            ]);

            return back()->withErrors(['fiscalapi' => 'Error interno del sistema: ' . $e->getMessage()]);
        }
    }

    /**
     * Preparar datos para la API de FiscalAPI
     */
    private function prepareApiData(array $data): array
    {
        // Mapear campos locales a los esperados por FiscalAPI
        $apiData = [
            'legalName' => $data['legalName'] ?? null,
            'email' => $data['email'] ?? null,
            'password' => $data['password'] ?? null, // Sin hashear para la API
            'capitalRegime' => $data['capitalRegime'] ?? null,
            'satTaxRegimeId' => $data['satTaxRegimeId'] ?? null,
            'satCfdiUseId' => $data['satCfdiUseId'] ?? null,
            'tin' => $data['tin'] ?? null,
            'zipCode' => $data['zipCode'] ?? null,
            'taxPassword' => $data['taxPassword'] ?? null, // Sin hashear para la API
        ];

        // Remover campos null para evitar enviar valores vacíos
        return array_filter($apiData, function ($value) {
            return $value !== null;
        });
    }
}
