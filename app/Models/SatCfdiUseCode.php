<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatCfdiUseCode extends Model
{
    /** @use HasFactory<\Database\Factories\SatCfdiUseCodeFactory> */
    use HasFactory;

    protected $table = 'sat_cfdi_use_codes';

    protected $fillable = [
        'code',
        'description',
    ];

    public $timestamps = false;

    /**
     * Get the people that use this CFDI use code.
     */
    public function people()
    {
        return $this->hasMany(Person::class, 'satCfdiUseId', 'code');
    }
}
