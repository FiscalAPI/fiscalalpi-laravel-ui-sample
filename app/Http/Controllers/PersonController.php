<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\SatTaxRegimeCode;
use App\Models\SatCfdiUseCode;
use App\Http\Requests\StorePersonRequest;
use App\Http\Requests\UpdatePersonRequest;
use Illuminate\Support\Facades\Hash;

class PersonController extends Controller
{
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
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        if (isset($data['taxPassword'])) {
            $data['taxPassword'] = Hash::make($data['taxPassword']);
        }

        $person = Person::create($data);
        return redirect()->route('people.index')->with('success', 'Persona creada correctamente');
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
        $data = $request->validated();

        // Solo actualizar password si se proporciona uno nuevo
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // Remover el campo si está vacío
        }

        // Solo actualizar taxPassword si se proporciona uno nuevo
        if (isset($data['taxPassword']) && !empty($data['taxPassword'])) {
            $data['taxPassword'] = Hash::make($data['taxPassword']);
        } else {
            unset($data['taxPassword']); // Remover el campo si está vacío
        }

        $person->update($data);
        return redirect()->route('people.index')->with('success', 'Persona actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Person $person)
    {
        $person->delete();
        return redirect()->route('people.index')->with('success', 'Persona eliminada correctamente');
    }
}
