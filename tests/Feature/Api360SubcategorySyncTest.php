<?php

namespace Tests\Feature;

use App\Models\Subcategory;
use App\Services\Api360Service;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class Api360SubcategorySyncTest extends TestCase
{
    use DatabaseTransactions;

    public function test_it_syncs_category_photo_into_subcategory_image_from_360_response(): void
    {
        Http::fake([
            'https://sistema.360sys.com.pe/api/online-store/categories' => Http::response([
                'status' => 200,
                'data' => [
                    'categories' => [
                        [
                            'id' => 528,
                            'name' => 'SHORTS',
                            'status' => true,
                            'order_code' => '00000004',
                            'level' => 1,
                            'photo' => 'https://cdn.example.com/categories/shorts.jpg',
                            'category_id' => null,
                        ],
                    ],
                ],
            ], 200),
        ]);

        $result = app(Api360Service::class)->fetch_subcategory('test-uuid');

        $this->assertTrue($result['status']);
        $this->assertDatabaseHas('subcategory', [
            'server_id' => 528,
            'name' => 'SHORTS',
            'image' => 'https://cdn.example.com/categories/shorts.jpg',
        ]);

        $this->assertSame(
            'https://cdn.example.com/categories/shorts.jpg',
            Subcategory::firstWhere('server_id', 528)->image
        );
    }
}
