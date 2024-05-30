<?php

namespace Database\Seeders;

use App\Models\WishItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WishItemSeeder extends Seeder
{
    protected $model = WishItem::class;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            ['user_id' => 1, 'product_details_id' => 1],
            ['user_id' => 1, 'product_details_id' => 2],
            ['user_id' => 1, 'product_details_id' => 3],
            ['user_id' => 1, 'product_details_id' => 4],
            ['user_id' => 1, 'product_details_id' => 5],
            ['user_id' => 1, 'product_details_id' => 6],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
