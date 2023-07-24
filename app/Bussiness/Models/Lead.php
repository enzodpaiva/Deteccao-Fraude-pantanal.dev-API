<?php

namespace App\Bussiness\Models;

class Lead extends RelationshipModel
{
    /**
     * $id
     * $status
     * $subStatus
     * $dataInicio
     * $dataFechamento
     */

    protected $attributes = [
        'id' => null,
        'status' => '',
        'subStatus' => '',
        'dataInicio' => '',
        'dataFechamento' => '',
    ];

    public function save(array $options = [])
    {
        parent::save();
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
        $this->dataFechamento = $data['dataFechamento'] ?? '';

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = (Integer) $id;

        return $this;
    }

    public function getStatus(): ?String
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSubStatus(): ?String
    {
        return $this->subStatus;
    }

    public function setSubStatus($subStatus): self
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

    public function getDataFechamento()
    {
        return $this->dataFechamento;
    }

    public function setDataFechamento($dataFechamento): self
    {
        $this->dataFechamento = $dataFechamento;

        return $this;
    }

    public function getVendedor(): ?Seller
    {
        return $this->vendedor;
    }

    public function setVendedor(Seller $vendedor): self
    {
        unset($this->vendedor);
        $this->vendedor = $vendedor->toArray();

        return $this;
    }
}
