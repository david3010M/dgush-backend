<?php
namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderService
{

    public function __construct()
    {

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
                        'number' => $order['number'] ?? null,
                        'stage'       => $order['stage'] ?? null,
                        'bill_number' => $order['bill_number'] ?? null
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
