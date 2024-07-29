<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="Banner",
 *     title="Banner",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="type", type="string", example="image"),
 *     @OA\Property(property="name", type="string", example="Verano"),
 *     @OA\Property(property="route", type="string", example="https://dgush-storage.sfo3.digitaloceanspaces.com/banner/verano.png")
 * )
 */
class BannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'name' => $this->name,
            'route' => $this->route,
        ];
    }
}
