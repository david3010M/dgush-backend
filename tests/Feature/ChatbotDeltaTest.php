<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;
use Tests\Feature\Concerns\ChatbotFixtures;
use Tests\TestCase;

/**
 * Delta 1.1: reservas de stock a nivel tienda (D2/D3), server-ids (D1) y geo (D4–D8).
 * La reserva nunca toca product_details.stock: disponible = stock - reservas vigentes.
 */
class ChatbotDeltaTest extends TestCase
{
    use DatabaseTransactions;
    use ChatbotFixtures;

    private const KEY = 'chatbot-test-key';
    private const SUBCATEGORIA = 'ZZBOT SHORTS';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpChatbotFixtures();
    }

    private function item(int $productId, int $quantity = 1): array
    {
        return ['product_id' => $productId, 'color_id' => $this->colorId, 'size_id' => $this->sizeId, 'quantity' => $quantity];
    }

    private function reservar(string $referencia, array $items, ?string $key = null): TestResponse
    {
        return $this->chatbot('stock/reservar', compact('referencia', 'items'), ['Idempotency-Key' => $key ?? uniqid('k-')]);
    }

    private function liberar(array $body, ?string $key = null): TestResponse
    {
        return $this->chatbot('stock/liberar', $body, ['Idempotency-Key' => $key ?? uniqid('k-')]);
    }

    private function disponible(int $productId): int
    {
        return $this->chatbot('disponibilidad/verificar', [
            'product_id' => $productId, 'color_id' => $this->colorId, 'size_id' => $this->sizeId,
        ])->json('stock');
    }

    public function test_reservar_exige_idempotency_key(): void
    {
        $this->chatbot('stock/reservar', ['referencia' => 'WA-1', 'items' => [$this->item(1)]])
            ->assertStatus(400)
            ->assertExactJson(['error' => 'parametro_invalido', 'detalle' => 'falta Idempotency-Key']);
    }

    public function test_reserva_descuenta_disponible_sin_tocar_el_stock_de_360(): void
    {
        $id = $this->product('ZZBOT RESERVA', [], 3);

        $this->reservar('WA-1', [$this->item($id, 2)])
            ->assertOk()
            ->assertJsonPath('reservado', true)
            ->assertJsonPath('items.0.stock_restante', 1);

        $this->assertSame(1, $this->disponible($id));
        $this->assertEquals(3, DB::table('product_details')->where('product_id', $id)->value('stock'));
        $this->assertEquals(1, Product::getProductDetails($id)->first()->stock); // la web también lo ve
    }

    public function test_reserva_sobrevive_a_la_sincronizacion_de_360_y_nunca_es_negativa(): void
    {
        $id = $this->product('ZZBOT SYNC', [], 2);
        $this->reservar('WA-1', [$this->item($id, 2)])->assertJsonPath('reservado', true);

        DB::table('product_details')->where('product_id', $id)->update(['stock' => 5]); // 360 empuja stock
        $this->assertSame(3, $this->disponible($id));

        DB::table('product_details')->where('product_id', $id)->update(['stock' => 1]); // venta en tienda física
        $this->assertSame(0, $this->disponible($id));
    }

    public function test_reserva_es_todo_o_nada(): void
    {
        $conStock = $this->product('ZZBOT OK', [], 5);
        $sinStock = $this->product('ZZBOT CORTO', [], 1);

        $this->reservar('WA-1', [$this->item($conStock, 1), $this->item($sinStock, 2)])
            ->assertOk()
            ->assertJsonPath('reservado', false)
            ->assertJsonPath('items.0.suficiente', true)
            ->assertJsonPath('items.1.suficiente', false)
            ->assertJsonPath('items.1.stock_disponible', 1);

        $this->assertSame(0, DB::table('stock_reservations')->where('reference', 'WA-1')->count());
    }

    public function test_misma_key_devuelve_lo_mismo_sin_reservar_dos_veces(): void
    {
        $id = $this->product('ZZBOT IDEMP', [], 5);

        $primera = $this->reservar('WA-1', [$this->item($id)], 'key-1')->getContent();
        $segunda = $this->reservar('WA-1', [$this->item($id)], 'key-1')->getContent();

        $this->assertSame($primera, $segunda);
        $this->assertSame(1, DB::table('stock_reservations')->where('reference', 'WA-1')->count());
    }

    public function test_misma_key_con_otro_body_responde_409(): void
    {
        $id = $this->product('ZZBOT CONFLICTO', [], 5);
        $this->reservar('WA-1', [$this->item($id)], 'key-1');

        $this->reservar('WA-1', [$this->item($id, 2)], 'key-1')
            ->assertStatus(409)
            ->assertExactJson(['error' => 'conflicto_idempotencia']);
    }

    public function test_reserva_vencida_deja_de_descontar(): void
    {
        $id = $this->product('ZZBOT VENCIDA', [], 4);
        DB::table('stock_reservations')->insert($this->item($id, 3) + [
            'reference' => 'WA-OLD', 'status' => 'activa', 'expires_at_utc' => now()->utc()->subMinute(),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->assertSame(4, $this->disponible($id));

        $this->artisan('chatbot:cerrar-reservas-vencidas')->assertSuccessful();
        $this->assertSame('vencida', DB::table('stock_reservations')->where('reference', 'WA-OLD')->value('status'));
    }

    public function test_liberar_por_referencia_devuelve_el_disponible_y_es_idempotente(): void
    {
        $id = $this->product('ZZBOT LIBERAR', [], 3);
        $this->reservar('WA-1', [$this->item($id, 2)]);

        $this->liberar(['referencia' => 'WA-1', 'motivo' => 'convertida'])
            ->assertOk()
            ->assertJsonPath('liberado', true)
            ->assertJsonPath('items.0.stock_restante', 3);

        $this->assertSame('convertida', DB::table('stock_reservations')->where('reference', 'WA-1')->value('status'));
        $this->liberar(['referencia' => 'WA-1'])->assertJsonPath('liberado', false);
    }

    public function test_liberar_solo_los_items_indicados(): void
    {
        $a = $this->product('ZZBOT A', [], 3);
        $b = $this->product('ZZBOT B', [], 3);
        $this->reservar('WA-1', [$this->item($a), $this->item($b)]);

        $this->liberar(['referencia' => 'WA-1', 'items' => [array_diff_key($this->item($a), ['quantity' => 0])]])
            ->assertJsonCount(1, 'items');

        $this->assertSame(3, $this->disponible($a));
        $this->assertSame(2, $this->disponible($b));
    }

    public function test_server_ids_marca_no_resueltos_con_null(): void
    {
        $id = $this->product('ZZBOT SERVER', ['server_id' => '990001']);
        DB::table('color')->where('id', $this->colorId)->update(['server_id' => '30']);
        DB::table('size')->where('id', $this->sizeId)->update(['server_id' => '31']);

        $this->chatbot('despacho/server-ids', ['items' => [
            ['product_id' => $id, 'color_id' => $this->colorId, 'size_id' => $this->sizeId],
            ['product_id' => 999999999, 'color_id' => $this->colorId, 'size_id' => $this->sizeId],
        ]])
            ->assertOk()
            ->assertJsonPath('items.0.product_server_id', 990001)
            ->assertJsonPath('items.0.resuelto', true)
            ->assertJsonPath('items.1.product_server_id', null)
            ->assertJsonPath('items.1.resuelto', false);
    }

    public function test_geo_distritos_expone_cobertura(): void
    {
        $departmentId = DB::table('department')->insertGetId(['name' => 'ZZBOT DEP', 'created_at' => now()]);
        $provinceId = DB::table('province')->insertGetId(['name' => 'ZZBOT PROV', 'department_id' => $departmentId, 'created_at' => now()]);
        $districtId = DB::table('district')->insertGetId([
            'name' => 'ZZBOT DIST', 'province_id' => $provinceId, 'status' => '1', 'ubigeo' => '999999',
            'sendCost' => 12, 'excess' => 2, 'excessFactor' => 3, 'created_at' => now(),
        ]);

        $distrito = collect($this->getJson('/api/chatbot/geo/distritos', ['X-API-Key' => self::KEY])->json('distritos'))
            ->firstWhere('id', $districtId);

        $this->assertSame(['location_code' => '999999', 'send_cost' => 12.0, 'available' => 1],
            array_intersect_key($distrito, array_flip(['location_code', 'send_cost', 'available'])));
    }

    public function test_geo_zonas_devuelve_status_entero(): void
    {
        $this->getJson('/api/chatbot/geo/zonas', ['X-API-Key' => self::KEY])
            ->assertOk()
            ->assertJsonStructure(['zonas' => [['id', 'server_id', 'name', 'price', 'status']]])
            ->assertJsonPath('zonas.0.status', 1);
    }
}
