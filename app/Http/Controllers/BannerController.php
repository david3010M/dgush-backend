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
    public function index()
    {
        return response()->json(BannerResource::collection(Banner::all()));
    }

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
            return response()->json(['error' => $validator->errors()->first()], 400);
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
            'name' => $request->input('name'),
            'route' => $image->url,
            'image_id' => $image->id
        ];

        $banner = Banner::create($data);


        return response()->json(new BannerResource($banner));
    }

    public function show(int $id)
    {
        $banner = Banner::find($id);

        if (!$banner) {
            return response()->json(['message' => 'Banner not found'], 404);
        }

        return response()->json(new BannerResource($banner));
    }

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
