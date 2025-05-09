<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexSedeRequest;
use App\Http\Resources\SedeResource;
use App\Models\Sede;
use App\Http\Requests\StoreSedeRequest;
use App\Http\Requests\UpdateSedeRequest;
use App\Services\Api360Service;
use App\Traits\Filterable;
use Illuminate\Http\Request;


class SedeController extends Controller
{
    use  Filterable;

    protected $api360Service;

    // Inyectamos el servicio en el controlador
    public function __construct(Api360Service $api360Service)
    {
        $this->api360Service   = $api360Service;
    }

    public function index(IndexSedeRequest $request)
    {
        return $this->getFilteredResults(
            Sede::class,
            $request,
            Sede::filters,
            Sede::sorts,
            SedeResource::class
        );
    }

    public function store(StoreSedeRequest $request)
    {
        $sede = Sede::create($request->validated());
        return response()->json(new SedeResource($sede));
    }

    public function show(int $id)
    {
        $sede = Sede::find($id);
        if (!$sede) return response()->json(['message' => 'Sede not found'], 404);
        return response()->json(new SedeResource($sede));
    }

    public function update(UpdateSedeRequest $request, int $id)
    {
        $sede = Sede::find($id);
        if (!$sede) return response()->json(['message' => 'Sede not found'], 404);
        $sede->update($request->validated());
        return response()->json(new SedeResource($sede));
    }

    public function destroy(int $id)
    {
        $sede = Sede::find($id);
        if (!$sede) return response()->json(['message' => 'Sede not found'], 404);
        $sede->delete();
        return response()->json(['message' => 'Sede deleted']);
    }

    public function getsedes(Request $request)
    {
        $uuid = $request->input('uuid', '');
        $data = $this->api360Service->fetch_sedes($uuid);

        return response()->json($data); // Devolvemos la respuesta
    }
}
