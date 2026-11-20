<?php

namespace Tests\Feature;

use App\Models\OilTrade;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OilTradeDebtTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(Carbon::parse('2036-10-20 12:00:00', 'Asia/Amman'));
        DB::table('settings')->updateOrInsert(['key' => 'active_year'], ['value' => 'all']);
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
    }

    private function createTrade(string $type, string $status, float $quantity, float $amount): OilTrade
    {
        $this->post(route('cashier.oil-trade.store'), [
            'type' => $type,
            'payment_status' => $status,
            'counterparty_name' => $type === 'buy' ? 'Test Supplier' : 'Test Customer',
            'quantity_kg' => $quantity,
            'total_amount' => $amount,
        ])->assertSessionHasNoErrors()->assertRedirect();

        return OilTrade::latest('id')->first();
    }

    private function financialData(string $day): array
    {
        return $this->withSession(['financial_unlocked' => true])
            ->get(route('reports.financial', ['from_date' => $day, 'to_date' => $day]))
            ->assertOk()->viewData('totals');
    }

    public function test_buy_and_sell_debts_change_stock_and_separate_debts_but_not_cash(): void
    {
        $stockBefore = OilTrade::totalOilStock();
        $buy = $this->createTrade('buy', 'debt', 10, 55);
        $sell = $this->createTrade('sell', 'debt', 4, 30);
        $this->createTrade('buy', 'paid', 2, 11);
        $this->createTrade('sell', 'paid', 1, 8);

        $this->assertEqualsWithDelta($stockBefore + 7, OilTrade::totalOilStock(), 0.001);
        $this->assertEquals('debt', $buy->payment_status);
        $this->assertNull($sell->debt_paid_at);

        $page = $this->get(route('cashier.oil-trade', ['from_date' => '2036-10-20', 'to_date' => '2036-10-20']))->assertOk();
        foreach (['totalBuyDebt' => 55, 'totalSellDebt' => 30, 'totalBuyPaid' => 11,
            'totalSellPaid' => 8, 'totalBuyVal' => 66, 'totalSellVal' => 38] as $key => $value) {
            $this->assertEquals($value, $page->viewData($key), $key);
        }
        $page->assertSee('Test Supplier')->assertSee('Test Customer');

        $dashboard = $this->get(route('dashboard'))->assertOk();
        foreach (['todayOilBuyDebt' => 55, 'todayOilSellDebt' => 30, 'todayOilBuyTotal' => 11,
            'todayOilSellTotal' => 8, 'todayNetFees' => -3, 'netOilKg' => 7] as $key => $value) {
            $this->assertEquals($value, $dashboard->viewData($key), $key);
        }
        $totals = $this->financialData('2036-10-20');
        foreach (['oil_buy_debt' => 55, 'oil_sell_debt' => 30, 'oil_buy' => 11,
            'oil_sell' => 8, 'net' => -3] as $key => $value) {
            $this->assertEquals($value, $totals[$key], $key);
        }
        $this->get(route('reports'))->assertOk();
    }

    public function test_settlement_uses_payment_day_once_and_does_not_move_stock_again(): void
    {
        $buy = $this->createTrade('buy', 'debt', 10, 55.125);
        $sell = $this->createTrade('sell', 'debt', 3, 21.375);
        $stock = OilTrade::totalOilStock();
        $this->travelTo(Carbon::parse('2036-10-21 13:00:00', 'Asia/Amman'));

        foreach ([$buy, $sell] as $trade) {
            $this->post(route('cashier.oil-trade.settle', $trade))->assertRedirect();
            $this->assertEquals('paid', $trade->fresh()->payment_status);
            $this->assertEquals('2036-10-21', $trade->fresh()->debt_paid_at->toDateString());
        }
        $this->assertEqualsWithDelta($stock, OilTrade::totalOilStock(), 0.001);
        $dashboard = $this->get(route('dashboard'))->assertOk();
        $this->assertEquals(55.125, $dashboard->viewData('todayOilBuyTotal'));
        $this->assertEquals(21.375, $dashboard->viewData('todayOilSellTotal'));
        $this->assertEquals(-33.75, $dashboard->viewData('todayNetFees'));
        $this->assertEquals(0, $dashboard->viewData('netOilKg'));
        $summary = $this->withSession(['reports_unlocked' => true])
            ->get(route('receipts.index', ['from_date' => '2036-10-21', 'to_date' => '2036-10-21']))
            ->assertOk()->viewData('summary');
        $this->assertEquals(-33.75, $summary['netCashFees']);
        $print = $this->get(route('receipts.print-table', ['from_date' => '2036-10-21', 'to_date' => '2036-10-21']))->assertOk();
        $this->assertEquals(-33.75, $print->viewData('netCashFees'));

        $creationDay = $this->financialData('2036-10-20');
        $this->assertEquals(0, $creationDay['oil_buy']);
        $this->assertEquals(0, $creationDay['oil_sell']);
        $paymentDay = $this->financialData('2036-10-21');
        $this->assertEquals(55.125, $paymentDay['oil_buy']);
        $this->assertEquals(21.375, $paymentDay['oil_sell']);
        $this->assertEquals(0, $paymentDay['oil_buy_debt']);
        $this->assertEquals(0, $paymentDay['oil_sell_debt']);

        $paymentTime = $buy->fresh()->debt_paid_at->toDateTimeString();
        $this->travelTo(Carbon::parse('2036-10-22 13:00:00', 'Asia/Amman'));
        $this->post(route('cashier.oil-trade.settle', $buy))->assertRedirect();
        $this->assertEquals($paymentTime, $buy->fresh()->debt_paid_at->toDateTimeString());
        $this->assertEquals(0, $this->financialData('2036-10-22')['oil_buy']);
        $page = $this->get(route('cashier.oil-trade', ['from_date' => '2036-10-20']))->assertOk();
        $this->assertEquals(0, $page->viewData('totalBuyDebt'));
        $this->assertEquals(0, $page->viewData('totalSellDebt'));
    }

    public function test_debt_filters_and_deleting_a_debt_reverse_only_that_trade(): void
    {
        $stock = OilTrade::totalOilStock();
        $buy = $this->createTrade('buy', 'debt', 10, 55);
        $sell = $this->createTrade('sell', 'debt', 3, 21);
        $this->createTrade('buy', 'paid', 2, 11);

        $page = $this->get(route('cashier.oil-trade', [
            'from_date' => '2036-10-20', 'payment_status' => 'debt', 'search' => 'Test Supplier',
        ]))->assertOk();
        $this->assertEquals(55, $page->viewData('totalBuyDebt'));
        $this->assertEquals(0, $page->viewData('totalSellDebt'));
        $this->assertEquals(0, $page->viewData('totalBuyPaid'));
        $this->assertCount(1, $page->viewData('buys'));

        DB::table('settings')->updateOrInsert(['key' => 'cashier_delete_password'], ['value' => 'test-delete']);
        $this->delete(route('cashier.oil-trade.destroy', $buy), ['delete_password' => 'wrong'])->assertRedirect();
        $this->assertDatabaseHas('oil_trades', ['id' => $buy->id]);
        $this->delete(route('cashier.oil-trade.destroy', $buy), ['delete_password' => 'test-delete'])->assertRedirect();
        $this->assertDatabaseMissing('oil_trades', ['id' => $buy->id]);
        $this->assertDatabaseHas('oil_trades', ['id' => $sell->id, 'payment_status' => 'debt']);
        $this->assertEqualsWithDelta($stock - 1, OilTrade::totalOilStock(), 0.001);
        $this->assertEquals(11, $this->financialData('2036-10-20')['oil_buy']);
    }

    public function test_cash_remains_default_and_debts_require_a_party_and_cashier_access(): void
    {
        $this->post(route('cashier.oil-trade.store'), [
            'type' => 'buy', 'quantity_kg' => 2, 'total_amount' => 12,
        ])->assertSessionHasNoErrors()->assertRedirect();
        $cash = OilTrade::latest('id')->first();
        $this->assertEquals('paid', $cash->payment_status);
        $this->post(route('cashier.oil-trade.settle', $cash))->assertRedirect();
        $this->assertNull($cash->fresh()->debt_paid_at);

        $count = OilTrade::count();
        $this->post(route('cashier.oil-trade.store'), [
            'type' => 'buy', 'quantity_kg' => 2, 'total_amount' => 12, 'payment_status' => 'debt',
        ])->assertSessionHasErrors('counterparty_name');
        $this->post(route('cashier.oil-trade.store'), [
            'type' => 'sell', 'quantity_kg' => 2, 'total_amount' => 12, 'payment_status' => 'unknown',
        ])->assertSessionHasErrors('payment_status');
        $this->assertEquals($count, OilTrade::count());

        $debt = $this->createTrade('buy', 'debt', 2, 12);
        $normalUser = User::factory()->create(['role' => 'cashier']);
        // The legacy SQLite fixture predates the MySQL-only role enum migration.
        $normalUser->role = 'user';
        $this->actingAs($normalUser)
            ->post(route('cashier.oil-trade.settle', $debt))->assertRedirect();
        $this->assertEquals('debt', $debt->fresh()->payment_status);
        $this->assertNull($debt->fresh()->debt_paid_at);
    }
}
