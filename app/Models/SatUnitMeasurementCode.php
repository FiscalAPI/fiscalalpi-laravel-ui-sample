<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SatUnitMeasurementCode extends Model
{
    protected $table = 'sat_unit_measurement_codes';

    protected $fillable = [
        'code',
        'description',
    ];

    /**
     * Get the primary key for the model.
     */
    public function getKeyName()
    {
        return 'code';
    }

    /**
     * Indicates if the model's ID is auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     */
    protected $keyType = 'string';

    /**
     * Get products that use this unit measurement code.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'sat_unit_measurement_id', 'code');
    }
}
