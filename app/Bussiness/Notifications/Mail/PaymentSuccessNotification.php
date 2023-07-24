<?php

namespace App\Bussiness\Notifications\Mail;

use App\Bussiness\Models\Sale;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PaymentSuccessNotification extends Notification
{
    public $sale;
    private $template;
    private $subject;

    public function __construct(Sale $sale)
    {
        $this->sale = $sale;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $this->subject = 'Seguro Pitzi pago com sucesso!';
        $this->template = 'emails/paymentSuccess';

        Log::info('[' . $this->sale->getId() . '] send paymentSuccess pitzi e-mail: ' . $this->template);

        $mailMessage = (new MailMessage)
            ->subject($this->subject)
            ->view($this->template, [
                'nome' => $this->sale->getConsumidor()->getNome(),
                'plano' => $this->sale->getSeguro()->getPlanoId(),
                'aparelho' => $this->sale->getAparelho()->getModelo(),
                'valor' => $this->sale->getSeguro()->getPrecoParcelado(),
                'orderId' => $this->sale->getOportunidade()->getOrderIdPitzi(),
            ]);

        if (env('APP_ENV') === 'production') {
            $mailMessage->replyTo(env('EMAIL_COMERCIAL_PITZI'));
        }

        return $mailMessage;
    }
}
