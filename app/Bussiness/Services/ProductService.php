<?php

namespace App\Bussiness\Services;

use App\Exceptions\ApiLeadException;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ProductService
{
    private $pitziIntegrationService;

    public function __construct(PitziIntegrationService $pitziIntegrationService)
    {
        $this->pitziIntegrationService = $pitziIntegrationService;
    }

    public function getProductsByTacV2($query)
    {
        try {
            if (!$query) {
                Log::error('[pitzi] invalid parameters: ' . json_encode($query));
                throw new InvalidArgumentException('parametros invalidos: ' . json_encode($query), 400);
            }

            return $this->pitziIntegrationService->getProductsByTacV2($query);

        } catch (InvalidArgumentException $e) {
            Log::error('Error InvalidArgumentException /getProductsByTacV2: ' . $e->getMessage());
            throw new ApiLeadException(null, 400, ['mensagem' => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('error Exception /getProductsByTacV2: ' . $e->getMessage());
            throw new ApiLeadException(null, 500, ['mensagem' => $e->getMessage()]);
        }
    }

    public function consultFullSearchProducts($query)
    {
        try {
            if (!$query) {
                Log::error('[pitzi] invalid parameters: ' . json_encode($query));
                throw new InvalidArgumentException('parametros invalidos: ' . json_encode($query), 400);
            }

            return $this->pitziIntegrationService->consultFullSearchProducts($query);

        } catch (InvalidArgumentException $e) {
            Log::error('Error InvalidArgumentException /consultFullSearchProducts: ' . $e->getMessage());
            throw new ApiLeadException(null, 400, ['mensagem' => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('error Exception /consultFullSearchProducts: ' . $e->getMessage());
            throw new ApiLeadException(null, 500, ['mensagem' => $e->getMessage()]);
        }
    }
}
