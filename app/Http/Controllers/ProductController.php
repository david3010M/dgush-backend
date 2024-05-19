<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use App\Models\Size;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * SHOW ALL PRODUCTS WITH PAGINATION OF 12
     * @OA\Get(
     *     path="/dgush-backend/public/api/product",
     *     tags={"Product"},
     *     summary="Show all products",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Show all products",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example="1"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="first_page_url", type="string", example="https://develop.garzasoft.com/dgush-backend/public/api/product?page=1"),
     *             @OA\Property(property="from", type="integer", example="1"),
     *             @OA\Property(property="next_page_url", type="string", example="https://develop.garzasoft.com/dgush-backend/public/api/product?page=2"),
     *             @OA\Property(property="path", type="string", example="https://develop.garzasoft.com/dgush-backend/public/api/product"),
     *             @OA\Property(property="per_page", type="integer", example="12"),
     *             @OA\Property(property="prev_page_url", type="string", example="null"),
     *             @OA\Property(property="to", type="integer", example="10"),
     *        )
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Product::simplePaginate(12));
    }

    /**
     * SEARCH PRODUCTS
     * @OA\Post(
     *     path="/api/product/search",
     *     tags={"Product"},
     *     summary="Search products",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"filter"},
     *              @OA\Property(property="filter", type="object",
     *                  @OA\Property(property="search", type="string", example="Product 1"),
     *                  @OA\Property(property="status", type="string", example="onsale"),
     *                  @OA\Property(property="score", type="integer", example="5"),
     *                  @OA\Property(property="category", type="array", @OA\Items(type="string", example="1")),
     *                  @OA\Property(property="price", type="array", @OA\Items(type="number", example="100.00")),
     *                  @OA\Property(property="color", type="array", @OA\Items(type="string", example="Red")),
     *                  @OA\Property(property="size", type="array", @OA\Items(type="string", example="S")),
     *                  @OA\Property(property="sort", type="string", example="id"),
     *                  @OA\Property(property="direction", type="string", example="desc"),
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search products",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example="1"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Product")),
     *             @OA\Property(property="first_page_url", type="string", example="https://develop.garzasoft.com/dgush-backend/public/api/product?page=1"),
     *             @OA\Property(property="from", type="integer", example="1"),
     *             @OA\Property(property="next_page_url", type="string", example="https://develop.garzasoft.com/dgush-backend/public/api/product?page=2"),
     *             @OA\Property(property="path", type="string", example="https://develop.garzasoft.com/dgush-backend/public/api/product"),
     *             @OA\Property(property="per_page", type="integer", example="12"),
     *             @OA\Property(property="prev_page_url", type="string", example="null"),
     *             @OA\Property(property="to", type="integer", example="10"),
     *        )
     *     )
     * )
     */
    public function search()
    {
//        VALIDATE DATA
        request()->validate([
            'filter' => 'required|array',
            'filter.search' => 'string',
//            'filter.status' => 'string|in:onsale,new',
            'filter.score' => 'integer',
            'filter.category' => 'array',
            'filter.category.*' => 'string',
            'filter.price' => 'array|size:2',
            'filter.price.*' => 'numeric',
            'filter.color' => 'array',
            'filter.color.*' => 'string',
            'filter.size' => 'array',
            'filter.size.*' => 'string',
            'filter.sort' => 'string|in:id,name,description,price1,price2,score,status,subcategory_id,none',
            'filter.direction' => 'string',
        ]);


        $products = Product::search(
            request('filter.search'),
            request('filter.status'),
            request('filter.score'),
            request('filter.category'), // SUBCATEGORY IS AN ARRAY OF STRING
            request('filter.price'),
            request('filter.color'), // COLOR IS AN ARRAY OF STRING
            request('filter.size'), // SIZE IS AN ARRAY OF STRING
            request('filter.sort', 'id'),
            request('filter.direction', 'desc'),
        );
        return response()->json($products);
    }


    public function listImages()
    {
        $disk = Storage::disk('spaces');

        $files = $disk->allFiles();

        return $files;
    }

    public function deleteImage(Request $request)
    {
        $request->validate(
            [
                'fileName' => 'required|string'
            ]
        );

        Storage::disk('spaces')->delete($request->fileName);

        return response()->json(['message' => 'Imagen eliminada correctamente']);
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


    /**
     * GET ALL PRODUCTS WITH TRASHED
     * @OA\Get(
     *     path="/dgush-backend/public/api/products",
     *     description="Get all products with trashed",
     *     tags={"Product"},
     *     summary="Get all products",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="All products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *        )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function getAllProducts()
    {
        //        ALL PRODUCTS WITH TRASHED
        $allProducts = Product::withTrashed()->get();
        return response()->json($allProducts);
    }

    /**
     * CREATE PRODUCT
     * @OA\Post(
     *     path="/dgush-backend/public/api/product",
     *     tags={"Product"},
     *     summary="Create product",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"name", "description", "detailweb", "price1", "price2", "score", "subcategory_id"},
     *              @OA\Property(property="name", type="string", example="Product 1"),
     *              @OA\Property(property="description", type="string", example="Description of product 1"),
     *              @OA\Property(property="detailweb", type="string", example="Detail of product 1"),
     *              @OA\Property(property="price1", type="number", example="100.00"),
     *              @OA\Property(property="price2", type="number", example="90.00"),
     *              @OA\Property(property="score", type="integer", example="5"),
     *              @OA\Property(property="subcategory_id", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product created",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
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
     *         description="Subcategory not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subcategory not found")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        //        VALIDATE DATA
        $request->validate(
            [
                'name' => 'required|string|unique:product',
                'description' => 'required|string',
                'detailweb' => 'required|string',
                'price1' => 'required|numeric',
                'price2' => 'required|numeric',
                'score' => 'required|integer',
                'status' => 'string|in:onsale,new',
                'subcategory_id' => 'required|integer',
            ]
        );

        //        VALIDATE SUBCATEGORY
        $subcategory = Subcategory::find($request->subcategory_id);
        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }

        //        CREATE PRODUCT
        $product = Product::create($request->all());
        return response()->json($product);
    }


    /**
     * SHOW PRODUCT
     * @OA\Get(
     *     path="/dgush-backend/public/api/product/{id}",
     *     tags={"Product"},
     *     summary="Show product",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show product",
     *         @OA\JsonContent(
     *             @OA\Property(property="product", ref="#/components/schemas/Product"),
     *             @OA\Property(property="colors", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="name", type="string", example="Red"),
     *                 @OA\Property(property="hex", type="string", example="#FF0000")
     *             )),
     *             @OA\Property(property="sizes", type="array", @OA\Items(ref="#/components/schemas/Size")),
     *             @OA\Property(property="comments", type="array", @OA\Items(ref="#/components/schemas/Comment")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        //        VALIDATE ID IS INTEGER
        if (!is_int($id)) {
            return response()->json(['message' => 'ID must be an integer'], 422);
        }

        //        FIND PRODUCT
        $product = Product::find($id);
        if ($product) {
            //            GET COMMENTS
            $colors = $product->getColors($id);
            $sizes = $product->getSizes($id);
            $comments = $product->comments($id);
            $images = $product->images($id);
            return response()->json([
                'product' => $product,
                'colors' => $colors,
                'sizes' => $sizes,
                'comments' => $comments,
                'images' => $images
            ]);
        } else {
            //            PRODUCT NOT FOUND
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * UPDATE PRODUCT
     * @OA\Put(
     *     path="/dgush-backend/public/api/product/{id}",
     *     tags={"Product"},
     *     summary="Update product",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"name", "description", "detailweb", "price1", "price2", "score", "subcategory_id"},
     *              @OA\Property(property="name", type="string", example="Product 1"),
     *              @OA\Property(property="description", type="string", example="Description of product 1"),
     *              @OA\Property(property="detailweb", type="string", example="Detail of product 1"),
     *              @OA\Property(property="price1", type="number", example="100.00"),
     *              @OA\Property(property="price2", type="number", example="90.00"),
     *              @OA\Property(property="score", type="integer", example="5"),
     *              @OA\Property(property="subcategory_id", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
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
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        //        FIND PRODUCT
        $product = Product::find($id);
        if ($product) {
            //            VALIDATE DATA
            $request->validate(
                [
                    'name' => 'required|string|unique:product,name,' . $id,
                    'description' => 'required|string',
                    'detailweb' => 'required|string',
                    'price1' => 'required|numeric',
                    'price2' => 'required|numeric',
                    'score' => 'required|integer',
                    'subcategory_id' => 'required|integer',
                ]
            );

            //            VALIDATE CATEGORY
            $category = Subcategory::find($request->subcategory_id);
            if (!$category) {
                return response()->json(['message' => 'Subcategory not found'], 404);
            }

            //            UPDATE PRODUCT
            $product->update($request->all());
            return response()->json($product);
        } else {
            //            PRODUCT NOT FOUND
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * DELETE PRODUCT
     * @OA\Delete(
     *     path="/dgush-backend/public/api/product/{id}",
     *     tags={"Product"},
     *     summary="Delete product",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product deleted")
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
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Product has relations",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product has relations")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        //        FIND PRODUCT
        $product = Product::find($id);
        if ($product) {
            //            VALIDATE PRODUCT NOT HAS COMMENTS
            if ($product->comments()->count() > 0) {
                return response()->json(['message' => 'Product has comments'], 409);
            }

            //            VALIDATE PRODUCT NOT HAS PRODUCT SIZE
            //            if ($product->productSize()->count() > 0) {
            //                return response()->json(['message' => 'Product has product size'], 409);
            //            }

            //            VALIDATE PRODUCT NOT HAS PRODUCT COLOR
            //            if ($product->productColor()->count() > 0) {
            //                return response()->json(['message' => 'Product has product color'], 409);
            //            }

            //            DELETE PRODUCT
            $product->delete();
            return response()->json(['message' => 'Product deleted']);
        } else {
            //            PRODUCT NOT FOUND
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/dgush-backend/public/api/product/setColors/{id}",
     *     tags={"Product"},
     *     summary="Set colors",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"colors"},
     *              @OA\Property(property="colors", type="array", @OA\Items(type="integer", example="1")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Colors set",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Color"),
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
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"colors": {"The colors field is required."}})
     *         )
     *     )
     * )
     */

    public function setColors(Request $request, int $id)
    {
        $request->validate([
            'colors' => 'required|array',
            'colors.*' => 'required|integer'
        ]);

        $product = Product::find($id);

        if ($product) {
            //           VALIDATE COLORS EXIST
            $colors = Color::whereIn('id', $request->colors)->get();
            if ($colors->count() != count($request->colors)) {
                return response()->json(['message' => 'Color not found'], 404);
            }


            //           DELETE COLORS
            $product->productColors()->detach();

            //            SET COLORS
            $product->productColors()->attach($request->colors);

            //            RETURN COLORS
            return response()->json($product->getColors($product->id));
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/dgush-backend/public/api/product/setSizes/{id}",
     *     tags={"Product"},
     *     summary="Set sizes",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"sizes"},
     *              @OA\Property(property="sizes", type="array", @OA\Items(type="integer", example="1")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sizes set",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Size"),
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
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"sizes": {"The sizes field is required."}})
     *         )
     *     )
     * )
     */
    public function setSizes(Request $request, int $id)
    {
        $request->validate([
            'sizes' => 'required|array',
            'sizes.*' => 'required|integer'
        ]);

        //        FIND PRODUCT
        $product = Product::find($id);

        if ($product) {
            //            VALIDATE SIZES EXIST
            $sizes = Size::whereIn('id', $request->sizes)->get();
            if ($sizes->count() != count($request->sizes)) {
                return response()->json(['message' => 'Size not found'], 404);
            }

            //           DELETE SIZES
            $product->productSizes()->detach();

            //            SET SIZES
            $product->productSizes()->attach($request->sizes);

            //            RETURN SIZES
            return response()->json($product->getSizes($product->id));
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }
}
