<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryCountForm extends Model
{

    protected $primaryKey = 'inventory_id';

    protected $table = 'inventory_count_form';

    protected $fillable = [
        'entity_id',
        'inventory_date',
        'article_item',
        'description',
        'old_property_no',
        'new_property_no',
        'unit',
        'unit_value',
        'qty_card',
        'qty_physical',
        'location',
        'condition',
        'remarks',
        'received_by_name',
        'prepared_by_name',
        'reviewed_by_name',
        'prepared_by_position',
        'reviewed_by_position',
    ];

    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }
}
