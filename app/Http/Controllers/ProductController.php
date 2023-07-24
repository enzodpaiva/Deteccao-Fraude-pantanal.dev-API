<?php

namespace App\Http\Controllers;

use App\Bussiness\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class ProductController extends BaseController
{

    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function searchDevices(Request $request)
    {
        $query = $request->query();

        Log::debug(json_encode($query));

        $response = $this->productService->getProductsByTacV2($query);

        return response()->json([
            'data' => $response['data'],
        ], $response['status']);
    }

    public function searchDeviceComplete(Request $request)
    {
        $query = $request->query();

        Log::debug(json_encode($query));

        $response = $this->productService->consultFullSearchProducts($query);

        return response()->json([
            'data' => $response['data'],
        ], $response['status']);
    }
}
