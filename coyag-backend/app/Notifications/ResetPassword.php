<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    use Queueable;

    public $actionUrl;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        // $this->actionUrl = env('APP_URL') . 'reset-password?token=' . $token;
        $this->actionUrl = config('app.url') . 'reset-password?token=' . $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->from('notificaciones@coyag.test', 'Notificaciones Cohen y Aguirre')
                    ->subject('Recuperar contraseña - Cohen y Aguirre')
                    ->greeting('Recuperar contraseña')
                    ->line('Es simple, solo debes hacer click en el botón de abajo (Recuperar contraseña) para iniciar el proceso.')
                    ->action('Recuperar contraseña', $this->actionUrl)
                    ->line('¡Gracias por ser parte de nuestra familia!')
                    ->line('Saludos,')
                    ->salutation('Cohen y Aguirre');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
