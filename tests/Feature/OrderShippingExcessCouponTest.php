<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\District;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * D5: el cupón con indicator = sendCost se aplica sobre base + exceso.
 *
 * Sale gratis del cambio en calculate(), así que estos tests existen para que
 * nadie lo revierta sin darse cuenta.
 *
 * Distrito: sendCost 15, excess 5, factor 3. 10 prendas => envío 30.
 *
 * Corre contra la BD local real con DatabaseTransactions (rollback al terminar),
 * con server_id y código de cupón centinela para no chocar con datos reales.
 */
class OrderShippingExcessCouponTest extends TestCase
{
    use DatabaseTransactions;

    private const SUBTOTAL = 1000.0;
    private const DISTRICT_SERVER_ID = 999223;

    protected function setUp(): void
    {
        parent::setUp();

        District::create([
            'name' => 'CHACHAPOYAS',
            'server_id' => self::DISTRICT_SERVER_ID,
            'sendCost' => 15,
            'excess' => 5,
            'excessFactor' => 3,
        ]);
    }

    private function coupon(string $code, string $type, string $indicator, float $value): void
    {
        Coupon::create([
            'name' => $code,
            'description' => 'test',
            'code' => $code,
            'type' => $type,
            'indicator' => $indicator,
            'value' => $value,
            'expires_at' => now()->addYear(),
        ]);
    }

    private function calculateWithCoupon(?string $code): array
    {
        $payload = [
            'mode' => 'ENVIO',
            'district_id' => self::DISTRICT_SERVER_ID,
            // 10 prendas a 100 => subtotal 1000
            'products' => [['id' => 1, 'quantity' => 10, 'price' => 100]],
        ];

        if ($code !== null) {
            $payload['coupon'] = $code;
        }

        $request = Request::create('/api/order', 'POST', [], [], [], [], json_encode($payload));
        $request->headers->set('Content-Type', 'application/json');

        return app(OrderService::class)->calculate($request);
    }

    public function test_without_coupon_the_full_shipping_is_charged(): void
    {
        $result = $this->calculateWithCoupon(null);

        $this->assertSame(30.0, $result['sendCost']);
        $this->assertSame(0, $result['discount']);
        $this->assertSame(self::SUBTOTAL + 30, $result['total']);
    }

    public function test_percentage_coupon_discounts_base_plus_excess(): void
    {
        $this->coupon('TEST_ENVIOGRATIS_999', 'percentage', 'sendCost', 100);

        $result = $this->calculateWithCoupon('TEST_ENVIOGRATIS_999');

        // 100% sobre 30 (no sobre 15): el envío queda realmente en 0.
        $this->assertSame(30.0, $result['discount']);
        $this->assertSame(30.0, $result['sendCost']);
        $this->assertSame(self::SUBTOTAL, $result['total']);
    }

    public function test_half_percentage_coupon_discounts_base_plus_excess(): void
    {
        $this->coupon('TEST_MITADENVIO_999', 'percentage', 'sendCost', 50);

        $result = $this->calculateWithCoupon('TEST_MITADENVIO_999');

        $this->assertSame(15.0, $result['discount']);
        $this->assertSame(30.0, $result['sendCost']);
        $this->assertSame(self::SUBTOTAL + 15, $result['total']);
    }

    /** Monto fijo: no depende del indicador, el exceso no lo altera. */
    public function test_fixed_discount_coupon_is_unaffected_by_the_excess(): void
    {
        $this->coupon('TEST_FIJO20_999', 'discount', 'sendCost', 20);

        $result = $this->calculateWithCoupon('TEST_FIJO20_999');

        $this->assertEquals(20, $result['discount']);
        $this->assertSame(30.0, $result['sendCost']);
        $this->assertSame(self::SUBTOTAL + 10, $result['total']);
    }
}
