<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class SuscripcionService
{
    public function obtenerSuscripciones(array $params)
    {
        $queryParams = [
            'reference' => ['eq' => explode(': ', $params['reference'])[2]],
            'product_id' => ['in' => explode(', ', explode(': ', $params['product_id'])[2])],
            'amount' => ['gte' => (float) explode(': ', $params['amount'])[2]],
        ];

        $response = Http::get('https://api.servicioexterno.com/suscripciones', $queryParams);
        
        if ($response->failed()) {
            throw new Exception('Error en la llamada al servicio externo');
        }

        return $response->json();
    }
}