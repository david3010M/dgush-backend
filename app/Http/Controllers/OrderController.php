<?php
namespace App\Http\Controllers;

use App\Http\Requests\Order\ListOrders360Request;
use App\Http\Requests\Order\PayOrderRequest;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Mail\ConfirmOrder;
use App\Mail\StatusOrder;
use App\Models\Coupon;
use App\Models\District;
use App\Models\Image;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductDetails;
use App\Models\Sede;
use App\Models\SendInformation;
use App\Models\User;
use App\Models\Zone;
use App\Services\Api360Service;
use App\Services\AuditLogService;
use App\Services\CulquiService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{

    protected $api360Service;
    protected $culqiService;
    protected $orderService;
    // Inyectamos el servicio en el controlador
    public function __construct(Api360Service $api360Service, CulquiService $culqiService, OrderService $orderService)
    {
        $this->api360Service = $api360Service;
        $this->culqiService = $culqiService;
        $this->orderService = $orderService;
    }

    /**
     * Orders from the authenticated user
     * @OA\Get (
     *     path="/dgush-backend/public/api/order",
     *     summary="Get all orders",
     *     security={{"bearerAuth": {}}},
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
        $orders = Order::with(
            'user',
            'orderItems.productDetail.product.image',
            'orderItems.productDetail.color',
            'orderItems.productDetail.size',
            'coupon'
        )
            ->where('user_id', auth()->user()->id)
            ->orderBy('id', 'desc');

        return $pageSize ? OrderResource::collection($orders->simplePaginate($pageSize))
            : response()->json(OrderResource::collection($orders->get()));
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
     *             @OA\Property(property="status", type="string", example="VERIFICANDO"),
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
            'status' => 'nullable|string|in:VERIFICANDO,CONFIRMADO,enviado,entregado,cancelado,recojotiendaproceso,recojotiendalisto',
            'sort' => 'nullable|string|in:none,date-asc,date-desc',
            'date' => 'nullable|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $orders = Order::with(
            'user',
            'orderItems.productDetail.product.image',
            'orderItems.productDetail.color',
            'orderItems.productDetail.size',
            'coupon',
            'sendInformation.district'
        )
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', 'like', '%' . $request->input('status') . '%');
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->whereDate('date', $request->input('date'))
                        ->orWhereDate('created_at', $request->input('date'));
                });
            });

        $sort = $request->input('sort');

        if ($sort === 'date-asc') {
            $orders->orderBy('date');
        } else if ($sort === 'date-desc') {
            $orders->orderBy('date', 'desc');
        } else {
            $orders->orderBy('id', 'desc');
        }

        return response()->json(OrderResource::collection($orders->get()));
    }

    public function showOrder(int $id)
    {
        $order = Order::with(
            'user',
            'orderItems.productDetail.product.image',
            'orderItems.productDetail.color',
            'orderItems.productDetail.size',
            'coupon'
        )
            ->find($id);

        $user = auth()->user();
        $user = User::find($user->id);

        if (!$order || $order->user_id !== $user->id && $user->typeuser->name !== 'Admin') {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json($order);
    }

    /**
     * @OA\Post  (
     *     path="/dgush-backend/public/api/order/searchPaginate",
     *     summary="Search orders",
     *     tags={"Order"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="VERIFICANDO"),
     *             @OA\Property(property="sort", type="string", example="date-asc"),
     *             @OA\Property(property="direction", type="string", example="asc"),
     *             @OA\Property(property="date", type="string", example="2024-05-26"),
     *             @OA\Property(property="per_page", type="integer", example="10"),
     *             @OA\Property(property="page", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response( response=200, description="Orders retrieved successfully", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/OrderResource")) ),
     *     @OA\Response( response=422, description="Validation error", @OA\JsonContent(@OA\Property(property="error", type="string")) ),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(@OA\Property(property="error", type="string")) )
     * )
     *
     */
    public function searchPaginate(Request $request)
    {
        $validator = validator($request->all(), [
            'status' => 'nullable|string|in:VERIFICANDO,CONFIRMADO,enviado,entregado,cancelado,recojotiendaproceso,recojotiendalisto',
            'sort' => 'nullable|string|in:none,date-asc,date-desc',
            'direction' => 'nullable|string|in:asc,desc',
            'date' => 'nullable|date_format:Y-m-d',
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $orders = Order::with(
            'user',
            'orderItems.productDetail.product.image',
            'orderItems.productDetail.color',
            'orderItems.productDetail.size',
            'coupon',
            'sendInformation.district'
        )
            ->where('status', 'like', '%' . $request->input('status') . '%')
            ->whereDate('date', 'like', '%' . $request->input('date') . '%');

        $sort = $request->input('sort', 'none');
        $direction = $request->input('direction', 'desc');

        if ($sort === 'date-asc') {
            $orders->orderBy('date');
        } else if ($sort === 'date-desc') {
            $orders->orderBy('date', 'desc');
        }

        $per_page = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $orders = $orders->orderBy($sort == 'none' ? 'id' : $sort, $direction)->paginate($per_page, ['*'], 'page', $page);
        OrderResource::collection($orders);
        return response()->json($orders);
    }

    /**
     * @OA\Post(
     *     path="/dgush-backend/public/api/order",
     *     summary="Create a new order",
     *     security={{"bearerAuth": {}}},
     *     tags={"Order"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 ref="#/components/schemas/360StoreOrderRequest"
     *             )
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

    public function pay_order($id_order, PayOrderRequest $request)
    {
        try {

            $order = Order::find($id_order);
            if (!$order) {
                return response()->json([
                    'error' => 'Orden No encontrada',
                ], 422);
            }
            //VALIDAR CALCULOS ANTES DE HACER EL CARGO
            $calculatedValues = $this->orderService->calculate($request);

            // Verificar si hubo algún error en el cálculo
            if (isset($calculatedValues['error'])) {
                return response()->json([
                    'error' => 'Error en los cálculos',
                    'message' => $calculatedValues['message'],
                ], 400);
            }

            $total = $calculatedValues['total'];
            $shipping_cost = $calculatedValues['sendCost'];

            // 1. Procesar el pago con Culqi
            $result = $this->culqiService->createCharge($request);
            AuditLogService::log('culqi_create_charge', $request->all(), $result);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'El pago falló.',
                    'error' => $result['message'] ?? 'Error desconocido en el pago.',
                ], 400);
            }

            // 2. Preparar el payload para 360
            $data_adicional = [];


            $districtServer = null;
            $zoneServer = null;
            $branchServer = null;

            if ($request->mode === 'ENVIO') {
                $district = District::find($request->district_id);
                if ($district) {
                    $districtServer = $district->server_id;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Distrito no encontrado.',
                    ], 422);
                }
            }

            if ($request->mode === 'DELIVERY') {
                $zone = Zone::find($request->zone_id);
                if ($zone) {
                    $zoneServer = $zone->server_id;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Zona no encontrada.',
                    ], 422);
                }
            }

            if ($request->mode === 'RECOJO') {
                $branch = Sede::find($request->branch_id);
                if ($branch) {
                    $branchServer = $branch->server_id;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sucursal no encontrada.',
                    ], 422);
                }
            }

            $payload = [
                "mode" => $request->mode, //RECOJO, DELIVERY, ENVIO
                "scheduled_date" => $request->scheduled_date,
                "cellphone_number" => $request->cellphone,
                "email_address" => $request->email_address,
                "address" => $request->address,
                "zone_id" => $zoneServer,     // Requerido cuando el modo es DELIVERY
                "district_id" => $districtServer, // Requerido cuando el modo es ENVIO
                "branch_id" => $branchServer,   // Requerido cuando el modo es RECOJO
                "customer" => [
                    "dni" => $request->customer_dni,
                    "first_name" => $request->customer_first_name,
                    "last_name" => $request->customer_last_name,
                ],
                "notes" => $request->notes ?? "-",
                "total" => $total, //calcular de los detalles
                "currency" => "PEN",
                "payment" => [
                    "method" => $request->payment_method ?? 'TARJETA', // Opciones válidas: TARJETA, BILLETERA DIGITAL
                    "pos" => "CULQI",                               // Opciones válidas: IZIPAY, NIUBIZ, CULQI
                    "card" => [
                        "name" => $request->payment_card_name ?? "VISA",    // Opciones válidas: VISA, MASTERCARD, AMERICAN EXPRESS, DINERS CLUB INTERNATIONAL
                        "type" => $request->payment_card_type ?? "CREDITO", // Opciones válidas: CREDITO, DEBITO
                    ],
                    "digitalwallet" => $request->payment_digitalwallet ?? null, // Opcional, puede ser YAPE o null
                ],
                //verificar el requerido para ambos ENVIO(distrito), Delivery(zona)
                "shipping_cost" => (float) $shipping_cost ?? 0, // puede ser 0, no negativo

                "products" => $request->products ?? [],
            ];

            if (isset($request->coupon)) {
                $coupon = Coupon::where('code', $request->coupon)->first();
                $data_adicional['coupon_id'] = $coupon->id;
            }

            // Si el UUID no está presente, lo dejamos vacío
            $uuid = $request->input('UUID', '');

            // 3. Enviar el pedido a la API externa (360)
            $api360Response = $this->culqiService->orderPostRequest('orders', $uuid, $payload);
            AuditLogService::log('api360_order_post', ['uuid' => $uuid, 'payload' => $payload], $api360Response);

            // Verificar la respuesta de la API 360
            if (!$api360Response['status']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al registrar el pedido.',
                    'api_error' => $api360Response['message'],
                    'api_details' => $api360Response['data'] ?? [],
                ]);
            }

            // 4. Obtener y guardar la orden usando el ID recibido
            $orderId360 = $api360Response['data']['data']['id'] ?? null;
            //actualizar id de servide_id de la orden
            $order->server_id = $orderId360;
            $order->save();
            $orderInfo = $this->orderService->getOrdertosave(
                $orderId360,
                $uuid,
                $data_adicional,
                Order::class,
                Order::getfields360
            );
            AuditLogService::log('api360_order_get_save', ['order_id' => $orderId360], $orderInfo);

            // 5. Respuesta final con el pago y el pedido procesado correctamente
            return response()->json([
                'success' => true,
                'message' => 'Pago y pedido registrados correctamente.',
                'payment_data' => $result['object'],
                'api_360_result' => $api360Response,
            ]);

        } catch (\Exception $e) {
            AuditLogService::log('exception_caught', request()->all(), ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            // Validar cálculos antes de hacer el cargo
            $calculatedValues = $this->orderService->calculate($request);

            // Verificar si hubo algún error en el cálculo
            if (!empty($calculatedValues['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $calculatedValues['message'] ?? 'Error en los cálculos',
                ], 400);
            }

            // Obtener datos validados y combinar con los cálculos

            $subtotal = $calculatedValues['subtotal'] ?? 0;
            $total = $calculatedValues['total'] ?? 0;
            $shipping_cost = $calculatedValues['sendCost'] ?? 0;
            $discount = $calculatedValues['discount'] ?? 0;

            $payload = [
                "mode" => $request->mode, //RECOJO, DELIVERY, ENVIO
                "scheduled_date" => $request->scheduled_date,
                "cellphone_number" => $request->cellphone,
                "email_address" => $request->email_address,
                "address" => $request->address,
                "zone_id" => $request->zone_id,     // Requerido cuando el modo es DELIVERY
                "district_id" => $request->district_id, // Requerido cuando el modo es ENVIO
                "branch_id" => $request->branch_id,   // Requerido cuando el modo es RECOJO
                "customer" => [
                    "dni" => $request->customer_dni,
                    "first_name" => $request->customer_first_name,
                    "last_name" => $request->customer_last_name,
                ],
                "notes" => $request->notes,
                "subtotal" => $subtotal,
                "total" => $total, //calcular de los detalles
                "currency" => "PEN",
                "payment" => [
                    "method" => $request->payment_method ?? 'TARJETA', // Opciones válidas: TARJETA, BILLETERA DIGITAL
                    "pos" => "CULQI",                               // Opciones válidas: IZIPAY, NIUBIZ, CULQI
                    "card" => [
                        "name" => $request->payment_card_name ?? "VISA",    // Opciones válidas: VISA, MASTERCARD, AMERICAN EXPRESS, DINERS CLUB INTERNATIONAL
                        "type" => $request->payment_card_type ?? "CREDITO", // Opciones válidas: CREDITO, DEBITO
                    ],
                    "digitalwallet" => $request->payment_digitalwallet ?? null, // Opcional, puede ser YAPE o null
                ],
                //verificar el requerido para ambos ENVIO(distrito), Delivery(zona)
                "shipping_cost" => $shipping_cost ?? 0, // puede ser 0, no negativo
                "sendCost" => $shipping_cost ?? 0, // puede ser 0, no negativo
                "discount" => $discount ?? 0, // puede ser 0, no negativo
                "products" => $request->products ?? [],
            ];

            // Crear la orden
            $order = $this->orderService->createOrder($payload);

            return response()->json([
                'success' => true,
                'message' => 'Orden creada correctamente',
                'order' => new OrderResource(Order::find($order->id)),
            ], 201);

        } catch (\Exception $e) {
            AuditLogService::log('exception_caught', $request->all(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error inesperado: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post  (
     *     path="/dgush-backend/public/api/order/updateStatus/{id}",
     *     summary="Search orders",
     *     tags={"Order"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Order ID", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"status"},
     *                  @OA\Property(property="status", type="string", enum={"VERIFICANDO", "CONFIRMADO", "enviado", "entregado", "cancelado", "recojotiendaproceso", "recojotiendalisto", "agencia"}),
     *                  @OA\Property(property="description", type="string", example="Order confirmed"),
     *                  @OA\Property(property="tracking", type="string", example="123456"),
     *                  @OA\Property(property="voucher", type="string", example="123456"),
     *                  @OA\Property(property="image", type="file", format="binary")
     *              )
     *         )
     *     ),
     *     @OA\Response( response=200, description="Orders retrieved successfully", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/OrderResource")) ),
     *     @OA\Response( response=422, description="Validation error", @OA\JsonContent(@OA\Property(property="error", type="string")) ),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(@OA\Property(property="error", type="string")) )
     * )
     *
     */
    public function updateStatus(Request $request, int $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:VERIFICANDO,CONFIRMADO,enviado,entregado,cancelado,recojotiendaproceso,recojotiendalisto,agencia',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        if ($request->input('status') === 'agencia') {
            if (!$order->sendInformation) {
                return response()->json(['error' => 'Send information not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'tracking' => 'nullable|string',
                'voucher' => 'required|string',
                'image' => 'required|image',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 422);
            }

            $image = $request->file('image');
            $fileName = 'vouchers_envio/' . $order->number . '/' . $image->getClientOriginalName();
            Storage::disk('spaces')->put($fileName, file_get_contents($image), 'private');
            $imageUrl = Storage::disk('spaces')->url($fileName);
            $sendInformation = SendInformation::where('order_id', $order->id)->first();
            $sendInformation->update([
                'tracking' => $request->input('tracking'),
                'voucher' => $request->input('voucher'),
                'voucherUrl' => $imageUrl,
                'voucherFileName' => $fileName,
            ]);
        }

        $data = [
            'status' => $request->input('status'),
            'description' => $request->input('description'),
        ];

        $order->update($data);
        $order = Order::with(
            'user',
            'orderItems.productDetail.product.image',
            'orderItems.productDetail.color',
            'orderItems.productDetail.size',
            'coupon'
        )
            ->find($order->id);

        $dictionaryMessages = [
            'enviado' => '¡Buenas noticias! Tu pedido ha sido enviado y está en camino. Gracias por tu paciencia. Si tienes alguna duda, no dudes en contactarnos.',
            'entregado' => 'Queremos informarte que tu pedido ha sido entregado con éxito. Esperamos que disfrutes de tu compra. Si tienes alguna duda, no dudes en contactarnos.',
            'recojotiendaproceso' => 'Tu pedido está en proceso de preparación para el recojo en tienda. Te informaremos cuando esté listo para que puedas pasar a retirarlo.',
            'recojotiendalisto' => '¡Tu pedido está listo para ser recogido en nuestra tienda! Puedes pasar por él en cualquier momento dentro de nuestro horario de atención.',
            'agencia' => 'Tu pedido ha sido enviado a la agencia de transporte. Pronto te llegará a la dirección que nos proporcionaste. Si tienes alguna duda, no dudes en contactarnos.',
        ];

        $dictonarySubject = [
            'enviado' => ' Pedido ' . $order->number . ' Enviado',
            'entregado' => 'Pedido ' . $order->number . ' Entregado',
            'recojotiendaproceso' => 'Pedido ' . $order->number . ' en Proceso para Recojo en Tienda',
            'recojotiendalisto' => 'Pedido ' . $order->number . ' Listo para Recojo en Tienda',
            'agencia' => 'Pedido ' . $order->number . ' Enviado a la Agencia de Transporte',
        ];

        $statusDictionary = [
            'enviado' => 'Enviado',
            'entregado' => 'Entregado',
            'recojotiendaproceso' => 'En Proceso para Recojo en Tienda',
            'recojotiendalisto' => 'Listo para Recojo en Tienda',
            'agencia' => 'Enviado a la Agencia de Transporte',
        ];

        if (
            $order->status !== 'VERIFICANDO' &&
            $order->status !== 'CONFIRMADO' &&
            $order->status !== 'cancelado'
        ) {
            Mail::to($order->user->email)->send(new StatusOrder(
                $order,
                $order->user,
                $order->orderItems,
                $order->total,
                $dictionaryMessages[$order->status],
                $dictonarySubject[$order->status],
                $statusDictionary[$order->status]
            ));
        }

        return response()->json($order);
    }

    /**
     * @OA\Get (
     *     path="/dgush-backend/public/api/downloadVoucherSend/{id}",
     *     summary="Download voucher send",
     *     tags={"Order"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Order ID", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Voucher retrieved successfully", @OA\JsonContent(type="file") ),
     *     @OA\Response( response=404, description="Voucher not found", @OA\JsonContent(@OA\Property(property="error", type="string")) ),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(@OA\Property(property="error", type="string")) )
     * )
     */
    public function downloadVoucherSend(int $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $sendInformation = SendInformation::where('order_id', $order->id)->first();
        if (!$sendInformation) {
            return response()->json(['error' => 'Send information not found'], 404);
        }

        if (!$sendInformation->voucherFileName) {
            return response()->json(['error' => 'Voucher not found'], 404);
        }

        $file = Storage::disk('spaces')->get($sendInformation->voucherFileName);
        return response($file, 200)->header('Content-Type', 'image/jpeg');
    }

    /**
     * @OA\Get  (
     *     path="/dgush-backend/public/api/order/{id}",
     *     summary="Search orders",
     *     tags={"Order"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Order ID", @OA\Schema(type="integer")),
     *     @OA\Response( response=200, description="Orders retrieved successfully", @OA\JsonContent(ref="#/components/schemas/Order")),
     *     @OA\Response( response=422, description="Validation error", @OA\JsonContent(@OA\Property(property="error", type="string")) ),
     *     @OA\Response( response=401, description="Unauthenticated", @OA\JsonContent(@OA\Property(property="error", type="string")) )
     * )
     *
     */
    public function show(int $id)
    {
        $order = Order::with(
            'user',
            'orderItems.productDetail.product.image',
            'orderItems.productDetail.color',
            'orderItems.productDetail.size',
            'coupon',
            'sendInformation'
        )
            ->find($id);

        $user = auth()->user();
        $user = User::find($user->id);

        if (!$order || $order->user_id !== $user->id && $user->typeuser->name !== 'Admin') {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json(new OrderResource($order));
    }

    public function update(Request $request, int $id)
    {
        // Encontrar la orden existente
        $order = Order::find($id);
        $user = auth()->user();
        if (!$order || $order->user_id !== $user->id) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($order->status !== 'VERIFICANDO') {
            return response()->json(['error' => 'Order has already been confirmed'], 422);
        }

        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'orderItems' => 'required|array',
            'orderItems.*.product_id' => 'required|exists:product,id',
            'orderItems.*.color_id' => 'required|exists:color,id',
            'orderItems.*.size_id' => 'required|exists:size,id',
            'orderItems.*.quantity' => 'required|integer',
            'coupon_id' => 'nullable|exists:coupon,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Eliminar los productos actuales de la orden y revertir el stock
        foreach ($order->orderItems as $orderItem) {
            $productDetail = $orderItem->productDetail;
            $productDetail->update([
                'stock' => $productDetail->stock + $orderItem->quantity,
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
            $stock = (float) $productDetail->stock;
            if ($stock < $products[$key]['quantity']) {
                return response()->json(['error' => 'The product is out of stock'], 422);
            }

            $quantityOfProduct = $products[$key]['quantity'];
            $priceChose = ($productDetail->product->liquidacion == true) ? $productDetail->product->priceLiquidacion :
                ($productDetail->product->status == 'onsale' ? $productDetail->product->priceOferta :
                    ($quantityOfProduct >= 12 ? $productDetail->product->price12 :
                        ($quantityOfProduct >= 3 ? $productDetail->product->price2 :
                            $productDetail->product->price1)));

            OrderItem::create([
                'quantity' => $quantityOfProduct,
                'price' => $priceChose,
                'product_detail_id' => $productDetail->id,
                'order_id' => $order->id,
            ]);

            $productDetail->update([
                'stock' => $stock - $products[$key]['quantity'],
            ]);

            $quantity += $products[$key]['quantity'];
            $subtotal += $productDetail->product->price1 * $products[$key]['quantity'];
        }

        // Actualizar la orden con el nuevo subtotal y cantidad
        $order->update([
            'subtotal' => $subtotal,
            'total' => $subtotal, // Puedes aplicar descuentos o impuestos aquí si es necesario
            'quantity' => $quantity,
            'coupon_id' => $request->input('coupon_id'),
        ]);

        // Devolver la orden actualizada
        $order = Order::with(
            'user',
            'orderItems.productDetail.product.image',
            'orderItems.productDetail.color',
            'orderItems.productDetail.size',
            'coupon'
        )
            ->find($order->id);

        return response()->json($order);
    }

    public function destroy(int $id)
    {
        $order = Order::find($id);
        $user = auth()->user();

        if (
            !$order
            || $order->status !== 'VERIFICANDO'
            || $order->user_id !== $user->id
        ) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        foreach ($order->orderItems as $orderItem) {
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
        $order = Order::find($id);
        $user = auth()->user();

        if (!$order || $order->user_id !== $user->id) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($order->status !== 'VERIFICANDO') {
            return response()->json(['error' => 'Order has already been verified'], 422);
        }

        $validator = Validator::make($request->all(), [
            'coupon' => 'required|exists:coupon,code',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
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
            if ($coupon->indicator === 'subtotal') {
                $discount = $order->subtotal * $coupon->value / 100;
            } else if ($coupon->indicator === 'total') {
                $discount = $order->total * $coupon->value / 100;
            } else if ($coupon->indicator === 'sendCost') {
                $discount = $order->sendCost * $coupon->value / 100;
            }

        } else if ($coupon->type === 'discount') {
            $discount = $coupon->value;
        }

        $total = $order->subtotal + $order->sendCost - $discount;

        $order->update([
            'coupon_id' => $coupon->id,
            'discount' => $discount,
            'total' => $total,
        ]);

        $order = Order::with(
            'user',
            'orderItems.productDetail.product.image',
            'orderItems.productDetail.color',
            'orderItems.productDetail.size',
            'coupon'
        )
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
     *             required={"names", "dni", "email", "phone", "address", "reference", "comment", "method", "paymentId"},
     *             @OA\Property(property="names", type="string", example="John Doe"),
     *             @OA\Property(property="dni", type="string", example="12345678"),
     *             @OA\Property(property="email", type="string", example="johndoe@gmail.com"),
     *             @OA\Property(property="phone", type="string", example="987654321"),
     *             @OA\Property(property="address", type="string", example="123 Main St."),
     *             @OA\Property(property="reference", type="string", example="Near the park"),
     *             @OA\Property(property="comment", type="string", example="Please call before delivery"),
     *             @OA\Property(property="method", type="string", example="cash"),
     *             @OA\Property(property="district_id", type="integer", example="1"),
     *             @OA\Property(property="sede_id", type="integer", example="1"),
     *             @OA\Property(property="paymentId", type="string", example="123456"),
     *             @OA\Property(property="paymentNumber", type="string", example="123456")
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

        if ($order->status !== 'VERIFICANDO') {
            return response()->json(['error' => 'Order has already been confirmed'], 422);
        }

        $validator = Validator::make($request->all(), [
            'names' => 'required|string',
            'dni' => 'required|string|size:8',
            'email' => 'required|string',
            'phone' => 'required|string|min:9|max:9',
            'address' => 'required|string',
            'reference' => 'required|string',
            'comment' => 'nullable|string',
            'method' => 'required|string|in:delivery,pickup,send',
            'paymentId' => 'required|string',
            'paymentNumber' => 'nullable|string',
        ]);

        if ($request->input('method') === 'pickup') {
            $validator->addRules([
                'sede_id' => [
                    'required',
                    Rule::exists('sedes', 'id')->whereNull('deleted_at'),
                ],
            ]);
        } elseif ($request->input('method') === 'send') {
            $validator->addRules([
                'district_id' => [
                    'required',
                    Rule::exists('district', 'id')->whereNull('deleted_at'),
                ],
            ]);
        } else {
            $validator->addRules([
                'zone_id' => [
                    'required',
                    Rule::exists('zones', 'id')->whereNull('deleted_at'),
                ],
            ]);
        }

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        if ($request->input('method') === 'delivery' && !$request->input('zone_id')) {
            return response()->json(['error' => 'The zone field is required'], 422);
        }

        if ($request->input('method') === 'pickup' && !$request->input('sede_id')) {
            return response()->json(['error' => 'The sede field is required'], 422);
        }

        if ($request->input('method') === 'send' && !$request->input('district_id')) {
            return response()->json(['error' => 'The district field is required'], 422);
        }

        $method = $request->input('method');

        $data = [
            'names' => $request->input('names'),
            'dni' => $request->input('dni'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'reference' => $request->input('reference'),
            'comment' => $request->input('comment'),
            'method' => $method,
            'zone_id' => $method === 'delivery' ? $request->input('zone_id') : null,
            'sede_id' => $method === 'pickup' ? $request->input('sede_id') : null,
            'district_id' => $method === 'send' ? $request->input('district_id') : null,
            'order_id' => $id,
            //            NUMBER OF PAYMENT
            'paymentId' => $request->input('paymentId'),
            'paymentNumber' => $request->input('paymentNumber'),
        ];

        $sendInformation = SendInformation::create($data);

        if (!$sendInformation) {
            return response()->json(['error' => 'Error creating send information'], 500);
        }

        if ($request->input('method') === 'delivery') {
            $zone = Zone::find($request->input('zone_id'));
            $order->update([
                'sendCost' => $zone->sendCost,
                'total' => $order->subtotal + $zone->sendCost,
            ]);

            if ($order->coupon_id) {
                $coupon = Coupon::find($order->coupon_id);
                $discount = 0;

                if ($coupon->type === 'percentage') {
                    if ($coupon->indicator === 'subtotal') {
                        $discount = $order->subtotal * $coupon->value / 100;
                    } else if ($coupon->indicator === 'total') {
                        $discount = $order->total * $coupon->value / 100;
                    } else if ($coupon->indicator === 'sendCost') {
                        $discount = $order->sendCost * $coupon->value / 100;
                    }

                } else if ($coupon->type === 'discount') {
                    $discount = $coupon->value;
                }

                $order->update([
                    'status' => 'CONFIRMADO',
                    'sendCost' => $zone->sendCost,
                    'discount' => $discount,
                    'total' => $order->subtotal + $zone->sendCost - $discount,
                ]);
            } else {
                $order->update([
                    'status' => 'CONFIRMADO',
                    'sendCost' => $zone->sendCost,
                    'total' => $order->subtotal + $zone->sendCost,
                ]);
            }
        } elseif ($request->input('method') === 'send') {
            $district = District::find($request->input('district_id'));

            $order->update([
                'sendCost' => $district->sendCost,
                'total' => $order->subtotal + $district->sendCost,
            ]);

            if ($order->coupon_id) {
                $coupon = Coupon::find($order->coupon_id);
                $discount = 0;

                if ($coupon->type === 'percentage') {
                    if ($coupon->indicator === 'subtotal') {
                        $discount = $order->subtotal * $coupon->value / 100;
                    } else if ($coupon->indicator === 'total') {
                        $discount = $order->total * $coupon->value / 100;
                    } else if ($coupon->indicator === 'sendCost') {
                        $discount = $order->sendCost * $coupon->value / 100;
                    }

                } else if ($coupon->type === 'discount') {
                    $discount = $coupon->value;
                }

                $order->update([
                    'status' => 'CONFIRMADO',
                    'sendCost' => $district->sendCost,
                    'discount' => $discount,
                    'total' => $order->subtotal + $district->sendCost - $discount,
                ]);
            } else {
                $order->update([
                    'status' => 'CONFIRMADO',
                    'sendCost' => $district->sendCost,
                    'total' => $order->subtotal + $district->sendCost,
                ]);
            }

        } else {
            $order->update([
                'status' => 'CONFIRMADO',
                'paymentId' => $request->input('paymentId'),
                'paymentNumber' => $request->input('paymentNumber'),
                'sendCost' => 0,
                'total' => $order->subtotal - $order->discount,
            ]);
        }

        $isAnyPreSale = false;
        $orderItems = $order->orderItems;
        foreach ($orderItems as $orderItem) {
            if ($orderItem->productDetail->product->status === 'preventa') {
                $isAnyPreSale = true;
                break;
            }
        }
        $order->update([
            'deliveryDate' => $order->sendInformation->method === 'delivery' || $order->sendInformation->method === 'send' ? now()->addDays($isAnyPreSale ? 12 : 3) : null,
            'shippingDate' => $order->sendInformation->method === 'pickup' ? now()->addDays($isAnyPreSale ? 12 : 3) : null,
        ]);

        $order = Order::with(
            'user',
            'orderItems.productDetail.product.image',
            'orderItems.productDetail.color',
            'orderItems.productDetail.size',
            'coupon',
            'sendInformation'
        )
            ->find($order->id);

        $orderItems = OrderItem::where('order_id', $order->id)->get();
        foreach ($orderItems as $orderItem) {
            $productDetail = ProductDetails::find($orderItem->product_detail_id);
            $productDetail->update([
                'stock' => $productDetail->stock - $orderItem->quantity,
            ]);
        }

        Mail::to($user->email)->send(new ConfirmOrder(
            $order,
            $order->user,
            $order->orderItems,
            $order->total
        ));

        return response()->json($order);
    }

    /**
     * @OA\Post (
     *     path="/dgush-backend/public/api/updateDates/{id}",
     *     summary="Update dates of an order",
     *     tags={"Order"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="Order ID", @OA\Schema(type="integer")),
     *     @OA\RequestBody( required=true, @OA\JsonContent( required={"date"}, @OA\Property(property="date", type="string", example="2021-12-31") ) ),
     *     @OA\Response( response=200, description="Dates updated successfully", @OA\JsonContent(ref="#/components/schemas/Order") ),
     *     @OA\Response( response=404, description="Order not found", @OA\JsonContent(@OA\Property(property="error", type="string")) ),
     *     @OA\Response( response=422, description="Validation error", @OA\JsonContent(@OA\Property(property="error", type="string")) )
     * )
     */
    public function updateDates(Request $request, int $id)
    {
        $order = Order::find($id);
        $user = auth()->user();

        if (!$order || $order->user_id !== $user->id) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        if ($order->status !== 'CONFIRMADO') {
            return response()->json(['error' => 'Order has not been confirmed'], 422);
        }

        if ($order->sendInformation->method === 'delivery' || $order->sendInformation->method === 'send') {
            $order->update([
                'deliveryDate' => $request->input('date'),
                'shippingDate' => null,
            ]);
        } else {
            $order->update([
                'deliveryDate' => null,
                'shippingDate' => $request->input('date'),
            ]);
        }

        $order = Order::with(
            'user',
            'orderItems.productDetail.product.image',
            'orderItems.productDetail.color',
            'orderItems.productDetail.size',
            'coupon',
            'sendInformation'
        )
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
            return response()->json(['error' => 'Orden no encontrada'], 404);
        }

        if ($order->status !== 'VERIFICANDO') {
            return response()->json(['error' => 'La orden ya ha sido confirmada'], 422);
        }

        $order->update([
            'status' => 'cancelado',
        ]);

        Mail::to($order->user->email)->send(new StatusOrder(
            $order,
            $order->user,
            $order->orderItems,
            $order->total,
            'Lamentamos informarte que tu pedido ' . $order->number . ' ha sido cancelado. Si crees que esto es un error o necesitas más información, por favor contáctanos.',
            'Pedido ' . $order->number . ' Cancelado',
            'Cancelado'
        ));

        return response()->json(['message' => 'Orden cancelada exitosamente']);
    }

    /**
     * @OA\Get (
     *     path="/dgush-backend/public/api/orderStatus",
     *     summary="Get order status",
     *     tags={"Order"},
     *     @OA\Response( response=200, description="Order status retrieved successfully", @OA\JsonContent(
     *     @OA\Property(property="VERIFICANDO", type="integer", example="10"),
     *     @OA\Property(property="CONFIRMADO", type="integer", example="5"),
     *     @OA\Property(property="enviado", type="integer", example="3"),
     *     @OA\Property(property="recojotiendaproceso", type="integer", example="1"),
     *     @OA\Property(property="recojotiendalisto", type="integer", example="1"),
     *     @OA\Property(property="entregado", type="integer", example="1"),
     *     @OA\Property(property="cancelado", type="integer", example="1")
     *    ))
     * )
     */
    public function orderStatus()
    {
        $orders = Order::all();
        $VERIFICANDO = $orders->where('status', 'VERIFICANDO')->count();
        $CONFIRMADO = $orders->where('status', 'CONFIRMADO')->count();
        $enviado = $orders->where('status', 'enviado')->count();
        $recojotiendaproceso = $orders->where('status', 'recojotiendaproceso')->count();
        $recojotiendalisto = $orders->where('status', 'recojotiendalisto')->count();
        $entregado = $orders->where('status', 'entregado')->count();
        $agencia = $orders->where('status', 'agencia')->count();
        $cancelado = $orders->where('status', 'cancelado')->count();
        return response()->json([
            'VERIFICANDO' => $VERIFICANDO,
            'CONFIRMADO' => $CONFIRMADO,
            'enviado' => $enviado,
            'recojotiendaproceso' => $recojotiendaproceso,
            'recojotiendalisto' => $recojotiendalisto,
            'entregado' => $entregado,
            'agencia' => $agencia,
            'cancelado' => $cancelado,
        ]);
    }

    /**
     * @OA\Post (
     *     path="/dgush-backend/public/api/updateMethod/{id}",
     *     summary="Set order district",
     *     tags={"Order"},
     *     @OA\RequestBody( required=true,
     *         @OA\JsonContent(
     *             required={"method"},
     *             @OA\Property(property="method", type="string", example="delivery"),
     *             @OA\Property(property="district_id", type="integer", example="1"),
     *             @OA\Property(property="zone_id", type="integer", example="1")
     *
     *         )
     *     ),
     *     @OA\Response( response=200, description="Order district set successfully", @OA\JsonContent(ref="#/components/schemas/Order")),
     *     @OA\Response( response=404, description="Order not found", @OA\JsonContent(@OA\Property(property="error", type="string", example="Order not found")) ),
     *     @OA\Response( response=422, description="Validation error", @OA\JsonContent(@OA\Property(property="error", type="string", example="The district field is required")) )
     * )
     */
    public function setOrderMethod(Request $request, int $id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($order->status !== 'VERIFICANDO') {
            return response()->json(['error' => 'Order has already been verified'], 422);
        }

        $validator = Validator::make($request->all(), [
            'method' => 'required|string|in:send,pickup,delivery',
            'district_id' => 'nullable|integer|exists:district,id',
            'zone_id' => 'nullable|integer|exists:zones,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        if ($request->input('method') === 'send' && !$request->input('district_id')) {
            return response()->json(['error' => 'The district field is required'], 422);
        }

        if ($request->input('method') === 'delivery' && !$request->input('zone_id')) {
            return response()->json(['error' => 'The zone field is required'], 422);
        }

        if ($request->input('method') === 'send') {
            $district = District::find($request->input('district_id'));
            if (!$district) {
                return response()->json(['error' => 'District not found'], 404);
            }

            $order->update([
                'sendCost' => $district->sendCost,
                'total' => $order->subtotal + $district->sendCost,
            ]);

            //            VALIDAR EL CUPON
            if ($order->coupon_id) {
                $coupon = Coupon::find($order->coupon_id);
                $discount = 0;

                if ($coupon->type === 'percentage') {
                    if ($coupon->indicator === 'subtotal') {
                        $discount = $order->subtotal * $coupon->value / 100;
                    } else if ($coupon->indicator === 'total') {
                        $discount = $order->total * $coupon->value / 100;
                    } else if ($coupon->indicator === 'sendCost') {
                        $discount = $order->sendCost * $coupon->value / 100;
                    }

                } else if ($coupon->type === 'discount') {
                    $discount = $coupon->value;
                }

                $order->update([
                    'sendCost' => $district->sendCost,
                    'discount' => $discount,
                    'total' => $order->subtotal + $district->sendCost - $discount,
                ]);
            } else {
                $order->update([
                    'sendCost' => $district->sendCost,
                    'total' => $order->subtotal + $district->sendCost,
                ]);
            }
        } elseif ($request->input('method') === 'delivery') {
            $zone = Zone::find($request->input('zone_id'));
            if (!$zone) {
                return response()->json(['error' => 'Zone not found'], 404);
            }

            $order->update([
                'sendCost' => $zone->sendCost,
                'total' => $order->subtotal + $zone->sendCost,
            ]);

            if ($order->coupon_id) {
                $coupon = Coupon::find($order->coupon_id);
                $discount = 0;

                if ($coupon->type === 'percentage') {
                    if ($coupon->indicator === 'subtotal') {
                        $discount = $order->subtotal * $coupon->value / 100;
                    } else if ($coupon->indicator === 'total') {
                        $discount = $order->total * $coupon->value / 100;
                    } else if ($coupon->indicator === 'sendCost') {
                        $discount = $order->sendCost * $coupon->value / 100;
                    }

                } else if ($coupon->type === 'discount') {
                    $discount = $coupon->value;
                }

                $order->update([
                    'sendCost' => $zone->sendCost,
                    'discount' => $discount,
                    'total' => $order->subtotal + $zone->sendCost - $discount,
                ]);
            } else {
                $order->update([
                    'sendCost' => $zone->sendCost,
                    'total' => $order->subtotal + $zone->sendCost,
                ]);
            }
        } else {
            $order->update([
                'sendCost' => 0,
            ]);

            if ($order->coupon_id) {
                $coupon = Coupon::find($order->coupon_id);
                $discount = 0;

                if ($coupon->type === 'percentage') {
                    if ($coupon->indicator === 'subtotal') {
                        $discount = $order->subtotal * $coupon->value / 100;
                    } else if ($coupon->indicator === 'total') {
                        $discount = $order->total * $coupon->value / 100;
                    } else if ($coupon->indicator === 'sendCost') {
                        $discount = $order->sendCost * $coupon->value / 100;
                    }

                } else if ($coupon->type === 'discount') {
                    $discount = $coupon->value;
                }

                $order->update([
                    'discount' => $discount,
                    'total' => $order->subtotal - $discount,
                ]);
            } else {
                $order->update([
                    'sendCost' => 0,
                    'total' => $order->subtotal,
                ]);
            }
        }

        $order = Order::with(
            'user',
            'orderItems.productDetail.product.image',
            'orderItems.productDetail.color',
            'orderItems.productDetail.size',
            'coupon',
            'sendInformation'
        )
            ->find($order->id);

        if ($request->input('method') === 'send') {
            $order->district = $district->name;
        }

        if ($request->input('method') === 'delivery') {
            $order->zone = $zone->name;
        }

        return response()->json($order);
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
     *             @OA\Property(property="CONFIRMADO", type="integer", example="1")
     *         )
     *     )
     * )
     */
    public function dashboardOrders()
    {
        $orders = Order::all();
        $total = $orders->count();
        $VERIFICANDO = $orders->where('status', 'VERIFICANDO')->count();
        $CONFIRMADO = $orders->where('status', 'CONFIRMADO')->count();
        $enviado = $orders->where('status', 'enviado')->count();
        $entregado = $orders->where('status', 'entregado')->count();
        $cancelado = $orders->where('status', 'cancelado')->count();
        $recojotiendaproceso = $orders->where('status', 'recojotiendaproceso')->count();
        $recojotiendalisto = $orders->where('status', 'recojotiendalisto')->count();
        $agency = $orders->where('status', 'agencia')->count();

        return response()->json([
            [
                'description' => 'Total de Órdenes',
                'value' => $total,
            ],
            [
                'description' => 'Órdenes Generadas',
                'value' => $VERIFICANDO,
            ],
            [
                'description' => 'Órdenes Pagadas',
                'value' => $CONFIRMADO,
            ],
            [
                'description' => 'Órdenes Enviadas',
                'value' => $enviado,
            ],
            [
                'description' => 'Órdenes en Proceso para Recojo en Tienda',
                'value' => $recojotiendaproceso,
            ],
            [
                'description' => 'Órdenes Listas para Recojo en Tienda',
                'value' => $recojotiendalisto,
            ],
            [
                'description' => 'Órdenes Entregadas',
                'value' => $entregado,
            ],
            [
                'description' => 'Órdenes en Agencia',
                'value' => $agency,
            ],
            [
                'description' => 'Órdenes Canceladas',
                'value' => $cancelado,
            ],
        ]);
    }

    public function sincronizar_orden_by_id($id)
    {
        $pedido = $this->api360Service->find_by_server_id(Order::class, $id);

        // Verifica si se encontró el pedido en la API externa
        if (!$pedido || empty($pedido['data'])) {
            return response()->json([
                'message' => 'Orden no encontrado.',
                'id' => $id,
            ], 422);
        }

        // Si se encontró, sincroniza la orden
        $resultado = $this->orderService->getOrdertosave($id, '');

        return Order::find($pedido['data']->id);
    }

    // public function sincronizarOrders360(ListOrders360Request $request)
    // {
    //     $uuid = $request->header('Authorization2') ?? env('APP_UUID');

    //     $start = $request->input('start');
    //     $end = $request->input('end');

    //     // Ruta del ejecutable PHP en XAMPP
    //     $phpPath = base_path('C:\\xampp\\php\\php.exe'); // Ajusta si usas otra ruta

    //     // Comando base con 'start /B' para ejecutar en segundo plano
    //     $cmd = 'start /B "' . $phpPath . '" ' . base_path('artisan') . ' sincronizar:ordenes360 ' . escapeshellarg($uuid);

    //     if ($start) {
    //         $cmd .= ' --start=' . escapeshellarg($start);
    //     }

    //     if ($end) {
    //         $cmd .= ' --end=' . escapeshellarg($end);
    //     }

    //     // Sin manejo de salida por redirección; solo se ejecuta en background
    //     pclose(popen($cmd, 'r'));

    //     Log::info("🚀 Sincronización de órdenes 360 enviada al fondo", [
    //         'uuid' => $uuid,
    //         'start' => $start,
    //         'end' => $end,
    //     ]);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Sincronización de órdenes 360 iniciada con éxito.',
    //     ]);
    // }



    public function sincronizarOrders360(ListOrders360Request $request)
    {
        $uuid = $request->header('Authorization2') ?? env('APP_UUID');

        // 🔹 Obtener fechas si están presentes en la request
        $start = $request->input('start');
        $end = $request->input('end');

        // 🔹 Comando base
        // $cmd = 'start /B php ' . base_path('artisan') . ' sincronizar:ordenes360 ' . escapeshellarg($uuid);
        $cmd = '/usr/bin/php ' . base_path('artisan') . ' sincronizar:ordenes360 ' . escapeshellarg($uuid);

        // 🔹 Añadir opciones correctamente formateadas
        if ($start) {
            $cmd .= ' --start=' . escapeshellarg($start);
        }

        if ($end) {
            $cmd .= ' --end=' . escapeshellarg($end);
        }

        // 🔹 Definir manejo de salida
        $descriptorspec = [
            0 => ['pipe', 'r'],                                                           // stdin
            1 => ['file', storage_path('logs/ejecucion_sincronizacion_orders.log'), 'a'], // stdout
            2 => ['file', storage_path('logs/ejecucion_sincronizacion_orders.log'), 'a'], // stderr
        ];

        // 🔹 Ejecutar el comando en segundo plano (solo Windows con `start /B`)
        // proc_open($cmd, $descriptorspec, $pipes);
        $process = proc_open($cmd . ' > /dev/null 2>&1 &', $descriptorspec, $pipes);

        Log::info("🚀 Sincronización de órdenes 360 enviada al fondo", [
            'uuid' => $uuid,
            'start' => $start,
            'end' => $end,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Sincronización de órdenes 360 iniciada con éxito.',
        ]);
    }

    public function updatepedidos(UpdateOrderRequest $request)
    {
        $updatedOrders = []; // Arreglo para almacenar los pedidos actualizados

        // Procesar cada pedido en el arreglo 'orders'
        foreach ($request->orders as $pedidoData) {
            // Asegurarse de que 'order_id' esté disponible y renombrarlo a 'id'

            // Buscar el pedido por su 'id' (ahora 'order_id' se ha convertido en 'id')
            $pedido = $this->api360Service->find_by_server_id(Order::class, $pedidoData['id']);

            if ($pedido) {
                // Llamar al servicio para actualizar o crear el pedido
                $this->orderService->update_or_create_item(
                    $pedidoData, // Pasamos los datos del pedido con 'id' en lugar de 'order_id'
                    Order::class,
                    Order::getfields360
                );

                // Después de actualizar el pedido, lo agregamos al arreglo de órdenes actualizadas
                $updatedOrders[] = $this->api360Service->find_by_server_id(Order::class, $pedidoData['id'])['data'];

            }
        }

        // Si se actualizaron órdenes correctamente, devolvemos las órdenes actualizadas
        return response()->json([
            'status' => true,
            'message' => 'Órdenes actualizadas correctamente.',
            'updated_orders' => ($updatedOrders), // Usamos la colección de OrderResource si es necesario
        ]);
    }

}
