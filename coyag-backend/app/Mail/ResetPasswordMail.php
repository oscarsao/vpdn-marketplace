<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $frontend;
    protected $actionUrl;
    protected $mailMessage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($frontend, $token)
    {
        $this->frontend = $frontend;
        if ($this->frontend === 'videoportal') {
            $url = "https://videoportaldenegocios.es/videoportal/#/";
        } else if ($this->frontend === 'extranjeria') {
            $url = "https://cohenyaguirre.es/intranet/#/";
        } else {
            $url = config('app.url');
        }
        $this->actionUrl = $url . 'reset-password?token=' . $token;

        $this->mailMessage = (new MailMessage)
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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = array_merge(array( 'frontend' => $this->frontend ), $this->mailMessage->data());
        return  $this->markdown('vendor.notifications.email', $data);
    }
}
