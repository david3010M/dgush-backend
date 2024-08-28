<?php

namespace App\Http\Controllers;

use App\Models\Order;

class IziPayController extends Controller
{
    /**
     * Create a payment token for an order
     * @OA\Post(
     *     path="/dgush-backend/public/api/izipay/izipay/createPaymentToken/{orderId}",
     *     tags={"IziPay"},
     *     summary="Create a payment token for an order",
     *     description="Create a payment token for an order",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="orderId", in="path", required=true, description="Order ID", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="Payment token created",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="integer", example="200"),
     *              @OA\Property(property="formToken", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9")
     *          ),
     *     ),
     *     @OA\Response(response="404", description="Order not found",
     *          @OA\JsonContent(
     *               @OA\Property(property="status", type="integer", example="0"),
     *               @OA\Property(property="formToken", type="string", example="Order not found")
     *           ),
     *     ),
     *     @OA\Response(response="422", description="Order must be verified",
     *         @OA\JsonContent(
     *               @OA\Property(property="status", type="integer", example="0"),
     *               @OA\Property(property="formToken", type="string", example="Order must be verified")
     *           ),
     *     ),
     *     @OA\Response(response="401", description="Unauthorized",
     *          @OA\JsonContent(
     *                @OA\Property(property="status", type="integer", example="0"),
     *                @OA\Property(property="formToken", type="string", example="Order must be verified")
     *            ),
     *      ),
     * )
     *
     */
    public function createPaymentToken(int $orderId)
    {
        $user = auth()->user();
        $order = Order::with('user')
            ->where('id', $orderId)
            ->where('user_id', $user->id)->first();
        if (!$order) return response()->json(['status' => 0, 'message' => 'Order not found'], 404);
        if ($order->status !== 'verificado') return response()->json(['status' => 0, 'message' => 'Order must be verified'], 422);

        $store = array(
            "amount" => 100 * round((float)$order->total, 2),
            "currency" => "PEN",
            "orderId" => $order->number,
            "customer" => array(
                "email" => $order->user->email,
            )
        );

        $response = $this->post("V4/Charge/CreatePayment", $store);

        if ($response['status'] !== 'SUCCESS') {
            $error = $response['answer'];
            return response()->json(['status' => $error['errorCode'], 'message' => $error['errorMessage']], 422);
        }

        $formToken = $response["answer"]["formToken"];
        $data = ['status' => 200, 'formToken' => $formToken];

        return response()->json($data);
    }

    private function post(string $target, array $datos)
    {
        $auth = env('IZIPAY_USERNAME') . ":" . env('IZIPAY_PASSWORD');
        $url = env('IZIPAY_ENDPOINT') . "/api-payment/" . $target;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_USERPWD, $auth);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($datos));
        $raw_response = curl_exec($curl);
        $response = json_decode($raw_response, true);
        return $response;
    }
}
