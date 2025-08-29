<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $products = [
            [
                'name' => 'Producto 1',
                'description' => 'Descripción del producto 1',
                'unitPrice' => 100,
                'fiscalapiId' => 1,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
