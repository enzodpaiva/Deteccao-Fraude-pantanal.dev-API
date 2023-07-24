<?php

namespace App\Http\Controllers;

use App\Bussiness\Enums\BitrixEnum;
use App\Bussiness\Enums\OpportunityDataEnum;
use App\Bussiness\Services\BitrixService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Laravel\Lumen\Routing\Controller as BaseController;

class BitrixController extends BaseController
{
    private $service;

    public function __construct(BitrixService $service)
    {
        $this->service = $service;
    }

    public function bitrixDealCreate($opportunityId, $status, $subStatus, $createdId, $lostSubStatus = '')
    {
        $status = OpportunityDataEnum::BITRIX_STATUS_UPSERT['deal'][urldecode($status)];
        $subStatus = OpportunityDataEnum::BITRIX_SUBSTATUS_UPSERT['deal'][urldecode($subStatus)];
        $lostSubStatus = OpportunityDataEnum::BITRIX_LOSTSUBSTATUS_UPSERT['deal'][urldecode($lostSubStatus)];
        $createdId = BitrixEnum::BITRIX_USER_UPSERT[urldecode($createdId)];
        Log::info('--------------- Create Bitrix opportunity: ' . $opportunityId);

        try {
            $this->service->bitrixDealUpsert($opportunityId, $status, $subStatus, $lostSubStatus, $createdId, true);

            return response()->json([
                'mensagem' => 'Deal criado com sucesso',
            ]);

        } catch (InvalidArgumentException $e) {
            Log::notice('[' . $opportunityId . '] Deal not created: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');
            return response()->json(array("message" => $e->getMessage()), 400);
        } catch (Exception $e) {
            Log::error('Error to creadte Deal: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

    public function bitrixDealUpdate($opportunityId, $status, $subStatus, $createdId, $lostSubStatus = '')
    {
        $status = OpportunityDataEnum::BITRIX_STATUS_UPSERT['deal'][urldecode($status)];
        $subStatus = OpportunityDataEnum::BITRIX_SUBSTATUS_UPSERT['deal'][urldecode($subStatus)];
        $lostSubStatus = OpportunityDataEnum::BITRIX_LOSTSUBSTATUS_UPSERT['deal'][urldecode($lostSubStatus)];
        $createdId = BitrixEnum::BITRIX_USER_UPSERT[urldecode($createdId)];

        Log::info('--------------- Update Bitrix opportunity: ' . $opportunityId);

        try {
            $this->service->bitrixDealUpsert($opportunityId, $status, $subStatus, $lostSubStatus, $createdId, false);

            return response()->json([
                'mensagem' => 'Deal atualizado com sucesso',
            ]);

        } catch (InvalidArgumentException $e) {
            Log::notice('[' . $opportunityId . '] Deal not updated: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');
            return response()->json(array("message" => $e->getMessage()), 400);
        } catch (Exception $e) {
            Log::error('Error to update Deal: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')' . $e->getTraceAsString());
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

    public function bitrixDealDelete(Request $request)
    {
        $bitrixRequest = $request->toArray();

        Log::info('--------------- Delete Bitrix Deal: ' . $bitrixRequest['data']['FIELDS']['ID'] . ' ---------------');

        $this->service->bitrixDealDelete($bitrixRequest['data']['FIELDS']['ID']);
    }

    public function bitrixLeadUpdate($leadId, $status, $subStatus, $createdId, $lostSubStatus = '')
    {
        $status = OpportunityDataEnum::BITRIX_STATUS_UPSERT['lead'][urldecode($status)];
        $subStatus = OpportunityDataEnum::BITRIX_SUBSTATUS_UPSERT['lead'][urldecode($subStatus)];
        $lostSubStatus = OpportunityDataEnum::BITRIX_LOSTSUBSTATUS_UPSERT['lead'][urldecode($lostSubStatus)];
        $createdId = BitrixEnum::BITRIX_USER_UPSERT[urldecode($createdId)];

        Log::info('--------------- Update Bitrix Lead: ' . $leadId . ' ---------------');
        try {
            $this->service->bitrixLeadUpsert($leadId, $status, $subStatus, $lostSubStatus, $createdId, false);

            return response()->json([
                "mensagem" => "Lead atualizado com sucesso",
            ]);
        } catch (InvalidArgumentException $e) {
            Log::notice('[' . $leadId . '] Lead not update: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');
            return response()->json(array("message" => $e->getMessage()), 400);
        } catch (Exception $e) {
            Log::error('Error to update Lead: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')' . $e->getTraceAsString());
            return response()->json(array("erro" => $e->getMessage()), 500);
        }
    }

    public function bitrixLeadCreate($leadId, $status, $subStatus, $createdId, $lostSubStatus = '')
    {
        $status = OpportunityDataEnum::BITRIX_STATUS_UPSERT['lead'][urldecode($status)];
        $subStatus = OpportunityDataEnum::BITRIX_SUBSTATUS_UPSERT['lead'][urldecode($subStatus)];
        $lostSubStatus = OpportunityDataEnum::BITRIX_LOSTSUBSTATUS_UPSERT['lead'][urldecode($lostSubStatus)];
        $createdId = BitrixEnum::BITRIX_USER_UPSERT[urldecode($createdId)];

        Log::info('--------------- Create Bitrix Lead: ' . $leadId . ' ---------------');

        try {
            $this->service->bitrixLeadUpsert($leadId, $status, $subStatus, $lostSubStatus, $createdId, true);

            return response()->json([
                "mensagem" => "Lead criado com sucesso",
            ]);
        } catch (InvalidArgumentException $e) {
            Log::notice('[' . $leadId . '] Lead not created: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');
            return response()->json(array("message" => $e->getMessage()), 400);
        } catch (Exception $e) {
            Log::error('Error to create Lead: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');
            return response()->json(array("erro" => $e->getMessage()), 500);
        }
    }

    public function bitrixLeadDelete(Request $request)
    {
        $bitrixRequest = $request->toArray();

        Log::info('--------------- Delete Bitrix Lead: ' . $bitrixRequest["data"]["FIELDS"]["ID"] . ' ---------------');
        $this->service->bitrixLeadDelete($bitrixRequest["data"]["FIELDS"]["ID"]);
    }

    public function checkUserHook($userId)
    {
        $listExcludeUsers = explode(',', env('BITRIX_USER_EXCLUDE'));
        return in_array($userId, $listExcludeUsers);
    }

    public function checkCreateUserHook($userId)
    {
        $listExcludeUsers = explode(',', env('BITRIX_USER_CREATE_EXCLUDE'));
        return in_array($userId, $listExcludeUsers);
    }
}
