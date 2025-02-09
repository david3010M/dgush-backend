<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Image;
use App\Models\Subcategory;
use App\Services\Api360Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SubcategoryController extends Controller
{
    protected $api360Service;

    // Inyectamos el servicio en el controlador
    public function __construct(Api360Service $api360Service)
    {
        $this->api360Service = $api360Service;
    }
    /**
     * SHOW ALL SUBCATEGORIES
     * @OA\Get(
     *     path="/dgush-backend/public/api/subcategory",
     *     summary="Get all subcategories",
     *     tags={"Subcategory"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of all subcategories",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Subcategory")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *        )
     *     )
     * )
     */
    public function index()
    {
        return Subcategory::all();
    }

    /**
     * @OA\Get (
     *     path="/dgush-backend/public/api/subcategory/search",
     *     tags={"Subcategory"},
     *     summary="Search subcategories",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter (name="search", in="query", required=false, @OA\Schema(type="string"), example="T-Shirts"),
     *     @OA\Parameter (name="sort", in="query", required=false, @OA\Schema(type="string"), example="name"),
     *     @OA\Parameter (name="direction", in="query", required=false, @OA\Schema(type="string"), example="asc"),
     *     @OA\Parameter (name="all", in="query", required=false, @OA\Schema(type="boolean"), example="false"),
     *     @OA\Response (response="200", description="Subcategories found", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Subcategory"))),
     *     @OA\Response (response="404", description="Subcategories not found", @OA\JsonContent(@OA\Property(property="message", type="string", example="Subcategories not found"))),
     *     @OA\Response (response="401", description="Unauthorized", @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))),
     * )
     */
    public function search()
    {
        $subcategories = Subcategory::search(
            request('search'),
            request('sort', 'score'),
            request('direction', 'desc'),
            request('all', false)
        );
        return response()->json($subcategories);
    }

    /**
     * CREATE A NEW SUBCATEGORY
     * @OA\Post(
     *     path="/dgush-backend/public/api/subcategory",
     *     summary="Create a new subcategory",
     *     tags={"Subcategory"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(ref="#/components/schemas/SubcategoryRequest")
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Subcategory created",
     *         @OA\JsonContent(ref="#/components/schemas/Subcategory")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'name'        => [
                'required',
                'string',
                Rule::unique('subcategory', 'name')->whereNull('deleted_at'),
            ],
            'category_id' => 'required|integer|exists:category,id',
            'image'       => 'required|image',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $name  = $request->input('name');
        $value = strtolower(str_replace(' ', '-', $name));

        $data = [
            'name'        => $name,
            'value'       => $value,
            'category_id' => $request->category_id,
        ];

        $subcategory = Subcategory::create($data);

        if ($request->hasFile('image')) {
            $image    = $request->file('image');
            $fileName = 'Subcategories/' . $subcategory->id . '/' . $image->getClientOriginalName();
            Storage::disk('spaces')->put($fileName, file_get_contents($image), 'public');
            $imageUrl = Storage::disk('spaces')->url($fileName);

            Image::create([
                'name'           => $fileName,
                'url'            => $imageUrl,
                'subcategory_id' => $subcategory->id,
            ]);

            $subcategory->image = $imageUrl;
            $subcategory->save();
        }

        $subcategory = Subcategory::find($subcategory->id);

        return response()->json($subcategory);
    }

    /**
     * SHOW A SUBCATEGORY
     * @OA\Get(
     *     path="/dgush-backend/public/api/subcategory/{id}",
     *     summary="Get a subcategory",
     *     tags={"Subcategory"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the subcategory",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subcategory found",
     *         @OA\JsonContent(ref="#/components/schemas/Subcategory")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Subcategory not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subcategory not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $subcategory = Subcategory::find($id)->load('products');

        if ($subcategory) {
            return $subcategory;
        } else {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }
    }

    /**
     * UPDATE A SUBCATEGORY
     * @OA\Put(
     *     path="/dgush-backend/public/api/subcategory/{id}",
     *     summary="Update a subcategory",
     *     tags={"Subcategory"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the subcategory",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref="#/components/schemas/SubcategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subcategory updated",
     *         @OA\JsonContent(ref="#/components/schemas/Subcategory")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Subcategory not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subcategory not found")
     *         )
     *     )
     * )
     *
     */
    public function update(Request $request, int $id)
    {
        $subcategory = Subcategory::find($id);

        if ($subcategory) {
            $validator = validator()->make($request->all(), [
                'name'        => [
                    'nullable',
                    'string',
                    Rule::unique('subcategory', 'name')->ignore($subcategory->id)->whereNull('deleted_at'),
                ],
                'isHome'      => 'nullable',
                'category_id' => 'nullable|integer|exists:category,id',
                'image'       => 'nullable|image',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 422);
            }

            $name  = $request->input('name') ?? $subcategory->name;
            $value = strtolower(str_replace(' ', '-', $name));

            $data = [
                'name'        => $name,
                'value'       => $value,
                'isHome'      => $request->input('isHome') == 'true' ?? $subcategory->isHome,
                'category_id' => $request->input('category_id') ?? $subcategory->category_id,
            ];

            $subcategory->update($data);

            if ($request->hasFile('image')) {
                Storage::disk('spaces')->deleteDirectory('Subcategories/' . $id . "/");
                Image::where('subcategory_id', $id)->delete();

                $image    = $request->file('image');
                $fileName = 'Subcategories/' . $subcategory->id . '/' . $image->getClientOriginalName();
                Storage::disk('spaces')->put($fileName, file_get_contents($image), 'public');
                $imageUrl = Storage::disk('spaces')->url($fileName);

                Image::create([
                    'name'           => $fileName,
                    'url'            => $imageUrl,
                    'subcategory_id' => $subcategory->id,
                ]);

                $subcategory->image = $imageUrl;
                $subcategory->save();
            }

            $subcategory = Subcategory::find($subcategory->id);

            return response()->json($subcategory);
        } else {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }
    }

    /**
     * DELETE A SUBCATEGORY
     * @OA\Delete(
     *     path="/dgush-backend/public/api/subcategory/{id}",
     *     summary="Delete a subcategory",
     *     tags={"Subcategory"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Subcategory ID",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subcategory deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subcategory deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Subcategory not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subcategory not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Subcategory has products",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subcategory has products")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $subcategory = Subcategory::find($id);

        if ($subcategory) {
//            VERIFY IF SUBCATEGORY HAS PRODUCTS
            if ($subcategory->products()->count() > 0) {
                return response()->json(['message' => 'Subcategory has products'], 409);
            }

            $subcategory->delete();
            return response()->json(['message' => 'Subcategory deleted']);
        } else {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/dgush-backend/public/api/subcategoryMostPopular",
     *     summary="Most popular subcategories",
     *     tags={"Subcategory"},
     *     @OA\Response(
     *          response=200,
     *          description="List of most popular subcategories",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Subcategory")
     *          )
     *     )
     * )
     */
    public function mostPopular()
    {
        $subcategories = Subcategory::orderBy('score', 'desc')
            ->limit(5)
            ->get();

        return $subcategories;
    }

    public function getSubCategories(Request $request)
    {
        $uuid = $request->input('uuid', '');
        $data = $this->api360Service->fetch_subcategory($uuid);

        return response()->json($data); // Devolvemos la respuesta
    }
}
