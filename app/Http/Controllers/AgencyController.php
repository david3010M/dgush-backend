<?php
namespace App\Http\Controllers;

use App\Models\Agency;

class AgencyController extends Controller
{
    /**
     * @OA\Get(
     *     path="/dgush-backend/public/api/agency",
     *     tags={"Agency"},
     *     summary="Agencias activas para el modo ENVIO (sección Método de Envío)",
     *     description="Ordenadas por sort_order. Enviar server_id como agency_id al crear el pedido. Si shipping_cost es false, el envío y el exceso del distrito salen en 0.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Agency"))
     *     )
     * )
     */
    public function index()
    {
        return Agency::active()->get();
    }
}
