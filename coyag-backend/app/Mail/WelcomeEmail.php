<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $client;
    protected $password;
    protected $linkWebApp;
    protected $serviceName;
    protected $title;
    protected $pathView;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Client $client, $password, $linkWebApp, $serviceName, $title, $pathView)
    {
        $this->client = $client;
        $this->password = $password;
        $this->linkWebApp = $linkWebApp;
        $this->serviceName = $serviceName;
        $this->title = $title;
        $this->pathView = $pathView;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->pathView)
                    ->subject($this->title)
                    ->with([
                        'clientFullName' => trim("{$this->client->names} {$this->client->surnames}"),
                        'clientEmail' => $this->client->user->email,
                        'linkWebApp' => $this->linkWebApp,
                        'serviceName' => $this->serviceName,
                        'password'    => $this->password,
                    ]);
    }
}
