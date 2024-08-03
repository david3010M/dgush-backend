<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lastOrder = Order::orderBy('id', 'desc')->first();
        $lastNumber = $lastOrder ? (int)$lastOrder->number : 0;

        $orders = Order::factory()->count(50)->make(); // Generate 50 orders without saving

        foreach ($orders as $order) {
            $lastNumber++;
            $order->number = str_pad($lastNumber, 9, "0", STR_PAD_LEFT);
            $order->save();
        }
    }
}
