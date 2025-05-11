<?php
namespace App\Services;

use App\Models\Coupon;
use App\Models\District;
use App\Models\Order;
use App\Models\Zone;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderService
{

    public function __construct()
    {

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

    public function getOrdertosave(
        string $order_id,
        string $authorizationUuid
    ) {
        try {
            $url               = "https://sistema.360sys.com.pe/api/online-store/order/" . $order_id;
            $authorizationUuid = ! empty($authorizationUuid) ? $authorizationUuid : env('APP_UUID_DEMO_360');

            $response = Http::withHeaders([
                'Authorization' => $authorizationUuid,
                'Accept'        => 'application/json',
            ])->get($url);

            $responseData = $response->json();

            if ($response->successful()) {
                $data  = $responseData['data'];
                $order = $data['order'];

                // Guardar o actualizar el pedido en la base de datos
                Order::updateOrCreate(
                    ['server_id' => $order['id']],
                    [
                        'number'      => $order['number'] ?? null,
                        'stage'       => $order['stage'] ?? null,
                        'bill_number' => $order['bill_number'] ?? null,
                    ]
                );

                return [
                    'status'  => true,
                    'message' => 'Pedido registrado exitosamente.',
                    'data'    => $data,
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

}
