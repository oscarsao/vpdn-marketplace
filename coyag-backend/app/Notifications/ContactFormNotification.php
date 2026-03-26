<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactFormNotification extends Notification
{
    use Queueable;

    private $object;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($object)
    {
        $this->object = $object;
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
                ->from('info@videoportaldenegocios.es', 'Notificaciones Video Portal de Negocios - Cohen y Aguirre')
                ->subject('Solicitud de Servicio - Video Portal de Negocios')
                ->greeting('Hola, se ha producido una nueva solicitud de Servicios desde el Video Portal')
                ->line("**Nombres:** {$this->object->names}")
                ->line("**Apellidos:** {$this->object->surnames}")
                ->line("**Email:** {$this->object->email}")
                ->line("**Teléfono Celular:** {$this->object->phone}")
                ->line("**Motivo de Consulta:** {$this->object->reason}")
                ->line('**Link de Negocio:** [Click aqui](' . url($this->object->business) . ')')
                ->action('Link de Cliente', url($this->object->link))
                ->salutation('**Saludos**');

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
