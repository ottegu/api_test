<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\SuscripcionService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

class SuscripcionTest extends TestCase
{
    public function test_obtener_suscripciones_exitosamente()
    {
        Http::fake([
            'api.servicioexterno.com/*' => Http::response(['data' => 'resultado'], 200)
        ]);

        $response = $this->getJson(route('api.suscripciones', [
            'reference' => 'reference: eq: texto_referencia',
            'product_id' => 'product_id: in: 25005, 25097',
            'amount' => 'amount: gte: 2.0'
        ]));

        $response->assertStatus(200)
                 ->assertJson(['data' => 'resultado']);
    }

    public function test_error_parametros_invalidos()
    {
        $response = $this->getJson(route('api.suscripciones', [
            'reference' => 'invalid',
            'product_id' => 'product_id: in:',
            'amount' => 'amount: gte: texto'
        ]));
        
        $response->assertStatus(422)
                 ->assertJsonStructure(['error', 'detalles']);
    }

    public function test_error_llamada_externa_fallida()
    {
        Http::fake([
            'api.servicioexterno.com/*' => Http::response(null, 500)
        ]);

        $response = $this->getJson(route('api.suscripciones', [
            'reference' => 'reference: eq: texto_referencia',
            'product_id' => 'product_id: in: 25005, 25097',
            'amount' => 'amount: gte: 2.0'
        ]));
        
        $response->assertStatus(500)
                 ->assertJson(['error' => 'Error procesando la solicitud']);
    }

    public function test_service_sends_correct_query_parameters()
    {
        // Finge la respuesta para evitar llamadas reales
        Http::fake([
            'https://api.servicioexterno.com/suscripciones*' => Http::response(['data' => 'resultado'], 200),
        ]);

        $service = new SuscripcionService();

        // Valores de entrada de ejemplo
        $params = [
            'reference'  => 'reference: eq: texto_referencia',
            'product_id' => 'product_id: in: 25005, 25097',
            'amount'     => 'amount: gte: 2.0'
        ];

        // Se invoca el servicio
        $service->obtenerSuscripciones($params);

        // Verifica que se haya enviado una petición con la URL y los query parameters correctos
        Http::assertSent(function ($request) {
            // La URL base que esperamos
            $expectedBaseUrl = 'https://api.servicioexterno.com/suscripciones';
            // Verifica que la URL inicie con la base esperada
            if (strpos($request->url(), $expectedBaseUrl) !== 0) {
                return false;
            }

            // Extrae el query string de la URL
            $queryString = parse_url($request->url(), PHP_URL_QUERY);
            $parsedQuery = [];
            parse_str($queryString, $parsedQuery);

            // Los valores se envían como arrays anidados.
            $expectedQuery = [
                'reference' => [
                    'eq' => 'texto_referencia'
                ],
                'product_id' => [
                    'in' => ['25005', '25097']
                ],
                'amount' => [
                    'gte' => '2.0'
                ],
            ];

            return $parsedQuery == $expectedQuery;
        });
    }

    /**
     * Comprueba lo mismo que el "test_service_sends_correct_query_parameters" pero da un mensaje de error mas detallado
     * Tambien se monta de manera diferente la comprobación para tener otro ejemplo más de como poder hacerlo
     */
    public function test_service_sends_correct_parameters()
    {
        // Finge la respuesta para evitar llamadas reales al servicio externo.
        Http::fake([
            'https://api.servicioexterno.com/suscripciones*' => Http::response(['data' => 'resultado'], 200),
        ]);

        $service = new SuscripcionService();

        // Valores de entrada de ejemplo.
        $params = [
            'reference'  => 'reference: eq: texto_referencia',
            'product_id' => 'product_id: in: 25005, 25097',
            'amount'     => 'amount: gte: 2.0'
        ];

        // Invoca el servicio.
        $service->obtenerSuscripciones($params);

        // Define el array de parámetros esperado.
        $expectedQuery = [
            'reference'  => ['eq' => 'texto_referencia'],
            'product_id' => ['in' => ['25005', '25097']],
            'amount'     => ['gte' => 2.0]
        ];

        // Genera el query string esperado (codificado).
        $expectedQueryString = http_build_query($expectedQuery);
        $expectedUrl = 'https://api.servicioexterno.com/suscripciones?' . $expectedQueryString;

        // Verifica que se haya enviado una petición con la URL completa (incluyendo el query string) igual a la esperada.
        Http::assertSent(function ($request) use ($expectedUrl, $expectedQuery) {
            $sentUrl = $request->url();
            
            if ($sentUrl !== $expectedUrl) {
                $this->fail(
                    "La URL enviada no coincide con lo esperado.\n" .
                    "URL esperada: {$expectedUrl}\n" .
                    "URL recibida: {$sentUrl}"
                );
            }
            return true;
        });
    }
}