<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexZoneRequest;
use App\Http\Resources\ZoneResource;
use App\Models\Zone;
use App\Http\Requests\StoreZoneRequest;
use App\Http\Requests\UpdateZoneRequest;

class ZoneController extends Controller
{
    /**
     * @OA\Get (
     *     path="/dgush-backend/public/api/zone",
     *     tags={"Zone"},
     *     summary="Get all zones",
     *     description="Get all zones",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="all", name="all", in="query", required=false, description="Get all zones", @OA\Schema(type="boolean")),
     *     @OA\Parameter(parameter="page", name="page", in="query", required=false, description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="per_page", name="per_page", in="query", required=false, description="Items per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(parameter="sort", name="sort", in="query", required=false, description="Sort by column", @OA\Schema(type="string", enum={"name", "sendCost"})),
     *     @OA\Parameter(parameter="direction", name="direction", in="query", required=false, description="Sort direction", @OA\Schema(type="string", enum={"asc", "desc"})),
     *     @OA\Parameter(parameter="name", name="name", in="query", required=false, description="Filter by name", @OA\Schema(type="string")),
     *     @OA\Parameter(parameter="sendCost", name="sendCost", in="query", required=false, description="Filter by sendCost", @OA\Schema(type="number")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/ZoneCollection")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function index(IndexZoneRequest $request)
    {
        return $this->getFilteredResults(
            Zone::class,
            $request,
            Zone::filters,
            Zone::sorts,
            ZoneResource::class
        );
    }

    /**
     * @OA\Post (
     *     path="/dgush-backend/public/api/zone",
     *     tags={"Zone"},
     *     summary="Create a zone",
     *     description="Create a zone",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreZoneRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/ZoneResource")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ValidationError"))
     * )
     */
    public function store(StoreZoneRequest $request)
    {
        $zone = Zone::create($request->validated());
        return response()->json(new ZoneResource($zone));
    }

    /**
     * @OA\Get (
     *     path="/dgush-backend/public/api/zone/{id}",
     *     tags={"Zone"},
     *     summary="Get zone by id",
     *     description="Get zone by id",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Zone id", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/ZoneResource")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Not found", @OA\JsonContent( @OA\Property(property="error", type="string", example="Zona no encontrada")))
     * )
     */
    public function show(int $id)
    {
        $zone = Zone::find($id);
        if (!$zone) return response()->json(['error' => 'Zona no encontrada'], 404);
        return response()->json(new ZoneResource($zone));
    }

    /**
     * @OA\Put (
     *     path="/dgush-backend/public/api/zone/{id}",
     *     tags={"Zone"},
     *     summary="Update a zone",
     *     description="Update a zone",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Zone id", @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateZoneRequest")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/ZoneResource")),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Not found", @OA\JsonContent( @OA\Property(property="error", type="string", example="Zona no encontrada")))
     * )
     */
    public function update(UpdateZoneRequest $request, int $id)
    {
        $zone = Zone::find($id);
        if (!$zone) return response()->json(['error' => 'Zona no encontrada'], 404);
        $zone->update($request->validated());
        return response()->json(new ZoneResource($zone));
    }

    /**
     * @OA\Delete (
     *     path="/dgush-backend/public/api/zone/{id}",
     *     tags={"Zone"},
     *     summary="Delete a zone",
     *     description="Delete a zone",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(parameter="id", name="id", in="path", required=true, description="Zone id", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent( @OA\Property(property="message", type="string", example="Zona eliminada"))),
     *     @OA\Response(response=401, description="Unauthenticated", @OA\JsonContent(ref="#/components/schemas/Unauthenticated")),
     *     @OA\Response(response=404, description="Not found", @OA\JsonContent( @OA\Property(property="error", type="string", example="Zona no encontrada"))),
     *     @OA\Response(response=409, description="Conflict", @OA\JsonContent( @OA\Property(property="error", type="string", example="No se puede eliminar la zona porque tiene pedidos asociados")))
     * )
     */
    public function destroy(int $id)
    {
        $zone = Zone::find($id);
        if (!$zone) return response()->json(['error' => 'Zona no encontrada'], 404);
        if ($zone->sendInformation()->count() > 0) return response()->json(['error' => 'No se puede eliminar la zona porque tiene pedidos asociados'], 409);
        $zone->delete();
        return response()->json(['message' => 'Zona eliminada']);
    }
}
