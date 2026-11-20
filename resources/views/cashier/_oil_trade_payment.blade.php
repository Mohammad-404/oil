<div class="fw-bold small">{{ $trade->counterparty_name ?: '—' }}</div>
@if($trade->payment_status === 'debt')
    <span class="badge bg-danger">{{ $trade->type === 'buy' ? 'دين علينا' : 'دين إلنا' }}</span>
    <form action="{{ route('cashier.oil-trade.settle', $trade) }}" method="POST" class="mt-1"
          onsubmit="return confirm('تأكيد {{ $trade->type === 'buy' ? 'دفع' : 'قبض' }} كامل المبلغ {{ number_format($trade->total_amount, 3) }} د.أ؟')">
        @csrf
        <button type="submit" class="btn btn-sm btn-outline-success">
            {{ $trade->type === 'buy' ? 'تسديد للمورّد' : 'قبض من الزبون' }}
        </button>
    </form>
@else
    <span class="badge bg-success">{{ $trade->type === 'buy' ? 'مدفوع' : 'مقبوض' }}</span>
    @if($trade->debt_paid_at)
        <div class="small text-muted">تسديد: {{ $trade->debt_paid_at->format('Y/m/d') }}</div>
    @endif
@endif
