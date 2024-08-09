<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Image;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\SizeGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        $products = Product::with('image')->orderBy('id', 'desc')->simplePaginate(12);
        ProductResource::collection($products);
        return response()->json($products);
    }

    /**
     * SEARCH PRODUCTS
     * @OA\Post(
     *     path="/dgush-backend/public/api/product/search",
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
     *                  @OA\Property(property="category", type="array", @OA\Items(type="string", example="vestidos")),
     *                  @OA\Property(property="price", type="array", @OA\Items(type="number", example="100.00, 200.00")),
     *                  @OA\Property(property="color", type="array", @OA\Items(type="string", example="azul-oscuro")),
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
            'search' => 'nullable|string',
            'score' => 'nullable|integer',
            'status' => 'nullable|string|in:onsale,new',
            'liquidacion' => 'nullable|boolean',
            'category' => 'nullable|array',
            'category.*' => 'nullable|string',
            'price' => 'nullable|array|size:2',
            'price.*' => 'nullable|numeric',
            'color' => 'nullable|array',
            'color.*' => 'nullable|string',
            'size' => 'nullable|array',
            'size.*' => 'nullable|string',
            'sort' => 'nullable|string|in:none,price-asc,price-desc,score',
            'direction' => 'nullable|string',
        ]);


        $products = Product::search(
            request('search'),
            request('status'),
            request('liquidacion'),
            request('score'),
            request('category'), // SUBCATEGORY IS AN ARRAY OF STRING
            request('price'),
            request('color'), // COLOR IS AN ARRAY OF STRING
            request('size'), // SIZE IS AN ARRAY OF STRING
            request('sort', 'id'),
            request('direction', 'desc'),
        );
        return response()->json($products);
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
        //        ALL PRODUCTS WITH TRASHED WITH PAGINATION OF 6
        $products = Product::withTrashed()->simplePaginate(6);
        return response()->json($products);
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
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(ref="#/components/schemas/ProductRequest")
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
        if (auth()->user()->typeuser_id != 1) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('product', 'name')->whereNull('deleted_at')
            ],
            'description' => 'required|string',
            'detailweb' => 'required|string',
            'price1' => 'required|numeric',
            'price2' => 'required|numeric',
//            'condition' => 'nullable|string|in:new,used',
            'status' => 'nullable|string|in:onsale,new',
            'subcategory_id' => 'required|integer|exists:subcategory,id',
            'product_details' => 'required|array',
            'product_details.*.stock' => 'required|numeric',
            'product_details.*.color_id' => 'required|integer|exists:color,id',
            'product_details.*.size_id' => 'required|integer|exists:size,id',
            'images' => 'required|array',
            'images.*' => 'required|image',
            'sizeGuideImage' => 'nullable|image'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'detailweb' => $request->input('detailweb'),
            'price1' => $request->input('price1'),
            'price2' => $request->input('price2'),
            'status' => $request->input('status') ?? '',
            'subcategory_id' => $request->input('subcategory_id'),
        ];

        $product = Product::create($data);
        $id = $product->id;

        $images = $request->file('images');

        $productDetails = $request->input('product_details');

        foreach ($productDetails as $productDetail) {
            $dataProductDetail = [
                'stock' => $productDetail['stock'],
                'color_id' => $productDetail['color_id'],
                'size_id' => $productDetail['size_id'],
                'product_id' => $id
            ];
            ProductDetails::create($dataProductDetail);
        }

        foreach ($images as $image) {
            $fileName = $id . '/' . $image->getClientOriginalName();
            Storage::disk('spaces')->put($fileName, file_get_contents($image), 'public');

            $imageUrl = Storage::disk('spaces')->url($fileName);

            Image::create([
                'name' => $fileName,
                'url' => $imageUrl,
                'product_id' => $id
            ]);
        }

        if ($request->hasFile('sizeGuideImage')) {
            $guideSize = $request->file('sizeGuideImage');
            $fileName = 'SizeGuides/' . $id . '/' . $guideSize->getClientOriginalName();
            Storage::disk('spaces')->put($fileName, file_get_contents($guideSize), 'public');
            $imageUrl = Storage::disk('spaces')->url($fileName);

            SizeGuide::create([
                'name' => $fileName,
                'route' => $imageUrl,
                'product_id' => $id
            ]);
        }

        $product = Product::with('productDetails', 'imagesProduct', 'guideSize')->find($id);
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
            $colors = $product->getColorsByProduct($id);
            $sizes = $product->getSizesByProduct($id);
            $comments = $product->comments($id);
            $images = $product->images($id);
            $productDetails = $product->getProductDetails($id);
            $productRelated = $product->getRelatedProducts($id);
            $sizeGuide = $product->SizeGuide($id);
            return response()->json([
                'product' => $product,
                'colors' => $colors,
                'sizes' => $sizes,
                'comments' => $comments,
                'productDetails' => $productDetails,
                'images' => $images,
                'productRelated' => $productRelated,
                'sizeGuide' => $sizeGuide
            ]);
        } else {
            //            PRODUCT NOT FOUND
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    public function productShow(int $id)
    {
        // VALIDATE ID IS INTEGER
        if (!is_int($id)) {
            return response()->json(['message' => 'ID must be an integer'], 422);
        }

        // FIND PRODUCT
        $product = Product::find($id);
        if ($product) {
            // GET PRODUCT DETAILS
            $productDetails = $product->getProductDetailsWithSizes($id);
            $comments = $product->comments($id);
            $images = $product->images($id);
            $productRelated = $product->getRelatedProducts($id);
            $sizeGuide = $product->SizeGuide($id);

            return response()->json([
                'product' => $product,
                'productDetails' => $productDetails,
                'comments' => $comments,
                'images' => $images,
                'productRelated' => $productRelated,
                'sizeGuide' => $sizeGuide->route
            ]);
        } else {
            // PRODUCT NOT FOUND
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
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(ref="#/components/schemas/ProductRequest")
     *          )
     *      ),
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
        if (auth()->user()->typeuser_id != 1) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'name' => [
                'nullable',
                'string',
                Rule::unique('product')->whereNull('deleted_at')->ignore($id),
            ],
            'description' => 'nullable|string',
            'detailweb' => 'nullable|string',
            'price1' => 'nullable|numeric',
            'price2' => 'nullable|numeric',
            'status' => 'nullable|string|in:onsale,new,none',
            'liquidacion' => 'nullable|boolean',
            'subcategory_id' => 'nullable|integer|exists:subcategory,id',
            'product_details' => 'nullable|array',
            'product_details.*.stock' => 'required|numeric',
            'product_details.*.color_id' => 'required|integer|exists:color,id',
            'product_details.*.size_id' => 'required|integer|exists:size,id',
            'images' => 'nullable|array',
            'images.*' => 'required|image',
            'sizeGuideImage' => 'nullable|image'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'name' => $request->input('name') ?? $product->name,
            'description' => $request->input('description') ?? $product->description,
            'detailweb' => $request->input('detailweb') ?? $product->detailweb,
            'price1' => $request->input('price1') ?? $product->price1,
            'price2' => $request->input('price2') ?? $product->price2,
            'status' => $request->input('status') === 'none' ? "" : $request->input('status') ?? $product->status,
            'liquidacion' => $request->input('liquidacion') ?? $product->liquidacion,
            'subcategory_id' => $request->input('subcategory_id') ?? $product->subcategory_id,
        ];

        $product->update($data);
        $id = $product->id;

        $images = $request->file('images');
        $productDetails = $request->input('product_details');

        if ($productDetails) {
            ProductDetails::where('product_id', $id)->delete();

            foreach ($productDetails as $productDetail) {
                $dataProductDetail = [
                    'stock' => $productDetail['stock'],
                    'color_id' => $productDetail['color_id'],
                    'size_id' => $productDetail['size_id'],
                    'product_id' => $id
                ];
                ProductDetails::create($dataProductDetail);
            }
        }

        if ($images) {
            Storage::disk('spaces')->deleteDirectory("/" . $id . "/");
            Image::where('product_id', $id)->delete();

            foreach ($images as $image) {
                $fileName = $id . '/' . $image->getClientOriginalName();
                Storage::disk('spaces')->put($fileName, file_get_contents($image), 'public');

                $imageUrl = Storage::disk('spaces')->url($fileName);

                Image::create([
                    'name' => $fileName,
                    'url' => $imageUrl,
                    'product_id' => $id
                ]);
            }
        }

        if ($request->hasFile('sizeGuideImage')) {
            Storage::disk('spaces')->deleteDirectory('SizeGuides/' . $id . "/");
            SizeGuide::where('product_id', $id)->delete();

            $guideSize = $request->file('sizeGuideImage');
            $fileName = 'SizeGuides/' . $id . '/' . $guideSize->getClientOriginalName();
            Storage::disk('spaces')->put($fileName, file_get_contents($guideSize), 'public');
            $imageUrl = Storage::disk('spaces')->url($fileName);

            SizeGuide::create([
                'name' => $fileName,
                'route' => $imageUrl,
                'product_id' => $id
            ]);
        }

        $product = Product::with('productDetails', 'imagesProduct', 'guideSize')->find($id);
        return response()->json($product);
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
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json(['message' => 'Product deleted']);
        } else {
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * UPDATE PRODUCT ON SALE
     * @OA\Post(
     *     path="/dgush-backend/public/api/productsOnSale",
     *     tags={"Product"},
     *     summary="Update products on sale",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"products"},
     *              @OA\Property(property="products", type="array", @OA\Items(
     *                  @OA\Property(property="id", type="integer", example="1")
     *              ))
     *         )
     *     ),
     *     @OA\Response( response=422 , description="Validation error", @OA\JsonContent( @OA\Property(property="error", type="string", example="The given data was invalid.") ) ),
     *     @OA\Response( response=200, description="Products updated", @OA\JsonContent( @OA\Property(property="message", type="string", example="Products updated") ) )
     * )
     */
    public function updateProductsOnSale(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'products' => 'required|array',
            'products.*' => 'required|array',
            'products.*.id' => 'required|integer|exists:product,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $products = $request->input('products');

        $productsCurrentOnSale = Product::where('status', 'onsale')->get();

        foreach ($productsCurrentOnSale as $product) {
            $product->update(['status' => '']);
        }

        foreach ($products as $product) {
            $product = Product::find($product['id']);
            $product->update(['status' => 'onsale']);
        }

        return response()->json(['message' => 'Products updated']);
    }

}
