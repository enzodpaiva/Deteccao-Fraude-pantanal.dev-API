<?php

namespace App\Bussiness\Services;

use App\Bussiness\Enums\BitrixMap;
use App\Bussiness\Models\Sale;
use App\Exceptions\ApiLeadException;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use MongoDB\BSON\UTCDateTime;

class LeadService
{
    use BitrixMap;

    private $crm;
    private $pitziIntegrationService;

    public function __construct(BitrixCRMIntegrationService $crm, PitziIntegrationService $pitziIntegrationService)
    {
        $this->crm = $crm;
        $this->pitziIntegrationService = $pitziIntegrationService;

    }

    public static function factory(): LeadService
    {
        return app()->make(LeadService::class);
    }

    public function upsertSale(array $data): Sale
    {
        $sale = Sale::factory();

        // check if exists
        if (!empty($data['_id'])) {
            // check exists opportunity
            $sale = $this->checkExistsOpportunity($data['_id']);
        }

        // save lead
        $sale = $sale->populate($data);
        $this->populatePlanFromMap($sale);
        $sale->save();

        return $sale;
    }

    public function checkExistsOpportunity($saleId): Sale
    {
        $sale = Sale::factory();

        // get lead db
        $sale = $sale->getSale($saleId);
        // lead db not found
        if (empty($sale)) {
            Log::error('invalid id: ' . $saleId);
            throw new ApiLeadException('idinvalido', 400);
        }

        // opportunity already created
        if (!empty($sale->getOportunidade())) {
            $idOportunidade = $sale->getOportunidade()->getId();
            Log::error('[' . $sale->getId() . '] opportunity already created: ' . json_encode($sale->toArray()));
            throw new ApiLeadException(null, 200, ['mensagem' => 'oportunidadecriada', 'oportunidade' => $idOportunidade]);
        }

        return $sale;
    }

    public function checkDuplicatedOpportunity(Sale $sale)
    {
        $listDuplicatedOpportunity = $sale->listDuplicatedOpportunity($sale);

        if (count($listDuplicatedOpportunity) > 0) {

            $idOportunidade = $listDuplicatedOpportunity[0]['oportunidade']['id'];

            if (count($listDuplicatedOpportunity) > 1) {
                Log::error('[' . $sale->getId() . '] More than one Duplicated Opportunity ' . print_r(array_column($listDuplicatedOpportunity, '_id'), true));
            }

            // delete sale db
            $sale->deleteSale($sale->getId());

            Log::info('[' . $sale->getId() . '] Duplicated sale by Opportunity: ' . json_encode($listDuplicatedOpportunity));

            throw new ApiLeadException(null, 200, ['mensagem' => 'oportunidadeduplicada', 'oportunidade' => $idOportunidade]);
        }
    }

    public function createOpportunity(Sale $sale): Sale
    {
        // valid sale fields
        if (empty($sale->getOportunidade())) {
            Log::error('[' . $sale->getId() . '] Opportunity tag is empty: ' . json_encode($sale->toArray(), JSON_PRETTY_PRINT));
            throw new InvalidArgumentException('[' . $sale->getId() . '] Opportunity tag is empty');
        }

        if (!empty($sale->getOportunidade()->getId())) {
            Log::error('[' . $sale->getId() . '] Opportunity already created: ' . json_encode($sale->toArray(), JSON_PRETTY_PRINT));
            throw new InvalidArgumentException('[' . $sale->getId() . '] Opportunity already created: ' . $sale->getOportunidade()->getId());
        }

        // send sale to CRM
        try {
            $opportunityId = $this->crm->saveOpportunity($sale);
            $sale->getOportunidade()->setId($opportunityId);
            $sale->getOportunidade()->setDataInicio(new UTCDateTime(Carbon::now()));
            $sale->save();

            Log::info('[' . $sale->getId() . '] Created opportunity with id: ' . $sale->getOportunidade()->getId() . ' - status: ' . $sale->getOportunidade()->getStatus() . ' - substatus: ' . $sale->getOportunidade()->getSubStatus());

        } catch (Exception $e) {
            $sale->save();
            throw new Exception('[' . $sale->getId() . '] Error to create opportunity: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');
        }

        try {
            NotifyService::factory()->execute($sale);
        } catch (Exception $e) {
            Log::error('[' . $sale->getId() . '] Error to send mail to opportunity: ' . $sale->getOportunidade()->getId() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');
        }

        return $sale;
    }

    /** @todo set carrinho */

    public function createPartialOpportunity(Sale $sale, $substatus, $callNotify = true): Sale
    {
        // valid sale fields
        if (empty($sale->getLead())) {
            Log::error('[' . $sale->getId() . '] Lead tag is empty: ' . json_encode($sale->toArray(), JSON_PRETTY_PRINT));
            throw new InvalidArgumentException('[' . $sale->getId() . '] Lead tag is empty');
        }

        if (!empty($sale->getLead()->getId())) {
            Log::error('[' . $sale->getId() . '] Lead already created: ' . json_encode($sale->toArray(), JSON_PRETTY_PRINT));
            throw new InvalidArgumentException('[' . $sale->getId() . '] Lead already created: ' . $sale->getLead()->getId());
        }

        if ($sale->verifyDuplicatesLeadCount(3, 0, $sale)) {
            Sale::factory()->deleteSale($sale->getId());
            throw new InvalidArgumentException('[' . $sale->getId() . '] partial - alredy exists partial - deleted ');
        }

        if ($sale->verifyDuplicatesOpportunityCount(3, 0, $sale)) {
            Sale::factory()->deleteSale($sale->getId());
            throw new InvalidArgumentException('[' . $sale->getId() . '] partial - alredy exists opportunity - deleted ');
        }

        try {
            $leadId = $this->crm->savePartialOpportunity($sale, true);

            $sale->getLead()->setId($leadId);
            $sale->getLead()->setDataInicio(new UTCDateTime(Carbon::now()));
            $sale->save();

            //Send partial notify
            try {
                NotifyService::factory()->executePartial($sale);
            } catch (Exception $e) {
                Log::error('[' . $sale->getId() . '] Exception to Notify contact: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')' . $e->getTraceAsString());
            }

            return $sale;

        } catch (Exception $e) {
            // $sale->setLead(Lead::factory()->populate([]));
            // $sale->save();
            throw new Exception("[" . $sale->getId() . "] Error to create partial Lead: " . $e->getMessage());
        }
    }

    public function validEmail(Sale $sale): Sale
    {
        // Validate e-mail
        if (
            !empty($sale->getVerificacaoEmail()) and
            $sale->getVerificacaoEmail()->getApiStatus() === true and
            $sale->getVerificacaoEmail()->getEnviado() === true and
            $sale->getVerificacaoEmail()->getConfirmado() === false
        ) {
            try {
                // api suppoort valid email
                $isValidEmail = ApiSupportIntegrationService::factory()->validateEmail($sale->getConsumidor()->getEmail());

                $sale->getVerificacaoEmail()->setConfirmado($isValidEmail);
                $sale->getVerificacaoEmail()->setApiStatus(true);
                $sale->save();
            } catch (Exception $e) {
                $sale->getVerificacaoEmail()->setConfirmado(false);
                $sale->getVerificacaoEmail()->setApiStatus(false);
                $sale->save();
                Log::error('[' . $sale->getId() . '] Error on validate e-mail: ' . $e->getMessage());
            }
        }

        return $sale;
    }

    public function populatePlanFromMap(Sale $sale)
    {
        if (empty($this->planNameCode[$sale->getSeguro()->getPlanoId()])) {
            Log::error('[' . $sale->getId() . '][planonaoencontrado] Error in populatePlanFromMap: ' . json_encode($sale->getSeguro()->toArray()));
            throw new ApiLeadException('planonaoencontrado', 400);
        }

        $sale->getSeguro()->setNome($this->planNameCode[$sale->getSeguro()->getPlanoId()]);
    }

    public function getCrm(): BitrixCRMIntegrationService
    {
        return $this->crm;
    }

    public function createSignaturePitziSite(Sale $sale)
    {
        try {
            Log::info('[' . $sale->getId() . ']' . 'Data to be formatted to send createSignaturePitziSite: ' . json_encode($sale->toArray()));

            $order = [
                "representant_order" => [
                    "model" => $sale->getAparelho()->getModelo(),
                    "phonenumber" => $sale->getConsumidor()->getTelefoneServico(),
                    "email" => $sale->getConsumidor()->getEmail(),
                    "imei" => $sale->getAparelho()->getImei(),
                    "price" => number_format(((Integer) $sale->getSeguro()->getPrecoTotal()) / 100, 2, '.', ''),
                    "external_id" => $sale->getId(),
                    "installments" => $sale->getSeguro()->getNumeroParcelas(),
                    "plan" => $sale->getSeguro()->getPlanoId(),
                    "customer_document_number" => $sale->getConsumidor()->getCpf(),
                    "customer_birthdate" => DateTime::createFromFormat('dmY', $sale->getConsumidor()->getNascimento())->format('Y-m-d'),
                    "invoice_number" => $sale->getAparelho()->getNotaFiscal(),
                    "invoice_date" => DateTime::createFromFormat('dmY', $sale->getAparelho()->getDataCompra())->format('Y-m-d'),
                    "customer_name" => $sale->getConsumidor()->getNome(),
                    "customer_address_street" => $sale->getConsumidor()->getEndereco()->getLogradouro(),
                    "customer_address_state" => $sale->getConsumidor()->getEndereco()->getUf(),
                    "customer_address_city" => $sale->getConsumidor()->getEndereco()->getCidade(),
                    "customer_address_complement" => $sale->getConsumidor()->getEndereco()->getComplemento(),
                    "customer_address_number" => $sale->getConsumidor()->getEndereco()->getNumero(),
                    "customer_address_zipcode" => $sale->getConsumidor()->getEndereco()->getCep(),
                    "customer_address_neighborhood" => $sale->getConsumidor()->getEndereco()->getBairro(),
                    "device_value_paid" => number_format(((Integer) $sale->getAparelho()->getPreco()) / 100, 2, '.', ''),
                    "used_device" => $sale->getAparelho()->getUsado() ? true : false,
                ],
            ];

            Log::info('[' . $sale->getId() . ']' . 'Data send to createSignaturePitziSite: ' . json_encode($order));

            return $this->pitziIntegrationService->createSignaturePitziSite($order);

        } catch (Exception $e) {
            Log::error('[' . $sale->getId() . '] Error to create Signature Pitzi Site: ' . $e->getMessage());
            return [
                'status' => 500,
                'data' => "error send to pitzi",
            ];
        }
    }

    public function consultSubscriptionByImei($query)
    {
        try {
            if (!$query) {
                Log::error('[pitzi] invalid parameters: ' . json_encode($query));
                throw new InvalidArgumentException('parametros invalidos: ' . json_encode($query), 400);
            }

            return $this->pitziIntegrationService->consultSubscriptionByImei($query);

        } catch (InvalidArgumentException $e) {
            Log::error('Error InvalidArgumentException /consultSubscriptionByImei: ' . $e->getMessage());
            throw new ApiLeadException(null, 400, ['mensagem' => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('error Exception /consultSubscriptionByImei: ' . $e->getMessage());
            throw new ApiLeadException(null, 500, ['mensagem' => $e->getMessage()]);
        }
    }

    // verificação de foto do aparelho via endpoint não será mais feita
    // public function devicePhotoVerification($data, $orderId)
    // {
    //     try {
    //         if (empty($data) || empty($orderId)) {
    //             Log::error('[pitzi] order id: ' . $orderId . ' - invalid data: ' . json_encode($data));
    //             throw new InvalidArgumentException('parametros invalidos: ' . json_encode($data), 400);
    //         }

    //         return $this->pitziIntegrationService->devicePhotoVerification($data, $orderId);

    //     } catch (InvalidArgumentException $e) {
    //         Log::error('Error InvalidArgumentException /devicePhotoVerification: ' . $e->getMessage());
    //         throw new ApiLeadException(null, 400, ['mensagem' => $e->getMessage()]);
    //     } catch (Exception $e) {
    //         Log::error('error Exception /devicePhotoVerification: ' . $e->getMessage());
    //         throw new ApiLeadException(null, 500, ['mensagem' => $e->getMessage()]);
    //     }
    // }

    public function termsUseSignature($orderId)
    {
        try {
            if (empty($orderId)) {
                Log::error('[pitzi] order id invalid: ' . $orderId);
                throw new InvalidArgumentException('parametros invalidos: ' . $orderId, 400);
            }

            return $this->pitziIntegrationService->getTermsUse($orderId);

        } catch (InvalidArgumentException $e) {
            Log::error('Error InvalidArgumentException /termsUseSignature: ' . $e->getMessage());
            throw new ApiLeadException(null, 400, ['mensagem' => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('error Exception /termsUseSignature: ' . $e->getMessage());
            throw new ApiLeadException(null, 500, ['mensagem' => $e->getMessage()]);
        }
    }

    public function signatureCancel($orderId)
    {
        try {
            if (empty($orderId)) {
                Log::error('[pitzi] order id invalid: ' . $orderId);
                throw new InvalidArgumentException('parametros invalidos: ' . $orderId, 400);
            }

            return $this->pitziIntegrationService->signatureCancel($orderId);

        } catch (InvalidArgumentException $e) {
            Log::error('Error InvalidArgumentException /signatureCancel: ' . $e->getMessage());
            throw new ApiLeadException(null, 400, ['mensagem' => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('error Exception /signatureCancel: ' . $e->getMessage());
            throw new ApiLeadException(null, 500, ['mensagem' => $e->getMessage()]);
        }
    }

    public function termsUseSignatureCancel($orderId)
    {
        try {
            if (empty($orderId)) {
                Log::error('[pitzi] order id invalid: ' . $orderId);
                throw new InvalidArgumentException('parametros invalidos: ' . $orderId, 400);
            }

            return $this->pitziIntegrationService->getTermsSignatureCancel($orderId);

        } catch (InvalidArgumentException $e) {
            Log::error('Error InvalidArgumentException /termsUseSignatureCancel: ' . $e->getMessage());
            throw new ApiLeadException(null, 400, ['mensagem' => $e->getMessage()]);
        } catch (Exception $e) {
            Log::error('error Exception /termsUseSignatureCancel: ' . $e->getMessage());
            throw new ApiLeadException(null, 500, ['mensagem' => $e->getMessage()]);
        }
    }

    public function listOpportunitysForClient($cpf, $email)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf); // tira pontuacao cpf se tiver

        $sale = Sale::factory();
        $listOpportunitys = $sale->listOpportunitysForClient($cpf, $email);

        Log::info('List Opportunity for Client CPF: ' . $cpf . ' and email: ' . $email . '  - response: ' . json_encode($listOpportunitys));

        if (!$listOpportunitys->count()) {
            Log::error('No opportunity bot found for email ' . $email . ' and cpf ' . $cpf);
            throw new ApiLeadException(null, 400, ['mensagem' => 'No opportunity bot found for email ' . $email . ' and cpf ' . $cpf]);
        }

        return $listOpportunitys;
    }

}
