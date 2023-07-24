<?php

namespace App\Bussiness\Services;

use App\Bussiness\Enums\OpportunityDataEnum;
use App\Bussiness\Models\Sale;
use App\Bussiness\Notifications\Mail\DeviceVerifyNotification;
use App\Bussiness\Notifications\Mail\PartialNotification;
use App\Bussiness\Notifications\Mail\PaymentSuccessNotification;
use App\Bussiness\Notifications\Mail\PurchaseNotCompletedNotification;
use App\Bussiness\Notifications\Mail\SuccessOpportunityNotification;
use Exception;
use GuzzleHttp\Promise\Promise;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotifyService
{
    public static function factory(): self
    {
        return app()->make(NotifyService::class);
    }

    public function execute(Sale $sale)
    {
        $promise = new Promise();
        $promise->then(
            function ($lead) {
                try {
                    $this->sendStatusNotify($lead);
                } catch (Exception $e) {
                    Log::error('[' . $lead->getId() . '] Exception to Notify contact: ' . $e->getMessage() . ' - ' . $e->getFile() . ' (' . $e->getLine() . ')');
                }
            },
            function ($reason) { // $onRejected
                Log::error(' Rejected to notify contact: ' . $reason->getMessage());
            }
        );
        $promise->resolve($sale);
    }

    public function sendStatusNotify(Sale $sale)
    {
        switch ($sale->getOportunidade()->getStatus()) {

            case OpportunityDataEnum::PENDENTE:
                if ($sale->getOportunidade()->getSubStatus() == OpportunityDataEnum::PAGAMENTO_EFETUADO) {
                    $this->paymentSuccess($sale);
                } else if ($sale->getOportunidade()->getSubStatus() == OpportunityDataEnum::ENVIO_FOTO_NAO_EFETUADO) {
                    $this->deviceVerify($sale);
                }
                break;

            case OpportunityDataEnum::GANHA:
                if ($sale->getOportunidade()->getSubStatus() == OpportunityDataEnum::CONCLUIDO) {
                    $this->success($sale);
                }

                break;

        }
    }

    public function paymentSuccess(Sale $sale)
    {
        $email = $sale->getConsumidor()->getEmail();
        Notification::route('mail', $email)->notify(app()->makeWith(PaymentSuccessNotification::class, ['sale' => $sale]));
    }

    public function deviceVerify(Sale $sale)
    {
        $email = $sale->getConsumidor()->getEmail();
        Notification::route('mail', $email)->notify(app()->makeWith(DeviceVerifyNotification::class, ['sale' => $sale]));
    }

    public function success(Sale $sale)
    {
        $email = $sale->getConsumidor()->getEmail();
        Notification::route('mail', $email)->notify(app()->makeWith(SuccessOpportunityNotification::class, ['sale' => $sale]));
    }

    public function executePartial(Sale $sale)
    {
        switch ($sale->getLead()->getStatus()) {
            case OpportunityDataEnum::CARRINHO_ABANDONADO:
                if ($sale->getLead()->getSubStatus() == OpportunityDataEnum::CADASTRO_EFETUADO) {
                    $this->partialOpportunity($sale);
                } else if ($sale->getLead()->getSubStatus() == OpportunityDataEnum::PAGAMENTO_NAO_EFETUADO) {
                    $this->purchaseNotCompleted($sale);
                }
                break;
        }
    }

    public function purchaseNotCompleted(Sale $sale)
    {
        $email = $sale->getConsumidor()->getEmail();
        Notification::route('mail', $email)->notify(app()->makeWith(PurchaseNotCompletedNotification::class, ['sale' => $sale]));
    }

    public function partialOpportunity(Sale $sale)
    {
        $email = $sale->getConsumidor()->getEmail();
        Notification::route('mail', $email)->notify(app()->makeWith(PartialNotification::class, ['sale' => $sale]));
    }
}
