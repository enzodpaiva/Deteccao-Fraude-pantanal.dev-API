<?php

namespace App\Bussiness\Models;

use App\Bussiness\Repository\SaleRepository;
use App\Exceptions\ApiLeadException;
use Illuminate\Support\Facades\Log;

class Sale extends SaleRepository
{
    /** @attributes
     * $id
     * canal
     * $aparelho
     * $seguro
     * $consumidor
     * $verificacaoEmail
     * $oportunidade
     * $lead
     */

    public static function factory(): self
    {
        return app()->make(self::class);
    }

    public function save(array $options = [])
    {
        parent::save();
        ($this->seguro) ? parent::seguro()->save($this->seguro) : null;
        ($this->consumidor) ? parent::consumidor()->save($this->consumidor) : null;
        ($this->aparelho) ? parent::aparelho()->save($this->aparelho) : null;
        ($this->verificacaoEmail) ? parent::verificacaoEmail()->save($this->verificacaoEmail) : null;
        ($this->oportunidade) ? parent::oportunidade()->save($this->oportunidade) : null;
        ($this->lead) ? parent::lead()->save($this->lead) : null;

    }

    public function populate(array $data): self
    {
        $this->canal = $data['canal'] ?? '';

        $this->consumidor = Customer::factory()->populate($data['consumidor'] ?? [])->toArray();
        $this->seguro = Insurance::factory()->populate($data['seguro'] ?? [])->toArray();
        $this->aparelho = Device::factory()->populate($data['aparelho'] ?? [])->toArray();

        (!empty($data['_id'])) ? $this->_id = $data['_id'] : null;

        (!empty($data['oportunidade'])) ? $this->oportunidade = Opportunity::factory()->populate($data['oportunidade'])->toArray() : null;
        (!empty($data['lead'])) ? $this->lead = Lead::factory()->populate($data['lead'])->toArray() : null;
        (!empty($data['verificacaoEmail'])) ? $this->verificacaoEmail = EmailCheck::factory()->populate($data['verificacaoEmail'])->toArray() : null;

        return $this;
    }

    public function filled()
    {
        $discardSeguro = [
            '',
        ];

        $discardAparelho = [
            '',
        ];

        $discardCustomer = [
            'complemento',
            'contato',
            'filiacao',
            'referencia',
        ];

        $this->checkFieldFilled($this->getSeguro()->toArray(), 'seguro', $discardSeguro);
        $this->checkFieldFilled($this->getAparelho()->toArray(), 'aparelho', $discardAparelho);
        $this->checkFieldFilled($this->getConsumidor()->toArray(), 'consumidor', $discardCustomer);
    }

    protected function checkFieldFilled($fieldList, $objectName, $discard = array())
    {
        foreach ($fieldList as $key => $value) {

            // If value is array
            if (is_array($value) && !in_array($key, $discard)) {
                $this->checkFieldFilled($value, $objectName . '.' . $key, $discard);
            }

            // Value is requerid
            if (((empty($value) and $value != 0) || !filled($value)) && !in_array($key, $discard)) {
                Log::notice('[' . $this->getId() . '] Not filled ' . $objectName . ' object key ( ' . $key . ' ) --  Origin  ' . $this->getCanal() . ' --');
                throw new ApiLeadException(null, 400, [
                    'campoInvalido' => $objectName . '.' . $key,
                ]);
            }
        }
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

    public function getCanal():  ? String
    {
        return $this->canal;
    }

    public function setCanal(String $canal) : self
    {
        $this->canal = $canal;

        return $this;
    }

    public function getSeguro():  ? Insurance
    {
        return $this->seguro;
    }

    public function setSeguro(Insurance $seguro) : self
    {
        unset($this->seguro);
        $this->seguro = $seguro->toArray();

        return $this;
    }

    public function getAparelho():  ? Device
    {
        return $this->aparelho;
    }

    public function setAparelho(Device $aparelho) : self
    {
        unset($this->aparelho);
        $this->aparelho = $aparelho->toArray();

        return $this;
    }

    public function getConsumidor():  ? Customer
    {
        return $this->consumidor;
    }

    public function setConsumidor(Customer $consumidor) : self
    {
        unset($this->consumidor);
        $this->consumidor = $consumidor->toArray();

        return $this;
    }

    public function getOportunidade():  ? Opportunity
    {
        return $this->oportunidade;
    }

    public function setOportunidade(Opportunity $oportunidade) : self
    {
        unset($this->oportunidade);
        $this->oportunidade = $oportunidade->toArray();

        return $this;
    }

    public function getLead():  ? Lead
    {
        return $this->lead;
    }

    public function setLead(Lead $lead) : self
    {
        unset($this->lead);
        $this->lead = $lead->toArray();

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getVerificacaoEmail():  ? EmailCheck
    {
        return $this->verificacaoEmail;
    }

    public function setVerificacaoEmail(EmailCheck $verificacaoEmail)
    {
        unset($this->verificacaoEmail);
        $this->verificacaoEmail = $verificacaoEmail->toArray();

        return $this;
    }
}
