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
            ['code' => '81111810', 'description' => 'Servicios de codificación de software'],
            ['code' => '81111500', 'description' => 'Ingeniería de software o hardware'],
            ['code' => '43231500', 'description' => 'Software funcional específico de la empresa'],
            ['code' => '43232408', 'description' => 'Software de desarrollo de plataformas web'],
            ['code' => '81112200', 'description' => 'Mantenimiento y soporte de software'],
            ['code' => '81112501', 'description' => 'Servicio de licencias del software del computador'],
            ['code' => '81112502', 'description' => 'Servicio de arriendo o leasing de software de computadores'],
            ['code' => '43232403', 'description' => 'Software de integración de aplicaciones de empresas'],
            ['code' => '43232303', 'description' => 'Software de manejo de relaciones con el cliente crm'],
            ['code' => '43231602', 'description' => 'Software de planeación de recursos del negocio erp'],
        ];

        foreach ($codes as $code) {
            SatProductCode::create($code);
        }
    }
}
