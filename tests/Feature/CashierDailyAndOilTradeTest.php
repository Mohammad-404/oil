<?php

namespace Tests\Feature;

use App\Models\OilTrade;
use App\Models\Owner;
use App\Models\Receipt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CashierDailyAndOilTradeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_oil_trades_save_the_entered_total_and_keep_quantity_based_stock(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $stockBefore = OilTrade::totalOilStock();

        $this->actingAs($cashier)->post(route('cashier.oil-trade.store'), [
            'type' => 'buy', 'quantity_kg' => 8, 'total_amount' => 19,
        ])->assertRedirect();

        $buy = OilTrade::latest('id')->first();
        $this->assertEquals(8, $buy->quantity_kg);
        $this->assertEquals(19, $buy->total_amount);
        $this->assertEquals(2.375, $buy->price_per_kg);

        $this->actingAs($cashier)->post(route('cashier.oil-trade.store'), [
            'type' => 'sell', 'quantity_kg' => 3, 'total_amount' => 7,
        ])->assertRedirect();

        $sell = OilTrade::latest('id')->first();
        $this->assertEquals(3, $sell->quantity_kg);
        $this->assertEquals(7, $sell->total_amount);
        $this->assertEqualsWithDelta($stockBefore + 5, OilTrade::totalOilStock(), 0.001);
        $this->get(route('cashier.oil-trade'))
            ->assertOk()
            ->assertSee('السعر الإجمالي')
            ->assertSee('19.000');
    }

    public function test_dashboard_only_counts_todays_receipts_and_oil_trades(): void
    {
        Carbon::setTestNow(Carbon::parse('2035-10-18 12:00:00', 'Asia/Amman'));

        try {
            $cashier = User::factory()->create(['role' => 'cashier']);
            $owner = Owner::create(['name' => 'Daily Test', 'city' => 'Amman', 'street' => 'Test', 'phone' => '0790000010']);
            $todayReceipt = Receipt::create([
                'owner_id' => $owner->id, 'carrier_name' => 'Test', 'olive_weight_kg' => 100,
                'oil_weight_kg' => 10, 'fee_per_kg' => 1, 'payment_method' => 'cash', 'status' => 'completed',
            ]);
            $oldReceipt = Receipt::create([
                'owner_id' => $owner->id, 'carrier_name' => 'Test', 'olive_weight_kg' => 200,
                'oil_weight_kg' => 20, 'fee_per_kg' => 1, 'payment_method' => 'cash', 'status' => 'completed',
            ]);
            DB::table('receipts')->where('id', $oldReceipt->id)->update(['created_at' => '2035-10-17 12:00:00']);

            $todayBuy = OilTrade::create(['type' => 'buy', 'quantity_kg' => 2, 'price_per_kg' => 2.5, 'total_amount' => 5]);
            OilTrade::create(['type' => 'sell', 'quantity_kg' => 1, 'price_per_kg' => 3, 'total_amount' => 3]);
            $oldBuy = OilTrade::create(['type' => 'buy', 'quantity_kg' => 10, 'price_per_kg' => 2, 'total_amount' => 20]);
            DB::table('oil_trades')->where('id', $oldBuy->id)->update(['created_at' => '2035-10-17 12:00:00']);

            $response = $this->actingAs($cashier)->get(route('dashboard'))->assertOk();
            $this->assertEquals(1, $response->viewData('todayCount'));
            $this->assertEquals(100, $response->viewData('todayOlive'));
            $this->assertEquals(5, $response->viewData('todayOilBuyTotal'));
            $this->assertEquals(3, $response->viewData('todayOilSellTotal'));
            $this->assertEquals(1, $response->viewData('netOilKg'));
            $this->assertEquals(8, $response->viewData('todayNetFees'));
            $this->assertTrue($response->viewData('recentReceipts')->contains('id', $todayReceipt->id));
            $this->assertFalse($response->viewData('recentReceipts')->contains('id', $oldReceipt->id));
        } finally {
            Carbon::setTestNow();
        }
    }
}
