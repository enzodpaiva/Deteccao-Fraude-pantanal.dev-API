<?php

namespace App\Console\Commands;

use App\Bussiness\Enums\OpportunityDataEnum;
use App\Bussiness\Models\Sale;
use App\Bussiness\Services\LeadService;
use App\Bussiness\Services\NotifyService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;
use Symfony\Component\HttpFoundation\Response;

class VerifyDeviceInsuranceActive extends Command
{
    protected $name = 'verifyDeviceInsuranceActive';
    protected $service;
    protected $saleRepository;
    protected $timeOut;

    protected $description = 'Verify Opportunity Status in Pitzi';

    protected $count = [
        'total' => 0,
        'success' => 0,
        'error' => 0,
    ];

    protected $take = 10;

    private $statusSubstatusList = [
        ['status' => OpportunityDataEnum::PENDENTE, 'substatus' => OpportunityDataEnum::ENVIO_FOTO_NAO_EFETUADO],
        ['status' => OpportunityDataEnum::PENDENTE, 'substatus' => OpportunityDataEnum::PAGAMENTO_EFETUADO],
        ['status' => OpportunityDataEnum::PENDENTE, 'substatus' => OpportunityDataEnum::CADASTRO_EFETUADO],
    ];

    public function __construct(LeadService $service)
    {
        parent::__construct();
        $this->service = $service;
        $this->saleRepository = Sale::factory();
        $this->timeOut = Carbon::now()->addMinutes(10);
    }

    public function handle()
    {
        Log::info('--------------- Checking for verifyDeviceInsuranceActive at ' . Carbon::now()->toIso8601String() . ' -----------------');

        foreach ($this->statusSubstatusList as $statusSubstatus) { // foreach statusSubstatus
            /**
             * @todo quando for informado quanto tempo a url de pagamento fica on, definir melhor este tempo
             */
            $startDate = Carbon::now()->subDays(7)->endOfDay();
            $finishDate = Carbon::now()->subHours(1)->endOfDay();

            try {
                do {
                    $listSale = $this->saleRepository->listOpportunityVerifyInsurancePitzi($startDate, $finishDate, $statusSubstatus['status'], $statusSubstatus['substatus'], $this->take);

                    Log::info('Processing ' . count($listSale) . ' verifyDeviceInsuranceActive  [' . $startDate->toDateTimeString() . '][' . $finishDate->toDateTimeString() . ']');

                    $this->count['total'] += count($listSale);

                    foreach ($listSale as $sale) {

                        // SET NEXT START DATE RANGE
                        $startDate = $sale['created_at']->addMilliseconds();

                        try {
                            $response = $this->service->consultSubscriptionByImei(['imei' => $sale->getAparelho()->getImei()]);
                            $dadosVendaPitzi = $response['data'];

                            if ($response['status'] != Response::HTTP_OK) {
                                $this->count['error']++;
                                throw new Exception('[' . $sale->getId() . ']' . '[verifyDeviceInsuranceActive]' . '[' . $sale->getOportunidade()->getId() . ']' . ' Failed to consult Subscription By Imei. Reason: ' . $response['data']);
                            }

                            if ($dadosVendaPitzi['order']['id'] != $sale->getOportunidade()->getOrderIdPitzi()) {
                                Log::info('Id: ' . '[' . $sale->getId() . ']' . ' Oportunidade: ' . '[' . $sale->getOportunidade()->getId() . ']' . ' Imei: ' . '[' . $sale->getAparelho()->getImei() . ']' . ' - Set order id pitzi: ' . $dadosVendaPitzi['order']['id']);
                                $sale->getOportunidade()->setOrderIdPitzi($dadosVendaPitzi['order']['id']);

                                try {
                                    $this->service->getCrm()->addCommentToDeal($sale->getOportunidade()->getId(), 'order id pitzi: ' . $sale->getOportunidade()->getOrderIdPitzi());
                                } catch (Exception $e) {
                                    Log::error('[' . $sale->getId() . '][verifyDeviceInsuranceActive][' . $sale->getOportunidade()->getId() . '] Error to send order id pitzi comment message: ' . $e->getMessage() . ' - ' . Carbon::now()->toIso8601String());
                                }
                            }

                            if ($dadosVendaPitzi['order']['subscribed_at']) {
                                Log::info('Id: ' . '[' . $sale->getId() . ']' . ' Oportunidade: ' . '[' . $sale->getOportunidade()->getId() . ']' . ' Imei: ' . '[' . $sale->getAparelho()->getImei() . ']' . ' - Set date signature pitzi: ' . $dadosVendaPitzi['order']['subscribed_at']);
                                $dateSubscribed = $dadosVendaPitzi['order']['subscribed_at'];
                                $dateSubscribed = preg_replace("/[^0-9]/", "", $dateSubscribed);
                                if ($dateSubscribed != $sale->getOportunidade()->getDataAssinaturaPitzi()) {
                                    $sale->getOportunidade()->setDataAssinaturaPitzi($dateSubscribed);

                                    try {
                                        $dataPitziFormated = empty($datePitzi = DateTime::createFromFormat('dmY', $sale->getOportunidade()->getDataAssinaturaPitzi())) ? '' : $datePitzi->format('d/m/Y');

                                        $this->service->getCrm()->addCommentToDeal($sale->getOportunidade()->getId(), 'Data assinatura pitzi: ' . $dataPitziFormated);
                                    } catch (Exception $e) {
                                        Log::error('[' . $sale->getId() . '][verifyDeviceInsuranceActive][' . $sale->getOportunidade()->getId() . '] Error to send date signature pitzi comment message: ' . $e->getMessage() . ' - ' . Carbon::now()->toIso8601String());
                                    }
                                }
                            }

                            if (OpportunityDataEnum::STATUS_VERIFICACAO_APARELHO[$dadosVendaPitzi['order']['device_verification_status']] != $sale->getAparelho()->getValidado()) {
                                Log::info('Id: ' . '[' . $sale->getId() . ']' . ' Oportunidade: ' . '[' . $sale->getOportunidade()->getId() . ']' . ' Imei: ' . '[' . $sale->getAparelho()->getImei() . ']' . ' - Set device verification status: ' . $dadosVendaPitzi['order']['device_verification_status']);
                                $sale->getAparelho()->setValidado(OpportunityDataEnum::STATUS_VERIFICACAO_APARELHO[$dadosVendaPitzi['order']['device_verification_status']]);

                                $message = $sale->getAparelho()->getValidado() ? "Aparelho a ser assegurado validado." : "Aparelho a ser assegurado não validado.";
                                try {
                                    $this->service->getCrm()->addCommentToDeal($sale->getOportunidade()->getId(), $message);
                                } catch (Exception $e) {
                                    Log::error('[' . $sale->getId() . '][verifyDeviceInsuranceActive][' . $sale->getOportunidade()->getId() . '] Error to send device validation comment message: ' . $e->getMessage() . ' - ' . Carbon::now()->toIso8601String());
                                }
                            }

                            if ($dadosVendaPitzi['order']['installments'] != $sale->getSeguro()->getNumeroParcelas() or
                                $dadosVendaPitzi['order']['plan_price'] != (float) number_format(((Integer) $sale->getSeguro()->getPrecoTotal()) / 100, 2, '.', '')) {
                                Log::info('Id: ' . '[' . $sale->getId() . ']' . ' Oportunidade: ' . '[' . $sale->getOportunidade()->getId() . ']' . ' Imei: ' . '[' . $sale->getAparelho()->getImei() . ']' . ' - Set installments: ' . $dadosVendaPitzi['order']['installments']);
                                Log::info('Id: ' . '[' . $sale->getId() . ']' . ' Oportunidade: ' . '[' . $sale->getOportunidade()->getId() . ']' . ' Imei: ' . '[' . $sale->getAparelho()->getImei() . ']' . ' - Set payment_method: ' . $dadosVendaPitzi['order']['payment_method']);

                                $priceInstallments = $dadosVendaPitzi['order']['plan_price'] / $dadosVendaPitzi['order']['installments'];
                                $numFormatted = str_replace('.', '', number_format($priceInstallments, 2, '.', ''));

                                Log::info('Id: ' . '[' . $sale->getId() . ']' . ' Oportunidade: ' . '[' . $sale->getOportunidade()->getId() . ']' . ' Imei: ' . '[' . $sale->getAparelho()->getImei() . ']' . ' -  Plan price: ' . $dadosVendaPitzi['order']['plan_price'] . ' Set plan price installment: ' . $numFormatted);

                                $sale->getSeguro()->setNumeroParcelas($dadosVendaPitzi['order']['installments']);
                                $sale->getSeguro()->setPrecoParcelado($numFormatted);
                                $sale->getSeguro()->setPrecoTotal(str_replace('.', '', number_format($dadosVendaPitzi['order']['plan_price'], 2, '.', '')));
                                $sale->getSeguro()->setFormaPagamento($dadosVendaPitzi['order']['payment_method']);

                                $message = "Alteração no numero de parcelas de pagamento pitzi: \n";
                                $message .= "Numero de parcelas: " . $sale->getSeguro()->getNumeroParcelas() . PHP_EOL;
                                $message .= "Preço da parcela: R$" . number_format($sale->getSeguro()->getPrecoParcelado() / 100, 2, ',', '') . PHP_EOL;
                                $message .= "Preço Total: R$" . number_format($sale->getSeguro()->getPrecoTotal() / 100, 2, ',', '') . PHP_EOL;
                                $message .= "Forma de pagamento: " . OpportunityDataEnum::FORMA_PAGAMENTO_NAME[$sale->getSeguro()->getFormaPagamento()] . PHP_EOL;

                                try {
                                    $this->service->getCrm()->addCommentToDeal($sale->getOportunidade()->getId(), $message);
                                } catch (Exception $e) {
                                    Log::error('[' . $sale->getId() . '][verifyDeviceInsuranceActive][' . $sale->getOportunidade()->getId() . '] Error to send order id pitzi comment message: ' . $e->getMessage() . ' - ' . Carbon::now()->toIso8601String());
                                }
                            }

                            $status = OpportunityDataEnum::PENDENTE;
                            $subStatus = OpportunityDataEnum::CADASTRO_EFETUADO;

                            if ($this->isSaleConfirmed($dadosVendaPitzi)) {
                                $status = OpportunityDataEnum::GANHA;
                                $subStatus = OpportunityDataEnum::CONCLUIDO;
                                $sale->getOportunidade()->setDataVenda(new UTCDateTime(new DateTime()));
                                $sale->getOportunidade()->setDataFechamento(new UTCDateTime(new DateTime()));
                            } else if ($this->isPaymentEffected($sale) && $this->isPhotoNotSend($dadosVendaPitzi)) {
                                $status = OpportunityDataEnum::PENDENTE;
                                $subStatus = OpportunityDataEnum::ENVIO_FOTO_NAO_EFETUADO;
                            } else if ($this->isRegisterEffected($sale) && $this->isPhotoNotSend($dadosVendaPitzi)) {
                                $status = OpportunityDataEnum::PENDENTE;
                                $subStatus = OpportunityDataEnum::PAGAMENTO_EFETUADO;
                            }

                            if ($status != $sale->getOportunidade()->getStatus() or
                                $subStatus != $sale->getOportunidade()->getSubstatus()
                            ) {

                                $sale->getOportunidade()->setStatus($status);
                                $sale->getOportunidade()->setSubstatus($subStatus);

                                // save DB
                                $sale->save();

                                // Update Bitrix
                                $this->service->getCrm()->updateStatusDealCRM($sale);

                                //Send notify
                                NotifyService::factory()->execute($sale);
                            }

                            sleep(2);

                            Log::debug('Processed opportunity-> ' . 'id bd: (' . $sale->getId() . ') id Lead: (' . $sale->getOportunidade()->getId() . ') channel: ' . $sale->getCanal());

                            $this->count['success']++;
                        } catch (Exception $e) {
                            Log::error('Error processing ' . $sale->getId() . ': ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');
                        }
                    }

                } while (count($listSale) > 0 and Carbon::now() <= $this->timeOut);

            } catch (Exception $e) {
                Log::error('Partials Exception: ' . $e->getMessage() . ' - ' . Carbon::now()->toIso8601String());
            }
        }

        Log::info('--------------- Finished verifyDeviceInsuranceActive at ' . Carbon::now()->toIso8601String() . json_encode($this->count) . ' ---------------');
    }

    private function isSaleConfirmed($dadosVendaPitzi)
    {
        return isset($dadosVendaPitzi['order']) &&
        $dadosVendaPitzi['order']['active'] &&
        $dadosVendaPitzi['order']['payment_confirmed'] &&
        $dadosVendaPitzi['order']['device_verification_status'] === OpportunityDataEnum::VERIFICACAO_FOTO_APARELHO_CONFIRMADO;
    }

    private function isPaymentEffected($sale)
    {
        return $sale->getOportunidade()->getStatus() == OpportunityDataEnum::PENDENTE &&
        $sale->getOportunidade()->getSubStatus() == OpportunityDataEnum::PAGAMENTO_EFETUADO;
    }

    private function isRegisterEffected($sale)
    {
        return $sale->getOportunidade()->getStatus() == OpportunityDataEnum::PENDENTE &&
        $sale->getOportunidade()->getSubStatus() == OpportunityDataEnum::CADASTRO_EFETUADO;
    }

    private function isPhotoNotSend($dadosVendaPitzi)
    {
        return isset($dadosVendaPitzi['order']) &&
        $dadosVendaPitzi['order']['active'] &&
        $dadosVendaPitzi['order']['payment_confirmed'] &&
        $dadosVendaPitzi['order']['device_verification_status'] !== OpportunityDataEnum::VERIFICACAO_FOTO_APARELHO_CONFIRMADO;
    }
}
