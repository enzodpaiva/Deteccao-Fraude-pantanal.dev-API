<?php

namespace App\Bussiness\Services;

use App\Bussiness\Helpers\GuzzleHelper;
use App\Bussiness\Services\Interfaces\ApiServerServiceInterface;

class ApiServerIntegrationService implements ApiServerServiceInterface
{
    public function __construct()
    {

    }

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function sendSampleView(array $data)
    {
        $response = GuzzleHelper::post('sample-view', $data);
        $responseJson = $response->json();

        if ($response->failed()) {
            $errorMessage = $response->reason();

            return $this->formatResponseReturn($response->status(), $errorMessage);
        }

        return $this->formatResponseReturn($response->status(), $responseJson);
    }

    public function sendRandomTransaction(array $data = [])
    {
        $response = GuzzleHelper::post('random-transaction', $data);
        $responseJson = $response->json();

        if ($response->failed()) {
            $errorMessage = $response->reason();

            return $this->formatResponseReturn($response->status(), $errorMessage);
        }

        return $this->formatResponseReturn($response->status(), $responseJson);
    }

    public function sendStoreFraud(array $data)
    {
        $response = GuzzleHelper::post('add-fraud', $data);
        $responseJson = $response->json();

        if ($response->failed()) {
            $errorMessage = $response->reason();

            return $this->formatResponseReturn($response->status(), $errorMessage);
        }

        return $this->formatResponseReturn($response->status(), $responseJson);
    }

    private function formatResponseReturn($status, $data)
    {
        return [
            'status' => $status,
            'data' => $data,
        ];
    }

}
