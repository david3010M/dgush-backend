<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ZoneResource",
 *     type="object",
 *     title="ZoneResource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Zona 1"),
 *     @OA\Property(property="sendCost", type="number", example="10.5")
 * )
 *
 * @OA\Schema(
 *     schema="ZoneCollection",
 *     type="object",
 *     title="ZoneCollection",
 *     @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ZoneResource")),
 *     @OA\Property(property="links", type="object", ref="#/components/schemas/PaginationLinks"),
 *     @OA\Property(property="meta", type="object", ref="#/components/schemas/PaginationMeta")
 *
 * )
 */
class ZoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sendCost' => $this->sendCost,
        ];
    }
}
