<?php

namespace App\Bussiness\Models;

class Opportunity extends RelationshipModel
{
    /**
     * $id
     * $status
     * $subStatus
     * $dataInicio
     * $orderIdPitzi
     * $dataAssinaturaPitzi
     * $urlPagamentoPitzi
     * $motivoFalhaPitzi
     * $dataVenda
     * $dataFechamento
     * $vendedor
     */

    protected $attributes = [
        'id' => null,
        'status' => '',
        'subStatus' => '',
        'dataInicio' => '',
        'orderIdPitzi' => '',
        'dataAssinaturaPitzi' => '',
        'urlPagamentoPitzi' => '',
        'motivoFalhaPitzi' => '',
        'dataVenda' => '',
        'dataFechamento' => '',
        'vendedor' => '',
    ];

    public function save(array $options = [])
    {
        parent::save();
        ($this->vendedor) ? $this->vendedor()->save($this->vendedor) : null;
    }

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function populate(array $data): self
    {
        $this->id = (!empty($data['id'])) ? (Integer) $data['id'] : null;
        $this->status = $data['status'] ?? '';
        $this->subStatus = $data['subStatus'] ?? '';
        $this->dataInicio = $data['dataInicio'] ?? '';
        $this->orderIdPitzi = $data['orderIdPitzi'] ?? '';
        $this->dataAssinaturaPitzi = $data['dataAssinaturaPitzi'] ?? '';
        $this->urlPagamentoPitzi = $data['urlPagamentoPitzi'] ?? '';
        $this->motivoFalhaPitzi = $data['motivoFalhaPitzi'] ?? '';
        $this->dataVenda = $data['dataVenda'] ?? '';
        $this->dataFechamento = $data['dataFechamento'] ?? '';

        $this->vendedor = Seller::factory()->populate($data['vendedor'] ?? [])->toArray();

        return $this;
    }

    public function vendedor()
    {
        return $this->embedsOne(Seller::class);
    }

    public function getVendedor():  ? Seller
    {
        return $this->vendedor;
    }

    public function getId() :  ? int
    {
        return $this->id;
    }

    public function setId($id) : self
    {
        $this->id = (Integer) $id;

        return $this;
    }

    public function getStatus():  ? String
    {
        return $this->status;
    }

    public function setStatus($status) : self
    {
        $this->status = $status;

        return $this;
    }

    public function getSubStatus():  ? String
    {
        return $this->subStatus;
    }

    public function setSubStatus($subStatus) : self
    {
        $this->subStatus = $subStatus;

        return $this;
    }

    public function getDataInicio()
    {
        return $this->dataInicio;
    }

    public function setDataInicio($dataInicio): self
    {
        $this->dataInicio = $dataInicio;

        return $this;
    }

    public function getDataAssinaturaPitzi()
    {
        return $this->dataAssinaturaPitzi;
    }

    public function setDataAssinaturaPitzi($dataAssinaturaPitzi): self
    {
        $this->dataAssinaturaPitzi = $dataAssinaturaPitzi;

        return $this;
    }

    public function getUrlPagamentoPitzi()
    {
        return $this->urlPagamentoPitzi;
    }

    public function setUrlPagamentoPitzi($urlPagamentoPitzi): self
    {
        $this->urlPagamentoPitzi = $urlPagamentoPitzi;

        return $this;
    }

    public function getMotivoFalhaPitzi()
    {
        return $this->motivoFalhaPitzi;
    }

    public function setMotivoFalhaPitzi($motivoFalhaPitzi): self
    {
        $this->motivoFalhaPitzi = $motivoFalhaPitzi;

        return $this;
    }

    public function getDataVenda()
    {
        return $this->dataVenda;
    }

    public function setDataVenda($dataVenda): self
    {
        $this->dataVenda = $dataVenda;

        return $this;
    }

    public function getOrderIdPitzi()
    {
        return $this->orderIdPitzi;
    }

    public function setOrderIdPitzi($orderIdPitzi): self
    {
        $this->orderIdPitzi = $orderIdPitzi;

        return $this;
    }

    public function getDataFechamento()
    {
        return $this->dataFechamento;
    }

    public function setDataFechamento($dataFechamento): self
    {
        $this->dataFechamento = $dataFechamento;

        return $this;
    }
}
