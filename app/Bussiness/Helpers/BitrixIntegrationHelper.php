<?php

namespace App\Bussiness\Helpers;

use App\Bussiness\Enums\BitrixMap;
use App\Bussiness\Enums\OpportunityDataEnum;
use App\Bussiness\Models\Sale;
use Carbon\Carbon;
use DateTime;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;

abstract class BitrixIntegrationHelper
{
    use BitrixMap;

    private $client;
    private $crmServiceConfig;

    public function __construct()
    {
        $this->crmServiceConfig = config('apiServices.crm');
        $this->client = new Client($this->crmServiceConfig['client']);
    }

    protected function setDealFields(Sale $sale): array
    {
        if (!$sale->getCreatedAt()) {
            $saleRepository = Sale::factory();
            $saleConsult = $saleRepository->getSale($sale->getId())->toArray();
            $sale->setCreatedAt(new UTCDateTime(DateTime::createFromFormat('Y-m-d H:i:s', $saleConsult['created_at'])));
        }

        $data = [
            'fields' => [
                'TITLE' => $this->getTitle($sale, false),
                'TYPE_ID' => 'GOODS',
                'STAGE_ID' => 'NEW',
                'OPENED' => 'Y',
                'CURRENCY_ID' => 'BRL',
                'OPPORTUNITY' => number_format($sale->getSeguro()->getPrecoTotal() / 100, 2, ',', ''),
                'BEGINDATE' => Carbon::now()->toIso8601String(),
                'UF_CRM_63876E40E7945' => $this->sourceNameCode['deal'][$sale->getCanal()],
                'UF_CRM_1681308349' => $sale->getCreatedAt()->toDateTime()->format('Y-m-d H:i:s'), // Data de criação

                //consumidor
                'UF_CRM_1595880784' => $sale->getConsumidor()->getNome(),
                'UF_CRM_1681825642263' => $sale->getConsumidor()->getFiliacao(),
                'UF_CRM_5F1743E4E6988' => $sale->getConsumidor()->getCpf(),
                'UF_CRM_1595527789' => $sale->getConsumidor()->getEmail(),
                'UF_CRM_1595880942' => empty($birthday = DateTime::createFromFormat('dmY', $sale->getConsumidor()->getNascimento())) ? '' : $birthday->format('Y-m-d'),
                'UF_CRM_1626124892' => $sale->getConsumidor()->getTelefoneServico(), //telefone cliente
                'UF_CRM_1682002763625' => $sale->getConsumidor()->getTelefoneContato(), //telefone alternativo cliente

                //seguro
                'UF_CRM_1626125137' => $this->planNameCode[$sale->getSeguro()->getPlanoId()], //plano contratado
                'UF_CRM_1681926312' => $this->paymentForm['deal'][$sale->getSeguro()->getFormaPagamento()], //forma de pagamento
                'UF_CRM_5F1743E756F2B' => number_format($sale->getSeguro()->getPrecoTotal() / 100, 2, ',', ''), // valor total do plano
                'UF_CRM_1680814249' => $sale->getSeguro()->getNumeroParcelas(), // numero de parcelas plano
                'UF_CRM_1680814267' => number_format($sale->getSeguro()->getPrecoParcelado() / 100, 2, ',', ''), // valor do plano parcelado

                //aparelho
                'UF_CRM_1626124941' => $sale->getAparelho()->getImei(), //imei
                'UF_CRM_1626125006' => $sale->getAparelho()->getModelo(), //modelo aparelho
                'UF_CRM_1626125031' => number_format(((Integer) $sale->getAparelho()->getPreco()) / 100, 2, ',', ''), // valor aparelho
                'UF_CRM_1626125073' => $sale->getAparelho()->getNotaFiscal(), //numero da nota
                'UF_CRM_1626125051' => empty($dataCompra = DateTime::createFromFormat('dmY', $sale->getAparelho()->getDataCompra())) ? '' : $dataCompra->format('Y-m-d'), //data da compra do aparelho
                'UF_CRM_1681308422' => $sale->getAparelho()->getValidado(), //aparelho validado
                'UF_CRM_1681756511' => $sale->getAparelho()->getId(), // id aparelho pitzi
                'UF_CRM_1681756457' => $sale->getAparelho()->getUsado(), //aparelho usado

                //endereco
                'UF_CRM_5F1743E503203' => $sale->getConsumidor()->getEndereco()->getLogradouro(),
                'UF_CRM_5F1743E50AE5A' => $sale->getConsumidor()->getEndereco()->getBairro(),
                'UF_CRM_5F1743E5106EA' => $sale->getConsumidor()->getEndereco()->getCidade(),
                'UF_CRM_5F1743E5166AB' => $sale->getConsumidor()->getEndereco()->getUf(),
                'UF_CRM_5F1743E52C81C' => $sale->getConsumidor()->getEndereco()->getCep(),
                'UF_CRM_5F1743E5347BC' => $sale->getConsumidor()->getEndereco()->getNumero(),
                'UF_CRM_5F1743E53E09D' => $sale->getConsumidor()->getEndereco()->getComplemento(),
                'UF_CRM_1681908577405' => $sale->getConsumidor()->getEndereco()->getReferencia(),

            ],
            'params' => array('REGISTER_SONET_EVENT' => 'Y'),
        ];

        // set substatus if exists oportunidade
        if (!empty($sale->getOportunidade())) {
            $data['fields']['STAGE_ID'] = $this->opportunityStatus['deal'][$sale->getOportunidade()->getStatus()];

            if ($sale->getOportunidade()->getStatus() === OpportunityDataEnum::PERDIDA or
                $sale->getOportunidade()->getStatus() === OpportunityDataEnum::ANALISE_FALHA or
                $sale->getOportunidade()->getStatus() === OpportunityDataEnum::CANCELADO
            ) {
                $data['fields']['UF_CRM_63876E409D4DF'] = $this->opportunityLostSubStatus['deal'][$sale->getOportunidade()->getSubStatus()];
                $data['fields']['UF_CRM_1683829222235'] = $sale->getOportunidade()->getMotivoFalhaPitzi();
            } else {
                $data['fields']['UF_CRM_1629487085'] = $this->opportunitySubStatus['deal'][$sale->getOportunidade()->getSubStatus()];
                $data['fields']['UF_CRM_1683829177102'] = $sale->getOportunidade()->getUrlPagamentoPitzi();
            }
        }

        return $data;
    }

    protected function setLeadFields(Sale $sale): array
    {
        if (!$sale->getCreatedAt()) {
            $saleRepository = Sale::factory();
            $saleConsult = $saleRepository->getSale($sale->getId())->toArray();
            $sale->setCreatedAt(new UTCDateTime(DateTime::createFromFormat('Y-m-d H:i:s', $saleConsult['created_at'])));
        }

        $data = [
            'fields' => [
                'TITLE' => $this->getTitle($sale, true),
                'NAME' => $sale->getConsumidor()->getNome(),
                'IS_RETURN_CUSTOMER' => 'N',
                'STATUS_ID' => 'NEW',
                'CURRENCY_ID' => 'BRL',
                'OPPORTUNITY' => number_format($sale->getSeguro()->getPrecoTotal() / 100, 2, ',', ''),
                'HAS_PHONE' => 'Y',
                'HAS_EMAIL' => 'Y',
                'HAS_IMOL' => 'N',
                'STATUS_SEMANTIC_ID' => 'P',
                'OPENED' => 'Y',
                'BIRTHDATE' => empty($birthday = DateTime::createFromFormat('dmY', $sale->getConsumidor()->getNascimento())) ? '' : $birthday->format('Y-m-d'), // Data de nascimento,
                'PHONE' => [ // Telefones
                    [
                        'VALUE' => $sale->getConsumidor()->getTelefoneServico(),
                        'VALUE_TYPE' => 'WORK',
                    ],
                ],
                'EMAIL' => [ // E-mails
                    [
                        'VALUE' => $sale->getConsumidor()->getEmail(),
                        'VALUE_TYPE' => 'WORK',
                    ],
                ],
                'UF_CRM_1667915980' => $this->sourceNameCode['lead'][$sale->getCanal()],
                'UF_CRM_1681310357' => $sale->getCreatedAt()->toDateTime()->format('Y-m-d H:i:s'), // Data de criação

                //consumidor
                'UF_CRM_1624288150' => $sale->getConsumidor()->getNome(),
                'UF_CRM_1593723209' => $sale->getConsumidor()->getCpf(),
                'UF_CRM_1595527731' => $sale->getConsumidor()->getEmail(),
                'UF_CRM_1624288169' => empty($birthday = DateTime::createFromFormat('dmY', $sale->getConsumidor()->getNascimento())) ? '' : $birthday->format('Y-m-d'),
                'UF_CRM_1626124868' => $sale->getConsumidor()->getTelefoneServico(),
                'UF_CRM_1681845085915' => $sale->getConsumidor()->getFiliacao(),
                'UF_CRM_1682002482760' => $sale->getConsumidor()->getTelefoneContato(), //telefone alternativo cliente

                //plano
                'UF_CRM_1624369553' => $this->planNameCode[$sale->getSeguro()->getPlanoId()], //plano contratado
                'UF_CRM_1595017429' => number_format($sale->getSeguro()->getPrecoTotal() / 100, 2, ',', ''), // valor total do plano
                'UF_CRM_1680814449' => $sale->getSeguro()->getNumeroParcelas(), // numero de parcelas plano
                'UF_CRM_1680814471' => number_format($sale->getSeguro()->getPrecoParcelado() / 100, 2, ',', ''), // valor do plano parcelado
                'UF_CRM_1681995026' => $this->paymentForm['lead'][$sale->getSeguro()->getFormaPagamento()], //forma de pagamento

                //aparelho
                'UF_CRM_1624287964' => $sale->getAparelho()->getImei(), //imei
                'UF_CRM_1624287990' => $sale->getAparelho()->getModelo(), //modelo aparelho
                'UF_CRM_1624287939' => number_format(((Integer) $sale->getAparelho()->getPreco()) / 100, 2, ',', ''), // valor aparelho
                'UF_CRM_1624287818' => $sale->getAparelho()->getNotaFiscal(), //numero da nota
                'UF_CRM_1681310372' => $sale->getAparelho()->getValidado(), //aparelho validado
                'UF_CRM_1681994168' => $sale->getAparelho()->getUsado(), //aparelho usado
                'UF_CRM_1681994310611' => $sale->getAparelho()->getId(), // id aparelho pitzi
                'UF_CRM_1624287865' => empty($dataCompra = DateTime::createFromFormat('dmY', $sale->getAparelho()->getDataCompra())) ? '' : $dataCompra->format('Y-m-d'), //data da nota fiscal

                //endereco
                'UF_CRM_1593723236' => $sale->getConsumidor()->getEndereco()->getLogradouro(),
                'UF_CRM_1593723261' => $sale->getConsumidor()->getEndereco()->getBairro(),
                'UF_CRM_1593723289' => $sale->getConsumidor()->getEndereco()->getCidade(),
                'UF_CRM_1593723318' => $sale->getConsumidor()->getEndereco()->getUf(),
                'UF_CRM_1593723412' => $sale->getConsumidor()->getEndereco()->getCep(),
                'UF_CRM_1593723432' => $sale->getConsumidor()->getEndereco()->getNumero(),
                'UF_CRM_1593723455' => $sale->getConsumidor()->getEndereco()->getComplemento(),
                'UF_CRM_1682002326195' => $sale->getConsumidor()->getEndereco()->getReferencia(),
            ],
            'params' => array('REGISTER_SONET_EVENT' => 'Y'),
        ];

        // set substatus if exists lead
        if (!empty($sale->getLead())) {
            $data['fields']['STATUS_ID'] = $this->opportunityStatus['lead'][$sale->getLead()->getStatus()];

            if ($sale->getLead()->getStatus() === OpportunityDataEnum::CANCELADO) {
                $data['fields']['UF_CRM_1633459319'] = $this->opportunityLostSubStatus['lead'][$sale->getLead()->getSubStatus()];
            } else {
                $data['fields']['UF_CRM_1628535982'] = $this->opportunitySubStatus['lead'][$sale->getLead()->getSubStatus()];
            }
        }

        return $data;
    }

    protected function getTitle(Sale $sale, $partial = false): string
    {
        // carrinho abandonado
        if ($partial) {
            return 'ABANDONO CARRINHO' . ' - ' . $sale->getConsumidor()->getEmail();
        }

        // mensagem padrão
        return $sale->getConsumidor()->getNome() . ' - ' . $sale->getSeguro()->getNome();
    }

    protected function requestCRM($uri, $data, $method = 'POST')
    {

        try {
            Log::debug('Bitrix request method: ' . $uri);

            $options = $this->crmServiceConfig['options'];
            $options['body'] = http_build_query($data);

            $response = $this->client->request($method, $uri, $options);

        } catch (Exception $e) {
            Log::error('[' . $uri . '] Request Error data: ' . json_encode($data, JSON_PRETTY_PRINT) . "\n" . 'Response Error message: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');
            throw $e;
        }

        return json_decode((String) $response->getBody())->result;
    }
}
