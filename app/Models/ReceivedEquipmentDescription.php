<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivedEquipmentDescription extends Model
{
    protected $table = 'received_equipment_description';
    protected $primaryKey = 'description_id';
    
    protected $fillable = [
        'equipment_id',
        'description',
        'quantity',
        'unit' // You might need to add this to your migration
    ];

    /**
     * Get the parent equipment
     */
    public function equipment()
    {
        return $this->belongsTo(ReceivedEquipment::class, 'equipment_id', 'equipment_id');
    }

    /**
     * Get the items for this description
     */
    public function items()
    {
        return $this->hasMany(ReceivedEquipmentItem::class, 'description_id', 'description_id');
    }
}