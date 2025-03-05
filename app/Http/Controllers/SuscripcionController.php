<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SuscripcionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(title="API de Suscripciones", version="1.0")
 * @OA\PathItem(path="/api/suscripciones")
 */
class SuscripcionController extends Controller
{
    protected SuscripcionService $suscripcionService;

    public function __construct(SuscripcionService $suscripcionService)
    {
        $this->suscripcionService = $suscripcionService;
    }

    /**
     * @OA\Get(
     *     path="/suscripciones",
     *     summary="Obtener suscripciones",
     *     @OA\Parameter(
     *         name="reference",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             pattern="^reference: eq: .+$"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             pattern="^product_id: in: \\d+(,\\s?\\d+)*$"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             pattern="^amount: gte: \\d+(\\.\\d+)?$"
     *         )
     *     ),
     *     @OA\Response(response=200, description="Operación exitosa"),
     *     @OA\Response(response=422, description="Parámetros inválidos"),
     *     @OA\Response(response=500, description="Error interno")
     * )
     */
    public function getSuscripciones(Request $request): JsonResponse
    {
        try {
            $params = $request->validate([
                'reference' => 'required|string|regex:/^reference: eq: .+$/',
                'product_id' => 'required|string|regex:/^product_id: in: \d+(,\s?\d+)*$/',
                'amount' => 'required|string|regex:/^amount: gte: \d+(\.\d+)?$/',
            ]);

            $response = $this->suscripcionService->obtenerSuscripciones($params);

            return response()->json($response);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Parámetros inválidos', 'detalles' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error procesando la solicitud'], 500);
        }
    }
}
