<?php

namespace App\Bussiness\Services;

use App\Bussiness\Services\Interfaces\EmailValidationService;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ApiSupportIntegrationService implements EmailValidationService
{
    private $client;

    public function __construct()
    {
        $clientServiceConfig = config('apiServices.service_support');
        $this->client = new Client($clientServiceConfig['client']);
    }

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function validateEmail(string $email): bool
    {
        try {

            $response = $this->client->request('GET', 'api/email/validate/' . $email, ['connect_timeout' => 15]);
            Log::info('Response Api Email validator: ' . $response->getBody());

            return json_decode((string) $response->getBody(), true)['status'] === '1';
        } catch (Exception $e) {
            Log::error('Error validateEmail: ' . $e->getMessage());
            throw $e;
        }
    }

}
