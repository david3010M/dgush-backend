<?php

namespace Database\Seeders;

use App\Models\SendInformation;
use Illuminate\Database\Seeder;

class SendInformationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 50) as $id) {
            SendInformation::factory()->create([
                'order_id' => $id,
            ]);
        }
    }
}
