<?php

namespace App\Notifications;

use App\Models\Business;
use App\Models\BusinessMultimedia;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NewBusinessVideoNotification extends Notification
{
    use Queueable;

    private $business;
    private $client;
    private $businessMultimedia;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Business $business, Client $client)
    {
        $this->business = $business;
        $this->client = $client;
        $this->businessMultimedia = BusinessMultimedia::where('business_id', $business->id)
                                                        ->where('type', 'image')
                                                        ->first();

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
        // TODO: Aquí debería calcularse con env('APP_URL')

        $mailMessage =  (new MailMessage)
                    ->from('info@videoportaldenegocios.es', 'Notificaciones Video Portal de Negocios - Cohen y Aguirre')
                    ->subject("Nuevo Videoanálisis de Negocio")
                    ->greeting("!Hola, {$this->client->names}!")
                    ->line(new HtmlString("<br>"))
                    ->line("Hemos cargado en nuestro Portal un Video del Negocio: **{$this->business->name}**.");

        if($this->businessMultimedia)
        {
            $mailMessage = $mailMessage->line(new HtmlString("<img src='https://api.cohenyaguirre.es/{$this->businessMultimedia->small_image_path}' alt='{$this->business->name}' style='max-width: 100%; height: auto;' />"))
                                        ->line(new HtmlString("<br>"));
        }

        $mailMessage = $mailMessage->action('Ver Negocio', url("https://videoportaldenegocios.es/videoportal/#/negocio/{$this->business->id_code}?active=ignore"))
                    ->line(new HtmlString("<br>"))
                    ->salutation('**Saludos**');

        return  $mailMessage;

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
