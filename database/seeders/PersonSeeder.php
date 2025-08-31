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
            // Personas Físicas - Régimen Simplificado de Confianza (626) - G03 Gastos en general
            [
                'legalName' => 'XOCHILT CASAS CHAVEZ',
                'email' => 'xochilt.casas@ejemplo.com',
                'password' => Hash::make('Admin12345!'),
                'tin' => 'CACX7605101P8',
                'zipCode' => '36257',
                'satTaxRegimeId' => '626',
                'satCfdiUseId' => 'G03',
                'taxPassword' => '12345678a',
            ],
            [
                'legalName' => 'RODRIGO KITIA CASTRO',
                'email' => 'rodrigo.kitia@ejemplo.com',
                'password' => Hash::make('Admin12345!'),
                'tin' => 'KICR630120NX3',
                'zipCode' => '36246',
                'satTaxRegimeId' => '626',
                'satCfdiUseId' => 'G03',
                'taxPassword' => '12345678a',
            ],

            // Personas Morales - General de Ley Personas Morales (601) - G03 Gastos en general
            [
                'legalName' => 'INDISTRIA ILUMINADORA DE ALMACENES',
                'email' => 'contacto@iia.com',
                'password' => Hash::make('Admin12345!'),
                'tin' => 'IIA040805DZ4',
                'zipCode' => '62661',
                'satTaxRegimeId' => '601',
                'satCfdiUseId' => 'G03',
                'taxPassword' => '12345678a',
            ],
            [
                'legalName' => 'INNOVACION VALOR Y DESARROLLO',
                'email' => 'contacto@ivd.com',
                'password' => Hash::make('Admin12345!'),
                'tin' => 'IVD920810GU2',
                'zipCode' => '63901',
                'satTaxRegimeId' => '601',
                'satCfdiUseId' => 'G03',
                'taxPassword' => '12345678a',
            ]
        ];

        foreach ($people as $person) {
            Person::create($person);
        }
    }
}
