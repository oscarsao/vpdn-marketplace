<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NoActivityMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $nameClient;
    protected $nameVisaType;
    protected $totalVS;
    protected $completeVS;
    protected $pendingDocsUpload;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nameClient, $nameVisaType, $totalVS, $completeVS, $pendingDocsUpload)
    {
        $this->nameClient = $nameClient;
        $this->nameVisaType = $nameVisaType;
        $this->totalVS = $totalVS;
        $this->completeVS = $completeVS;
        $this->pendingDocsUpload = $pendingDocsUpload;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.immigration.no_activity')
                    ->subject("Notificación - Cohen&Aguirre")
                    ->with([
                        'nameClient'        => $this->nameClient,
                        'nameVisaType'      => $this->nameVisaType,
                        'totalVS'           => $this->totalVS,
                        'completeVS'        => $this->completeVS,
                        'pendingDocsUpload' => $this->pendingDocsUpload
                    ]);
    }
}
