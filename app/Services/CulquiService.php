<?php
namespace App\Services;

use Culqi\Culqi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CulquiService
{

    protected $culqi;

    public function __construct()
    {
        $this->culqi = new Culqi([
            'api_key' => config('services.culqi.secret_key'),
        ]);
    }

    /**
     * Crea un cargo con Culqi.
     *
     * @throws \Exception
     */
    public function createCharge(Request $request): array
    {
        $charge = $this->culqi->Charges->create([
            "amount"        => $request->amount,
            "capture"       => true,
            "currency_code" => "PEN",
            "description"   => $request->description,
            "email"         => $request->email,
            "installments"  => 0,
            "source_id"     => $request->token,
        ]);

        if (! isset($charge->id)) {
            return [
                'success' => false,
                'message' => $charge->user_message ?? $charge->merchant_message ?? 'Error al procesar el pago',
                'object'  => $charge,
                'status'  => 400,
            ];
        }

        return [
            'success' => true,
            'message' => 'Pago procesado correctamente',
            'object'  => $charge,
            'status'  => 200,
        ];
    }

    public function orderPostRequest(
        string $endpoint,
        string $authorizationUiid,
        array $postBody = []
    ) {
        try {
            $url               = "https://sistema.360sys.com.pe/api/online-store/" . $endpoint;
            $authorizationUiid = ! empty($authorizationUiid) ? $authorizationUiid : env('APP_UUID');

            $response = HttP::withHeaders([
                'Authorization' => $authorizationUiid,
                'Accept'        => 'application/json',
            ])->post($url, $postBody);

            if ($response->successful()) {
                return [
                    'status'  => true,
                    'message' => 'Solicitud POST exitosa.',
                    'data'    => $response->json(),
                ];
            }

            // Log de error si la respuesta no fue exitosa
            Log::error("POST Fallido a {$url}", [
                'status_code' => $response->status(),
                'body'        => $response->body(),
            ]);

            return [
                'status'  => false,
                'message' => 'La solicitud POST falló.',
                'data'    => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error("Excepción en POST a {$url}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'status'  => false,
                'message' => 'Error interno, revisa el log.',
            ];
        }
    }
}
