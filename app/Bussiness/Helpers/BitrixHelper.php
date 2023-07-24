<?php

namespace App\Bussiness\Helpers;

use App\Bussiness\Enums\BitrixMap;
use App\Bussiness\Enums\OpportunityDataEnum;
use App\Bussiness\Models\Lead;
use App\Bussiness\Models\Opportunity;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;

trait BitrixHelper
{
    use BitrixMap;

    public function parseCodeNameDeal()
    {
        $birthday = explode('t', strtolower($this->getConsumidor()->getNascimento()));
        // $invoiceDateDevice = explode('+', mb_strtolower($this->getAparelho()->getDataCompra()));

        $this->getConsumidor()->setNascimento(empty($birthday = DateTime::createFromFormat('Y-m-d', $birthday[0])) ? '' : $birthday->format('dmY'));

        $this->setCanal($this->getValueFromList($this->getCanal(), $this->sourceNameCode, 'deal'));

        // $this->getAparelho()->setUsado($this->getAparelho()->getUsado() ? true : false)
        //     ->setValidado($this->getAparelho()->getValidado() ? true : false)
        //     ->setDataCompra(!empty($invoiceDateDevice[0]) ? new UTCDateTime(DateTime::createFromFormat('Y-m-dtH:i:s', $invoiceDateDevice[0])->sub(new DateInterval('PT3H'))) : '');

        // $this->getSeguro()->setFormaPagamento($this->getValueFromList($this->getSeguro()->getFormaPagamento(), $this->paymentForm, 'deal'));

    }

    public function parseCodeNameLead()
    {
        $birthday = explode('t', strtolower($this->getConsumidor()->getNascimento()));
        // $invoiceDateDevice = explode('+', mb_strtolower($this->getAparelho()->getDataCompra()));

        $this->getConsumidor()->setNascimento(empty($birthday = DateTime::createFromFormat('Y-m-d', $birthday[0])) ? '' : $birthday->format('dmY'));

        $this->setCanal($this->getValueFromList($this->getCanal(), $this->sourceNameCode, 'lead'));

        // $this->getAparelho()->setUsado($this->getAparelho()->getUsado() ? true : false)
        //     ->setValidado($this->getAparelho()->getValidado() ? true : false)
        //     ->setDataCompra(!empty($invoiceDateDevice[0]) ? new UTCDateTime(DateTime::createFromFormat('Y-m-dtH:i:s', $invoiceDateDevice[0])->sub(new DateInterval('PT3H'))) : '');

    }

    public function parseOpportunity(Opportunity $opportunity)
    {
        $beginDate = explode('+', mb_strtolower($this->getOportunidade()->getDataInicio()));
        $saleDate = explode('+', mb_strtolower($this->getOportunidade()->getDataVenda()));
        // $saleSignature = explode('+', mb_strtolower($this->getOportunidade()->getDataAssinaturaPitzi()));
        $closeDate = explode('+', mb_strtolower($this->getOportunidade()->getDataFechamento()));

        // Campo data/hora - subtrair -6h
        // campo data - subtrair -3h
        $this->getOportunidade()
            ->setDataInicio(!empty($beginDate[0]) ? new UTCDateTime(DateTime::createFromFormat('Y-m-dtH:i:s', $beginDate[0])->sub(new DateInterval('PT3H'))) : '')
        // ->setDataAssinaturaPitzi(!empty($saleSignature[0]) ? new UTCDateTime(DateTime::createFromFormat('Y-m-dtH:i:s', $saleSignature[0])->sub(new DateInterval('PT3H'))) : '')
            ->setDataVenda(!empty($saleDate[0]) ? new UTCDateTime(DateTime::createFromFormat('Y-m-dtH:i:s', $saleDate[0])) : '')
            ->setStatus($this->getValueFromList($opportunity->getStatus(), $this->opportunityStatus, 'deal'))
            ->setSubStatus($this->getValueFromList($opportunity->getSubStatus(), ($this->getOportunidade()->getStatus() === OpportunityDataEnum::PERDIDA) ? $this->opportunityLostSubStatus : $this->opportunitySubStatus, 'deal'))
            ->setDataFechamento(!empty($closeDate[0]) ? new UTCDateTime(DateTime::createFromFormat('Y-m-dtH:i:s', $closeDate[0])->sub(new DateInterval('PT3H'))) : '');

    }

    public function parseLead(Lead $lead)
    {
        $beginDate = explode('+', mb_strtolower($lead->getDataInicio()));
        $closeDate = explode('+', mb_strtolower($lead->getDataFechamento()));

        // Campo data/hora - subtrair -6h
        // campo data - subtrair -3h
        $this->getLead()
            ->setDataInicio(!empty($beginDate[0]) ? new UTCDateTime(DateTime::createFromFormat('Y-m-dtH:i:s', $beginDate[0])->sub(new DateInterval('PT6H'))) : '')
            ->setDataFechamento(!empty($closeDate[0]) ? new UTCDateTime(DateTime::createFromFormat('Y-m-dtH:i:s', $closeDate[0])->sub(new DateInterval('PT6H'))) : '')
            ->setStatus($this->getValueFromList($lead->getStatus(), $this->opportunityStatus, 'lead'))
            ->setSubStatus($this->getValueFromList($lead->getSubStatus(), ($lead->getStatus() === OpportunityDataEnum::CANCELADO) ? $this->opportunityLostSubStatus : $this->opportunitySubStatus, 'lead'));
    }

    public function getValueFromList($id, $list, $type)
    {
        try {
            return array_search($id, $list[$type]);
        } catch (Exception $e) {
            Log::error('not found is list (  ' . json_encode($list) . '  ' . $id . '): ' . $e->getMessage());
            return '';
        }
    }
}
