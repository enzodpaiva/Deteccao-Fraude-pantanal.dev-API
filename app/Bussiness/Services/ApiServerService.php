<?php

namespace App\Bussiness\Services;

use App\Exceptions\ApiLeadException;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ApiServerService
{
    private $apiServerIntegrationService;

    public function __construct(ApiServerIntegrationService $apiServerIntegrationService)
    {
        $this->apiServerIntegrationService = $apiServerIntegrationService;
    }

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function sendSampleViewToServer(array $data)
    {
        try {

            if (!$data) {
                Log::error('Invalid data: ' . json_encode($data));
                throw new InvalidArgumentException('data invalida: ' . json_encode($data), 400);
            }

            return $this->apiServerIntegrationService->sendSampleView($data);

        } catch (InvalidArgumentException $e) {
            Log::error('Error InvalidArgumentException /sendSampleViewToServer: ' . $e->getMessage());
            throw new ApiLeadException(null, 400, ['mensagem' => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('Error Exception /sendSampleViewToServer: ' . $e->getMessage());
            throw new ApiLeadException(null, 500, ['mensagem' => $e->getMessage()]);
        }
    }

    public function sendRandomTransactionToServer(array $data = [])
    {
        try {

            return $this->apiServerIntegrationService->sendRandomTransaction($data);

        } catch (InvalidArgumentException $e) {
            Log::error('Error InvalidArgumentException /sendRandomTransactionToServer: ' . $e->getMessage());
            throw new ApiLeadException(null, 400, ['mensagem' => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('Error Exception /sendRandomTransactionToServer: ' . $e->getMessage());
            throw new ApiLeadException(null, 500, ['mensagem' => $e->getMessage()]);
        }
    }

    public function sendStoreFraudToServer(array $data)
    {
        try {

            if (!$data) {
                Log::error('Invalid data: ' . json_encode($data));
                throw new InvalidArgumentException('data invalida: ' . json_encode($data), 400);
            }

            return $this->apiServerIntegrationService->sendStoreFraud($data);

        } catch (InvalidArgumentException $e) {
            Log::error('Error InvalidArgumentException /sendStoreFraud: ' . $e->getMessage());
            throw new ApiLeadException(null, 400, ['mensagem' => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('Error Exception /sendStoreFraudToServer: ' . $e->getMessage());
            throw new ApiLeadException(null, 500, ['mensagem' => $e->getMessage()]);
        }
    }

}
