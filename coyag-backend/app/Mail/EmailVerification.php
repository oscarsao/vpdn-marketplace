<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    protected $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $linkEmailVerification = env('APP_URL') . 'confirm-email-verification?token=' . $this->token;
        $linkEmailVerification = config('app.url') . 'confirm-email-verification?token=' . $this->token;

        return $this->view('emails.email_verification')
                    ->subject("Verificación de correo en Cohen&Aguirre")
                    ->with([
                        'linkEmailVerification' =>  $linkEmailVerification
                    ]);
    }
}
