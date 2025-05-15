<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
class ReceivedEquipment extends Model
{use HasFactory;

    protected $table = 'received_equipment';

    protected $primaryKey = 'equipment_id';

    protected $fillable = [
        'entity_id',
        'quantity',
        'unit',
        'description',
        'serial_no',
        'property_no',
        'date_acquired',
        'amount',
        'received_by_name',
        'received_by_designation',
        'verified_by_name',
        'verified_by_designation',
        'receipt_date',
        'par_no',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($equipment) {
            $year = Carbon::now()->format('Y');
            $month = Carbon::now()->format('m');

            // Count how many have been created this year-month
            $serial = self::whereYear('created_at', $year)
                          ->whereMonth('created_at', $month)
                          ->count() + 1;

            $serialFormatted = str_pad($serial, 4, '0', STR_PAD_LEFT);
            $equipment->par_no = "{$year}-{$month}-{$serialFormatted}";
        });
    }
}
