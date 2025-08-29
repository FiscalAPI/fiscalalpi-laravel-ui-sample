<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $table = 'products';
    
    protected $fillable = [
        'description',
        'unitPrice',
        'fiscalapiId',
        'sat_unit_measurement_id',
        'sat_tax_object_id',
        'sat_product_code_id',
    ];
    
    protected $casts = [
        'unitPrice' => 'decimal:6',
    ];
    
    /**
     * Get the SAT unit measurement code (expandible relationship).
     */
    public function satUnitMeasurement()
    {
        return $this->belongsTo(SatUnitMeasurementCode::class, 'sat_unit_measurement_id', 'code');
    }
    
    /**
     * Get the SAT tax object code (expandible relationship).
     */
    public function satTaxObject()
    {
        return $this->belongsTo(SatTaxObjectCode::class, 'sat_tax_object_id', 'code');
    }
    
    /**
     * Get the SAT product code (expandible relationship).
     */
    public function satProductCode()
    {
        return $this->belongsTo(SatProductCode::class, 'sat_product_code_id', 'code');
    }
}
