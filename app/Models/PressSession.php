<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PressSession extends Model
{
    use HasFactory;
    protected $table = 'press_sessions';

    protected $fillable = [
        'queue_number', 'session_date', 'owner_id',
        'carrier_name', 'shares',
        'olives_weight_kg', 'olives_weight_gram',
        'extracted_oil_kg', 'filtered_oil_kg',
        'fee_per_kg', 'extra_fee', 'total_fee',
        'status', 'payment_status', 'notes',
    ];

    protected $casts = [
        'session_date' => 'date',
        'olives_weight_kg' => 'decimal:3',
        'extracted_oil_kg' => 'decimal:3',
        'filtered_oil_kg'  => 'decimal:3',
        'fee_per_kg'  => 'decimal:4',
        'extra_fee'   => 'decimal:3',
        'total_fee'   => 'decimal:3',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    // Auto-calculate total fee before saving
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($session) {
            $session->total_fee = ($session->olives_weight_kg * $session->fee_per_kg) + $session->extra_fee;
        });
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $q = $filters['search'];
            $query->whereHas('owner', fn($r) => $r->where('name','like',"%$q%"))
                  ->orWhere('queue_number','like',"%$q%")
                  ->orWhere('carrier_name','like',"%$q%");
        }
        if (!empty($filters['from'])) $query->whereDate('session_date', '>=', $filters['from']);
        if (!empty($filters['to']))   $query->whereDate('session_date', '<=', $filters['to']);
        if (!empty($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['owner_id'])) $query->where('owner_id', $filters['owner_id']);
        return $query;
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'received'   => '📥 مستلم',
            'processing' => '⚙️ جارٍ العصر',
            'done'       => '✅ منتهي',
            default      => $this->status,
        };
    }
}
