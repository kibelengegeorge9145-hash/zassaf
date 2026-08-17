<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentSucceeded extends Notification
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
            ->subject(__('notifications.payment_succeeded_subject'))
            ->greeting(__('notifications.hello', ['name' => $notifiable->name]))
            ->line(__('notifications.payment_succeeded_line'))
            ->line(__('notifications.payment_amount', ['amount' => $this->payment->formatted_amount]))
            ->line(__('notifications.payment_reference', ['reference' => $this->payment->transaction_reference]))
            ->line(__('notifications.payment_type', ['type' => $this->payment->type_label]))
            ->line(__('notifications.membership_updated'))
            ->action(
                $this->payment->payment_type === Payment::TYPE_BOOK
                    ? __('notifications.view_library')
                    : __('notifications.view_dashboard'),
                $this->payment->payment_type === Payment::TYPE_BOOK
                    ? route('member.library')
                    : route('member.dashboard')
            );
    }
}
