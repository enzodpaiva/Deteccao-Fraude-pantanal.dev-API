<?php

namespace App\Bussiness\Notifications\Mail;

use App\Bussiness\Models\Sale;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class PartialNotification extends Notification
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
        $this->subject = 'Volte e garanta o seu seguro Pitzi!';
        $this->template = 'emails/partialOpportunity';

        Log::info('[' . $this->sale->getId() . '] send partial pitzi e-mail: ' . $this->template);

        return (new MailMessage)
            ->subject($this->subject)
            ->view($this->template, [
                'cpf' => $this->sale->getConsumidor()->getCpf(),
            ]);
    }
}
