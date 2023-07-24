<?php

namespace App\Bussiness\Models;

class Location extends RelationshipModel
{
    /** @attributes
     * $ip
     */

    protected $attributes = [
        'ip' => '',
    ];

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function populate(array $data): self
    {
        $this->ip = $data['ip'] ?? '';

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp($ip): self
    {
        $this->ip = $ip;

        return $this;
    }
}
