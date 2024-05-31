<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductDetails;
use App\Models\WishItem;
use App\Http\Requests\StoreWishItemRequest;
use App\Http\Requests\UpdateWishItemRequest;
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
            'color' => 'required|string|exists:color,value',
            'size' => 'required|string|exists:size,value',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Encontrar los detalles del producto
        $productDetails = ProductDetails::where('product_id', $request->input('product_id'))
            ->whereHas('color', function ($query) use ($request) {
                $query->where('value', $request->input('color'));
            })
            ->whereHas('size', function ($query) use ($request) {
                $query->where('value', $request->input('size'));
            })
            ->get()->first();

        if (!$productDetails) {
            return response()->json(['error' => 'Product details not found'], 404);
        }

        // Crear el WishItem
        $wishItem = WishItem::create([
            'product_details_id' => $productDetails->id,
            'user_id' => auth()->user()->id
        ]);

        $wishItem = WishItem::with('productDetails.product', 'productDetails.color', 'productDetails.size')
            ->where('id', $wishItem->id)
            ->get()->first();

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
