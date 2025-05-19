<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\SendInformation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SendInformationSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            $sendInformation = SendInformation::factory()->create([
                'order_id' => $order->id,
            ]);

            if ($order->status !== 'VERIFICANDO') {
                $order->update([
                    'deliveryDate' => $sendInformation->method === 'delivery' ? Carbon::parse($order->date)->addDays(3) : null,
                    'shippingDate' => $sendInformation->method === 'pickup' ? Carbon::parse($order->date)->addDays(3) : null,
                ]);
            }
        }
    }

}
