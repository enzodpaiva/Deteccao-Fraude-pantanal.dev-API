<?php

namespace App\Bussiness\Models;

use App\Bussiness\Enums\OpportunityDataEnum;
use App\Bussiness\Helpers\BitrixHelper;
use App\Bussiness\Models\Sale;

class SaleDealBitrix extends Sale
{
    use BitrixHelper;

    public static function factory(): SaleDealBitrix
    {
        return app()->make(SaleDealBitrix::class);
    }

    private function populateObject()
    {
        $this->setSeguro(Insurance::factory()->populate([]));
        $this->setConsumidor(Customer::factory()->populate([]));
        $this->setAparelho(Device::factory()->populate([]));
        $this->setOportunidade(Opportunity::factory()->populate([]));
    }

    public function populate(array $data): Sale
    {
        // start new object
        if (empty(array_filter($data['dealRepository']->toArray()))) {
            $this->populateObject();
        }

        $data['deal']->STAGE_ID = strtoupper($data['deal']->STAGE_ID);
        $this->setCanal($data['deal']->UF_CRM_63876E40E7945);

        //aparelho - campos comentados para não serem modificados
        // $this->getAparelho()->setModelo($data['deal']->UF_CRM_1626125006);
        // $this->getAparelho()->setImei($data['deal']->UF_CRM_1626124941);
        // $this->getAparelho()->setNotaFiscal($data['deal']->UF_CRM_1626125073);
        // $this->getAparelho()->setDataCompra($data['deal']->UF_CRM_1626125051);
        // $this->getAparelho()->setPreco($data['deal']->UF_CRM_1626125031);
        // $this->getAparelho()->setId($data['deal']->UF_CRM_1681756511);
        // $this->getAparelho()->setUsado($data['deal']->UF_CRM_1681756457);
        // $this->getAparelho()->setValidado($data['deal']->UF_CRM_1681308422);

        //seguro - campos comentados para não serem modificados
        // $this->getSeguro()->setNome($data['deal']->UF_CRM_1626125137);
        // $this->getSeguro()->setPrecoTotal($data['deal']->UF_CRM_5F1743E756F2B);
        // $this->getSeguro()->setPrecoParcelado($data['deal']->UF_CRM_1680814267);
        // $this->getSeguro()->setFormaPagamento($data['deal']->UF_CRM_1681926312);
        // $this->getSeguro()->setNumeroParcelas($data['deal']->UF_CRM_1680814249);

        //consumidor
        $this->getConsumidor()->setNome($data['deal']->UF_CRM_1595880784);
        $this->getConsumidor()->setTelefoneServico($data['deal']->UF_CRM_1626124892);
        $this->getConsumidor()->setTelefoneContato($data['deal']->UF_CRM_1682002763625);
        $this->getConsumidor()->setEmail($data['deal']->UF_CRM_1595527789);
        $this->getConsumidor()->setCpf($data['deal']->UF_CRM_5F1743E4E6988);
        $this->getConsumidor()->setNascimento($data['deal']->UF_CRM_1595880942);
        $this->getConsumidor()->setFiliacao($data['deal']->UF_CRM_1681825642263);

        //endereco consumidor
        $this->getConsumidor()->getEndereco()->setLogradouro($data['deal']->UF_CRM_5F1743E503203);
        $this->getConsumidor()->getEndereco()->setNumero($data['deal']->UF_CRM_5F1743E5347BC);
        $this->getConsumidor()->getEndereco()->setComplemento($data['deal']->UF_CRM_5F1743E53E09D);
        $this->getConsumidor()->getEndereco()->setBairro($data['deal']->UF_CRM_5F1743E50AE5A);
        $this->getConsumidor()->getEndereco()->setCidade($data['deal']->UF_CRM_5F1743E5106EA);
        $this->getConsumidor()->getEndereco()->setUf($data['deal']->UF_CRM_5F1743E5166AB);
        $this->getConsumidor()->getEndereco()->setCep($data['deal']->UF_CRM_5F1743E52C81C);
        $this->getConsumidor()->getEndereco()->setReferencia($data['deal']->UF_CRM_1681908577405);

        //oportunidade - campos comentados para não serem modificados
        $this->getOportunidade()->setId($data['deal']->ID);
        $this->getOportunidade()->setStatus($data['deal']->STAGE_ID);
        $this->getOportunidade()->setSubStatus(($this->getOportunidade()->getStatus() == $this->opportunityStatus["deal"][OpportunityDataEnum::PERDIDA]) ? $data["deal"]->UF_CRM_63876E409D4DF : $data["deal"]->UF_CRM_1629487085);
        $this->getOportunidade()->setDataInicio($data['deal']->BEGINDATE);
        // $this->getOportunidade()->setDataAssinaturaPitzi($data['deal']->UF_CRM_1681835352778);
        $this->getOportunidade()->setDataVenda($data['deal']->UF_CRM_1681308312);
        // $this->getOportunidade()->setOrderIdPitzi($data['deal']->UF_CRM_1681839374808);
        $this->getOportunidade()->setDataFechamento($data['deal']->CLOSEDATE);
        // $this->getOportunidade()->setUrlPagamentoPitzi($data['deal']->UF_CRM_1683829177102);
        // $this->getOportunidade()->setMotivoFalhaPitzi($data['deal']->UF_CRM_1683829222235);

        if (!empty($data['seller'])) {
            $vendedor = $data['seller'];
            $this->getOportunidade()->getVendedor()->setId($vendedor['id']);
            $this->getOportunidade()->getVendedor()->setNome($vendedor['nome']);
        }

        // parse names/list and return list
        $this->parseCodeNameDeal();
        $this->parseOpportunity($this->getOportunidade());

        return $this;
    }
}
