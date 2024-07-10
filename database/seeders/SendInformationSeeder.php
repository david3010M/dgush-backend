<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use App\Models\SendInformation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SendInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 50) as $id) {
            SendInformation::factory()->create([
                'order_id' => $id,
            ]);
        }
    }
}
