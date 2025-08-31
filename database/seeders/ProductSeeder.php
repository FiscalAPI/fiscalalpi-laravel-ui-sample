<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SatUnitMeasurementCode;
use App\Models\SatTaxObjectCode;
use App\Models\SatProductCode;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define services and products for a software factory with realistic MXN prices
        $products = [
            [
                'description' => 'Consultoría y Desarrollo por Hora',
                'unitPrice' => 1250.00, // Tarifa por hora para un desarrollador
                'fiscalapiId' => 'SERV-DEV-HOUR',
                'sat_unit_measurement_id' => 'HUR', // Hora
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '81111500', // No existe en el catálogo
            ],
            [
                'description' => 'Implementación E-commerce',
                'unitPrice' => 95000.00, // Paquete estándar de e-commerce
                'fiscalapiId' => 'PROJ-ECOM-STD',
                'sat_unit_measurement_id' => 'E48', // Unidad de Servicio
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '81111500', // No existe en el catálogo
            ],
            [
                'description' => 'Diseño UI/UX para App',
                'unitPrice' => 65000.00, // Proyecto de diseño de interfaz
                'fiscalapiId' => 'SERV-UIUX-PROJ',
                'sat_unit_measurement_id' => 'E48', // Unidad de Servicio
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '81111500', // No existe en el catálogo
            ],

        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
