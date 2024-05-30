<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $pageSize = $request->input('page', 10);
        return response()->json(Order::with('user', 'orderItems.productDetail.product.image',
            'orderItems.productDetail.color', 'orderItems.productDetail.size', 'coupon')
            ->where('user_id', auth()->user()->id)
            ->simplePaginate($pageSize));
    }

    /**
     * @OA\Post (
     *     path="/dgush-backend/public/api/order",
     *     summary="Create a new order",
     *     tags={"Order"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"orderItems"},
     *             @OA\Property(property="orderItems", type="array", @OA\Items(
     *                 @OA\Property(property="product_id", type="integer"),
     *                 @OA\Property(property="color_id", type="integer"),
     *                 @OA\Property(property="size_id", type="integer"),
     *                 @OA\Property(property="quantity", type="integer")
     *             )),
     *             @OA\Property(property="coupon_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderItems' => 'required|array',
            'orderItems.*.product_id' => 'required|exists:product,id',
            'orderItems.*.color_id' => 'required|exists:color,id',
            'orderItems.*.size_id' => 'required|exists:size,id',
            'orderItems.*.quantity' => 'required|integer',
            'coupon_id' => 'nullable|exists:coupon,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Encontrar los detalles del producto
        $products = $request->input('orderItems');
        $productDetails = [];
        foreach ($products as $product) {
            $productDetail = ProductDetails::where('product_id', $product['product_id'])
                ->where('color_id', $product['color_id'])
                ->where('size_id', $product['size_id'])
                ->with(['product', 'color', 'size'])
                ->first();
            if ($productDetail) {
                $productDetails[] = $productDetail;
            } else {
                return response()->json(['error' => 'Product not found'], 404);
            }
        }

        // Crear la orden
        $order = Order::create([
            'subtotal' => 0,
            'total' => 0,
            'quantity' => 0,
            'date' => now(),
            'user_id' => auth()->user()->id,
//            'coupon_id' => $request->input('coupon_id')
        ]);

        // Adjuntar productos a la orden y calcular subtotal y cantidad
        $quantity = 0;
        $subtotal = 0;

        foreach ($productDetails as $key => $productDetail) {
//            DECIMAL STOCK
            $stock = (float)$productDetail->stock;
            if ($stock <= $products[$key]['quantity']) {
                return response()->json(['error' => 'The product is out of stock'], 422);
            }

            $orderItem = OrderItem::create([
                'quantity' => $products[$key]['quantity'],
                'product_detail_id' => $productDetail->id,
                'order_id' => $order->id
            ]);

            $productDetail->update([
                'stock' => $stock - $products[$key]['quantity']
            ]);

            $quantity += $products[$key]['quantity'];
            $subtotal += $productDetail->product->price1 * $products[$key]['quantity'];
        }

        // Actualizar la orden con el subtotal y la cantidad total
        $order->update([
            'subtotal' => $subtotal,
            'total' => $subtotal, // Puedes aplicar descuentos o impuestos aquí si es necesario
            'quantity' => $quantity
        ]);

        $order = Order::with('user', 'orderItems.productDetail.product.image',
            'orderItems.productDetail.color', 'orderItems.productDetail.size', 'coupon')
            ->find($order->id);

        return response()->json($order);
    }

    public function show(int $id)
    {
        $order = Order::with('user', 'orderItems.productDetail.product.image',
            'orderItems.productDetail.color', 'orderItems.productDetail.size', 'coupon')
            ->where('user_id', auth()->user()->id)
            ->find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    
        return response()->json($order);
    }

    public function update(Request $request, int $id)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'orderItems' => 'required|array',
            'orderItems.*.product_id' => 'required|exists:product,id',
            'orderItems.*.color_id' => 'required|exists:color,id',
            'orderItems.*.size_id' => 'required|exists:size,id',
            'orderItems.*.quantity' => 'required|integer',
            'coupon_id' => 'nullable|exists:coupon,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Encontrar la orden existente
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Eliminar los productos actuales de la orden y revertir el stock
        foreach ($order->orderItems as $orderItem) {
            $productDetail = $orderItem->productDetail;
            $productDetail->update([
                'stock' => $productDetail->stock + $orderItem->quantity
            ]);
            $orderItem->delete();
        }

        // Encontrar los detalles del nuevo producto
        $products = $request->input('orderItems');
        $productDetails = [];
        foreach ($products as $product) {
            $productDetail = ProductDetails::where('product_id', $product['product_id'])
                ->where('color_id', $product['color_id'])
                ->where('size_id', $product['size_id'])
                ->with(['product', 'color', 'size'])
                ->first();
            if ($productDetail) {
                $productDetails[] = $productDetail;
            } else {
                return response()->json(['error' => 'Product not found'], 404);
            }
        }

        // Adjuntar los nuevos productos a la orden y calcular subtotal y cantidad
        $quantity = 0;
        $subtotal = 0;

        foreach ($productDetails as $key => $productDetail) {
            $stock = (float)$productDetail->stock;
            if ($stock < $products[$key]['quantity']) {
                return response()->json(['error' => 'The product is out of stock'], 422);
            }

            $orderItem = OrderItem::create([
                'quantity' => $products[$key]['quantity'],
                'product_detail_id' => $productDetail->id,
                'order_id' => $order->id
            ]);

            $productDetail->update([
                'stock' => $stock - $products[$key]['quantity']
            ]);

            $quantity += $products[$key]['quantity'];
            $subtotal += $productDetail->product->price1 * $products[$key]['quantity'];
        }

        // Actualizar la orden con el nuevo subtotal y cantidad
        $order->update([
            'subtotal' => $subtotal,
            'total' => $subtotal, // Puedes aplicar descuentos o impuestos aquí si es necesario
            'quantity' => $quantity,
            'coupon_id' => $request->input('coupon_id')
        ]);

        // Devolver la orden actualizada
        $order = Order::with('user', 'orderItems.productDetail.product.image',
            'orderItems.productDetail.color', 'orderItems.productDetail.size', 'coupon')
            ->find($order->id);

        return response()->json($order);
    }


    public function destroy(int $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        foreach ($order->orderItems as $orderItem) {
            $productDetail = $orderItem->productDetail;
            $productDetail->update([
                'stock' => $productDetail->stock + $orderItem->quantity
            ]);
            $orderItem->delete();
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
