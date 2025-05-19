<?php

namespace App\Http\Controllers;

use App\Models\ProductDetails;
use App\Models\WishItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishItemController extends Controller
{

    public function index()
    {
        $wishItems = WishItem::with('productDetails.product.image', 'productDetails.color', 'productDetails.size')
            ->where('user_id', auth()->user()->id)
            ->get();

        return response()->json($wishItems);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:product,id',
            'color_id' => 'required|integer|exists:color,id',
            'size_id' => 'required|integer|exists:size,id',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Encontrar los detalles del producto
        $productDetails = ProductDetails::where('product_id', $request->input('product_id'))
            ->where('color_id', $request->input('color_id'))
            ->where('size_id', $request->input('size_id'))
            ->first();

        if (!$productDetails) {
            return response()->json(['error' => 'Product details not found'], 404);
        }

        // Verificar si el producto ya está en la lista de deseos
        $existingWishItem = WishItem::with('productDetails.product', 'productDetails.color', 'productDetails.size')
            ->where('product_details_id', $productDetails->id)
            ->where('user_id', auth()->user()->id)
            ->first();
        if ($existingWishItem) {
            return response()->json($existingWishItem);
        }

        // Crear el WishItem
        $wishItem = WishItem::create([
            'product_details_id' => $productDetails->id,
            'user_id' => auth()->user()->id,
        ]);

        $wishItem = WishItem::with('productDetails.product', 'productDetails.color', 'productDetails.size')
            ->where('id', $wishItem->id)
            ->first();

        return response()->json($wishItem);
    }

    public function show(int $id)
    {
        $wishItem = WishItem::with('productDetails.product', 'productDetails.color', 'productDetails.size')
            ->where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->get()->first();

        if (!$wishItem) {
            return response()->json(['error' => 'WishItem not found'], 404);
        }

        return response()->json($wishItem);
    }

    public function destroy(int $id)
    {
        $wishItem = WishItem::where('id', $id)
            ->where('user_id', auth()->user()->id)
            ->get()->first();

        if (!$wishItem) {
            return response()->json(['error' => 'WishItem not found'], 404);
        }

        $wishItem->delete();

        return response()->json(['message' => 'WishItem deleted successfully']);
    }
}
