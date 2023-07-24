<?php

namespace App\Bussiness\Services;

use App\Bussiness\Enums\BitrixMap;
use App\Bussiness\Enums\OpportunityDataEnum;
use App\Bussiness\Models\Sale;
use App\Bussiness\Models\SaleDealBitrix;
use App\Bussiness\Models\SaleLeadBitrix;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class BitrixService
{
    use BitrixMap;
    private $saleDealRepository;
    private $saleLeadRepository;
    private $crm;

    public function __construct(SaleDealBitrix $saleDealRepository, SaleLeadBitrix $saleLeadRepository, BitrixCRMIntegrationService $crm)
    {
        $this->saleDealRepository = $saleDealRepository;
        $this->saleLeadRepository = $saleLeadRepository;
        $this->crm = $crm;
    }

    public function bitrixDealUpsert($opportunityId, $status, $subStatus, $lostSubStatus, $createdId, $create = false)
    {
        // Check returned Deal
        if (empty($opportunityId)) {
            throw new Exception('Opportunity not returned from Bitrix:' . $opportunityId);
        }

        // Check user hook
        if ($this->checkUserHook($createdId)) {
            throw new InvalidArgumentException('User not authorized: ' . $createdId, 401);
        }

        // Check create user
        if ($this->checkCreateUserHook($createdId) and $create) {
            throw new InvalidArgumentException('User not authorized to create: ' . $createdId, 401);
        }

        $sale = $this->saleDealRepository->getSaleByOpportunity($opportunityId);

        // Check sale from lead
        if (empty($sale)) {
            $sale = SaleDealBitrix::factory();
            Log::info('Opportunity not found in database: ' . $opportunityId);
        }

        //verificar status e substatus somente se for update
        if ($create === false and !empty($sale->getOportunidade())) {

            $status = array_search($status, $this->opportunityStatus['deal']);

            if ($status === OpportunityDataEnum::PERDIDA) {
                $subStatus = array_search($lostSubStatus, $this->opportunityLostSubStatus['deal']);
            } else {
                $subStatus = array_search($subStatus, $this->opportunitySubStatus['deal']);
            }

            if (
                $sale->getOportunidade()->getStatus() === $status and
                $sale->getOportunidade()->getSubStatus() === $subStatus
            ) {

                Log::info('[digital] Opportunity not update in database [no changes]: ' . $opportunityId);
                return;
            }

        }

        $data['deal'] = $this->crm->getOportunidade($opportunityId);

        // Check returned Deal
        if (empty($data['deal'])) {
            throw new Exception('Opportunity not returned from Bitrix:' . $opportunityId);
        }

        // execute deal flow
        $this->executeDealFlow($data, $sale);
    }

    private function executeDealFlow(array $data, SaleDealBitrix $sale)
    {
        // get sale from Bitrix
        $data['seller'] = $this->bitrixGetSeller($data['deal']->ASSIGNED_BY_ID);
        $data['dealRepository'] = $sale;

        // populate from class SaleDealBitrix
        $sale->populate($data);

        // send notify
        $this->sendNotifyFromDealUpsert($sale);

        // save lead
        $sale->save();

        Log::info('[' . $sale->getId() . '] Save Deal from Bitrix ' . $sale->getOportunidade()->getId() . ' - Status: ' . $sale->getOportunidade()->getStatus() . ' - Substatus: ' . $sale->getOportunidade()->getSubStatus());
    }

    public function bitrixLeadUpsert($leadId, $status, $subStatus, $lostSubStatus, $createdId, $create = false)
    {
        // Check returned Deal
        if (empty($leadId)) {
            throw new Exception('Lead not returned from Bitrix:' . $leadId);
        }

        // Check user hook
        if ($this->checkUserHook($createdId)) {
            throw new InvalidArgumentException('User not authorized: ' . $createdId, 401);
        }

        // Check create user
        if ($this->checkCreateUserHook($createdId) and $create) {
            throw new InvalidArgumentException('User not authorized to create: ' . $createdId, 401);
        }

        $sale = $this->saleLeadRepository->getSaleByLead($leadId);

        // Check sale from lead
        if (empty($sale)) {
            $sale = SaleLeadBitrix::factory();
            Log::info('Lead not found in database: ' . $leadId);
        }

        if (!empty($sale->getOportunidade())) {
            Log::info('Opportunity already exists, Lead: ' . $leadId);
            throw new Exception('Opportunity already exists, Lead: ' . $leadId);
        }

        //verificar status e substatus somente se for update
        if ($create === false and !empty($sale->getLead())) {

            $status = array_search($status, $this->opportunityStatus['lead']);

            if ($status === OpportunityDataEnum::CANCELADO) {
                $subStatus = array_search($lostSubStatus, $this->opportunityLostSubStatus['lead']);
            } else {
                $subStatus = array_search($subStatus, $this->opportunitySubStatus['lead']);
            }

            if (
                $sale->getLead()->getStatus() === $status and
                $sale->getLead()->getSubStatus() === $subStatus
            ) {

                Log::info('[digital] Lead not update in database [no changes]: ' . $leadId);
                return;
            }

        }

        $data['lead'] = $this->crm->getLead($leadId);

        // Check returned lead
        if (empty($data['lead'])) {
            throw new Exception('Lead not returned from Bitrix:' . $leadId);
        }

        // execute lead flow
        $this->executeLeadFlow($data, $sale);
    }

    private function executeLeadFlow(array $data, SaleLeadBitrix $sale)
    {
        // get sale from Bitrix
        $data['seller'] = $this->bitrixGetSeller($data['lead']->ASSIGNED_BY_ID);
        $data['leadRepository'] = $sale;

        // populate from class saleLeadBitrix
        $sale->populate($data);

        //save lead
        $sale->save();

        Log::info('[' . $sale->getId() . '] Save Lead from Bitrix ' . $sale->getLead()->getId() . ' - Status: ' . $sale->getLead()->getStatus() . ' - Substatus: ' . $sale->getLead()->getSubStatus());
    }

    private function sendNotifyFromDealUpsert(Sale $sale)
    {
        if (
            (
                // change status
                $sale->getOportunidade()->getStatus() !== ($sale->getOriginal()['oportunidade']['status'] ?? '')
            ) or (
                // ganha - change substatus
                $sale->getOportunidade()->getStatus() === OpportunityDataEnum::GANHA and
                $sale->getOportunidade()->getSubStatus() !== ($sale->getOriginal()['oportunidade']['subStatus'] ?? '')
            ) or (
                // logistica - change substatus
                $sale->getOportunidade()->getStatus() === OpportunityDataEnum::PERDIDA and
                $sale->getOportunidade()->getSubStatus() !== ($sale->getOriginal()['oportunidade']['subStatus'] ?? '')
            )
        ) {
            NotifyService::factory()->execute($sale);
        }
    }

    public function bitrixGetSeller($userId): ?array
    {
        // Return Bitrix User
        $crmUser = $this->crm->getUser($userId);

        if (!empty($crmUser)) {
            return [
                'id' => $crmUser[0]->ID,
                'nome' => mb_strtolower($crmUser[0]->NAME . ' ' . $crmUser[0]->LAST_NAME),
            ];
        }
        return null;
    }

    public function checkUserHook($userId): bool
    {
        $listExcludeUsers = explode(',', env('BITRIX_USER_EXCLUDE'));
        return in_array($userId, $listExcludeUsers);
    }

    public function checkCreateUserHook($userId): bool
    {
        $listExcludeUsers = explode(',', env('BITRIX_USER_CREATE_EXCLUDE'));
        return in_array($userId, $listExcludeUsers);
    }

    public function bitrixDealDelete($opportunityId)
    {
        $this->saleDealRepository->deleteSaleByOpportunity($opportunityId);
        Log::notice('Deleted deal id:' . $opportunityId);
    }

    public function bitrixLeadDelete($leadId)
    {
        $this->saleLeadRepository->deleteSaleByLead($leadId);
        Log::notice('Deleted lead id:' . $leadId);
    }
}
