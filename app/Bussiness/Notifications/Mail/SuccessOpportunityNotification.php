<?php

namespace App\Bussiness\Notifications\Mail;

use App\Bussiness\Models\Sale;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SuccessOpportunityNotification extends Notification
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

        //status ganha
        $this->subject = 'Parabéns! Seguro Pitzi contratado!';
        $this->template = 'emails/successOpportunity';

        Log::info('[' . $this->sale->getId() . '] send success e-mail opportunity: ' . $this->template);

        return (new MailMessage)
            ->subject($this->subject)
            ->view($this->template, [
                'nome' => $this->sale->getConsumidor()->getNome(),
                'plano' => $this->sale->getSeguro()->getNome(),
                'aparelho' => $this->sale->getAparelho()->getModelo(),
                'valor' => $this->sale->getSeguro()->getPrecoParcelado(),
                'orderId' => $this->sale->getOportunidade()->getOrderIdPitzi(),
            ]);
    }
}
