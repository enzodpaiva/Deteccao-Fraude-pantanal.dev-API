<?php

namespace App\Http\Controllers;

use App\Bussiness\Enums\OpportunityDataEnum;
use App\Bussiness\Models\Opportunity;
use App\Bussiness\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller as BaseController;

class LeadController extends BaseController
{

    private $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    public function save(Request $request)
    {
        Log::info("--------------------RECEIVED DEAL SAVE PITZI --------------------");
        Log::info(json_encode($request->toArray()));

        $data = $request->toArray();

        Log::debug(json_encode($data));

        // set ip
        $data['consumidor']['rastreamento']['ip'] = $request->ip();

        // upsert sale
        $sale = $this->leadService->upsertSale($data);

        // check e-mail
        $sale = $this->leadService->validEmail($sale);

        // create response
        $response = [
            '_id' => $sale->getId(),
        ];

        !empty($sale->getVerificacaoEmail()) ? ($response['verificacaoEmail'] = $sale->getVerificacaoEmail()->toArray()) : null;

        return response()->json($response, Response::HTTP_OK);

    }

    public function saveFinish(Request $request)
    {
        Log::info("--------------------RECEIVED DEAL SAVE FINISH PITZI --------------------");
        Log::info(json_encode($request->toArray()));

        $data = $request->toArray();

        // set ip
        $data['consumidor']['rastreamento']['ip'] = $request->ip();

        // save sale
        $sale = $this->leadService->upsertSale($data);

        // check payload full  filled
        $sale->filled();

        // check duplicated
        $this->leadService->checkDuplicatedOpportunity($sale);

        //envio para pitzi
        $response = $this->leadService->createSignaturePitziSite($sale);

        if ($response['status'] == Response::HTTP_CREATED) {
            $opportunity = [
                'status' => OpportunityDataEnum::PENDENTE,
                'subStatus' => OpportunityDataEnum::CADASTRO_EFETUADO,
                'urlPagamentoPitzi' => $response['data']['payment_url'] ?? '',
            ];

        } else { // erro requisição para pitzi
            $opportunity = [
                'status' => OpportunityDataEnum::ANALISE_FALHA,
                'subStatus' => OpportunityDataEnum::ERRO_ENVIAR_PEDIDO_PITZI,
                'motivoFalhaPitzi' => $response['data'],
            ];
        }

        $sale->setOportunidade(Opportunity::factory()->populate($opportunity));

        // create opportunity
        $sale = $this->leadService->createOpportunity($sale);

        return response()->json([
            '_id' => $sale->getId(),
            'oportunidade' => $sale->getOportunidade()->getId(),
            'responsePitzi' => $response['data'],
        ], Response::HTTP_OK);
    }

    // verificação de foto do aparelho via endpoint não será mais feita
    // public function devicePhotoVerification(DevicePhotoRequest $request, $orderId)
    // {
    //     Log::info("--------------------RECEIVED POST DEVICE VERIFICATION PITZI --------------------");
    //     Log::info(json_encode($request->all()));

    //     $data = $request->all();

    //     //envio para pitzi
    //     $response = $this->leadService->devicePhotoVerification($data, $orderId);

    //     return response()->json([
    //         'data' => $response['data'],
    //     ], $response['status']);
    // }

    public function termsUseSignature(Request $request, $orderId)
    {
        Log::info("--------------------RECEIVED GET TERMS USE SIGNATURE PITZI --------------------");
        Log::info('orderId: ' . $orderId);

        //envio para pitzi
        $response = $this->leadService->termsUseSignature($orderId);

        if (is_array($response)) {
            return response()->json([
                'data' => $response['data'],
            ], $response['status']);
        }

        return $response;
    }

    public function signatureCancel(Request $request, $orderId)
    {
        Log::info("--------------------RECEIVED SIGNATURE CANCEL PITZI --------------------");
        Log::info('orderId: ' . $orderId);

        //envio para pitzi
        $response = $this->leadService->signatureCancel($orderId);

        return response()->json([
            'data' => $response['data'],
        ], $response['status']);
    }

    public function termsUseSignatureCancel(Request $request, $orderId)
    {
        Log::info("--------------------RECEIVED GET TERMS USE SIGNATURE CANCEL PITZI --------------------");
        Log::info('orderId: ' . $orderId);

        //envio para pitzi
        $response = $this->leadService->termsUseSignatureCancel($orderId);

        if (is_array($response)) {
            return response()->json([
                'data' => $response['data'],
            ], $response['status']);
        }

        return $response;
    }

    public function listOpportunitysForClient($cpf, $email)
    {
        $listOpportunitys = $this->leadService->listOpportunitysForClient($cpf, $email);

        return response()->json([
            'oportunidades' => $listOpportunitys,
        ], Response::HTTP_OK);
    }

}
