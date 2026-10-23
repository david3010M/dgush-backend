<?php

namespace Tests\Feature;

use App\Services\Chatbot\CatalogoService;
use RuntimeException;
use Tests\TestCase;

/**
 * Reglas transversales de /api/chatbot: auth por X-API-Key y cuerpo JSON
 * en todo error (400/401/404/500), sin filtrar mensajes de MySQL.
 */
class ChatbotApiAccessTest extends TestCase
{
    private const KEY = 'chatbot-test-key';

    protected function setUp(): void
    {
        parent::setUp();
        config(['chatbot.api_key' => self::KEY]);
    }

    public function test_sin_api_key_responde_401(): void
    {
        $this->getJson('/api/chatbot/negocio/sedes')
            ->assertStatus(401)
            ->assertExactJson(['error' => 'no_autorizado']);
    }

    public function test_api_key_incorrecta_responde_401(): void
    {
        $this->getJson('/api/chatbot/negocio/sedes', ['X-API-Key' => 'otra'])
            ->assertStatus(401);
    }

    public function test_sin_api_key_configurada_rechaza_todo(): void
    {
        config(['chatbot.api_key' => null]);

        $this->getJson('/api/chatbot/negocio/sedes', ['X-API-Key' => ''])
            ->assertStatus(401);
    }

    public function test_parametro_faltante_responde_400_con_detalle(): void
    {
        $this->postJson('/api/chatbot/catalogo/fotos', [], ['X-API-Key' => self::KEY])
            ->assertStatus(400)
            ->assertExactJson(['error' => 'parametro_invalido', 'detalle' => 'falta product_id']);
    }

    public function test_tipo_invalido_responde_400(): void
    {
        $this->postJson('/api/chatbot/catalogo/variantes', ['ids' => ['abc']], ['X-API-Key' => self::KEY])
            ->assertStatus(400)
            ->assertExactJson(['error' => 'parametro_invalido', 'detalle' => 'ids.0 invalido']);
    }

    public function test_ruta_inexistente_responde_json(): void
    {
        $this->get('/api/chatbot/no-existe', ['X-API-Key' => self::KEY])
            ->assertStatus(404)
            ->assertExactJson(['error' => 'no_encontrado']);
    }

    public function test_error_interno_no_expone_detalle_de_bd(): void
    {
        $this->mock(CatalogoService::class)
            ->shouldReceive('liquidacion')
            ->andThrow(new RuntimeException('SQLSTATE[42S02] tabla secreta'));

        $response = $this->get('/api/chatbot/catalogo/liquidacion', ['X-API-Key' => self::KEY])
            ->assertStatus(500)
            ->assertExactJson(['error' => 'error_interno']);

        $this->assertStringNotContainsString('SQLSTATE', $response->getContent());
    }
}
