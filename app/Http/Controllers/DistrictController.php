<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Services\Api360Service;
use Illuminate\Http\Request;

class DistrictController extends Controller
{

    protected $api360Service;

    // Inyectamos el servicio en el controlador
    public function __construct(Api360Service $api360Service)
    {
        $this->api360Service   = $api360Service;
    }

    /**
     * @OA\Get(
     *     path="/dgush-backend/public/api/district",
     *     tags={"District"},
     *     summary="Get all districts",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/District")
     *     )
     * )
     *
     */
    public function index()
    {
        return District::all();
    }

    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name' => 'required|string',
            'province_id' => 'required|integer',
            'sendCost' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = [
            'name' => $request->input('name'),
            'province_id' => $request->input('province_id'),
            'sendCost' => $request->input('sendCost'),
        ];

        $district = District::create($data);
        $district = District::find($district->id);

        return response()->json($district);
    }

    /**
     * @OA\Get(
     *     path="/dgush-backend/public/api/district/{id}",
     *     tags={"District"},
     *     summary="Get district by id",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID of district", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/District")
     *     )
     * )
     */
    public function show(int $id)
    {
        $district = District::find($id);

        if (!$district) {
            return response()->json(['error' => 'District not found'], 404);
        }

        return response()->json($district);
    }

    public function update(Request $request, int $id)
    {
        $district = District::find($id);

        if (!$district) {
            return response()->json(['error' => 'District not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'name' => 'nullable|string',
            'province_id' => 'nullable|integer',
            'sendCost' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = [
            'name' => $request->input('name', $district->name),
            'province_id' => $request->input('province_id', $district->province_id),
            'sendCost' => $request->input('sendCost', $district->sendCost),
        ];

        $district->update($data);
        $district = District::find($district->id);

        return response()->json($district);
    }

    public function destroy(int $id)
    {
        $district = District::find($id);

        if (!$district) {
            return response()->json(['error' => 'District not found'], 404);
        }

        if ($district->send_information()->count() > 0) {
            return response()->json(['error' => 'District is used in send information for order'], 409);
        }

        $district->delete();

        return response()->json(['message' => 'District deleted']);
    }

    public function getdistricts(Request $request)
    {
        $uuid = $request->input('uuid', '');
        $data = $this->api360Service->fetch_districts($uuid);

        return response()->json($data); // Devolvemos la respuesta
    }
}


