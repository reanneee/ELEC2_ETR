<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{protected $primaryKey = 'entity_id';

    protected $fillable = [
        'entity_name',
        'branch_id',
        'fund_cluster_id',
    ];
    public function branch()
{
    return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
}

public function fundCluster()
{
    return $this->belongsTo(FundCluster::class, 'fund_cluster_id', 'id');
}

    public function receivedEquipments()
    {
        return $this->hasMany(ReceivedEquipment::class, 'entity_id');
    }

    public function inventoryCounts()
    {
        return $this->hasMany(InventoryCountForm::class, 'entity_id');
    }

    public function propertyCards()
    {
        return $this->hasMany(PropertyCard::class, 'entity_id');
    }
}
