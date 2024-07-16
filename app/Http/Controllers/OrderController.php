<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductDetails;
use App\Models\SendInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    /**
     * Orders from the authenticated user
     * @OA\Get (
     *     path="/dgush-backend/public/api/order",
     *     summary="Get all orders",
     *     tags={"Order"},
     *     @OA\Parameter(name="page", in="query", description="Page number", required=false, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Orders retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example="1"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Order")),
     *             @OA\Property(property="first_page_url", type="string", example="http://localhost:8000/api/order?page=1"),
     *             @OA\Property(property="from", type="integer", example="1"),
     *             @OA\Property(property="next_page_url", type="string", example="null"),
     *             @OA\Property(property="path", type="string", example="http://localhost:8000/api/order"),
     *             @OA\Property(property="per_page", type="integer", example="10"),
     *             @OA\Property(property="prev_page_url", type="string", example="null"),
     *             @OA\Property(property="to", type="integer", example="1")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     *
     */
    public function index(Request $request)
    {
        $pageSize = $request->input('per_page');
        $orders = Order::with('user', 'orderItems.productDetail.product.image',
            'orderItems.productDetail.color', 'orderItems.productDetail.size', 'coupon')
            ->where('user_id', auth()->user()->id)
            ->orderBy('id', 'desc');

        return $pageSize ? OrderResource::collection($orders->simplePaginate($pageSize))
            : response()->json(OrderResource::collection($orders->get()));
//        return response()->json(OrderResource::collection($orders));
//        return response()->json(Order::with('user', 'orderItems.productDetail.product.image',
//            'orderItems.productDetail.color', 'orderItems.productDetail.size', 'coupon')
//            ->where('user_id', auth()->user()->id)->get());
//            ->simplePaginate($pageSize));
    }

    /**
     * @OA\Post  (
     *     path="/dgush-backend/public/api/order/search",
     *     summary="Search orders",
     *     tags={"Order"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="verificado"),
     *             @OA\Property(property="sort", type="string", example="date-asc"),
     *             @OA\Property(property="date", type="string", example="2024-05-26")
     *         )
     *     ),
     *     @OA\Response( response=200, description="Orders retrieved successfully", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/OrderResource")) ),
     *     @OA\Response( response=422, description="Validation error", @OA\JsonContent(@OA\Property(property="error", type="string")) ),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(@OA\Property(property="error", type="string")) )
     * )
     *
     */
    public function search(Request $request)
    {
        $validator = validator($request->all(), [
            'status' => 'nullable|string|in:verificado,confirmado,enviado,entregado,cancelado',
            'sort' => 'nullable|string|in:none,date-asc,date-desc',
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $orders = Order::with('user', 'orderItems.productDetail.product.image',
            'orderItems.productDetail.color', 'orderItems.productDetail.size', 'coupon', 'sendInformation.district')
            ->where('status', 'like', '%' . $request->input('status') . '%')
            ->whereDate('date', 'like', '%' . $request->input('date') . '%')
            ->where('user_id', auth()->user()->id);

        $sort = $request->input('sort');

        if ($sort === 'date-asc') {
            $orders->orderBy('date');
        } else if ($sort === 'date-desc') {
            $orders->orderBy('date', 'desc');
        }

        return response()->json(OrderResource::collection($orders->get()));
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
            'discount' => 0,
            'sendCost' => 10,
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
        }

        if ($quantity >= 3) {
            foreach ($productDetails as $key => $productDetail) {
                $subtotal += $productDetail->product->price2 * $products[$key]['quantity'];
            }
        } else {
            foreach ($productDetails as $key => $productDetail) {
                $subtotal += $productDetail->product->price1 * $products[$key]['quantity'];
            }
        }

        // Actualizar la orden con el subtotal y la cantidad total
        $order->update([
            'subtotal' => $subtotal,
            'total' => $subtotal + $order->sendCost,
            'quantity' => $quantity
        ]);

        $order = Order::with('user', 'orderItems.productDetail.product.image',
            'orderItems.productDetail.color', 'orderItems.productDetail.size', 'coupon')
            ->find($order->id);

        return response()->json($order);
    }

    public function updateStatus(Request $request, int $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:verificado,confirmado,enviado,entregado,cancelado',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'status' => $request->input('status'),
            'description' => $request->input('description')
        ];

        $order->update($data);
        $order = Order::with('user', 'orderItems.productDetail.product.image',
            'orderItems.productDetail.color', 'orderItems.productDetail.size', 'coupon')
            ->find($order->id);

        return response()->json($order);
    }

    public function show(int $id)
    {
        $order = Order::with('user', 'orderItems.productDetail.product.image',
            'orderItems.productDetail.color', 'orderItems.productDetail.size', 'coupon', 'sendInformation')
            ->where('user_id', auth()->user()->id)
            ->find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    public function update(Request $request, int $id)
    {
        // Encontrar la orden existente
        $order = Order::find($id);
        $user = auth()->user();
        if (!$order || $order->user_id !== $user->id) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($order->status !== 'verificado') {
            return response()->json(['error' => 'Order has already been confirmed'], 422);
        }

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

            OrderItem::create([
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
        $user = auth()->user();

        if (!$order || $order->user_id !== $user->id) {
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

    /**
     * @OA\Post (
     *     path="/dgush-backend/public/api/applyCouponToOrder/{id}",
     *     summary="Apply a coupon to an order",
     *     tags={"Order"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"coupon"},
     *             @OA\Property(property="coupon", type="string", example="PRIMERA31")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Coupon applied successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Order not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The coupon field is required")
     *         )
     *     )
     * )
     *
     */
    public function applyCoupon(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'coupon' => 'required|exists:coupon,code'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $order = Order::find($id);
        $user = auth()->user();

        if (!$order || $order->user_id !== $user->id) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $coupon = Coupon::where('code', $request->input('coupon'))->first();
        if (!$coupon) {
            return response()->json(['error' => 'Coupon not found'], 404);
        }

        if (!$coupon->active) {
            return response()->json(['error' => 'Coupon is not active'], 422);
        }

        if ($coupon->expires_at < now()) {
            return response()->json(['error' => 'Coupon has expired'], 422);
        }

        $discount = 0;

        if ($coupon->type === 'percentage') {
            $discount = $order->subtotal * $coupon->value / 100;
        } else if ($coupon->type === 'discount') {
            $discount = $coupon->value;
        }

        $total = $order->subtotal + $order->sendCost - $discount;

        $order->update([
            'coupon_id' => $coupon->id,
            'discount' => $discount,
            'total' => $total
        ]);

        $order = Order::with('user', 'orderItems.productDetail.product.image',
            'orderItems.productDetail.color', 'orderItems.productDetail.size', 'coupon')
            ->find($order->id);

        return response()->json($order);
    }

    /**
     * @OA\Post (
     *     path="/dgush-backend/public/api/confirmOrder",
     *     summary="Confirm order",
     *     tags={"Order"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"names", "dni", "email", "phone", "address", "reference", "comment", "method", "district_id"},
     *             @OA\Property(property="names", type="string", example="John Doe"),
     *             @OA\Property(property="dni", type="string", example="12345678"),
     *             @OA\Property(property="email", type="string", example="johndoe@gmail.com"),
     *             @OA\Property(property="phone", type="string", example="987654321"),
     *             @OA\Property(property="address", type="string", example="123 Main St."),
     *             @OA\Property(property="reference", type="string", example="Near the park"),
     *             @OA\Property(property="comment", type="string", example="Please call before delivery"),
     *             @OA\Property(property="method", type="string", example="cash"),
     *             @OA\Property(property="district_id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order confirmed successfully",
     *         @OA\JsonContent(ref="#/components/schemas/OrderConfirmation")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Order not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The names field is required")
     *         )
     *     )
     * )
     */
    public function confirmOrder(Request $request, int $id)
    {
        $order = Order::find($id);
        $user = auth()->user();

        if (!$order || $order->user_id !== $user->id) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($order->status !== 'verificado') {
            return response()->json(['error' => 'Order has already been confirmed'], 422);
        }

        $validator = Validator::make($request->all(), [
            'names' => 'required|string',
            'dni' => 'required|string|size:8',
            'email' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'reference' => 'required|string',
            'comment' => 'nullable|string',
            'method' => 'required|string',
            'district_id' => 'required|exists:district,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'names' => $request->input('names'),
            'dni' => $request->input('dni'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'reference' => $request->input('reference'),
            'comment' => $request->input('comment'),
            'method' => $request->input('method'),
            'district_id' => $request->input('district_id'),
            'order_id' => $id,
//            NUMBER OF PAYMENT
//            'payment' => $request->input('payment')
        ];

        $sendInformation = SendInformation::create($data);

        if (!$sendInformation) {
            return response()->json(['error' => 'Error creating send information'], 500);
        }

        $order->update([
            'status' => 'confirmado'
        ]);

        $order = Order::with('user', 'orderItems.productDetail.product.image',
            'orderItems.productDetail.color', 'orderItems.productDetail.size', 'coupon', 'sendInformation')
            ->find($order->id);

        return response()->json($order);
    }

    /**
     * @OA\Post (
     *     path="/dgush-backend/public/api/cancelOrder/{id}",
     *     summary="Cancel order",
     *     tags={"Order"},
     *     @OA\Response(
     *         response=200,
     *         description="Order cancelado successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Order cancelado successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Order not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Order has already been confirmed",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Order has already been confirmed")
     *         )
     *     )
     * )
     */
    public function cancelOrder(int $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($order->status !== 'verificado') {
            return response()->json(['error' => 'Order has already been confirmed'], 422);
        }

        $order->update([
            'status' => 'cancelado'
        ]);

        return response()->json(['message' => 'Order cancelado successfully']);
    }

    public function orderStatus()
    {
        $orders = Order::all();
        $verificado = $orders->where('status', 'verificado')->count();
        $confirmado = $orders->where('status', 'confirmado')->count();
        $enviado = $orders->where('status', 'enviado')->count();
        $entregado = $orders->where('status', 'entregado')->count();
        $cancelado = $orders->where('status', 'cancelado')->count();
        return response()->json([
            'verificado' => $verificado,
            'confirmado' => $confirmado,
            'enviado' => $enviado,
            'entregado' => $entregado,
            'cancelado' => $cancelado
        ]);
    }

    /**
     * @OA\Get (
     *     path="/dgush-backend/public/api/dashboardOrders",
     *     summary="Get orders for dashboard",
     *     tags={"Order"},
     *     @OA\Response(
     *         response=200,
     *         description="Orders retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="total", type="integer", example="10"),
     *             @OA\Property(property="pending", type="integer", example="5"),
     *             @OA\Property(property="confirmed", type="integer", example="3"),
     *             @OA\Property(property="cancelado", type="integer", example="1"),
     *             @OA\Property(property="confirmado", type="integer", example="1")
     *         )
     *     )
     * )
     */
    public function dashboardOrders()
    {
        $orders = Order::all();
        $total = $orders->count();
        $verificado = $orders->where('status', 'verificado')->count();
        $confirmado = $orders->where('status', 'confirmado')->count();
        $enviado = $orders->where('status', 'enviado')->count();
        $entregado = $orders->where('status', 'entregado')->count();
        $cancelado = $orders->where('status', 'cancelado')->count();


        return response()->json([
            [
                'description' => 'Total de Órdenes',
                'value' => $total
            ],
            [
                'description' => 'Órdenes Generadas',
                'value' => $verificado
            ],
            [
                'description' => 'Órdenes Pagadas',
                'value' => $confirmado
            ],
            [
                'description' => 'Órdenes Enviadas',
                'value' => $enviado
            ],
            [
                'description' => 'Órdenes Entregadas',
                'value' => $entregado
            ],
            [
                'description' => 'Órdenes Canceladas',
                'value' => $cancelado
            ]
        ]);
    }

}
