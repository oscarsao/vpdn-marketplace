<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VisaStepUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $nameClient;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nameClient)
    {
        $this->nameClient = $nameClient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.immigration.visa_step_update')
            ->subject("Cambio Estatus en Cohen&Aguirre")
            ->with([
                'nameClient' =>  $this->nameClient
            ]);
    }
}
