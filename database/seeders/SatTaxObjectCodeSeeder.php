<?php

namespace Database\Seeders;

use App\Models\SatTaxObjectCode;
use Illuminate\Database\Seeder;

class SatTaxObjectCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codes = [
            ['code' => '01', 'description' => 'No objeto de impuesto'],
            ['code' => '02', 'description' => 'Sí objeto de impuesto'],
            ['code' => '03', 'description' => 'Sí objeto del impuesto y no obligado al desglose'],
            ['code' => '04', 'description' => 'Sí objeto del impuesto y no causa impuesto'],
        ];

        foreach ($codes as $code) {
            SatTaxObjectCode::create($code);
        }
    }
}
