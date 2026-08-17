<?php

namespace App\Notifications;

use App\Models\Member;
use Illuminate\Bus\Queueable;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipExpiring extends Notification
{
    use Queueable;

    public function __construct(public Member $member)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.membership_expiring_subject'))
            ->greeting(__('notifications.hello', ['name' => $notifiable->name]))
            ->line(__('notifications.membership_expiring_line', ['date' => $this->member->expires_at->format('d M Y')]))
            ->line(__('notifications.membership_expiring_renew'))
            ->action(__('notifications.renew_membership'), route('member.payments.create', ['type' => 'monthly']));
    }
}
