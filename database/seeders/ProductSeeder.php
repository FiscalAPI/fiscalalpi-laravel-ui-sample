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
        $products = [
            [
                'description' => 'Laptop Dell Inspiron 15 3000',
                'unitPrice' => 15999.99,
                'fiscalapiId' => 'PROD001',
                'sat_unit_measurement_id' => 'H87', // Pieza
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '44121801',
            ],
            [
                'description' => 'Mouse inalámbrico Logitech',
                'unitPrice' => 299.99,
                'fiscalapiId' => 'PROD002',
                'sat_unit_measurement_id' => 'H87', // Pieza
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '50202306',
            ],
            [
                'description' => 'Papel bond tamaño carta',
                'unitPrice' => 85.50,
                'fiscalapiId' => 'PROD003',
                'sat_unit_measurement_id' => 'XPK', // Paquete
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '43211503',
            ],
            [
                'description' => 'Servicio de desarrollo de software personalizado',
                'unitPrice' => 2500.00,
                'fiscalapiId' => 'SERV001',
                'sat_unit_measurement_id' => 'E48', // Unidad de Servicio
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '81112200',
            ],
            [
                'description' => 'Tinta para impresora HP negra',
                'unitPrice' => 450.00,
                'fiscalapiId' => 'PROD004',
                'sat_unit_measurement_id' => 'H87', // Pieza
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '43191501',
            ],
            [
                'description' => 'Escritorio ejecutivo de madera',
                'unitPrice' => 3500.00,
                'fiscalapiId' => 'PROD005',
                'sat_unit_measurement_id' => 'H87', // Pieza
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '42142100',
            ],
            [
                'description' => 'Consultoría en sistemas de información',
                'unitPrice' => 1800.00,
                'fiscalapiId' => 'SERV002',
                'sat_unit_measurement_id' => 'HUR', // Hora
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '46181504',
            ],
            [
                'description' => 'Agua embotellada 1.5 litros',
                'unitPrice' => 15.00,
                'fiscalapiId' => 'PROD006',
                'sat_unit_measurement_id' => 'H87', // Pieza
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '50192200',
            ],
            [
                'description' => 'Servicio de limpieza mensual',
                'unitPrice' => 2500.00,
                'fiscalapiId' => 'SERV003',
                'sat_unit_measurement_id' => 'MON', // Mes
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '72141600',
            ],
            [
                'description' => 'Kit de herramientas profesionales',
                'unitPrice' => 1200.00,
                'fiscalapiId' => 'PROD007',
                'sat_unit_measurement_id' => 'KT', // Kit
                'sat_tax_object_id' => '02',
                'sat_product_code_id' => '25101500',
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
