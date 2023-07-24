<?php

namespace App\Bussiness\Models;

use App\Bussiness\Repository\ProductRepository;

class Product extends ProductRepository
{
    /** @attributes
     * id
     * $idPitzi
     * $nome
     * $preco
     */

    protected $attributes = [
        'idPitzi' => '',
        'nome' => '',
        'preco' => '',
    ];

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function save(array $options = [])
    {
        parent::save();
    }

    public function populate(array $data): self
    {
        $this->idPitzi = $data['id'] ?? '';
        $this->nome = $data['name'] ?? '';
        $this->preco = $data['price'] ?? '';

        return $this;
    }

    public function getIdPitzi(): ?string
    {
        return $this->idPitzi;
    }

    public function setIdPitzi($idPitzi): self
    {
        $this->idPitzi = $idPitzi;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome($nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getPreco(): ?string
    {
        return $this->preco;
    }

    public function setPreco($preco): self
    {
        $this->preco = $preco;

        return $this;
    }
}
