<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/filter/product",
     *      operationId="getFilterProduct",
     *      tags={"Filter"},
     *      summary="Get filter product",
     *      description="Return filter product",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="color", type="array", @OA\Items(ref="#/components/schemas/Color")),
     *              @OA\Property(property="sizes", type="array", @OA\Items(ref="#/components/schemas/Size")),
     *              @OA\Property(property="subcategories", type="array", @OA\Items(ref="#/components/schemas/Subcategory")),
     *              @OA\Property(property="price", type="number", example="100.00"),
     *              @OA\Property(property="status", type="array", @OA\Items(type="string", example="onsale")),
     *              @OA\Property(property="score", type="array", @OA\Items(type="string", example="5")),
     *              @OA\Property(property="sort", type="array", @OA\Items(type="string", example="name")),
     *              @OA\Property(property="direction", type="array", @OA\Items(type="string", example="asc")),
     *          )
     *      )
     * )
     *
     */
    public function product()
    {
        return response()->json([
            'color' => Color::all(),
            'sizes' => Size::all(),
            'subcategories' => Subcategory::all(),
            'price' => Product::max('price1'),
            'status' => [
                'onsale',
                'new'
            ],
            'score' => [
                '1',
                '2',
                '3',
                '4',
                '5'
            ],
            'sort' => [
                'name',
                'price',
            ],
            'direction' => [
                'asc',
                'desc'
            ]
        ]);

    }
}
