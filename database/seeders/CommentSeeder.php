<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * id
     * description
     * score
     * user_id -> Just 1
     * product_id -> 1-30
     */
    protected $model = Comment::class;

    public function run(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            Comment::create([
                'description' => 'Comentario de prueba para el producto ' . $i,
                'score' => 5,
                'user_id' => 1,
                'product_id' => $i,
            ]);
        }
    }
}
