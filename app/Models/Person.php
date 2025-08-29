<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    /** @use HasFactory<\Database\Factories\PersonFactory> */
    use HasFactory;

    protected $table = 'people';

    protected $fillable = [
        'fiscalapiId',
        'legalName',
        'email',
        'password',
        'capitalRegime',
        'satTaxRegimeId',
        'satCfdiUseId',
        'tin',
        'zipCode',
        'taxPassword',
    ];

    protected $hidden = [
        'password',
        'taxPassword',
    ];

    /**
     * Get the SAT tax regime code (expandible relationship).
     */
    public function satTaxRegime()
    {
        return $this->belongsTo(SatTaxRegimeCode::class, 'satTaxRegimeId', 'code');
    }

    /**
     * Get the SAT CFDI use code (expandible relationship).
     */
    public function satCfdiUse()
    {
        return $this->belongsTo(SatCfdiUseCode::class, 'satCfdiUseId', 'code');
    }
}
