<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkedEquipmentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fund_id',
        'original_property_no',
        'year',
        'reference_mmdd',
        'new_property_no',
        
        'location',
       
    ];

  
    /**
     * Get the fund that owns the linked equipment item.
     */
    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }

    /**
     * Get the location that owns the linked equipment item.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}