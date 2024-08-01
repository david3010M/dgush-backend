<?php

namespace App\Http\Controllers;

use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BannerController extends Controller
{
    /**
     * @OA\Get (
     *     path="/dgush-backend/public/api/banner",
     *     tags={"Banner"},
     *     summary="Obtener todos los banners",
     *     @OA\Response(response="200", description="Devuelve todos los banners", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Banner"))),
     *     @OA\Response(response="401", description="No autorizado", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function index()
    {
        return response()->json(BannerResource::collection(Banner::all()));
    }

    /**
     * @OA\Post (
     *     path="/dgush-backend/public/api/banner",
     *     tags={"Banner"},
     *     summary="Crear un banner",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Banner creado correctamente", @OA\JsonContent(ref="#/components/schemas/Banner")),
     *     @OA\Response(response="422", description="Error de validación", @OA\JsonContent(ref="#/components/schemas/ValidationError")),
     *     @OA\Response(response="401", description="No autorizado", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     *
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('banners', 'name')->whereNull('deleted_at')
            ],
            'image' => 'required|image'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $image = $request->file('image');

        $fileName = 'banner/' . $image->getClientOriginalName();
        Storage::disk('spaces')->put($fileName, file_get_contents($image), 'public');
        $imageUrl = Storage::disk('spaces')->url($fileName);

        $image = Image::create([
            'name' => $fileName,
            'url' => $imageUrl,
        ]);

        $data = [
            'type' => 'image',
            'name' => $request->input('name'),
            'route' => $image->url,
            'image_id' => $image->id
        ];

        $banner = Banner::create($data);


        return response()->json(new BannerResource($banner));
    }


    /**
     * @OA\Get (
     *     path="/dgush-backend/public/api/banner/{id}",
     *     tags={"Banner"},
     *     summary="Obtener un banner",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID del banner", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Devuelve un banner", @OA\JsonContent(ref="#/components/schemas/Banner")),
     *     @OA\Response(response="404", description="Banner no encontrado", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Banner not found"))),
     *     @OA\Response(response="401", description="No autorizado", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function show(int $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json(['message' => 'Banner not found'], 404);
        }

        return response()->json(new BannerResource($banner));
    }

    /**
     * @OA\Delete (
     *     path="/dgush-backend/public/api/banner/{id}",
     *     tags={"Banner"},
     *     summary="Eliminar un banner",
     *     @OA\Parameter(name="id", in="path", required=true, description="ID del banner", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Banner eliminado correctamente", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Banner eliminado correctamente"))),
     *     @OA\Response(response="404", description="Banner no encontrado", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Banner not found"))),
     *     @OA\Response(response="400", description="Error al eliminar la imagen", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="Error al eliminar la imagen"))),
     *     @OA\Response(response="401", description="No autorizado", @OA\JsonContent(ref="#/components/schemas/Unauthenticated"))
     * )
     */
    public function destroy(int $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json(['message' => 'Banner not found'], 404);
        }

        $response = Storage::disk('spaces')->delete('banner/' . basename($banner->image->url));

        if (!$response) {
            return response()->json(['message' => 'Error al eliminar la imagen'], 400);
        }

        $banner->delete();

        return response()->json(['message' => 'Banner eliminado correctamente']);
    }
}
