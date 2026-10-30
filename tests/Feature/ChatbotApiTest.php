<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Concerns\ChatbotFixtures;
use Tests\TestCase;

/**
 * Endpoints de /api/chatbot contra la BD local real con DatabaseTransactions
 * (rollback al terminar). Datos de prueba: ChatbotFixtures.
 */
class ChatbotApiTest extends TestCase
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

    public function test_buscar_categoria_castea_precios_y_respeta_null(): void
    {
        $id = $this->product('ZZBOT SHORT MON');
        DB::table('image')->insert(['name' => 'a', 'url' => 'https://img/1.jpg', 'product_id' => $id, 'created_at' => now()]);

        $this->chatbot('catalogo/buscar-categoria', ['subcategoria' => self::SUBCATEGORIA])
            ->assertOk()
            ->assertHeader('Content-Type', 'application/json; charset=utf-8')
            ->assertExactJson(['productos' => [[
                'id' => $id, 'name' => 'ZZBOT SHORT MON', 'price1' => 65.0, 'price2' => 60.0,
                'price12' => null, 'priceLiquidacion' => 52.0, 'liquidacion' => 1,
                'subcategoria' => self::SUBCATEGORIA, 'imagen_url' => 'https://img/1.jpg',
            ]]]);
    }

    public function test_productos_ocultos_en_tienda_no_se_ofrecen(): void
    {
        $this->product('ZZBOT OCULTO', ['status_server' => '0']);

        $this->chatbot('catalogo/buscar-nombre', ['termino' => 'ZZBOT OCULTO'])
            ->assertOk()
            ->assertExactJson(['productos' => []]);
    }

    public function test_buscar_nombre_marca_agotados_con_con_stock(): void
    {
        $this->product('ZZBOT AGOTADO', [], 0);

        $this->chatbot('catalogo/buscar-nombre', ['termino' => 'ZZBOT AGOTADO'])
            ->assertOk()
            ->assertJsonPath('productos.0.con_stock', 0);
    }

    public function test_buscar_palabras_no_expone_score(): void
    {
        $this->product('ZZBOT PALABRA');

        $row = $this->chatbot('catalogo/buscar-palabras', ['palabras' => ['ZZBOT PAL', 'nada']])
            ->assertOk()
            ->json('posibles_coincidencias.0');

        $this->assertSame(['id', 'name', 'subcategoria', 'imagen_url'], array_keys($row));
    }

    public function test_variantes_agrupa_tallas_por_color(): void
    {
        $id = $this->product('ZZBOT VARIANTE');

        $this->chatbot('catalogo/variantes', ['ids' => [$id]])
            ->assertOk()
            ->assertExactJson(['variantes' => [[
                'product_id' => $id, 'color_id' => $this->colorId, 'color' => 'ZZBOT CELESTE',
                'tallas' => [['size_id' => $this->sizeId, 'nombre' => 'ZZ32']],
            ]]]);
    }

    public function test_multifiltro_omite_filtros_ausentes(): void
    {
        $this->product('ZZBOT FILTRO');

        $this->chatbot('catalogo/multifiltro', ['subcategoria' => self::SUBCATEGORIA, 'color' => 'celes'])
            ->assertOk()
            ->assertJsonCount(1, 'productos');

        $this->chatbot('catalogo/multifiltro', ['subcategoria' => self::SUBCATEGORIA, 'talla' => 'XX'])
            ->assertOk()
            ->assertExactJson(['productos' => []]);
    }

    public function test_alternativas_excluye_ids(): void
    {
        $excluido = $this->product('ZZBOT ALT A');
        $otro = $this->product('ZZBOT ALT B');

        $ids = $this->chatbot('catalogo/alternativas', [
            'subcats_grupo' => [self::SUBCATEGORIA],
            'subcat_original' => self::SUBCATEGORIA,
            'ids_excluir' => [$excluido],
        ])->assertOk()->json('alternativas.*.id');

        $this->assertSame([$otro], $ids);
    }

    public function test_verificar_y_verificar_stock(): void
    {
        $id = $this->product('ZZBOT STOCK', [], 7);
        $variante = ['product_id' => $id, 'color_id' => $this->colorId, 'size_id' => $this->sizeId];

        $this->chatbot('disponibilidad/verificar', $variante)->assertExactJson(['disponible' => 1, 'stock' => 7]);
        $this->chatbot('carrito/verificar-stock', $variante + ['quantity' => 3])->assertExactJson(['suficiente' => 1, 'stock' => 7]);
        $this->chatbot('carrito/verificar-stock', $variante + ['quantity' => 8])->assertExactJson(['suficiente' => 0, 'stock' => 7]);
        $this->chatbot('disponibilidad/verificar', ['size_id' => 999999] + $variante)->assertExactJson(['disponible' => 0, 'stock' => 0]);
    }

    public function test_precio_de_producto_inexistente_es_null(): void
    {
        $this->chatbot('precios/producto', ['product_id' => 999999999])
            ->assertOk()
            ->assertExactJson(['producto' => null]);
    }

    public function test_beneficio_display_segun_tipo_real_de_cupon(): void
    {
        $base = ['description' => '', 'active' => true, 'expires_at' => now()->addDay(), 'created_at' => now()];
        DB::table('coupon')->insert([
            $base + ['name' => 'a', 'code' => 'ZZBOT-PCT', 'type' => 'percentage', 'indicator' => 'subtotal', 'value' => 10],
            $base + ['name' => 'b', 'code' => 'ZZBOT-FIJO', 'type' => 'discount', 'indicator' => 'subtotal', 'value' => 15.5],
            $base + ['name' => 'c', 'code' => 'ZZBOT-ENVIO', 'type' => 'percentage', 'indicator' => 'sendCost', 'value' => 100],
        ]);

        $display = fn (string $code) => $this->chatbot('negocio/validar-cupon', ['codigo' => $code])->json('cupon.beneficio_display');

        $this->assertSame('10% de descuento', $display('ZZBOT-PCT'));
        $this->assertSame('S/ 15.50 de descuento', $display('ZZBOT-FIJO'));
        $this->assertSame('Envío gratis', $display('ZZBOT-ENVIO'));
        $this->chatbot('precios/validar-cupon', ['codigo' => 'ZZBOT-PCT'])->assertJsonPath('cupon.es_valido', 1);
    }

    public function test_costo_envio_solo_distritos_con_cobertura(): void
    {
        $departmentId = DB::table('department')->insertGetId(['name' => 'ZZBOT DEP', 'created_at' => now()]);
        $provinceId = DB::table('province')->insertGetId(['name' => 'ZZBOT PROV', 'department_id' => $departmentId, 'created_at' => now()]);
        $district = ['province_id' => $provinceId, 'status' => '1', 'created_at' => now()];
        DB::table('district')->insert([
            $district + ['name' => 'ZZBOT CON COBERTURA', 'sendCost' => 12, 'excess' => 2, 'excessFactor' => 3],
            $district + ['name' => 'ZZBOT SIN COBERTURA', 'sendCost' => 0, 'excess' => 0, 'excessFactor' => 0],
        ]);

        $this->chatbot('negocio/costo-envio', ['ciudad' => 'ZZBOT'])
            ->assertOk()
            ->assertExactJson(['resultados' => [[
                'tipo' => 'envio_nacional', 'lugar' => 'ZZBOT CON COBERTURA, ZZBOT PROV, ZZBOT DEP',
                'costo' => 12.0, 'exceso' => 2.0, 'exceso_factor' => 3,
            ]]]);
    }
}
