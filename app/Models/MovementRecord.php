<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementRecord extends Model
{
    protected $primaryKey = 'movement_record_id';

    protected $fillable = [
        'property_card_id', 'movement_date', 'par', 
        'qty', 'movement_qty', 'office_officer',
        'balance', 'amount', 'remarks'
    ];

public function propertyCard()
{
    return $this->hasOne(PropertyCard::class, 'movement_record_id', 'movement_record_id');
}

}
