<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Zone;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Cálculo del envío con exceso interdepartamental.
 *
 * Se ejerce OrderService::calculate() directamente: POST /api/order pasa por
 * StoreOrderRequest, que consulta stock contra 360Sys en prepareForValidation.
 *
 * Corre contra la BD local real con DatabaseTransactions (rollback al terminar),
 * con server_id centinela para no chocar con datos sincronizados.
 */
class OrderShippingExcessTest extends TestCase
{
    use DatabaseTransactions;

    private const DISTRICT_SERVER_ID = 999223;
    private const ZONE_SERVER_ID = 999044;

    private function district(array $overrides = []): District
    {
        return District::create(array_merge([
            'name' => 'CHACHAPOYAS',
            'server_id' => self::DISTRICT_SERVER_ID,
            'sendCost' => 15,
            'excess' => 5,
            'excessFactor' => 3,
        ], $overrides));
    }

    private function calculate(array $payload): array
    {
        $request = Request::create('/api/order', 'POST', [], [], [], [], json_encode($payload));
        $request->headers->set('Content-Type', 'application/json');

        return app(OrderService::class)->calculate($request);
    }

    /** 7 prendas, factor 3 => floor(6/3) = 2 unidades => 15 + 10. */
    public function test_envio_mode_adds_excess_to_the_shipping_cost(): void
    {
        $this->district();

        $result = $this->calculate([
            'mode' => 'ENVIO',
            'district_id' => self::DISTRICT_SERVER_ID,
            'products' => [
                ['id' => 1, 'quantity' => 4, 'price' => 100],
                ['id' => 2, 'quantity' => 3, 'price' => 100],
            ],
        ]);

        $this->assertSame(7, $result['quantity']);
        $this->assertSame(15.0, $result['baseSendCost']);
        $this->assertSame(2, $result['excessUnits']);
        $this->assertSame(10.0, $result['excessAmount']);
        $this->assertSame(25.0, $result['sendCost']);
        $this->assertSame(700 + 25.0, $result['total']);
    }

    public function test_envio_below_the_factor_has_no_excess(): void
    {
        $this->district();

        $result = $this->calculate([
            'mode' => 'ENVIO',
            'district_id' => self::DISTRICT_SERVER_ID,
            'products' => [['id' => 1, 'quantity' => 3, 'price' => 100]],
        ]);

        $this->assertSame(0, $result['excessUnits']);
        $this->assertSame(0.0, $result['excessAmount']);
        $this->assertSame(15.0, $result['sendCost']);
    }

    public function test_delivery_mode_never_applies_excess(): void
    {
        Zone::create(['name' => 'LIMA CENTRO', 'server_id' => self::ZONE_SERVER_ID, 'sendCost' => 12, 'status' => true]);

        $result = $this->calculate([
            'mode' => 'DELIVERY',
            'zone_id' => self::ZONE_SERVER_ID,
            'products' => [['id' => 1, 'quantity' => 7, 'price' => 100]],
        ]);

        $this->assertSame(0, $result['excessUnits']);
        $this->assertSame(0, $result['excessAmount']);
        $this->assertEquals(12, $result['sendCost']);
        $this->assertEquals(12, $result['baseSendCost']);
    }

    public function test_recojo_mode_has_no_shipping_cost(): void
    {
        $result = $this->calculate([
            'mode' => 'RECOJO',
            'branch_id' => 9,
            'products' => [['id' => 1, 'quantity' => 7, 'price' => 100]],
        ]);

        $this->assertSame(0, $result['sendCost']);
        $this->assertSame(0, $result['excessAmount']);
    }

    public function test_district_without_excess_configured_keeps_the_base_cost(): void
    {
        $this->district(['excess' => 0, 'excessFactor' => 0]);

        $result = $this->calculate([
            'mode' => 'ENVIO',
            'district_id' => self::DISTRICT_SERVER_ID,
            'products' => [['id' => 1, 'quantity' => 10, 'price' => 100]],
        ]);

        $this->assertSame(0, $result['excessUnits']);
        $this->assertSame(15.0, $result['sendCost']);
    }

    public function test_breakdown_reports_the_district_configuration(): void
    {
        $this->district();

        $result = $this->calculate([
            'mode' => 'ENVIO',
            'district_id' => self::DISTRICT_SERVER_ID,
            'products' => [['id' => 1, 'quantity' => 10, 'price' => 50]],
        ]);

        $this->assertSame(5.0, $result['excess']);
        $this->assertSame(3, $result['excessFactor']);
        $this->assertSame(3, $result['excessUnits']);
        $this->assertSame(15.0, $result['excessAmount']);
        $this->assertSame(30.0, $result['sendCost']);
    }
}
