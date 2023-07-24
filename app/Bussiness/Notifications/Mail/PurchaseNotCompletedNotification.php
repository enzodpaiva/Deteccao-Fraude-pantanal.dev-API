<?php

namespace App\Bussiness\Notifications\Mail;

use App\Bussiness\Models\Sale;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PurchaseNotCompletedNotification extends Notification
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
        $this->subject = 'Seguro Pitzi não contratado!';
        $this->template = 'emails/purchaseNotCompleted';

        Log::info('[' . $this->sale->getId() . '] send purchaseNotCompleted e-mail opportunity: ' . $this->template);

        return (new MailMessage)
            ->subject($this->subject)
            ->view($this->template, [
                'nome' => $this->sale->getConsumidor()->getNome(),
                'cpf' => $this->sale->getConsumidor()->getNome(),
            ]);
    }
}
