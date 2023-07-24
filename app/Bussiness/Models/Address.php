<?php

namespace App\Bussiness\Models;

class Address extends RelationshipModel
{
    /** @attributes
     * $cep
     * $logradouro
     * $bairro
     * $cidade
     * $uf
     * $numero
     * $complemento
     * $referencia
     */

    protected $attributes = [
        'cep' => '',
        'logradouro' => '',
        'bairro' => '',
        'cidade' => '',
        'uf' => '',
        'numero' => '',
        'complemento' => '',
        'referencia' => '',
    ];

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function populate(array $data): self
    {
        $this->cep = $data['cep'] ?? '';
        $this->logradouro = $data['logradouro'] ?? '';
        $this->bairro = $data['bairro'] ?? '';
        $this->cidade = $data['cidade'] ?? '';
        $this->uf = $data['uf'] ?? '';
        $this->numero = $data['numero'] ?? '';
        $this->complemento = $data['complemento'] ?? '';
        $this->referencia = $data['referencia'] ?? '';

        return $this;
    }

    public function getCep(): ?string
    {
        return $this->cep;
    }

    public function setCep($cep): self
    {
        $this->cep = $cep;

        return $this;
    }

    public function getLogradouro(): ?string
    {
        return $this->logradouro;
    }

    public function setLogradouro($logradouro): self
    {
        $this->logradouro = $logradouro;

        return $this;
    }

    public function getBairro(): ?string
    {
        return $this->bairro;
    }

    public function setBairro($bairro): self
    {
        $this->bairro = $bairro;

        return $this;
    }

    public function getCidade(): ?string
    {
        return $this->cidade;
    }

    public function setCidade($cidade): self
    {
        $this->cidade = $cidade;

        return $this;
    }

    public function getUf(): ?string
    {
        return $this->uf;
    }

    public function setUf($uf): self
    {
        $this->uf = $uf;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero($numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getComplemento(): ?string
    {
        return $this->complemento;
    }

    public function setComplemento($complemento): self
    {
        $this->complemento = $complemento;

        return $this;
    }

    public function getReferencia(): ?string
    {
        return $this->referencia;
    }

    public function setReferencia($referencia): self
    {
        $this->referencia = $referencia;

        return $this;
    }
}
