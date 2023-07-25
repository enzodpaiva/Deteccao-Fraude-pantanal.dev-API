<?php

namespace App\Http\Controllers;

use App\Bussiness\Services\ApiServerService;
use Illuminate\Http\Client\Request;
use Illuminate\Routing\Controller;

class FrontApiController extends Controller
{
    private $apiServerService;

    public function __construct(ApiServerService $apiServerService)
    {
        $this->apiServerService = $apiServerService;
    }

    public function getTransaction(Request $request)
    {
        $data = [];
        $response = $this->apiServerService->sendRandomTransactionToServer($data);

        return response()->json([
            'data' => $response['data'],
        ], $response['status']);
    }

    public function sendAnalyseSample(Request $request)
    {
        $data = $request->toArray();
        $response = $this->apiServerService->sendSampleViewToServer($data);

        return response()->json([
            'data' => $response['data'],
        ], $response['status']);
    }

    public function sendStoreFraud(Request $request)
    {
        $data = $request->toArray();
        $response = $this->apiServerService->sendStoreFraudToServer($data);

        return response()->json([
            'data' => $response['data'],
        ], $response['status']);
    }
}
