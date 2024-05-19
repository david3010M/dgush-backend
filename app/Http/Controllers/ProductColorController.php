<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Http\Request;

class ProductColorController extends Controller
{
    public function index()
    {
        return ProductColor::all();
    }

    public function store(Request $request)
    {
//        VALIDATE DATA
        $request->validate([
            'product_id' => 'required|integer',
            'color_id' => 'required|integer',
        ]);

//        VALIDATE IF PRODUCT AND COLOR EXISTS
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $color = Color::find($request->color_id);
        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

//        VALIDATE IF PRODUCT AND COLOR EXISTS BOTH
        $productColor = ProductColor::where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->first();
        if ($productColor) {
            return response()->json(['message' => 'Product color already exists'], 409);
        }

//        CREATE PRODUCT COLOR
        $newProductColor = ProductColor::create([
            'product_id' => $request->product_id,
            'color_id' => $request->color_id,
        ]);

        return response()->json($newProductColor);

    }


    public function show(int $id)
    {
        $productColor = ProductColor::find($id);
        if (!$productColor) {
            return response()->json(['message' => 'Product color not found'], 404);
        }

        return $productColor;
    }


    public function update(Request $request, int $id)
    {
        $productColor = ProductColor::find($id);
        if (!$productColor) {
            return response()->json(['message' => 'Product color not found'], 404);
        }

//        VALIDATE DATA
        $request->validate([
            'product_id' => 'required|integer',
            'color_id' => 'required|integer',
        ]);

//        VALIDATE IF PRODUCT AND COLOR EXISTS
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $color = Color::find($request->color_id);
        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

//        VALIDATE IF PRODUCT AND COLOR EXISTS BOTH AND NOT THE SAME
        $productColorExists = ProductColor::where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->first();
        if ($productColorExists && $productColorExists->id !== $id) {
            return response()->json(['message' => 'Product color already exists'], 409);
        }

//        UPDATE PRODUCT COLOR
        $productColor->update([
            'product_id' => $request->product_id,
            'color_id' => $request->color_id,
        ]);

        return response()->json($productColor);
    }


    public function destroy(int $id)
    {
        $productColor = ProductColor::find($id);
        if (!$productColor) {
            return response()->json(['message' => 'Product color not found'], 404);
        }

        $productColor->delete();
        return response()->json(['message' => 'Product color deleted']);
    }
}
