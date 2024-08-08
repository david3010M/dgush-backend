<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Order;
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

            $orderItems = OrderItem::where('order_id', $id)->get();
            $order = Order::find($id);
            $order->subtotal = $orderItems->sum(fn($orderItem) => $orderItem->price * $orderItem->quantity);
            if ($order->coupon_id) {
                $coupon = Coupon::find($order->coupon_id);
                if ($coupon) {
                    if ($coupon->type === 'percentage') {
                        if ($coupon->indicator === 'subtotal') {
                            $order->discount = $order->subtotal * $coupon->value / 100;
                        } elseif ($coupon->indicator === 'total') {
                            $order->discount = $order->total * $coupon->value / 100;
                        } elseif ($coupon->indicator === 'sendCost') {
                            $order->discount = $order->sendCost * $coupon->value / 100;
                        }
                    } else {
                        $order->discount = $coupon->value;
                    }
                } else {
                    $order->coupon_id = null;
                    $order->discount = 0;
                }

            }
            $order->total = $order->subtotal - $order->discount + $order->sendCost;
            $order->save();
        }
    }
}
