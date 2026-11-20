<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'street',
        'phone',
        'floor_number',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($owner) {
            if (empty($owner->floor_number)) {
                $owner->floor_number = (self::max('floor_number') ?? 0) + 1;
            }
        });
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function getTotalOliveWeightAttribute(): float
    {
        if ($this->relationLoaded('receipts')) {
            return (float) $this->receipts->sum('olive_weight_kg');
        }
        return (float) $this->receipts()->sum('olive_weight_kg');
    }

    public function getTotalFeesAttribute(): float
    {
        if ($this->relationLoaded('receipts')) {
            return (float) $this->receipts->sum('total_fee');
        }
        return (float) $this->receipts()->sum('total_fee');
    }
}
