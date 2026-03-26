<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BusinessPreferenceMail extends Mailable
{
    /*
        Recordando que las Preferencias de Negocios de los Clientes se encuentran
        en la relación business_clients, no confundir con recomendaciones
    */

    use Queueable, SerializesModels;

    protected $client;
    protected $businessesPerClient;
    protected $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($client, $businessesPerClient, $token)
    {
        $this->client = $client;
        $this->businessesPerClient = $businessesPerClient;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.business_preference')
                    ->subject("Hemos encontrado el negocio ideal para ti - Cohen&Aguirre")
                    ->with([
                        'client'                =>  $this->client,
                        'businessesPerClient'   =>  $this->businessesPerClient,
                        'token'                 =>  $this->token
                    ]);
    }
}
