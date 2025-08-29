<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove old name field and add new fields
            $table->dropColumn('name');
            
            // Add new SAT fields
            $table->string('sat_unit_measurement_id')->default('H87')->after('unitPrice');
            $table->string('sat_tax_object_id')->default('02')->after('sat_unit_measurement_id');
            $table->string('sat_product_code_id')->default('01010101')->after('sat_tax_object_id');
            
            // Add foreign key constraints
            $table->foreign('sat_unit_measurement_id')->references('code')->on('sat_unit_measurement_codes');
            $table->foreign('sat_tax_object_id')->references('code')->on('sat_tax_object_codes');
            $table->foreign('sat_product_code_id')->references('code')->on('sat_product_codes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['sat_unit_measurement_id']);
            $table->dropForeign(['sat_tax_object_id']);
            $table->dropForeign(['sat_product_code_id']);
            
            // Drop SAT fields
            $table->dropColumn(['sat_unit_measurement_id', 'sat_tax_object_id', 'sat_product_code_id']);
            
            // Add back name field
            $table->string('name')->after('id');
        });
    }
};
