<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Person;
use Illuminate\Support\Facades\Hash;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $people = [
            [
                'legalName' => 'Juan Pérez González',
                'email' => 'juan.perez@ejemplo.com',
                'password' => Hash::make('Admin12345!'),
                'tin' => 'PERG800101ABC',
                'zipCode' => '06500',
                'satTaxRegimeId' => '612',
                'satCfdiUseId' => 'G03',
            ],
            [
                'legalName' => 'Empresa ABC S.A. de C.V.',
                'email' => 'contacto@empresaabc.com',
                'password' => Hash::make('Admin12345!'),
                'capitalRegime' => 'S.A. de C.V.',
                'tin' => 'EAB850101ABC',
                'zipCode' => '11560',
                'satTaxRegimeId' => '601',
                'satCfdiUseId' => 'G01',
                'fiscalapiId' => 'FISC001',
            ],
            [
                'legalName' => 'María García López',
                'email' => 'maria.garcia@ejemplo.com',
                'password' => Hash::make('Admin12345!'),
                'tin' => 'GALM750515DEF',
                'zipCode' => '44100',
                'satTaxRegimeId' => '612',
                'satCfdiUseId' => 'G03',
            ],
        ];

        foreach ($people as $person) {
            Person::create($person);
        }
    }
}
