<?php

namespace Tests\Feature\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Testing\TestResponse;

/**
 * Datos de prueba de /api/chatbot sobre la BD local real (usar con DatabaseTransactions).
 * Prefijo ZZBOT para no chocar con lo sincronizado desde 360Sys.
 *
 * La clase que lo usa define KEY y SUBCATEGORIA (constantes en traits exigen PHP 8.2; composer admite 8.1).
 */
trait ChatbotFixtures
{
    private int $subcategoryId;
    private int $colorId;
    private int $sizeId;

    protected function setUpChatbotFixtures(): void
    {
        config(['chatbot.api_key' => self::KEY]);

        $now = now();
        $categoryId = DB::table('category')->insertGetId(['name' => 'ZZBOT', 'created_at' => $now]);
        $this->subcategoryId = DB::table('subcategory')->insertGetId([
            'name' => self::SUBCATEGORIA, 'category_id' => $categoryId, 'created_at' => $now,
        ]);
        $this->colorId = DB::table('color')->insertGetId(['name' => 'ZZBOT CELESTE', 'hex' => '#AEE', 'created_at' => $now]);
        $this->sizeId = DB::table('size')->insertGetId(['name' => 'ZZ32', 'value' => 'zz32', 'created_at' => $now]);
    }

    private function product(string $name, array $overrides = [], float $stock = 5): int
    {
        $id = DB::table('product')->insertGetId(array_merge([
            'name' => $name,
            'description' => '',
            'detailweb' => '',
            'price1' => 65,
            'price2' => 60,
            'price12' => null,
            'priceLiquidacion' => 52,
            'liquidacion' => true,
            'status_server' => '1',
            'subcategory_id' => $this->subcategoryId,
            'currency' => 'PEN',
            'created_at' => now(),
        ], $overrides));

        DB::table('product_details')->insert([
            'product_id' => $id, 'color_id' => $this->colorId, 'size_id' => $this->sizeId,
            'stock' => $stock, 'status' => true, 'created_at' => now(),
        ]);

        return $id;
    }

    private function chatbot(string $path, array $body = [], array $headers = []): TestResponse
    {
        return $this->postJson("/api/chatbot/{$path}", $body, ['X-API-Key' => self::KEY] + $headers);
    }
}
