<?php

namespace Database\Seeders;

use App\Models\SatProductCode;
use Illuminate\Database\Seeder;

class SatProductCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codes = [
            ['code' => '01010101', 'description' => 'No existe en el catálogo'],
            ['code' => '50202306', 'description' => 'Equipo de cómputo'],
            ['code' => '43211503', 'description' => 'Papel para impresora'],
            ['code' => '44121710', 'description' => 'Computadoras de escritorio'],
            ['code' => '44121801', 'description' => 'Laptops'],
            ['code' => '43191501', 'description' => 'Tinta para impresora'],
            ['code' => '46181504', 'description' => 'Servicios de consultoría'],
            ['code' => '81112200', 'description' => 'Servicios de desarrollo de software'],
            ['code' => '85101800', 'description' => 'Servicios de capacitación'],
            ['code' => '93141500', 'description' => 'Servicios de soporte técnico'],
            ['code' => '25101500', 'description' => 'Artículos de oficina'],
            ['code' => '14111506', 'description' => 'Café'],
            ['code' => '50192200', 'description' => 'Agua embotellada'],
            ['code' => '42142100', 'description' => 'Mobiliario de oficina'],
            ['code' => '39112700', 'description' => 'Instrumentos de medición'],
            ['code' => '72141600', 'description' => 'Servicios de limpieza'],
            ['code' => '78101800', 'description' => 'Servicios de transporte'],
            ['code' => '80141600', 'description' => 'Servicios de publicidad'],
            ['code' => '82121500', 'description' => 'Servicios de contabilidad'],
            ['code' => '86101500', 'description' => 'Servicios legales'],
        ];

        foreach ($codes as $code) {
            SatProductCode::create($code);
        }
    }
}
