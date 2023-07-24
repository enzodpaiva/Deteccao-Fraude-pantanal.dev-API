<?php

namespace App\Bussiness\Notifications\Mail;

use App\Bussiness\Models\Sale;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class DeviceVerifyNotification extends Notification
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
        $this->subject = 'Atenção, seguro Pitzi pendente!';
        $this->template = 'emails/deviceVerify';

        Log::info('[' . $this->sale->getId() . '] send deviceVerify pitzi e-mail: ' . $this->template);

        $mailMessage = (new MailMessage)
            ->subject($this->subject)
            ->view($this->template, [
                'nome' => $this->sale->getConsumidor()->getNome(),
                'orderId' => $this->sale->getOportunidade()->getOrderIdPitzi(),
            ]);

        if (env('APP_ENV') === 'production') {
            $mailMessage->replyTo(env('EMAIL_COMERCIAL_PITZI'));
        }

        return $mailMessage;

    }
}
