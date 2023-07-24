<?php

namespace App\Bussiness\Models;

class Insurance extends RelationshipModel
{
    /** @attributes
     * $planoId
     * $nome
     * $precoTotal
     * $precoParcelado
     * $formaPagamento
     * $numeroParcelas
     *
     */

    protected $attributes = [
        'planoId' => null,
        'nome' => null,
        'precoTotal' => null,
        'precoParcelado' => null,
        'formaPagamento' => null,
        'numeroParcelas' => null,
    ];

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function populate(array $data): self
    {
        $this->planoId = $data['planoId'] ?? '';
        $this->nome = $data['nome'] ?? '';
        $this->precoTotal = $data['precoTotal'] ?? '';
        $this->precoParcelado = $data['precoParcelado'] ?? '';
        $this->formaPagamento = $data['formaPagamento'] ?? '';
        $this->numeroParcelas = $data['numeroParcelas'] ?? '';

        return $this;
    }

    public function getPlanoId():  ? string
    {
        return $this->planoId;
    }

    public function setPlanoId($planoId) : self
    {
        $this->planoId = $planoId;

        return $this;
    }

    public function getNome():  ? string
    {
        return $this->nome;
    }

    public function setNome($nome) : self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getPrecoTotal()
    {
        return $this->precoTotal;
    }

    public function setPrecoTotal($precoTotal): self
    {
        $this->precoTotal = $precoTotal;

        return $this;
    }

    public function getPrecoParcelado()
    {
        return $this->precoParcelado;
    }

    public function setPrecoParcelado($precoParcelado): self
    {
        $this->precoParcelado = $precoParcelado;

        return $this;
    }

    public function getFormaPagamento():  ? string
    {
        return $this->formaPagamento;
    }

    public function setFormaPagamento($formaPagamento) : self
    {
        $this->formaPagamento = $formaPagamento;

        return $this;
    }

    public function getNumeroParcelas():  ? string
    {
        return $this->numeroParcelas;
    }

    public function setNumeroParcelas($numeroParcelas) : self
    {
        $this->numeroParcelas = $numeroParcelas;

        return $this;
    }
}
