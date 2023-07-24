<?php

namespace App\Bussiness\Models;

class Device extends RelationshipModel
{
    /** @attributes
     * $modelo
     * $imei
     * $dataCompra
     * $preco
     * $id
     * $usado
     */

    protected $attributes = [
        'modelo' => '',
        'imei' => '',
        'notaFiscal' => '',
        'dataCompra' => '',
        'preco' => '',
        'id' => '',
        'usado' => false,
        'validado' => false,
    ];

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function populate(array $data): self
    {
        $this->modelo = $data['modelo'] ?? '';
        $this->imei = $data['imei'] ?? '';
        $this->notaFiscal = $data['notaFiscal'] ?? '';
        $this->dataCompra = $data['dataCompra'] ?? '';
        $this->preco = $data['preco'] ?? '';
        $this->id = $data['id'] ?? '';
        $this->usado = $data['usado'] ?? '';
        $this->validado = $data['validado'] ?? '';

        return $this;
    }

    public function getModelo():  ? string
    {
        return $this->modelo;
    }

    public function setModelo($modelo) : self
    {
        $this->modelo = $modelo;

        return $this;
    }

    public function getImei():  ? string
    {
        return $this->imei;
    }

    public function setImei($imei) : self
    {
        $this->imei = $imei;

        return $this;
    }

    public function getNotaFiscal():  ? string
    {
        return $this->notaFiscal;
    }

    public function setNotaFiscal($notaFiscal) : self
    {
        $this->notaFiscal = $notaFiscal;

        return $this;
    }

    public function getDataCompra():  ? string
    {
        return $this->dataCompra;
    }

    public function setDataCompra($dataCompra) : self
    {
        $this->dataCompra = $dataCompra;

        return $this;
    }

    public function getPreco():  ? string
    {
        return $this->preco;
    }

    public function setPreco($preco) : self
    {
        $this->preco = $preco;

        return $this;
    }

    public function getId():  ? string
    {
        return $this->id;
    }

    public function setId($id) : self
    {
        $this->id = $id;

        return $this;
    }

    public function getUsado():  ? string
    {
        return $this->usado;
    }

    public function setUsado($usado) : self
    {
        $this->usado = $usado;

        return $this;
    }

    public function getValidado():  ? string
    {
        return $this->validado;
    }

    public function setValidado($validado) : self
    {
        $this->validado = $validado;

        return $this;
    }
}
