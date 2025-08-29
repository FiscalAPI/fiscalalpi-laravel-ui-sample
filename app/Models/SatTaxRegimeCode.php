<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatTaxRegimeCode extends Model
{
    /** @use HasFactory<\Database\Factories\SatTaxRegimeCodeFactory> */
    use HasFactory;

    protected $table = 'sat_tax_regime_codes';
    
    protected $fillable = [
        'code',
        'description',
    ];
    
    public $timestamps = false;
    
    /**
     * Get the people that use this tax regime.
     */
    public function people()
    {
        return $this->hasMany(Person::class, 'satTaxRegimeId', 'code');
    }
}
