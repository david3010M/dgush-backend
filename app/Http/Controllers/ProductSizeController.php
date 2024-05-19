<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Size;
use Illuminate\Http\Request;

class ProductSizeController extends Controller
{

    public function index()
    {
        return ProductSize::all();
    }


    public function store(Request $request)
    {
//        VALIDATE DATA
        $request->validate([
            'product_id' => 'required|integer',
            'size_id' => 'required|integer'
        ]);

//        VALIDATE IF PRODUCT AND SIZE EXISTS
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $size = Size::find($request->size_id);
        if (!$size) {
            return response()->json(['message' => 'Size not found'], 404);
        }

//        VALIDATE IF PRODUCT AND COLOR EXISTS BOTH
        $productSize = ProductSize::where('product_id', $request->product_id)
            ->where('size_id', $request->size_id)
            ->first();
        if ($productSize) {
            return response()->json(['message' => 'Product size already exists'], 409);
        }

//        CREATE PRODUCT SIZE
        $productSize = ProductSize::create([
            'product_id' => $request->product_id,
            'size_id' => $request->size_id
        ]);

        return response()->json($productSize);

    }


    public function show(int $id)
    {
        $productSize = ProductSize::find($id);
        if ($productSize) {
            return $productSize;
        } else {
            return response()->json(['message' => 'Product size not found'], 404);
        }
    }


    public function update(Request $request, int $id)
    {
        $productSize = ProductSize::find($id);
        if (!$productSize) {
            return response()->json(['message' => 'Product size not found'], 404);
        }

//        VALIDATE DATA
        $request->validate([
            'product_id' => 'required|integer',
            'size_id' => 'required|integer'
        ]);

//        VALIDATE IF PRODUCT AND SIZE EXISTS
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $size = Size::find($request->size_id);
        if (!$size) {
            return response()->json(['message' => 'Size not found'], 404);
        }

//        VALIDATE IF PRODUCT AND COLOR EXISTS BOTH AND NOT THE SAME
        $productSize = ProductSize::where('product_id', $request->product_id)
            ->where('size_id', $request->size_id)
            ->first();
        if ($productSize && $productSize->id != $id) {
            return response()->json(['message' => 'Product size already exists'], 409);
        }

//        UPDATE PRODUCT SIZE
        $productSize->update([
            'product_id' => $request->product_id,
            'size_id' => $request->size_id
        ]);

        return response()->json($productSize);

    }


    public function destroy(int $id)
    {
        $productSize = ProductSize::find($id);
        if ($productSize) {
            $productSize->delete();
            return response()->json(['message' => 'Product size deleted successfully']);
        } else {
            return response()->json(['message' => 'Product size not found'], 404);
        }
    }
}
