<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'fund_id'];

    public function fund()
    {
        return $this->belongsTo(Fund::class);
    }
}
