<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
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
        return District::find($id);
    }

    public function update(Request $request, int $id)
    {

    }

    public function destroy(int $id)
    {

    }
}
