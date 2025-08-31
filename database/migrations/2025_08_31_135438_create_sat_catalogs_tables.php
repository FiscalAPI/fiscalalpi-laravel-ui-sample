<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration creates all the necessary SAT catalog tables.
     */
    public function up(): void
    {
        Schema::create('sat_unit_measurement_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('sat_tax_object_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('sat_product_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('sat_tax_regime_codes', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('description');
        });

        Schema::create('sat_cfdi_use_codes', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * This will drop all the SAT catalog tables in the reverse order of creation.
     */
    public function down(): void
    {
        Schema::dropIfExists('sat_cfdi_use_codes');
        Schema::dropIfExists('sat_tax_regime_codes');
        Schema::dropIfExists('sat_product_codes');
        Schema::dropIfExists('sat_tax_object_codes');
        Schema::dropIfExists('sat_unit_measurement_codes');
    }
};
