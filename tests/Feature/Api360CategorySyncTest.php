<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Services\Api360Service;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class Api360CategorySyncTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_syncs_category_photo_from_360_response(): void
    {
        Http::fake([
            'https://sistema.360sys.com.pe/api/online-store/categories' => Http::response([
                'status' => 200,
                'data' => [
                    'categories' => [
                        [
                            'id' => 527,
                            'name' => 'PANTALONES',
                            'status' => true,
                            'order_code' => '00000002',
                            'level' => 1,
                            'photo' => 'https://cdn.example.com/categories/pantalones.jpg',
                            'category_id' => null,
                        ],
                    ],
                ],
            ], 200),
        ]);

        $result = app(Api360Service::class)->fetch_category('test-uuid');

        $this->assertTrue($result['status']);
        $this->assertDatabaseHas('category', [
            'server_id' => 527,
            'name' => 'PANTALONES',
            'photo' => 'https://cdn.example.com/categories/pantalones.jpg',
        ]);

        $this->assertSame(
            'https://cdn.example.com/categories/pantalones.jpg',
            Category::firstWhere('server_id', 527)->photo
        );
    }
}
