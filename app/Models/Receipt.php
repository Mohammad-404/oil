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
        'driver_fee',       // أجرة الناقل — داخلية للمعصرة فقط، لا تدخل في الفاتورة
        'press_fee',
        'total_fee',
        'payment_method',   // cash | oil_return | can
        'oil_return_kg',    // رديات الزيت
        'oil_return_gram',
        'can_16_count',     // عدد تنك 16 كيلو
        'can_8_count',      // عدد تنك 8 كيلو
        'can_4_count',      // عدد تنك 4 كيلو
        'can_total',        // مجموع قيمة التنك
        'discount_percent', // محفوظ للتوافق مع الإيصالات القديمة
        'discount_amount',  // مبلغ الخصم اليدوي من إجمالي العصر والتنك
        'cans_in_return',   // هل التنك مُسوَّاة ضمن رديات الزيت؟
        'oil_return_divisor', // N₁: مقسوم رديات الزيت بدون تنك
        'can_return_divisor', // N₂: مقسوم رديات الزيت مع التنك
        'status',
        'notes',
        'received_at',
    ];

    protected $attributes = [
        'discount_percent' => 0,
        'discount_amount' => 0,
        'oil_return_divisor' => 5.5,
        'can_return_divisor' => 5.5,
    ];

    protected $casts = [
        'received_at'     => 'datetime',
        'olive_weight_kg' => 'decimal:2',
        'oil_weight_kg'   => 'decimal:2',
        'oil_weight_gram' => 'decimal:2',
        'fee_per_kg'      => 'decimal:3',
        'driver_fee'      => 'decimal:2',
        'press_fee'       => 'decimal:3',
        'total_fee'       => 'decimal:3',
        'oil_return_kg'   => 'decimal:3',
        'oil_return_gram' => 'decimal:2',
        'can_total'       => 'decimal:3',
        'discount_percent'=> 'decimal:2',
        'discount_amount' => 'decimal:3',
        'discounted_can_total' => 'decimal:3',
        'can_16_count'    => 'integer',
        'can_8_count'     => 'integer',
        'can_4_count'     => 'integer',
        'cans_in_return'    => 'boolean',
        'oil_return_divisor'=> 'decimal:3',
        'can_return_divisor'=> 'decimal:3',
    ];

    // أسعار التنك الافتراضية (تُقرأ من الإعدادات)
    const CAN_16_PRICE_DEFAULT = 2.000;
    const CAN_8_PRICE_DEFAULT  = 1.500;
    const CAN_4_PRICE_DEFAULT  = 0.750;

    private static ?array $canPricesCache = null;

    public static function getCanPrices(): array
    {
        if (self::$canPricesCache === null) {
            $s = \DB::table('settings')
                ->whereIn('key', ['can_16_price', 'can_8_price', 'can_4_price'])
                ->pluck('value', 'key');
            self::$canPricesCache = [
                'can_16' => (float)($s['can_16_price'] ?? self::CAN_16_PRICE_DEFAULT),
                'can_8'  => (float)($s['can_8_price']  ?? self::CAN_8_PRICE_DEFAULT),
                'can_4'  => (float)($s['can_4_price']  ?? self::CAN_4_PRICE_DEFAULT),
            ];
        }
        return self::$canPricesCache;
    }

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
     * حساب أجرة العصر:
     *   press_fee = كمية الزيت المعصور × سعر العصر / كيلو
     *   discount_amount is subtracted from press_fee + can_total.
     *   Driver fees are internal and never added to the customer's bill.
     *   can_total = (تنك16 × 2) + (تنك8 × 1.5) + (تنك4 × 0.75)
     */
    private static function calcFees(self $r): void
    {
        $oil      = (float) ($r->oil_weight_kg   ?? 0)
                  + (float) ($r->oil_weight_gram ?? 0) / 1000;
        $feePerKg = (float) ($r->fee_per_kg ?? 0);

        $r->press_fee = round($oil * $feePerKg, 3);

        // مجموع قيمة التنك (بالأسعار من الإعدادات)
        $prices = self::getCanPrices();
        $can16  = (int) ($r->can_16_count ?? 0);
        $can8   = (int) ($r->can_8_count  ?? 0);
        $can4   = (int) ($r->can_4_count  ?? 0);
        $r->can_total = round(
            $can16 * $prices['can_16'] +
            $can8  * $prices['can_8']  +
            $can4  * $prices['can_4'],
            3
        );

        $gross = round($r->press_fee + $r->can_total, 3);
        $r->discount_amount = min($gross, max(0, round((float) ($r->discount_amount ?? 0), 3)));
        $r->total_fee = round($gross - $r->discount_amount, 3);
        // توزيع الخصم على العصر والتنك بنسبة قيمة كل منهما، كما كان في الحساب السابق.
        $r->discounted_can_total = $gross > 0
            ? min($r->total_fee, round($r->can_total * $r->total_fee / $gross, 3))
            : 0;
    }

    public function getDiscountedPressFeeAttribute(): float
    {
        return round((float) $this->total_fee - (float) $this->discounted_can_total, 3);
    }

    public static function generateReceiptNumber(): string
    {
        $year   = date('Y');
        $prefix = 'OL-' . $year . '-';
        $last   = self::where('receipt_number', 'like', $prefix . '%')
            ->selectRaw('MAX(CAST(SUBSTRING(receipt_number, ?) AS UNSIGNED)) as max_seq', [strlen($prefix) + 1])
            ->value('max_seq') ?? 0;
        return $prefix . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class)->withDefault([
            'name'         => '—',
            'phone'        => '',
            'floor_number' => null,
            'city'         => '',
            'street'       => '',
        ]);
    }

    public function getExtractionRateAttribute(): float
    {
        if (!$this->olive_weight_kg || !$this->oil_weight_kg) return 0;
        return round(($this->oil_weight_kg / $this->olive_weight_kg) * 100, 1);
    }

    // نسبة الزيتون إلى الزيت (كم كيلو زيتون لكل كيلو زيت)
    public function getOliveOilRatioAttribute(): ?float
    {
        if (!$this->oil_weight_kg || $this->oil_weight_kg <= 0) return null;
        return round((float)$this->olive_weight_kg / (float)$this->oil_weight_kg, 1);
    }

    public function getOilTotalLabelAttribute(): string
    {
        if (!$this->oil_weight_kg) return '—';
        $label = number_format($this->oil_weight_kg, 0) . ' كيلو';
        if ($this->oil_weight_gram > 0) $label .= ' + ' . (int)$this->oil_weight_gram . ' غم';
        return $label;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method ?? '') {
            'cash'       => 'نقدي',
            'oil_return' => 'رديات زيت',
            'can'        => 'بدل تنكة',
            default      => $this->payment_method ?? '—',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status ?? '') {
            'pending'    => 'في الانتظار',
            'processing' => 'قيد المعالجة',
            'completed'  => 'مكتمل',
            'debt'       => 'دَين',
            default      => $this->status ?? '—',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status ?? '') {
            'pending'    => 'warning',
            'processing' => 'info',
            'completed'  => 'success',
            'debt'       => 'danger',
            default      => 'secondary',
        };
    }
}
