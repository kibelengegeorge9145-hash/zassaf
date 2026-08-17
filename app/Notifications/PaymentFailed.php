<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailed extends Notification
{
    use Queueable;

    public function __construct(public Payment $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.payment_failed_subject'))
            ->greeting(__('notifications.hello', ['name' => $notifiable->name]))
            ->line(__('notifications.payment_failed_line'))
            ->line(__('notifications.payment_amount', ['amount' => $this->payment->formatted_amount]))
            ->line(__('notifications.payment_reference', ['reference' => $this->payment->transaction_reference]))
            ->line(__('notifications.payment_retry'))
            ->action(__('notifications.retry_payment'), route('member.payments.index'));
    }
}
