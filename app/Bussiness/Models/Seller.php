<?php
namespace App\Bussiness\Models;

class Seller extends RelationshipModel
{
    /** @attributes
     * $id
     * $nome
     */

    protected $attributes = [
        'id' => '',
        'nome' => '',
    ];

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function populate(array $data): self
    {
        $this->id = $data['id'] ?? '';
        $this->nome = $data['nome'] ?? '';

        return $this;
    }

    public function getNome(): ?String
    {
        return $this->nome;
    }

    public function setNome($nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getId(): ?String
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }
}
