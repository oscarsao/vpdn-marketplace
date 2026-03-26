<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactNotification extends Notification
{
    use Queueable;

    private $contact;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
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
                    ->subject('Contacto web desde Video Portal de Negocios - Cohen y Aguirre')
                    ->greeting('Hola, se ha producido un nuevo contacto')
                    ->line('Nombre: ' . $this->contact->name)
                    ->line('Email: ' . $this->contact->email)
                    ->line('Mensaje: ' . $this->contact->message)
                    ->line('Fecha y Hora: ' . $this->contact->created_at->format('d-m-Y h:i:s a'))
                    ->line('¡Recuerda responder rápidamente!')
                    ->salutation('Saludos');
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
