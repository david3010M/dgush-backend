<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => "Video Home",
            'value' => "https://www.youtube.com/embed/06wZsa-55UE?si=oAHZA5JVGNDVynZV",
            'active' => true,
        ];
    }
}
