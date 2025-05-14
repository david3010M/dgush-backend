<?php
namespace App\Services;

use App\Jobs\FetchSincronizarOrdenesJob;
use App\Models\Color;
use App\Models\Coupon;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Sede;
use App\Models\Size;
use App\Models\Zone;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderService
{

    protected $api360Service;

    // Inyectamos el servicio en el controlador
    public function __construct(Api360Service $api360Service)
    {
        $this->api360Service = $api360Service;
    }

    public function calculate($request)
    {
        try {
            // 1. Calcular subtotal desde los productos del request
            $subtotal = collect($request->products)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $total    = 0;
            $sendCost = 0;

            // 2. Calcular envío según modo
            $mode = $request->mode ?? '';

            if ($mode === 'DELIVERY') {
                if (isset($request->zone_id) && ! empty($request->zone_id)) {
                    $zone     = Zone::firstWhere('server_id', $request->zone_id);
                    $sendCost = $zone && $zone->sendCost !== null ? $zone->sendCost : 0;
                }
            } elseif ($mode === 'ENVIO') {
                if (isset($request->district_id) && ! empty($request->district_id)) {
                    $district = District::firstWhere('server_id', $request->district_id);
                    $sendCost = $district && $district->sendCost !== null ? $district->sendCost : 0;
                }
            }

            // Validar que el costo de envío no sea negativo
            if ($sendCost < 0) {
                return [
                    'error'   => 'Error en el cálculo del costo de envío',
                    'message' => 'El costo de envío no puede ser negativo.',
                ];
            }

            // 3. Calcular descuento si hay cupón
            $discount = 0;

            if (isset($request->coupon_id) && ! empty($request->coupon_id)) {
                $coupon = Coupon::find($request->coupon_id);

                if ($coupon) {
                    if ($coupon->type === 'percentage') {
                        $discount = match ($coupon->indicator) {
                            'subtotal' => $subtotal * $coupon->value / 100,
                            'total' => $total * $coupon->value / 100,
                            'sendCost' => $sendCost * $coupon->value / 100,
                            default => 0,
                        };
                    } elseif ($coupon->type === 'discount') {
                        $discount = $coupon->value;
                    }
                }
            }

            // Validar que el descuento no sea negativo
            if ($discount < 0) {
                return [
                    'error'   => 'Error en el cálculo del descuento',
                    'message' => 'El descuento no puede ser negativo.',
                ];
            }

                                                                // 4. Total
            $total = max(0, $subtotal + $sendCost - $discount); // evitar negativos

            // Validar que el total no sea negativo
            if ($total < 0) {
                return [
                    'error'   => 'Error en el cálculo del total',
                    'message' => 'El total no puede ser negativo.',
                ];
            }

            return [
                'subtotal' => $subtotal,
                'sendCost' => $sendCost,
                'discount' => $discount,
                'total'    => $total,
            ];

        } catch (\Exception $e) {
            // Log de la excepción
            AuditLogService::log('order_calculation_service_exception', $request->all(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Enviar respuesta de error
            return [
                'error'   => 'Ocurrió un error al calcular el pedido.',
                'message' => $e->getMessage(),
            ];
        }
    }

    public function createOrder(array $data): Order
    {
        $data['user_id'] = Auth::id();
        $data['status']  = 'verificado';

        // Mapear IDs de zona, distrito y sede
        foreach ([
            'zone_id'     => Zone::class,
            'district_id' => District::class,
            'branch_id'   => Sede::class,
        ] as $key => $model) {
            if (! empty($data[$key]) && $found = $this->api360Service->find_by_server_id($model, $data[$key])) {
                $data[$key] = $found['data']->id;
            } else {
                unset($data[$key]);
            }
        }

        // Convertir arrays a JSON
        foreach (['customer', 'payments', 'products', 'invoices'] as $jsonField) {
            if (isset($data[$jsonField]) && is_array($data[$jsonField])) {
                $data[$jsonField] = json_encode($data[$jsonField]);
            }
        }

        $fillableFields = (new Order())->getFillable();
        $filteredData   = array_intersect_key($data, array_flip($fillableFields));

        $order = Order::create($filteredData);

        // Procesar productos del pedido
        $productDetails = isset($data['products']) ? json_decode($data['products'], true) : [];

        foreach ($productDetails as $item) {
            // Mapeo de server_id a IDs locales
            $ids = [
                'product_id' => Product::class,
                'color_id'   => Color::class,
                'talla_id'   => Size::class,
            ];
        
            $mappedIds = [];
            foreach ($ids as $key => $model) {
                $serverId = match ($key) {
                    'product_id' => $item['id']        ?? null,
                    'color_id'   => $item['color_id']  ?? null,
                    'talla_id'   => $item['size_id']   ?? null, // <-- size_id llega, se guarda como talla_id
                };
        
                $mappedIds[$key] = !empty($serverId) && ($found = $this->api360Service->find_by_server_id($model, $serverId))
                    ? $found['data']->id
                    : null;
            }
        
            // Crear detalle de orden
            OrderDetail::create([
                'order_id'   => $order->id,
                'product_id' => $mappedIds['product_id'],
                'color_id'   => $mappedIds['color_id'],
                'talla_id'   => $mappedIds['talla_id'],
                'quantity'   => (int) ($item['quantity'] ?? 0),
                'price'      => $item['price'] ?? 0,
                'note'       => $item['notes'] ?? null,
            ]);
        }
        

        return $order;
    }

    public function getOrdertosave(
        string $order_id,
        string $authorizationUuid,
        array $others_fields = [],
        string $modelClass = Order::class,
        array $fields = Order::getfields360,
    ) {

        try {
            $url               = "https://sistema.360sys.com.pe/api/online-store/orders/" . $order_id;
            $authorizationUuid = ! empty($authorizationUuid) ? $authorizationUuid : env('APP_UUID');

            $response = Http::withHeaders([
                'Authorization' => $authorizationUuid,
                'Accept'        => 'application/json',
            ])->get($url);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['data']['order'])) {
                $order            = $responseData['data']['order'];
                $order['user_id'] = Auth::user()->id;
                if ($others_fields != []) {
                    $order['coupon_id'] = $others_fields['coupon_id'];
                    $fields             = array_merge($fields, [
                        "coupon_id" => "coupon_id",
                    ]);
                }

                // Guardar o actualizar usando método genérico
                $this->update_or_create_item($order, $modelClass, $fields);

                return [
                    'status'  => true,
                    'message' => 'Pedido registrado exitosamente.',
                    'data'    => ($order),
                ];
            }

            return [
                'status'  => false,
                'message' => 'La solicitud GET falló o los datos no son válidos.',
                'data'    => $responseData,
            ];
        } catch (\Throwable $e) {
            Log::error('Error en getOrdertosave: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'status'  => false,
                'message' => 'Error interno del servidor. Revisa el log.',
            ];
        }
    }

    public function update_or_create_item(array $data, string $modelClass, array $fields): void
    {
        try {
            $map = [
                'zone_id'     => Zone::class,
                'district_id' => District::class,
                'branch_id'   => Sede::class,
            ];

            // Mapear zone_id, district_id, branch_id
            foreach ($map as $key => $model) {
                if (! empty($data[$key])) {
                    $found = $this->api360Service->find_by_server_id($model, $data[$key]);

                    if ($found) {
                        $data[$key] = $found['data']->id;
                    } else {
                        unset($data[$key]); // No incluir si no se encuentra
                    }
                }
            }

            // Convertir arrays a JSON si existen
            foreach (['customer', 'payments', 'products', 'invoices'] as $jsonField) {
                if (isset($data[$jsonField]) && is_array($data[$jsonField])) {
                    $data[$jsonField] = json_encode($data[$jsonField]);
                }
            }

            // Verificar que 'id' exista en los datos antes de continuar
            if (empty($data['id'])) {
                Log::error("Missing 'id' in the data for model {$modelClass}", [
                    'data'   => $data,
                    'fields' => $fields,
                ]);
                return; // O lanzar una excepción si prefieres
            }

            // Realizar updateOrCreate si 'id' está presente
            $modelClass::updateOrCreate(
                ['server_id' => $data['id']], // Condición de búsqueda
                collect($fields)
                    ->filter(fn($f) => isset($data[$f]))       // Solo usar campos presentes
                    ->mapWithKeys(fn($f) => [$f => $data[$f]]) // Mapear los campos
                    ->toArray()
            );

        } catch (\Throwable $e) {
            Log::error("Error in update_or_create_item for model {$modelClass}: " . $e->getMessage(), [
                'data'   => $data,
                'fields' => $fields,
                'trace'  => $e->getTraceAsString(),
            ]);
        }
    }

    public function sincronizarOrders360($uuid, $start, $end)
    {

        try {
            Bus::batch([
                new FetchSincronizarOrdenesJob($uuid, $start, $end),

            ])
                ->name('Sincronización de Ordenes 360')
                ->onConnection('sync') // 🔹 Forzar ejecución inmediata
                ->dispatch();

            Log::info("Sincronización 360 iniciada con éxito.");
        } catch (\Throwable $e) {
            Log::error('Error en sincronización de ordenes 360', [
                'message' => $e->getMessage(),
                'uuid'    => $uuid,
            ]);
        }
    }

    public function callListarPedidos360(string $uuid, string $start, string $end)
    {
        $request = new Request(['start' => $start, 'end' => $end]);

        return $this->listarPedidosPorFechas(
            $request,
            route: 'orders',
            key: 'orders',
            clase: Order::class,
            getfields: Order::getfields360,
            uuid: $uuid
        );
    }

    public function listarPedidosPorFechas(
        Request $request,
        string $route,
        string $key,
        string $clase,
        array $getfields,
        string $uuid
    ) {
        try {
            $url = "https://sistema.360sys.com.pe/api/online-store/{$route}";

            $response = Http::timeout(120) // segundos
                ->connectTimeout(30)->withHeaders([
                'Authorization' => $uuid,
                'Accept'        => 'application/json',
            ])->get($url, $request->only('start', 'end'));

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['data'])) {
                foreach ($responseData['data'][$key] as $item) {
                    $this->update_or_create_item($item, $clase, $getfields);
                }

                return response()->json([
                    'status'  => true,
                    'message' => 'Datos sincronizados correctamente.',
                    'data'    => $responseData['data'],
                ]);
            }

            return response()->json([
                'status'  => false,
                'message' => 'No se pudieron obtener los datos.',
                'data'    => $responseData,
            ], $response->status());

        } catch (\Throwable $e) {
            Log::error("Error en listarPedidosPorFechas: " . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Error interno del servidor.',
            ], 500);
        }
    }

}
