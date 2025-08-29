<?php

namespace Database\Seeders;

use App\Models\SatUnitMeasurementCode;
use Illuminate\Database\Seeder;

class SatUnitMeasurementCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $codes = [
            ['code' => 'H87', 'description' => 'Pieza'],
            ['code' => 'EA', 'description' => 'Elemento'],
            ['code' => 'E48', 'description' => 'Unidad de Servicio'],
            ['code' => 'ACT', 'description' => 'Actividad'],
            ['code' => 'KGM', 'description' => 'Kilogramo'],
            ['code' => 'E51', 'description' => 'Trabajo'],
            ['code' => 'A9', 'description' => 'Tarifa'],
            ['code' => 'MTR', 'description' => 'Metro'],
            ['code' => 'AB', 'description' => 'Paquete a granel'],
            ['code' => 'BB', 'description' => 'Caja base'],
            ['code' => 'KT', 'description' => 'Kit'],
            ['code' => 'SET', 'description' => 'Conjunto'],
            ['code' => 'LTR', 'description' => 'Litro'],
            ['code' => 'XBX', 'description' => 'Caja'],
            ['code' => 'MON', 'description' => 'Mes'],
            ['code' => 'HUR', 'description' => 'Hora'],
            ['code' => 'MTK', 'description' => 'Metro cuadrado'],
            ['code' => '11', 'description' => 'Equipos'],
            ['code' => 'MGM', 'description' => 'Miligramo'],
            ['code' => 'XPK', 'description' => 'Paquete'],
            ['code' => 'XKI', 'description' => 'Kit (Conjunto de piezas)'],
            ['code' => 'AS', 'description' => 'Variedad'],
            ['code' => 'GRM', 'description' => 'Gramo'],
            ['code' => 'PR', 'description' => 'Par'],
            ['code' => 'DPC', 'description' => 'Docenas de piezas'],
            ['code' => 'xun', 'description' => 'Unidad'],
            ['code' => 'DAY', 'description' => 'Día'],
            ['code' => 'XLT', 'description' => 'Lote'],
            ['code' => '10', 'description' => 'Grupos'],
            ['code' => 'MLT', 'description' => 'Mililitro'],
            ['code' => 'E54', 'description' => 'Viaje'],
        ];

        foreach ($codes as $code) {
            SatUnitMeasurementCode::create($code);
        }
    }
}
