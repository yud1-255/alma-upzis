<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailResetPasswordToken extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Permintaan untuk reset password anda')
            ->line('Assalamualaikum,')
            ->line('Anda menerima email ini sehubungan dengan permintaan untuk reset password untuk akun anda.')
            ->action('Reset Password', route('password.reset', [$this->token, 'email' => $this->email]))
            ->line('Silakan menghubungi administrator UPZIS apabila anda memiliki pertanyaan lebih lanjut.')
            ->line('Wassalamualaikum warahmatullahi wabarakatuh');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
