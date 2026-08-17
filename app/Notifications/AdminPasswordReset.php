<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class AdminPasswordReset extends BaseResetPassword
{
    use Queueable;

    protected function resetUrl($notifiable)
    {
        return url(route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('admin.reset_mail_subject'))
            ->greeting(__('admin.reset_mail_greeting', ['name' => $notifiable->name]))
            ->line(__('admin.reset_mail_line'))
            ->action(__('admin.reset_password'), $this->resetUrl($notifiable))
            ->line(__('admin.reset_mail_expiry'));
    }
}
