<?php

namespace Tests\Feature;

use App\Models\Owner;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReceiptDiscountTest extends TestCase
{
    use DatabaseTransactions;

    public function test_old_oil_returns_keep_their_saved_divisor_until_edited(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $owner = Owner::create(['name' => 'Old Return', 'city' => 'Amman', 'street' => 'Test', 'phone' => '0790000012']);
        $receipt = Receipt::create([
            'owner_id' => $owner->id, 'carrier_name' => 'Test', 'olive_weight_kg' => 210,
            'oil_weight_kg' => 35, 'fee_per_kg' => 2, 'payment_method' => 'oil_return',
            'oil_return_divisor' => 7, 'oil_return_kg' => 10, 'status' => 'completed',
        ]);

        $this->actingAs($cashier)->get(route('cashier.index', ['floor_number' => $owner->floor_number]))
            ->assertOk()
            ->assertSee('name="oil_return_divisor"', false)
            ->assertSee('value="7.000"', false);
        $this->assertEquals(7, $receipt->fresh()->oil_return_divisor);
        $this->assertEquals(10, $receipt->oil_return_kg);
    }

    public function test_unsettled_receipt_shows_five_point_five_in_both_editable_fields(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $owner = Owner::create(['name' => 'Unsettled Return', 'city' => 'Amman', 'street' => 'Test', 'phone' => '0790000014']);
        Receipt::create([
            'owner_id' => $owner->id, 'carrier_name' => 'Test', 'olive_weight_kg' => 210,
            'fee_per_kg' => 2, 'oil_return_divisor' => 7, 'can_return_divisor' => 7,
            'status' => 'pending',
        ]);

        $html = $this->actingAs($cashier)
            ->get(route('cashier.index', ['floor_number' => $owner->floor_number]))
            ->assertOk()
            ->getContent();
        $this->assertMatchesRegularExpression('/name="oil_return_divisor"[^>]*value="5\.500"/s', $html);
        $this->assertMatchesRegularExpression('/name="can_return_divisor"[^>]*value="5\.500"/s', $html);
    }

    public function test_existing_percentage_receipt_keeps_its_saved_discount_amount(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $owner = Owner::create(['name' => 'Old Discount', 'city' => 'Amman', 'street' => 'Test', 'phone' => '0790000013']);
        $receipt = Receipt::create([
            'owner_id' => $owner->id, 'carrier_name' => 'Test', 'olive_weight_kg' => 300,
            'oil_weight_kg' => 50, 'fee_per_kg' => 2, 'discount_percent' => 10,
            'discount_amount' => 10, 'status' => 'completed',
        ]);

        $receipt->update(['notes' => 'Old receipt edited without changing the discount']);
        $this->assertEquals(10, $receipt->fresh()->discount_amount);
        $this->assertEquals(90, $receipt->total_fee);
        $this->actingAs($cashier)->get(route('receipts.print', $receipt))
            ->assertOk()
            ->assertSee('خصم من الإجمالي')
            ->assertSee('10.000');
    }

    public function test_fractional_discount_amounts_and_blank_default(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $owner = Owner::create(['name' => 'Fraction Test', 'city' => 'Amman', 'street' => 'Test', 'phone' => '0790000011']);
        $receipt = Receipt::create([
            'owner_id' => $owner->id, 'carrier_name' => 'Test', 'olive_weight_kg' => 300,
            'fee_per_kg' => 2, 'status' => 'pending',
        ]);

        foreach (['0.05' => 0.05, '0.01' => 0.01, '' => 0] as $amount => $expectedDiscount) {
            $this->actingAs($cashier)->patch(route('cashier.update-oil', $receipt), [
                'oil_weight_kg' => 50, 'fee_per_kg' => 2, 'discount_amount' => $amount,
                'payment_method' => 'cash', 'status' => 'completed',
            ])->assertRedirect();

            $receipt->refresh();
            $this->assertEquals(0, (float) $receipt->discount_percent);
            $this->assertEquals($expectedDiscount, (float) $receipt->discount_amount);
            $this->assertEquals(100 - $expectedDiscount, (float) $receipt->total_fee);
        }
    }

    public function test_cashier_discount_applies_to_press_and_cans(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $owner = Owner::create(['name' => 'Test', 'city' => 'Amman', 'street' => 'Test', 'phone' => '0790000001']);
        $receipt = Receipt::create([
            'owner_id' => $owner->id,
            'carrier_name' => 'Test',
            'olive_weight_kg' => 300,
            'fee_per_kg' => 2,
            'status' => 'pending',
        ]);
        $this->assertEquals(5.5, $receipt->oil_return_divisor);
        $this->assertEquals(5.5, $receipt->can_return_divisor);

        $this->actingAs($cashier)->patch(route('cashier.update-oil', $receipt), [
            'oil_weight_kg' => 50,
            'fee_per_kg' => 2,
            'can_16_count' => 1,
            'discount_amount' => 7.5,
            'payment_method' => 'cash',
            'status' => 'completed',
        ])->assertRedirect();

        $receipt->refresh();
        $this->assertEquals(100, $receipt->press_fee);
        $this->assertEquals(2, $receipt->can_total);
        $this->assertEquals(7.5, $receipt->discount_amount);
        $this->assertEquals(1.853, $receipt->discounted_can_total);
        $this->assertEquals(94.5, $receipt->total_fee);

        $this->get(route('cashier.index', ['floor_number' => $owner->floor_number]))
            ->assertOk()
            ->assertSee('name="discount_amount"', false)
            ->assertDontSee('name="discount_percent"', false)
            ->assertSee('94.500');
        $this->get(route('receipts.print', $receipt))
            ->assertOk()
            ->assertSee('خصم من الإجمالي')
            ->assertSee('7.500');
        $this->get(route('cashier.print', $owner))->assertOk();
        $this->get(route('receipts.print-table'))->assertOk();

        $receipt->update(['discount_amount' => 103]);
        $this->assertEquals(102, $receipt->fresh()->discount_amount);
        $this->assertEquals(0, $receipt->fresh()->total_fee);
        $this->assertEquals(0, $receipt->fresh()->discounted_can_total);
    }

    public function test_oil_return_and_cash_can_portions_use_discounted_amounts(): void
    {
        $cashier = User::factory()->create(['role' => 'cashier']);
        $owner = Owner::create(['name' => 'Test', 'city' => 'Amman', 'street' => 'Test', 'phone' => '0790000002']);
        $receipt = Receipt::create([
            'owner_id' => $owner->id,
            'carrier_name' => 'Test',
            'olive_weight_kg' => 210,
            'fee_per_kg' => 2,
            'status' => 'pending',
        ]);

        $this->actingAs($cashier)->patch(route('cashier.update-oil', $receipt), [
            'oil_weight_kg' => 35,
            'fee_per_kg' => 2,
            'can_8_count' => 1,
            'discount_amount' => 14.3,
            'payment_method' => 'oil_return',
            'oil_return_divisor' => 5.5,
            'can_return_divisor' => 5.5,
            'cans_in_return' => 0,
            'status' => 'completed',
        ])->assertRedirect();

        $receipt->refresh();
        $this->assertEquals(57.2, $receipt->total_fee);
        $this->assertEquals(1.2, $receipt->discounted_can_total);
        $this->assertEquals(56, $receipt->discounted_press_fee);
        $this->assertEquals(5.5, $receipt->oil_return_divisor);
        $this->assertEquals(5.5, $receipt->can_return_divisor);
        $this->assertEquals(10.182, $receipt->oil_return_kg);
        $cashierPage = $this->get(route('cashier.index', ['floor_number' => $owner->floor_number]))
            ->assertOk()
            ->assertSee('أجرة العصر بعد الخصم ÷')
            ->assertSee('name="oil_return_divisor"', false)
            ->assertSee('name="can_return_divisor"', false)
            ->assertSee('value="5.500"', false);
        $this->assertMatchesRegularExpression('/class="oil-return-formula formula-disabled"\s+id="n2Row_'.$receipt->id.'"/', $cashierPage->getContent());

        $this->actingAs($cashier)->patch(route('cashier.update-oil', $receipt), [
            'oil_weight_kg' => 35,
            'fee_per_kg' => 2,
            'can_8_count' => 1,
            'discount_amount' => 14.3,
            'payment_method' => 'oil_return',
            'oil_return_divisor' => 5.5,
            'can_return_divisor' => 5.5,
            'cans_in_return' => 1,
            'status' => 'completed',
        ])->assertRedirect();

        $this->assertEquals(10.4, $receipt->fresh()->oil_return_kg);
        $cashierPage = $this->get(route('cashier.index', ['floor_number' => $owner->floor_number]))->assertOk();
        $this->assertMatchesRegularExpression('/class="oil-return-formula formula-disabled"\s+id="n1Row_'.$receipt->id.'"/', $cashierPage->getContent());

        $this->actingAs($cashier)->patch(route('cashier.update-oil', $receipt), [
            'oil_weight_kg' => 35,
            'fee_per_kg' => 2,
            'can_8_count' => 1,
            'discount_amount' => 14.3,
            'payment_method' => 'oil_return',
            'oil_return_divisor' => 6.25,
            'can_return_divisor' => 4.4,
            'cans_in_return' => 0,
            'status' => 'completed',
        ])->assertRedirect();

        $receipt->refresh();
        $this->assertEquals(6.25, $receipt->oil_return_divisor);
        $this->assertEquals(4.4, $receipt->can_return_divisor);
        $this->assertEquals(8.96, $receipt->oil_return_kg);

        $this->actingAs($cashier)->patch(route('cashier.update-oil', $receipt), [
            'oil_weight_kg' => 35,
            'fee_per_kg' => 2,
            'can_8_count' => 1,
            'discount_amount' => 14.3,
            'payment_method' => 'oil_return',
            'oil_return_divisor' => 6.25,
            'can_return_divisor' => 4.4,
            'cans_in_return' => 1,
            'status' => 'completed',
        ])->assertRedirect();
        $this->assertEquals(13, $receipt->fresh()->oil_return_kg);
    }
}
