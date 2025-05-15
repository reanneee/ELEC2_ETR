<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundCluster extends Model
{
    protected $fillable = ['name'];

public function entities()
{
    return $this->hasMany(Entity::class, 'fund_cluster_id');
}

}
