<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\District;
use App\Services\Api360Service;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Corre contra la BD local real: DatabaseTransactions envuelve cada test en una
 * transacción y hace rollback al terminar. No usa RefreshDatabase (haría
 * migrate:fresh, que borra la BD y además exige doctrine/dbal por las
 * migraciones con ->change()).
 *
 * server_id centinela alto para no chocar con distritos reales sincronizados.
 */
class Api360DistrictSyncTest extends TestCase
{
    use DatabaseTransactions;

    private const SERVER_ID = 999223;

    private function districtPayload(array $overrides = []): array
    {
        return array_merge([
            'id' => self::SERVER_ID,
            'name' => 'CHACHAPOYAS',
            'location_code' => '010101',
            'province_id' => 27,
            'price' => 15,
            'excess' => 5,
            'excess_factor' => 3,
            'status' => true,
        ], $overrides);
    }

    private function response(array $district)
    {
        return Http::response([
            'status' => 200,
            'data' => ['districts' => [$district]],
        ], 200);
    }

    private function fakeDistricts(array $overrides = []): void
    {
        Http::fake([
            'https://sistema.360sys.com.pe/api/online-store/districts' => $this->response(
                $this->districtPayload($overrides)
            ),
        ]);
    }

    public function test_it_syncs_excess_fields_from_360_response(): void
    {
        $this->fakeDistricts();

        $result = app(Api360Service::class)->fetch_districts('test-uuid');

        $this->assertTrue($result['status']);
        $this->assertDatabaseHas('district', [
            'server_id' => self::SERVER_ID,
            'name' => 'CHACHAPOYAS',
            'sendCost' => 15,
            'excess' => 5,
            'excessFactor' => 3,
        ]);
    }

    public function test_it_persists_zero_excess_fields(): void
    {
        $this->fakeDistricts(['excess' => 0, 'excess_factor' => 0]);

        app(Api360Service::class)->fetch_districts('test-uuid');

        $district = District::firstWhere('server_id', self::SERVER_ID);

        $this->assertSame(0, $district->excessUnitsFor(10));
        $this->assertSame(15.0, $district->shippingCostFor(10));
    }

    /**
     * fetchDataAndSync escribe null cuando la clave falta en la respuesta.
     * Debe seguir sincronizando y comportarse como "sin exceso" (D2).
     */
    public function test_missing_excess_keys_do_not_break_the_sync(): void
    {
        Http::fake([
            'https://sistema.360sys.com.pe/api/online-store/districts' => Http::response([
                'status' => 200,
                'data' => [
                    'districts' => [
                        [
                            'id' => self::SERVER_ID,
                            'name' => 'CHACHAPOYAS',
                            'location_code' => '010101',
                            'province_id' => 27,
                            'price' => 15,
                            'status' => true,
                        ],
                    ],
                ],
            ], 200),
        ]);

        $result = app(Api360Service::class)->fetch_districts('test-uuid');

        $this->assertTrue($result['status'], json_encode($result));

        $district = District::firstWhere('server_id', self::SERVER_ID);

        $this->assertNotNull($district);
        $this->assertSame(0, $district->excessUnitsFor(10));
        $this->assertSame(15.0, $district->shippingCostFor(10));
    }

    public function test_resync_updates_excess_values_on_the_same_server_id(): void
    {
        // Http::fake() fusiona stubs en vez de reemplazarlos: para dos respuestas
        // distintas sobre la misma URL hay que usar una secuencia.
        Http::fake([
            'https://sistema.360sys.com.pe/api/online-store/districts' => Http::sequence()
                ->push([
                    'status' => 200,
                    'data' => ['districts' => [$this->districtPayload()]],
                ], 200)
                ->push([
                    'status' => 200,
                    'data' => ['districts' => [
                        $this->districtPayload(['price' => 20, 'excess' => 8, 'excess_factor' => 2]),
                    ]],
                ], 200),
        ]);

        app(Api360Service::class)->fetch_districts('test-uuid');
        app(Api360Service::class)->fetch_districts('test-uuid');

        $this->assertSame(1, District::where('server_id', self::SERVER_ID)->count());

        $district = District::firstWhere('server_id', self::SERVER_ID);

        // 5 prendas, factor 2 => floor(4/2) = 2 unidades => 20 + 16
        $this->assertSame(2, $district->excessUnitsFor(5));
        $this->assertSame(36.0, $district->shippingCostFor(5));
    }

    /** Las agencias vienen en el mismo GET store/districts, clave 'agencies'. */
    public function test_it_syncs_agencies_from_the_districts_response(): void
    {
        Http::fake([
            'https://sistema.360sys.com.pe/api/online-store/districts' => Http::response([
                'status' => 200,
                'data' => [
                    'districts' => [$this->districtPayload()],
                    'agencies' => [
                        ['id' => 999901, 'name' => 'Shalom', 'description' => 'Paga en destino.', 'shipping_cost' => false, 'status' => true, 'order' => 1],
                        ['id' => 999902, 'name' => 'Olva', 'description' => 'Info clara.', 'shipping_cost' => true, 'status' => true, 'order' => 2],
                    ],
                ],
            ], 200),
        ]);

        $result = app(Api360Service::class)->fetch_agencies('test-uuid');

        $this->assertTrue($result['status'], json_encode($result));

        $shalom = Agency::firstWhere('server_id', 999901);
        $this->assertSame('Shalom', $shalom->name);
        $this->assertFalse($shalom->chargesShipping());
        $this->assertSame(1, $shalom->sort_order);
        $this->assertTrue(Agency::firstWhere('server_id', 999902)->chargesShipping());
    }
}
