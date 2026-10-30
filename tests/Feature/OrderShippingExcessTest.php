<?php

namespace Tests\Feature;

use App\Models\Agency;
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
    private const AGENCY_SERVER_ID = 999901;

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

    private function agency(array $overrides = []): Agency
    {
        return Agency::create(array_merge([
            'name' => 'SHALOM',
            'server_id' => self::AGENCY_SERVER_ID,
            'shipping_cost' => false,
            'status' => true,
            'sort_order' => 1,
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

    /** Agencia con shipping_cost=false (paga en destino): ni envío base ni exceso. */
    public function test_agency_without_shipping_cost_charges_nothing(): void
    {
        $this->district();
        $this->agency(['shipping_cost' => false]);

        $result = $this->calculate([
            'mode' => 'ENVIO',
            'district_id' => self::DISTRICT_SERVER_ID,
            'agency_id' => self::AGENCY_SERVER_ID,
            'products' => [['id' => 1, 'quantity' => 7, 'price' => 100]],
        ]);

        $this->assertSame(0, $result['baseSendCost']);
        $this->assertSame(0, $result['excessUnits']);
        $this->assertSame(0, $result['excessAmount']);
        $this->assertSame(0, $result['sendCost']);
        $this->assertEquals(700, $result['total']);
    }

    /** Agencia con shipping_cost=true: envío base + exceso del distrito. */
    public function test_agency_with_shipping_cost_charges_base_plus_excess(): void
    {
        $this->district();
        $this->agency(['shipping_cost' => true]);

        $result = $this->calculate([
            'mode' => 'ENVIO',
            'district_id' => self::DISTRICT_SERVER_ID,
            'agency_id' => self::AGENCY_SERVER_ID,
            'products' => [['id' => 1, 'quantity' => 7, 'price' => 100]],
        ]);

        $this->assertSame(15.0, $result['baseSendCost']);
        $this->assertSame(10.0, $result['excessAmount']);
        $this->assertSame(25.0, $result['sendCost']);
        $this->assertSame(725.0, $result['total']);
    }

    /** 360Sys sin dato de shipping_cost (NULL): se cobra, como antes de las agencias. */
    public function test_agency_without_shipping_cost_data_charges(): void
    {
        $this->district();
        $this->agency(['shipping_cost' => null]);

        $result = $this->calculate([
            'mode' => 'ENVIO',
            'district_id' => self::DISTRICT_SERVER_ID,
            'agency_id' => self::AGENCY_SERVER_ID,
            'products' => [['id' => 1, 'quantity' => 1, 'price' => 100]],
        ]);

        $this->assertSame(15.0, $result['sendCost']);
    }

    public function test_agency_does_not_affect_delivery(): void
    {
        $this->agency(['shipping_cost' => false]);
        Zone::create(['name' => 'LIMA CENTRO', 'server_id' => self::ZONE_SERVER_ID, 'sendCost' => 12, 'status' => true]);

        $result = $this->calculate([
            'mode' => 'DELIVERY',
            'zone_id' => self::ZONE_SERVER_ID,
            'agency_id' => self::AGENCY_SERVER_ID,
            'products' => [['id' => 1, 'quantity' => 7, 'price' => 100]],
        ]);

        $this->assertEquals(12, $result['sendCost']);
    }

    /** El distrito siempre expone lo sincronizado: el cero lo decide la agencia en el cálculo. */
    public function test_district_endpoint_exposes_db_values(): void
    {
        $district = $this->district();

        $this->getJson("/api/district/{$district->id}")
            ->assertOk()
            ->assertJson(['sendCost' => '15.00', 'excess' => '5.00', 'excessFactor' => 3]);
    }

    public function test_available_reflects_real_coverage(): void
    {
        $covered = $this->district(['status' => 1]);
        $uncovered = $this->district(['name' => 'TEST_SIN_ENVIO_999', 'server_id' => 999224, 'sendCost' => 0, 'status' => 1]);

        $this->getJson("/api/district/{$covered->id}")->assertJson(['available' => true]);
        $this->getJson("/api/district/{$uncovered->id}")->assertJson(['available' => false]);
    }

    /** Método de Envío: solo activas, en el orden de 360Sys. */
    public function test_agency_endpoint_lists_active_agencies_in_order(): void
    {
        $this->agency(['name' => 'TEST_OLVA_999', 'server_id' => 999902, 'sort_order' => 2, 'shipping_cost' => true]);
        $this->agency(['name' => 'TEST_SHALOM_999', 'server_id' => 999901, 'sort_order' => 1, 'shipping_cost' => false]);
        $this->agency(['name' => 'TEST_INACTIVA_999', 'server_id' => 999903, 'sort_order' => 0, 'status' => false]);

        $names = collect($this->getJson('/api/agency')->assertOk()->json())
            ->pluck('name')
            ->filter(fn ($name) => str_starts_with($name, 'TEST_'))
            ->values()
            ->all();

        $this->assertSame(['TEST_SHALOM_999', 'TEST_OLVA_999'], $names);

        $this->getJson('/api/agency')->assertJsonFragment([
            'server_id' => 999901,
            'name' => 'TEST_SHALOM_999',
            'shipping_cost' => false,
            'status' => true,
            'sort_order' => 1,
        ]);
    }
}
