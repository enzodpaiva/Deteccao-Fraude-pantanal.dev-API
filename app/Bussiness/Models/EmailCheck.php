<?php

namespace App\Bussiness\Models;

class EmailCheck extends RelationshipModel
{
    /** @attributes
     * $enviado;
     * $confirmado;
     * $apiStatus;
     */

    protected $attributes = [
        'enviado' => false,
        'confirmado' => false,
        'api' => [
            'status' => true,
        ],
    ];

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function populate(array $data): self
    {
        $this->enviado = filter_var($data['enviado'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->confirmado = filter_var($data['confirmado'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $this->api = [
            'status' => filter_var($data['api']['status'] ?? true, FILTER_VALIDATE_BOOLEAN),
        ];

        return $this;
    }

    /**
     * Get the value of enviado
     */
    public function getEnviado()
    {
        return $this->enviado;
    }

    /**
     * Set the value of enviado
     *
     * @return  self
     */
    public function setEnviado($enviado)
    {
        $this->enviado = $enviado;

        return $this;
    }

    /**
     * Get the value of confirmado
     */
    public function getConfirmado()
    {
        return $this->confirmado;
    }

    /**
     * Set the value of confirmado
     *
     * @return  self
     */
    public function setConfirmado($confirmado)
    {
        $this->confirmado = $confirmado;

        return $this;
    }

    /**
     * Get the value of apiStatus
     */
    public function getApiStatus()
    {
        return $this->api['status'];
    }

    /**
     * Set the value of apiStatus
     *
     * @return  self
     */
    public function setApiStatus($apiStatus)
    {
        $data = $this->api;
        $data['status'] = $apiStatus;

        $this->api = $data;

        return $this;
    }
}
