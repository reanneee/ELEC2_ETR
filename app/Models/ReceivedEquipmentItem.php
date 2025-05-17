<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivedEquipmentItem extends Model
{
    protected $table = 'received_equipment_item';
    protected $primaryKey = 'item_id';
    
    protected $fillable = [
        'description_id',
        'serial_no',
        'property_no',
        'date_acquired', // You need to add this to your migration
        'amount'         // You need to add this to your migration
    ];

    /**
     * Get the description this item belongs to
     */
    public function description()
    {
        return $this->belongsTo(ReceivedEquipmentDescription::class, 'description_id', 'description_id');
    }
}