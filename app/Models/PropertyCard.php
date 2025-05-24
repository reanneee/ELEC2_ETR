<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyCard extends Model
{
    protected $table = 'property_cards'; 
    protected $primaryKey = 'property_card_id';

    protected $fillable = ['entity_id', 'property_number', 'description'];

    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id', 'entity_id');
    }

public function movements()
{
    return $this->hasMany(MovementRecord::class, 'property_card_id', 'property_card_id');
}

public function getRouteKeyName()
{
    return 'property_card_id';
}




}
