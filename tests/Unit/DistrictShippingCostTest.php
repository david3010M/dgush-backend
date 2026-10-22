<?php

namespace Tests\Unit;

use App\Models\District;
use PHPUnit\Framework\TestCase;

/**
 * Fórmula del exceso de envío interdepartamental (modo ENVIO).
 *
 * No toca BD: District se instancia en memoria.
 */
class DistrictShippingCostTest extends TestCase
{
    private function district(float $sendCost, float $excess, int $factor): District
    {
        $district = new District();
        $district->sendCost = $sendCost;
        $district->excess = $excess;
        $district->excessFactor = $factor;

        return $district;
    }

    /**
     * @dataProvider quantityProvider
     */
    public function test_shipping_cost_applies_excess_every_factor_items(int $quantity, float $expected): void
    {
        $district = $this->district(15, 5, 3);

        $this->assertSame($expected, $district->shippingCostFor($quantity));
    }

    public static function quantityProvider(): array
    {
        return [
            '0 prendas' => [0, 15.0],
            '1 prenda' => [1, 15.0],
            '2 prendas' => [2, 15.0],
            '3 prendas' => [3, 15.0],
            '4 prendas' => [4, 20.0],
            '5 prendas' => [5, 20.0],
            '6 prendas' => [6, 20.0],
            '7 prendas' => [7, 25.0],
            '8 prendas' => [8, 25.0],
            '9 prendas' => [9, 25.0],
            '10 prendas' => [10, 30.0],
        ];
    }

    public function test_excess_units_and_amount_are_consistent(): void
    {
        $district = $this->district(15, 5, 3);

        $this->assertSame(2, $district->excessUnitsFor(7));
        $this->assertSame(10.0, $district->excessAmountFor(7));
    }

    public function test_zero_excess_means_no_surcharge(): void
    {
        $district = $this->district(15, 0, 3);

        $this->assertSame(0, $district->excessUnitsFor(10));
        $this->assertSame(15.0, $district->shippingCostFor(10));
    }

    public function test_zero_factor_means_no_surcharge(): void
    {
        $district = $this->district(15, 5, 0);

        $this->assertSame(0, $district->excessUnitsFor(10));
        $this->assertSame(15.0, $district->shippingCostFor(10));
    }

    /** 360Sys puede omitir las claves: fetchDataAndSync escribe null. */
    public function test_null_excess_fields_are_treated_as_zero(): void
    {
        $district = new District();
        $district->sendCost = 15;
        $district->excess = null;
        $district->excessFactor = null;

        $this->assertSame(0, $district->excessUnitsFor(10));
        $this->assertSame(15.0, $district->shippingCostFor(10));
    }

    /** Factor 1: cobra desde la 2da prenda. */
    public function test_factor_one_charges_from_the_second_item(): void
    {
        $district = $this->district(15, 5, 1);

        $this->assertSame(15.0, $district->shippingCostFor(1));
        $this->assertSame(30.0, $district->shippingCostFor(4));
    }

    public function test_negative_quantity_is_treated_as_zero(): void
    {
        $district = $this->district(15, 5, 3);

        $this->assertSame(0, $district->excessUnitsFor(-5));
        $this->assertSame(15.0, $district->shippingCostFor(-5));
    }
}
