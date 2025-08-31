<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the products table and establishes foreign key relationships
     * with the SAT catalog tables.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('unitPrice', 18, 6);
            $table->string('fiscalapiId')->nullable();

            // SAT Fields
            $table->string('sat_product_code_id')->default('01010101'); // default value for the product code
            $table->string('sat_unit_measurement_id')->default('H87'); // default value for the unit measurement
            $table->string('sat_tax_object_id')->default('02'); // default value for the tax object

            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('sat_product_code_id')->references('code')->on('sat_product_codes');
            $table->foreign('sat_unit_measurement_id')->references('code')->on('sat_unit_measurement_codes');
            $table->foreign('sat_tax_object_id')->references('code')->on('sat_tax_object_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the products table.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
