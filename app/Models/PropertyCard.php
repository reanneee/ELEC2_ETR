<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyCard extends Model
{
    use HasFactory;

    protected $table = 'property_cards';
    protected $primaryKey = 'property_card_id';

    protected $fillable = [
        'received_equipment_item_id',
        'qty_physical',
        'condition',
        'remarks',
        'issue_transfer_disposal',
        'received_by_name',
        'article',
        'locations_id',
    ];

    protected $casts = [
        'qty_physical' => 'integer',
        'locations_id' => 'integer',
        'received_equipment_item_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with received equipment item
    public function receivedEquipmentItem()
    {
        return $this->belongsTo(ReceivedEquipmentItem::class, 'received_equipment_item_id', 'item_id');
    }

    // Relationship with location
    public function location()
    {
        return $this->belongsTo(Location::class, 'locations_id');
    }
}