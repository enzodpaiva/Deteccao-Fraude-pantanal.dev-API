<?php

namespace App\Bussiness\Services;

use App\Bussiness\Enums\OpportunityDataEnum;
use App\Bussiness\Helpers\BitrixIntegrationHelper;
use App\Bussiness\Models\Sale;
use DateInterval;
use DateTime;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use Throwable;

class BitrixCRMIntegrationService extends BitrixIntegrationHelper
{
    public static function factory(): self
    {
        return app()->make(BitrixCRMIntegrationService::class);
    }

    public function saveOpportunity(Sale $sale): int
    {
        // fields to deal
        $dealFields = $this->setDealFields($sale);

        try {
            // send deal to CRM
            $opportunityId = $this->requestCRM('crm.deal.add.json', $dealFields);

            return $opportunityId;
        } catch (Exception $e) {
            if ($e->getCode() === 400) {
                $response = json_decode($e->getResponse()->getBody(), true);

                if (strip_tags($response['error_description']) == 'O campo "Email de trabalho" contém um endereço incorreto.') {

                    // set mock e-mail
                    $sale->getConsumidor()->setEmail('email@invalido.com');

                    // resend lead
                    return $this->saveOpportunity($sale);
                }

                // error to send deal to CRM
                throw new Exception('[' . $sale->getId() . '] Error to send CRM Deal: ' . json_encode($dealFields) . ' Error Message: ' . $e->getMessage());
            }

        }
    }

    public function savePartialOpportunity(Sale $lead, $partial): int
    {
        $leadFields = $this->setLeadFields($lead, $partial);

        try {
            // send lead to CRM
            $leadId = $this->requestCRM('crm.lead.add.json', $leadFields);
            Log::debug('CRM Lead Sent with success');

            return $leadId;
        } catch (Exception $e) {

            if ($e->getCode() === 400) {
                $response = json_decode($e->getResponse()->getBody(), true);

                if (strip_tags($response['error_description']) == 'O campo "Email de trabalho" contém um endereço incorreto.') {

                    // set mock e-mail
                    $lead->getConsumidor()->setEmail('email@invalido.com');

                    // resend lead
                    return $this->savePartialOpportunity($lead, $partial);
                }
            }
            // error to send deal to CRM
            Log::critical("[" . $lead->getId() . "] Error to send CRM Lead(" . $lead->getLead()->getId() . "): " . json_encode($leadFields) . " Error Message: " . $e->getMessage() . " - " . $e->getFile() . " (" . $e->getLine() . ")" . "\n" . $e->getTraceAsString());
        }
    }

    public function getOportunidade($idOpportunity)
    {
        try {
            return $this->requestCRM('crm.deal.get', ["id" => $idOpportunity]);
        } catch (ClientException $e) {

            $body = json_decode($e->getResponse()->getBody(), true);

            // if ($body["error_description"] === "Not found" and $e->getCode() === 400) {

            //     $timControleBoletoRepository = app()->make(LeadRepository::class);
            //     $timControleBoletoRepository->deleteSaleByOpportunity($idOpportunity);
            //     Log::notice('Bitrix deal not exists - deleted local opportunity id: ' . $idOpportunity);

            // } else {
            //     Log::error('Error to get Crm Deal (' . $idOpportunity . '): ' . $e->getMessage());
            // }

            return null;
        } catch (Throwable $e) {
            Log::error('Error to get Crm Deal (' . $idOpportunity . '): ' . $e->getMessage());
            return null;
        }
    }

    public function getUser($idUser)
    {
        try {
            return $this->requestCRM('user.get', ["id" => $idUser]);
        } catch (Throwable $e) {
            Log::error('Error to get CRM User (' . $idUser . '): ' . $e->getMessage());
            return null;
        }
    }

    public function getLead($idLead)
    {
        try {
            return $this->requestCRM('crm.lead.get', ["id" => $idLead]);
        } catch (ClientException $e) {

            $body = json_decode($e->getResponse()->getBody(), true);

            // if ($body["error_description"] === "Not found" and $e->getCode() === 400) {

            //     $timControleBoletoRepository = app()->make(LeadRepository::class);
            //     $timControleBoletoRepository->deleteSaleByLead($idLead);

            //     Log::notice('Bitrix lead not exists - deleted local lead id: ' . $idLead);
            // } else {
            //     Log::error('Error to get Crm Leads (' . $idLead . '): ' . $e->getMessage());
            // }
            return null;
        } catch (Throwable $e) {
            Log::error('Error to get Crm Deal (' . $idLead . '): ' . $e->getMessage());
            return null;
        }
    }

    public function updateStatusDealCRM(Sale $sale, $obs = null)
    {
        if (empty($sale->getOportunidade()->getId())) {
            Log::critical('Id deal not found in Opportunity bd id: ' . $sale->getId());
            return false;
        }

        $data['id'] = $sale->getOportunidade()->getId();
        $data['fields']['STAGE_ID'] = $this->opportunityStatus['deal'][$sale->getOportunidade()->getStatus()];

        if ($sale->getOportunidade()->getStatus() === OpportunityDataEnum::PERDIDA or
            $sale->getOportunidade()->getStatus() === OpportunityDataEnum::ANALISE_FALHA or
            $sale->getOportunidade()->getStatus() === OpportunityDataEnum::CANCELADO
        ) {
            $data['fields']['UF_CRM_63876E409D4DF'] = $this->opportunityLostSubStatus['deal'][$sale->getOportunidade()->getSubStatus()];
        } else {
            $data['fields']['UF_CRM_1629487085'] = $this->opportunitySubStatus['deal'][$sale->getOportunidade()->getSubStatus()];
        }

        (!empty($sale->getOportunidade()->getDataAssinaturaPitzi())) ? $data['fields']['UF_CRM_1681835352778'] = empty($datePitzi = DateTime::createFromFormat('dmY', $sale->getOportunidade()->getDataAssinaturaPitzi())) ? '' : $datePitzi->format('Y-m-d') : null;

        (!empty($sale->getOportunidade()->getOrderIdPitzi())) ? $data['fields']['UF_CRM_1681839374808'] = $sale->getOportunidade()->getOrderIdPitzi() : null;

        (!empty($sale->getAparelho()->getValidado())) ? $data['fields']['UF_CRM_1681308422'] = $sale->getAparelho()->getValidado() : null;

        (!empty($sale->getOportunidade()->getDataVenda())) ? $data['fields']['UF_CRM_1681308312'] = $sale->getOportunidade()->getDataVenda()->toDateTime()->sub(new DateInterval('PT3H'))->format('Y-m-d H:i:s') : null;

        (!empty($sale->getOportunidade()->getDataFechamento())) ? $data['fields']['UF_CRM_1681308312'] = $sale->getOportunidade()->getDataFechamento()->toDateTime()->sub(new DateInterval('PT3H'))->format('Y-m-d H:i:s') : null;

        (!empty($sale->getSeguro()->getFormaPagamento())) ? $data['fields']['UF_CRM_1681926312'] = $this->paymentForm['deal'][$sale->getSeguro()->getFormaPagamento()] : null;

        (!empty($sale->getSeguro()->getPrecoParcelado())) ? $data['fields']['UF_CRM_1680814267'] = number_format($sale->getSeguro()->getPrecoParcelado() / 100, 2, ',', '') : null;

        (!empty($sale->getSeguro()->getNumeroParcelas())) ? $data['fields']['UF_CRM_1680814249'] = $sale->getSeguro()->getNumeroParcelas() : null;

        (!empty($sale->getSeguro()->getPrecoTotal())) ? $data['fields']['OPPORTUNITY'] = number_format($sale->getSeguro()->getPrecoTotal() / 100, 2, ',', '') : null;

        (!empty($sale->getSeguro()->getPrecoTotal())) ? $data['fields']['UF_CRM_5F1743E756F2B'] = number_format($sale->getSeguro()->getPrecoTotal() / 100, 2, ',', '') : null;

        try {
            // send deal to CRM
            $this->requestCRM('crm.deal.update', $data);
            Log::info("[" . $sale->getId() . "] Bitrix update success Deal: " . $sale->getOportunidade()->getId() . " - Status: " . $sale->getOportunidade()->getStatus() . " - Substatus: " . $sale->getOportunidade()->getSubstatus());

            return true;
        } catch (ClientException $e) {

            $body = json_decode($e->getResponse()->getBody(), true);

            if ($body["error_description"] === "Not found" and $e->getCode() === 400) {

                $saleRepository = Sale::factory();
                $saleRepository->deleteSale($sale->getId());

                Log::notice('**** Deleted sale id: ' . $sale->getId());

            }
            // error to send deal to CRM
            Log::critical('[' . $sale->getId() . '] Error to update CRM Deal(' . $sale->getOportunidade()->getId() . '): ' . json_encode($data) . ' Message: ' . $e->getMessage());
            return false;

        } catch (Exception $e) {
            // error to send deal to CRM
            Log::critical("[" . $sale->getId() . "] Error to update status CRM Deal(" . $sale->getOportunidade()->getId() . "): " . json_encode($data) . " Error Message: " . $e->getMessage() . " - " . $e->getFile() . " (" . $e->getLine() . ")" . "\n" . $e->getTraceAsString());
            return false;
        }
    }

    public function updateStatusLeadCRM(Sale $sale)
    {
        if (empty($sale->getLead()->getId())) {
            Log::critical('Id sale not found in lead bd id: ' . $sale->getId());
            return;
        }

        $data['id'] = $sale->getLead()->getId();
        $data['fields']['STATUS_ID'] = $this->opportunityStatus['lead'][$sale->getLead()->getStatus()];

        if ($sale->getLead()->getStatus() === OpportunityDataEnum::CANCELADO) {
            $data['fields']['UF_CRM_1633459319'] = $this->opportunityLostSubStatus['lead'][$sale->getLead()->getSubStatus()];
        } else {
            $data['fields']['UF_CRM_1628535982'] = $this->opportunitySubStatus['lead'][$sale->getLead()->getSubStatus()];
        }

        try {
            // send Lead to CRM
            $this->requestCRM($this->crm['param']['lead.update'], $data);
            Log::info("[" . $sale->getId() . "] Bitrix update success Lead: " . $sale->getLead()->getId() . " - Status: " . $sale->getLead()->getStatus() . " - Substatus: " . $sale->getLead()->getSubstatus());

            return true;
        } catch (ClientException $e) {

            $body = json_decode($e->getResponse()->getBody(), true);

            if ($body["error_description"] === "Lead is not found" and $e->getCode() === 400) {

                $saleRepository = Sale::factory();
                $saleRepository->deleteSale($sale->getId());

                Log::notice('**** Deleted sale id: ' . $sale->getId());

            }
            // error to send lead to CRM
            Log::critical('[' . $sale->getId() . '] Error to update CRM Lead(' . $sale->getLead()->getId() . '): ' . json_encode($data) . ' Message: ' . $e->getMessage());
            return false;

        } catch (Exception $e) {
            // error to send lead to CRM
            Log::critical("[" . $sale->getId() . "] Error to update status CRM Lead(" . $sale->getLead()->getId() . "): " . json_encode($data) . " Error Message: " . $e->getMessage() . " - " . $e->getFile() . " (" . $e->getLine() . ")" . "\n" . $e->getTraceAsString());
            return false;
        }
    }

    public function addCommentToDeal($dealId, $comment)
    {
        $data = [
            'fields' => [
                'ENTITY_ID' => $dealId,
                'ENTITY_TYPE' => 'deal',
                'COMMENT' => $comment,
            ],
        ];

        try {
            // send deal to CRM
            $this->requestCRM('crm.timeline.comment.add', $data);
            Log::debug('CRM Deal add comment to Bitrix id: ' . $dealId);
        } catch (Throwable $e) {
            Log::error('Error to add comment CRM Deal(' . $dealId . '): ' . json_encode($data) . ' Message: ' . $e->getMessage());
        }
    }
}
