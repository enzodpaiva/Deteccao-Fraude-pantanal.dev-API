<?php

namespace App\Bussiness\Models;

use App\Bussiness\Enums\OpportunityDataEnum;
use App\Bussiness\Helpers\BitrixHelper;
use App\Bussiness\Models\Sale;

class SaleLeadBitrix extends Sale
{
    use BitrixHelper;

    public static function factory(): SaleLeadBitrix
    {
        return app()->make(SaleLeadBitrix::class);
    }

    private function populateObject()
    {
        $this->setSeguro(Insurance::factory()->populate([]));
        $this->setConsumidor(Customer::factory()->populate([]));
        $this->setAparelho(Device::factory()->populate([]));
        $this->setLead(Lead::factory()->populate([]));
    }

    public function populate(array $data): Sale
    {
        // start new object
        if (empty(array_filter($data['leadRepository']->toArray()))) {
            $this->populateObject();
        }

        $data['lead']->ID = strtoupper($data['lead']->ID);
        $this->setCanal($data['lead']->UF_CRM_1667915980);

        // consumidor
        $this->getConsumidor()->setNome($data['lead']->UF_CRM_1624288150);
        $this->getConsumidor()->setCpf($data['lead']->UF_CRM_1593723209);
        $this->getConsumidor()->setEmail($data['lead']->UF_CRM_1595527731);
        $this->getConsumidor()->setFiliacao($data['lead']->UF_CRM_1681845085915);
        $this->getConsumidor()->setTelefoneServico($data['lead']->UF_CRM_1626124868);
        $this->getConsumidor()->setTelefoneContato($data['lead']->UF_CRM_1682002482760);
        $this->getConsumidor()->setNascimento($data['lead']->UF_CRM_1624288169);

        // endereco
        $this->getConsumidor()->getEndereco()->setCep($data['lead']->UF_CRM_1593723412);
        $this->getConsumidor()->getEndereco()->setLogradouro($data['lead']->UF_CRM_1593723236);
        $this->getConsumidor()->getEndereco()->setNumero($data['lead']->UF_CRM_1593723432);
        $this->getConsumidor()->getEndereco()->setBairro($data['lead']->UF_CRM_1593723261);
        $this->getConsumidor()->getEndereco()->setCidade($data['lead']->UF_CRM_1593723289);
        $this->getConsumidor()->getEndereco()->setUf($data['lead']->UF_CRM_1593723318);
        $this->getConsumidor()->getEndereco()->setComplemento($data['lead']->UF_CRM_1593723455);
        $this->getConsumidor()->getEndereco()->setReferencia($data['lead']->UF_CRM_1682002326195);

        // aparelho
        // $this->getAparelho()->setModelo($data['lead']->UF_CRM_1624287990);
        // $this->getAparelho()->setImei($data['lead']->UF_CRM_1624287964);
        // $this->getAparelho()->setDataCompra($data['lead']->UF_CRM_1624287865);
        // $this->getAparelho()->setPreco($data['lead']->UF_CRM_1624287939);
        // $this->getAparelho()->setId($data['lead']->UF_CRM_1681994310611);
        // $this->getAparelho()->setUsado($data['lead']->UF_CRM_1681994168);
        // $this->getAparelho()->setValidado($data['lead']->UF_CRM_1681310372);

        // plano
        // $this->getSeguro()->setNome($data['lead']->UF_CRM_1624369553);
        // $this->getSeguro()->setPrecoTotal($data['lead']->UF_CRM_1595017429);
        // $this->getSeguro()->setNumeroParcelas($data['lead']->UF_CRM_1680814449);
        // $this->getSeguro()->setPrecoParcelado($data['lead']->UF_CRM_1680814471);
        // $this->getSeguro()->setFormaPagamento($data['lead']->UF_CRM_1681995026);

        // lead
        $this->getLead()->setId($data['lead']->ID);
        $this->getLead()->setStatus($data['lead']->STATUS_ID);
        $this->getLead()->setSubStatus(($this->getLead()->getStatus() == $this->opportunityStatus["lead"][OpportunityDataEnum::CANCELADO]) ? $data["lead"]->UF_CRM_1633459319 : $data["lead"]->UF_CRM_1628535982);
        $this->getLead()->setDataInicio($data['lead']->DATE_CREATE);
        $this->getLead()->setDataFechamento($data['lead']->DATE_CLOSED);

        if (!empty($data['seller'])) {
            $vendedor = $data['seller'];
            $this->getLead()->getVendedor()->setId($vendedor['id']);
            $this->getLead()->getVendedor()->setNome($vendedor['nome']);
        }

        $this->parseCodeNameLead();
        $this->parseLead($this->getLead());

        return $this;
    }
}
