<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryCountForm extends Model
{
    use HasFactory;

    protected $table = 'inventory_count_form';
    protected $primaryKey = 'id';

    protected $fillable = [
        'entity_id',
        'inventory_date',
        'prepared_by_name',
        'prepared_by_position',
        'reviewed_by_name',
        'reviewed_by_position',
    ];

    protected $casts = [
        'inventory_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with entity
    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id', 'entity_id');
    }

    // Relationship with property cards (inventory items)
    public function propertyCards()
    {
        return $this->hasMany(PropertyCard::class, 'inventory_count_form_id');
    }
}