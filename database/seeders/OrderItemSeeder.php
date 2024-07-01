<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 50) as $id) {
            for ($i = 1; $i <= 2; $i++) {
                OrderItem::factory()->create([
                    'order_id' => $id,
                ]);
            }
        }
    }
}
