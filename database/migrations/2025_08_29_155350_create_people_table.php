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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('fiscalapiId')->nullable();
            $table->string('legalName');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('capitalRegime')->nullable();
            $table->string('satTaxRegimeId')->nullable();
            $table->string('satCfdiUseId')->nullable();
            $table->string('tin')->nullable();
            $table->string('zipCode')->nullable();
            $table->string('taxPassword')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('satTaxRegimeId')->references('code')->on('sat_tax_regime_codes')->onDelete('set null');
            $table->foreign('satCfdiUseId')->references('code')->on('sat_cfdi_use_codes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
