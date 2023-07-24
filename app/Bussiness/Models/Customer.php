<?php

namespace App\Bussiness\Models;

class Customer extends RelationshipModel
{
    /**
     * $nome
     * $cpf
     * $nascimento
     * $email
     * $endereco
     * $filiacao
     * $telefoneContato
     * $telefoneServico
     */

    protected $attributes = [
        'nome' => '',
        'cpf' => '',
        'nascimento' => '',
        'email' => '',
        'telefone' => [
            'contato' => '',
            'servico' => '',
        ],
        'endereco' => null,
        'filiacao' => '',
        'rastreamento' => null,
    ];

    public function save(array $options = [])
    {
        parent::save();
        ($this->endereco) ? $this->endereco()->save($this->endereco) : null;
        ($this->rastreamento) ? $this->rastreamento()->save($this->rastreamento) : null;
    }

    public function endereco()
    {
        return $this->embedsOne(Address::class);
    }

    public function rastreamento()
    {
        return $this->embedsOne(Location::class);
    }

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function populate(array $data): self
    {
        $this->nome = $data['nome'] ?? '';
        $this->cpf = $data['cpf'] ?? '';
        $this->nascimento = $data['nascimento'] ?? '';
        $this->email = $data['email'] ?? '';

        if (!empty($data['telefone'])) {
            $this->telefone = [
                'contato' => $data['telefone']['contato'] ?? '',
                'servico' => $data['telefone']['servico'] ?? '',
            ];
        }

        $this->endereco = Address::factory()->populate($data['endereco'] ?? [])->toArray();
        $this->filiacao = $data['filiacao'] ?? '';
        $this->rastreamento = Location::factory()->populate($data['rastreamento'] ?? [])->toArray();

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getNascimento(): ?string
    {
        return $this->nascimento;
    }

    public function setNascimento(string $nascimento): self
    {
        $this->nascimento = $nascimento;

        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf): self
    {
        $this->cpf = $cpf;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEndereco(): ?Address
    {
        return $this->endereco;
    }

    public function setEndereco(Address $endereco): self
    {
        unset($this->endereco);
        $this->endereco = $endereco->toArray();

        return $this;
    }

    public function getTelefoneServico(): ?string
    {
        return $this->telefone['servico'] ?? '';
    }

    public function setTelefoneServico(string $telefoneServico): self
    {
        $data = $this->telefone;
        $data['servico'] = $telefoneServico;

        $this->telefone = $data;
        return $this;
    }

    public function getTelefoneContato(): ?string
    {
        return $this->telefone['contato'] ?? '';
    }

    public function setTelefoneContato(string $telefoneContato): self
    {
        $data = $this->telefone;
        $data['contato'] = $telefoneContato;

        $this->telefone = $data;
        return $this;
    }

    public function getFiliacao(): ?string
    {
        return $this->filiacao;
    }

    public function setFiliacao(string $filiacao): self
    {
        $this->filiacao = $filiacao;
        return $this;
    }

    public function getRastreamento(): ?Location
    {
        return $this->rastreamento;
    }

    public function setRastreamento(Location $location): self
    {
        unset($this->rastreamento);
        $this->rastreamento = $location->toArray();

        return $this;
    }
}
