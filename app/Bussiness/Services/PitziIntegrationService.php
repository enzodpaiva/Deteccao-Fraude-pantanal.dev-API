<?php

namespace App\Bussiness\Services;

use App\Bussiness\Helpers\GuzzleHelper;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PitziIntegrationService
{
    private $clientServiceConfig;
    private $client;

    public function __construct()
    {
        $this->clientServiceConfig = config('apiServices.pitzi');
        $this->client = new Client($this->clientServiceConfig['client']);
    }

    public static function factory(): PitziIntegrationService
    {
        return app()->make(PitziIntegrationService::class);
    }

    public function consultFullSearchProducts($query)
    {
        $response = GuzzleHelper::get('products/full_search', $query);
        $responseJson = $response->json();
        Log::info('Response Api /consultFullSearchProducts?' . json_encode($query) . ' : ' . json_encode($responseJson));

        if ($response->failed()) {
            $errorMessage = $response->reason();
            $errorMessage .= is_array($responseJson) && array_key_exists('message', $responseJson) ? " - " . $responseJson['message'] : "";

            return $this->formatResponseReturn($response->status(), $errorMessage);
        }

        return $this->formatResponseReturn($response->status(), $responseJson);
    }

    // somente após o pagamento da assinatura é possivel obter o termo
    public function getTermsUse($orderId)
    {
        $response = GuzzleHelper::get('service_terms/' . $orderId, []);

        Log::info('Response Api getTermsUse /service_terms/' . $orderId . ' : ' . $response->getBody());

        if ($response->failed()) {
            return $this->formatResponseReturn($response->status(), $response->reason());
        }

        return response($response->body())->header('Content-Type', 'application/pdf');
    }

    //Caso a assinatura não seja encontrada ou não esteja cancelada de fato, o sistema responde com HTTP status 404.
    public function getTermsSignatureCancel($orderId)
    {
        $response = GuzzleHelper::get('cancellation_term/' . $orderId . ".pdf", []);

        Log::info('Response Api getTermsSignatureCancel /cancellation_term/' . $orderId . ' : ' . $response->getBody());

        if ($response->failed()) {
            return $this->formatResponseReturn($response->status(), $response->reason());
        }

        return response($response->body())->header('Content-Type', 'application/pdf');
    }

    public function createSignaturePitziSite($data)
    {
        $response = GuzzleHelper::post('representant_orders', $data);
        $responseJson = $response->json();
        Log::info('Response Api createSignaturePitziSite: ' . json_encode($responseJson));

        if ($response->failed()) {
            $errorMessage = $response->reason();
            $errorMessage .= is_array($responseJson) && array_key_exists('message', $responseJson) ? " - " . $responseJson['message'] : "";

            return $this->formatResponseReturn($response->status(), $errorMessage);
        }

        return $this->formatResponseReturn($response->status(), $responseJson);
    }

    // Quando o prazo de cancelamento(7 dias corridos a partir da data da venda) já foi excedido, o sistema responde com HTTP status 409
    // Quando a assinatura já teve algum pedido de serviço aberto, o sistema responde com HTTP status 409
    public function signatureCancel($orderId)
    {
        $response = GuzzleHelper::delete('orders/' . $orderId);
        $responseJson = $response->json();

        Log::info('Response Api signatureCancel /orders/' . $orderId . ' : ' . json_encode($responseJson));

        if ($response->failed()) {
            $errorMessage = $response->reason();
            $errorMessage .= is_array($responseJson) && array_key_exists('message', $responseJson) ? " - " . $responseJson['message'] : "";

            return $this->formatResponseReturn($response->status(), $errorMessage);
        }

        return $this->formatResponseReturn($response->status(), $responseJson);
    }

    public function consultSubscriptionByImei(array $query)
    {
        $response = GuzzleHelper::get('orders/search', $query);
        $responseJson = $response->json();
        Log::info('Response Api consultSubscriptionByImei /orders/search?' . json_encode($query) . ' : ' . $response->getBody());

        if ($response->failed()) {
            $errorMessage = $response->reason();
            $errorMessage .= is_array($responseJson) && array_key_exists('message', $responseJson) ? " - " . $responseJson['message'] : "";

            return $this->formatResponseReturn($response->status(), $errorMessage);
        }

        return $this->formatResponseReturn($response->status(), $responseJson);
    }

    public function getProductsByTacV2($query)
    {
        $response = GuzzleHelper::get('v2/products/tac_search', $query);
        $responseJson = $response->json();
        Log::info('Response Api getProductsByTacV2 v2/products/tac_search?' . json_encode($query) . ' : ' . $response->getBody());

        if ($response->failed()) {
            $errorMessage = $response->reason();
            $errorMessage .= is_array($responseJson) && array_key_exists('message', $responseJson) ? " - " . $responseJson['message'] : "";

            return $this->formatResponseReturn($response->status(), $errorMessage);
        }

        return $this->formatResponseReturn($response->status(), $responseJson);
    }

    // ---------- Endpoints não utilizados --------
    public function getProducts($query)
    {
        try {
            $response = GuzzleHelper::get('products', $query);
            $responseJson = $response->json();
            Log::info('Response Api getProducts /products?' . json_encode($query) . ' : ' . json_encode($responseJson));

            if ($response->failed()) {
                return response()->json([
                    'status' => $response->status(),
                    'message' => 'Failed to get products',
                    'error' => $response->reason(),
                ], $response->status());
            }

            return $responseJson;
        } catch (Exception $e) {
            Log::error('Error getProducts /products: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');

            return response()->json([
                'status' => 500,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createSubscriptionPaidStore($data)
    {
        try {
            $response = GuzzleHelper::post('orders', $data);
            $responseJson = $response->json();

            Log::info('Response Api createSubscriptionPaidStore /orders: ' . json_encode($responseJson));

            if ($response->failed()) {
                return response()->json([
                    'status' => $response->status(),
                    'message' => 'Failed to create Subscription Paid Stores',
                    'error' => $response->reason(),
                ], $response->status());
            }

            return $responseJson;
        } catch (Exception $e) {
            Log::error('Error createSubscriptionPaidStore /orders: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');

            return response()->json([
                'status' => 500,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function consultSubscriptionById($orderId)
    {
        try {
            $response = GuzzleHelper::get('orders/' . $orderId, []);
            $responseJson = $response->json();

            Log::info('Response Api consultSubscriptionById /orders/' . $orderId . ' : ' . $response->getBody());

            if ($response->failed()) {
                return response()->json([
                    'status' => $response->status(),
                    'message' => 'Failed to consult Subscription By Id',
                    'error' => $response->reason(),
                ], $response->status());
            }

            return $responseJson;
        } catch (Exception $e) {
            Log::error('Error consultSubscriptionById /orders/' . $orderId . ' : ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');

            return response()->json([
                'status' => 500,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCoveredDevicesPlanPrice($ean)
    {
        try {
            $response = GuzzleHelper::get('products/ean/' . $ean, []);
            $responseJson = $response->json();

            Log::info('Response Api getCoveredDevicesPlanPrice products/ean/' . $ean . ' : ' . $response->getBody());

            if ($response->failed()) {
                return response()->json([
                    'status' => $response->status(),
                    'message' => 'Failed to get Covered Devices Plan Price',
                    'error' => $response->reason(),
                ], $response->status());
            }

            return $responseJson;
        } catch (Exception $e) {
            Log::error('Error getCoveredDevicesPlanPrice products/ean/' . $ean . ' : ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');

            return response()->json([
                'status' => 500,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getProductsSearch($query)
    {

        try {
            $response = GuzzleHelper::get('products/search', $query);
            $responseJson = $response->json();

            Log::info('Response Api getProductsByNamePlan products/search?' . json_encode($query) . ' : ' . $response->getBody());

            if ($response->failed()) {
                return response()->json([
                    'status' => $response->status(),
                    'message' => 'Failed to get Products Search',
                    'error' => $response->reason(),
                ], $response->status());
            }

            return $responseJson;
        } catch (Exception $e) {
            Log::error('Error getProductsByNamePlan products/search' . json_encode($query) . ' : ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');

            return response()->json([
                'status' => 500,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getProductsByTimCode($query)
    {
        try {
            $response = GuzzleHelper::get('products/tim_code_search', $query);
            $responseJson = $response->json();

            Log::info('Response Api getProductsByTimCode products/tim_code_search?' . json_encode($query) . ' : ' . $response->getBody());

            if ($response->failed()) {
                return response()->json([
                    'status' => $response->status(),
                    'message' => 'Failed to get Products By Tim Code',
                    'error' => $response->reason(),
                ], $response->status());
            }

            return $responseJson;
        } catch (Exception $e) {
            Log::error('Error getProductsByTimCode products/tim_code_search' . json_encode($query) . ' : ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');

            return response()->json([
                'status' => 500,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function registerLeadPitzi($data)
    {
        try {
            $response = GuzzleHelper::post('leads', $data);
            $responseJson = $response->json();

            Log::info('Response Api registerLeadPitzi /leads: ' . json_encode($responseJson));

            if ($response->failed()) {
                return response()->json([
                    'status' => $response->status(),
                    'message' => 'Failed to register Lead Pitzi',
                    'error' => $response->reason(),
                ], $response->status());
            }

            return $responseJson;
        } catch (Exception $e) {
            Log::error('Error registerLeadPitzi /leads: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');

            return response()->json([
                'status' => 500,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // verificação de foto do aparelho via endpoint não será mais feita
    // public function devicePhotoVerification($data, $orderId)
    // {
    //     $response = GuzzleHelper::post('orders/' . $orderId . '/device_verification', $data);
    //     $responseJson = $response->json();

    //     Log::info('Response Api verifyDataPitzi orders/' . $orderId . '/device_verification: ' . json_encode($responseJson));

    //     if ($response->failed()) {
    //         return [
    //             'status' => $response->status(),
    //             'data' => $response->reason(),
    //         ];
    //     }

    //     return [
    //         'status' => $response->status(),
    //         'data' => $responseJson,
    //     ];
    // }

    public function consultSubscriptionActive($query)
    {
        try {
            $response = GuzzleHelper::get('orders/active', $query);
            $responseJson = $response->json();

            Log::info('Response Api registerLeadPitzi /orders/active?' . json_encode($query) . ' : ' . json_encode($responseJson));

            if ($response->failed()) {
                return response()->json([
                    'status' => $response->status(),
                    'message' => 'Failed to consult Subscription Active',
                    'error' => $response->reason(),
                ], $response->status());
            }

            return $responseJson;
        } catch (Exception $e) {
            Log::error('Error registerLeadPitzi /orders/active: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');

            return response()->json([
                'status' => 500,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function formatResponseReturn($status, $data)
    {
        return [
            'status' => $status,
            'data' => $data,
        ];
    }

}
