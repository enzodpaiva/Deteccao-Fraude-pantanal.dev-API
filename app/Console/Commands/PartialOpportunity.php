<?php

namespace App\Console\Commands;

use App\Bussiness\Enums\OpportunityDataEnum;
use App\Bussiness\Models\Lead;
use App\Bussiness\Models\Sale;
use App\Bussiness\Services\LeadService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PartialOpportunity extends Command
{
    protected $name = 'partial';
    protected $service;
    protected $saleRepository;

    protected $description = 'Processes Partial Opportunity';

    protected $count = [
        'total' => 0,
        'success' => 0,
        'duplicated' => 0,
    ];

    protected $take = 10;
    protected $startDate;
    protected $finishDate;
    protected $timeOut;

    public function __construct(LeadService $service)
    {
        parent::__construct();
        $this->service = $service;
        $this->saleRepository = Sale::factory();
        $this->startDate = Carbon::now()->subDays(7);
        $this->finishDate = Carbon::now()->subMinute(10);

        $this->timeOut = Carbon::now()->addMinutes(10);
    }

    public function handle()
    {
        Log::info('--------------- Checking for partials at ' . Carbon::now()->toIso8601String() . ' -----------------');

        try {
            do {
                $listPartial = $this->saleRepository->listPartialSales($this->startDate, $this->finishDate, $this->take);

                Log::info('Processing ' . count($listPartial) . ' partials  [' . $this->startDate->toDateTimeString() . '][' . $this->finishDate->toDateTimeString() . ']');

                $this->count['total'] += count($listPartial);

                foreach ($listPartial as $sale) {

                    // SET NEXT START DATE RANGE
                    $this->startDate = $sale['created_at']->addMilliseconds();

                    try {
                        // check duplicated lead - desativado
                        // if ($this->saleRepository->verifyDuplicatesLeadCount(3, 0, $sale)) {

                        //     Log::notice('[' . $sale->getId() . '] duplicate partial - deleted');

                        //     $this->saleRepository->deleteSale($sale->getId());
                        //     $this->count['duplicated']++;

                        //     continue;
                        // }

                        // check duplicated opportunity - desativado
                        // if ($this->saleRepository->verifyDuplicatesOpportunityCount(3, 0, $sale)) {

                        //     Log::notice('[' . $sale->getId() . '] already has opportunity - deleted');

                        //     $this->saleRepository->deleteSale($sale->getId());
                        //     $this->count['duplicated']++;

                        //     continue;
                        // }

                        $statusSubstatusLead = [
                            'status' => OpportunityDataEnum::CARRINHO_ABANDONADO,
                            'subStatus' => OpportunityDataEnum::CADASTRO_EFETUADO,
                        ];

                        if (!is_null($sale->getAparelho()->getImei())) {
                            $response = $this->service->consultSubscriptionByImei(['imei' => $sale->getAparelho()->getImei()]);
                            $dadosVendaPitzi = $response['data'];

                            if (
                                isset($dadosVendaPitzi['order']) and
                                $dadosVendaPitzi['order']['active'] and
                                !$dadosVendaPitzi['order']['payment_confirmed']
                            ) {
                                $statusSubstatusLead = [
                                    'status' => OpportunityDataEnum::CARRINHO_ABANDONADO,
                                    'subStatus' => OpportunityDataEnum::PAGAMENTO_NAO_EFETUADO,
                                ];
                            }
                        }

                        $sale->setLead(Lead::factory()->populate($statusSubstatusLead));

                        // Create Bitrix Deal
                        $sale = $this->service->createPartialOpportunity($sale, false);

                        Log::debug('Processed opportunity-> ' . 'id bd: (' . $sale->getId() . ') id Lead: (' . $sale->getLead()->getId() . ') channel: ' . $sale->getCanal());

                        $this->count['success']++;
                    } catch (Exception $e) {
                        Log::error('Error processing ' . $sale->getId() . ': ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')' . "\n" . $e->getTraceAsString());
                    }
                }

            } while (count($listPartial) > 0 and Carbon::now() <= $this->timeOut);

        } catch (Exception $e) {
            Log::error('Partials Exception: ' . $e->getMessage() . ' - ' . Carbon::now()->toIso8601String());
        }

        Log::info('--------------- Finished partials at ' . Carbon::now()->toIso8601String() . json_encode($this->count) . ' ---------------');
    }
}
