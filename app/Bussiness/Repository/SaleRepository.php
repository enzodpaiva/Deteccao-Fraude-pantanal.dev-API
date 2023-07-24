<?php

namespace App\Bussiness\Repository;

use App\Bussiness\Enums\OpportunityDataEnum;
use App\Bussiness\Models\Analytics;
use App\Bussiness\Models\Customer;
use App\Bussiness\Models\Device;
use App\Bussiness\Models\EmailCheck;
use App\Bussiness\Models\Insurance;
use App\Bussiness\Models\Lead;
use App\Bussiness\Models\Opportunity;
use App\Bussiness\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

/**
 * @property int $_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */

abstract class SaleRepository extends Model
{
    use SoftDeletes;

    protected $collection = 'sales';
    protected $primaryKey = '_id';

    /** @attributes - relationship
     * $canal
     * $seguro
     * $aparelho
     * $consumidor;
     * $analytics
     * $oportunidade;
     * $lead;
     */

    protected $attributes = [
        'canal' => '',
        'seguro' => null,
        'consumidor' => null,
    ];

    public static function factory(): SaleRepository
    {
        return app(SaleRepository::class);
    }

    public function seguro()
    {
        return $this->embedsOne(Insurance::class);
    }

    public function consumidor()
    {
        return $this->embedsOne(Customer::class);
    }

    public function aparelho()
    {
        return $this->embedsOne(Device::class);
    }

    public function verificacaoEmail()
    {
        return $this->embedsOne(EmailCheck::class);
    }

    public function oportunidade()
    {
        return $this->embedsOne(Opportunity::class);
    }

    public function lead()
    {
        return $this->embedsOne(Lead::class);
    }

    public function getSale($id):  ? Sale
    {
        return self::where('_id', $id)->first();
    }

    public function deleteSale(String $saleId)
    {
        if (empty($saleId)) {
            Log::error('Sale id is empty to delete');
            return;
        }

        self::where('_id', $saleId)->delete();
    }

    public function getSaleByOpportunity($opportunityId) :  ? Sale
    {
        $sales = self::where('oportunidade.id', (Integer) $opportunityId)
            ->orderBy('created_at', 'ASC')
            ->get();

        if (count($sales) == 1) {
            return $sales[0];
        } elseif (count($sales) > 1) {
            Log::warning("More then one opportunity returned by id: " . $opportunityId);
            return $sales[0];
        }

        return null;
    }

    public function getSaleByLead($leadId) :  ? Sale
    {
        if (empty($leadId)) {return null;}

        $sales = self::where('lead.id', (Integer) $leadId)
            ->orderBy('created_at', 'ASC')
            ->get();

        if (count($sales) == 1) {
            return $sales[0];
        } elseif (count($sales) > 1) {
            Log::warning("More then one Leads returned by id: " . $leadId);
            return $sales[0];
        }

        return null;
    }

    public function listDuplicatedOpportunity(Sale $sale) : array
    {
        if (empty($sale->getConsumidor()->getCpf())) {
            return [];
        }

        return self::whereNotNull('oportunidade')
            ->where('_id', '<>', $sale->getId())
            ->where('canal', $sale->getCanal())
            ->where('consumidor.cpf', $sale->getConsumidor()->getCpf())
            ->where('seguro.planoId', $sale->getSeguro()->getPlanoId())
            ->where('aparelho.imei', $sale->getAparelho()->getImei())
            ->where('aparelho.id', $sale->getAparelho()->getId())
            ->where('oportunidade.status', '!=', OpportunityDataEnum::ANALISE_FALHA)
            ->whereBetween('created_at', [Carbon::now()->subHours(24), Carbon::now()])
            ->get()->toArray();
    }

    public function listDuplicatedLead(Sale $sale)
    {
        if (empty($sale->getConsumidor()->getCpf())) {
            return [];
        }

        return self::whereNotNull('lead')
            ->whereNull('oportunidade')
            ->where('_id', "<>", $sale->getId())
            ->where('canal', $sale->getCanal())
            ->where('consumidor.cpf', $sale->getConsumidor()->getCpf())
            ->whereBetween('created_at', [Carbon::now()->subDays(3)->startOfDay(), Carbon::now()])
            ->get();
    }

    public function deleteSaleByLead(Int $leadId)
    {
        if (empty($leadId)) {
            Log::error("Lead id is empty to delete");
            return;
        }

        return self::whereNotNull('lead')
            ->where('lead.id', $leadId)
            ->delete();
    }

    public function deleteSaleByOpportunity(Int $opportunityId)
    {
        if (empty($opportunityId)) {
            Log::error("Opportunity id is empty to delete");
            return;
        }

        return self::whereNotNull('oportunidade')
            ->where('oportunidade.id', $opportunityId)
            ->delete();
    }

    public function listPartialSales(Carbon $startDate, Carbon $finishDate, int $take)
    {
        return self::where('oportunidade', null)
            ->where('lead', null)
            ->whereBetween('created_at', [$startDate, $finishDate])
            ->orderBy('created_at', 'ASC')
            ->take($take)
            ->get();
    }

    public function listOpportunityVerifyInsurancePitzi($startDate, $finishDate, $status, $substatus, $take)
    {
        return self::whereNotNull('oportunidade')
            ->where('oportunidade.subStatus', $substatus)
            ->where('oportunidade.status', $status)
            ->whereNotNull('aparelho.imei')
            ->whereBetween('created_at', [$startDate, $finishDate])
            ->take($take)
            ->orderBy('created_at', 'ASC')
            ->get();
    }

    public function verifyDuplicatesLeadCount(int $days, int $count, Sale $sale)
    {
        $returnCount = [
            'cpf' => 0,
            'contato' => 0,
            'email' => 0,
        ];

        if (!empty($sale->getConsumidor()->getCpf())) {
            $returnCount['cpf'] = self::whereNotNull('lead')
                ->whereNull('oportunidade')
                ->where('consumidor.cpf', $sale->getConsumidor()->getCpf())
                ->whereBetween('created_at', [Carbon::now()->subDays($days)->startOfDay(), Carbon::now()])
                ->count();

            if ($returnCount['cpf'] > $count) {
                return true;
            }
        }

        if (!empty($sale->getConsumidor()->getEmail())) {
            $returnCount['email'] = self::whereNotNull('lead')
                ->whereNull('oportunidade')
                ->where('consumidor.email', $sale->getConsumidor()->getEmail())
                ->whereBetween('created_at', [Carbon::now()->subDays($days)->startOfDay(), Carbon::now()])
                ->count();

            if ($returnCount['email'] > $count) {
                return true;
            }
        }

        if (!empty($sale->getConsumidor()->getTelefoneServico())) {
            $returnCount['contato'] = self::whereNotNull('lead')
                ->whereNull('oportunidade')
                ->where('consumidor.telefone.servico', $sale->getConsumidor()->getTelefoneServico())
                ->whereBetween('created_at', [Carbon::now()->subDays($days)->startOfDay(), Carbon::now()])
                ->count();

            if ($returnCount['contato'] > $count) {
                return true;
            }
        }

        return false;
    }

    public function verifyDuplicatesOpportunityCount(int $days, int $count, Sale $sale)
    {
        $returnCount = [
            'cpf' => 0,
            'contato' => 0,
            'email' => 0,
        ];

        if (!empty($sale->getConsumidor()->getCpf())) {
            $returnCount['cpf'] = self::whereNotNull('oportunidade')
                ->where('consumidor.cpf', $sale->getConsumidor()->getCpf())
                ->whereBetween('created_at', [Carbon::now()->subDays($days)->startOfDay(), Carbon::now()])
                ->count();

            if ($returnCount['cpf'] > $count) {
                return true;
            }
        }

        if (!empty($sale->getConsumidor()->getEmail())) {
            $returnCount['email'] = self::whereNotNull('oportunidade')
                ->where('consumidor.email', $sale->getConsumidor()->getEmail())
                ->whereBetween('created_at', [Carbon::now()->subDays($days)->startOfDay(), Carbon::now()])
                ->count();

            if ($returnCount['email'] > $count) {
                return true;
            }
        }

        if (!empty($sale->getConsumidor()->getTelefoneServico())) {
            $returnCount['contato'] = self::whereNotNull('oportunidade')
                ->where('consumidor.telefone.servico', $sale->getConsumidor()->getTelefoneServico())
                ->whereBetween('created_at', [Carbon::now()->subDays($days)->startOfDay(), Carbon::now()])
                ->count();

            if ($returnCount['contato'] > $count) {
                return true;
            }
        }

        return false;
    }

    public function listOpportunitysForClient($cpf, $email)
    {
        return self::whereNotNull('oportunidade')
            ->whereNull('lead')
            ->where('consumidor.cpf', $cpf)
            ->where('consumidor.email', $email)
            ->get();
    }
}
