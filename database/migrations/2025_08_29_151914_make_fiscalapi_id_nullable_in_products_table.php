<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added missing import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') === 'sqlite') {
            // For SQLite, we need to recreate the table
            Schema::create('products_new', function (Blueprint $table) {
                $table->id();
                $table->string('description');
                $table->decimal('unitPrice', 18, 6);
                $table->string('fiscalapiId')->nullable();
                $table->timestamps();
                $table->string('sat_unit_measurement_id')->default('H87');
                $table->string('sat_tax_object_id')->default('02');
                $table->string('sat_product_code_id')->default('01010101');

                // Add foreign key constraints
                $table->foreign('sat_unit_measurement_id')->references('code')->on('sat_unit_measurement_codes');
                $table->foreign('sat_tax_object_id')->references('code')->on('sat_tax_object_codes');
                $table->foreign('sat_product_code_id')->references('code')->on('sat_product_codes');
            });

            // Copy data from old table to new table
            DB::statement('INSERT INTO products_new SELECT id, description, unitPrice, fiscalapiId, created_at, updated_at, sat_unit_measurement_id, sat_tax_object_id, sat_product_code_id FROM products');

            // Drop old table and rename new table
            Schema::drop('products');
            Schema::rename('products_new', 'products');
        } else {
            // For other databases, use the standard approach
            Schema::table('products', function (Blueprint $table) {
                $table->string('fiscalapiId')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'sqlite') {
            // For SQLite, recreate the table with non-nullable fiscalapiId
            Schema::create('products_old', function (Blueprint $table) {
                $table->id();
                $table->string('description');
                $table->decimal('unitPrice', 18, 6);
                $table->string('fiscalapiId');
                $table->timestamps();
                $table->string('sat_unit_measurement_id')->default('H87');
                $table->string('sat_tax_object_id')->default('02');
                $table->string('sat_product_code_id')->default('01010101');

                // Add foreign key constraints
                $table->foreign('sat_unit_measurement_id')->references('code')->on('sat_unit_measurement_codes');
                $table->foreign('sat_tax_object_id')->references('code')->on('sat_tax_object_codes');
                $table->foreign('sat_product_code_id')->references('code')->on('sat_product_codes');
            });

            // Copy data back
            DB::statement('INSERT INTO products_old SELECT id, description, unitPrice, fiscalapiId, created_at, updated_at, sat_unit_measurement_id, sat_tax_object_id, sat_product_code_id FROM products');

            // Drop new table and rename old table
            Schema::drop('products');
            Schema::rename('products_old', 'products');
        } else {
            // For other databases, use the standard approach
            Schema::table('products', function (Blueprint $table) {
                $table->string('fiscalapiId')->nullable(false)->change();
            });
        }
    }
};
