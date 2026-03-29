<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'owner_id',
        'carrier_name',
        'olive_weight_kg',
        'oil_weight_kg',
        'oil_weight_gram',
        'floor_number',
        'fee_per_kg',
        'driver_fee',
        'press_fee',
        'total_fee',
        'status',
        'notes',
        'received_at',
    ];

    protected $casts = [
        'received_at'    => 'datetime',
        'olive_weight_kg'=> 'decimal:2',
        'oil_weight_kg'  => 'decimal:2',
        'oil_weight_gram'=> 'decimal:2',
        'fee_per_kg'     => 'decimal:2',
        'driver_fee'     => 'decimal:2',
        'press_fee'      => 'decimal:2',
        'total_fee'      => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($r) {
            if (empty($r->receipt_number)) {
                $r->receipt_number = self::generateReceiptNumber();
            }
            self::calcFees($r);
        });

        static::updating(function ($r) {
            self::calcFees($r);
        });
    }

    /**
     * الحساب الأردني القياسي لأجرة المعصرة:
     *   أجرة العصر  = كمية الزيتون (كيلو) × سعر العصر / كيلو
     *   أجرة السائق = قيمة يدوية
     *   الإجمالي    = أجرة العصر + أجرة السائق
     */
    private static function calcFees(self $r): void
    {
        $olive      = (float) ($r->olive_weight_kg ?? 0);
        $feePerKg   = (float) ($r->fee_per_kg  ?? 0);
        $driverFee  = (float) ($r->driver_fee   ?? 0);

        $r->press_fee  = round($olive * $feePerKg, 2);
        $r->total_fee  = round($r->press_fee + $driverFee, 2);
    }

    public static function generateReceiptNumber(): string
    {
        $year = date('Y');
        $last = self::whereYear('created_at', $year)->count() + 1;
        return 'OL-' . $year . '-' . str_pad($last, 4, '0', STR_PAD_LEFT);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    // ── نسبة الاستخلاص ──
    public function getExtractionRateAttribute(): float
    {
        if (!$this->olive_weight_kg || !$this->oil_weight_kg) return 0;
        return round(($this->oil_weight_kg / $this->olive_weight_kg) * 100, 1);
    }

    // ── الزيت الكامل بالكيلو + غرام ──
    public function getOilTotalLabelAttribute(): string
    {
        if (!$this->oil_weight_kg) return '—';
        $label = number_format($this->oil_weight_kg, 0) . ' كيلو';
        if ($this->oil_weight_gram > 0) $label .= ' + ' . (int)$this->oil_weight_gram . ' غم';
        return $label;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'في الانتظار',
            'processing' => 'قيد المعالجة',
            'completed'  => 'مكتمل',
            default      => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'warning',
            'processing' => 'info',
            'completed'  => 'success',
            default      => 'secondary',
        };
    }
}
