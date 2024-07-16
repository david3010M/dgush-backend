<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{

    /**
     * LIST IMAGES FROM STORAGE
     * @OA\Get (
     *     path="/dgush-backend/public/api/images",
     *     tags={"Images"},
     *     summary="List images from storage",
     *     description="List images from storage",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of images",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(type="string", example="1/image.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function listImages()
    {
        $disk = Storage::disk('spaces');
        $files = $disk->allFiles();

        return response()->json($files);
    }

    public function deleteDirectoryProduct(Request $request)
    {
        if (auth()->user()->typeuser_id != 1) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $validator = validator()->make($request->all(), [
            'id' => 'required|integer|exists:product,id'
        ]);

//        if ($validator->fails()) {
//            return response()->json(['message' => $validator->errors()->first()], 400);
//        }

        $response = Storage::disk('spaces')->deleteDirectory("/" . $request->id . "/");
        if (!$response) {
            return response()->json(['message' => 'Error al eliminar el directorio'], 400);
        }

        return response()->json(['message' => 'Directorio eliminado correctamente']);
    }

    public function uploadImages(Request $request, int $id)
    {
        //        VALIDATE DATA
        $request->validate(
            [
                'images' => 'required|array',
                'images.*' => 'required|image'
            ]
        );

        //        FIND PRODUCT
        $product = Product::find($id);

        if ($product) {
            $images = $request->file('images');
            $imagesResponse = [];

            //            VALIDATE IMAGE NAME MUST BE UNIQUE IN TABLE IMAGE WITH THE SAME PRODUCT_ID

            foreach ($images as $image) {
                $imageValidate = Image::where('name', $image->getClientOriginalName())
                    ->where('product_id', $id)
                    ->first();
                if ($imageValidate) {
                    return response()->json(['message' => 'Image is already uploaded'], 409);
                }
            }

            foreach ($images as $image) {
                //                UPLOAD IMAGE
                $fileName = $id . '/' . $image->getClientOriginalName();
                Storage::disk('spaces')->put($fileName, file_get_contents($image), 'public');

                //                GET IMAGE URLs
                $imageUrl = Storage::disk('spaces')->url($fileName);

                $image = Image::create([
                    'name' => $fileName,
                    'url' => $imageUrl,
                    'product_id' => $id
                ]);

                $imagesResponse[] = $image;
            }
            return response()->json($imagesResponse);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }
}
