<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PlanExpirationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $object;
    protected $title;
    protected $pathView;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($object, $title, $pathView)
    {
        $this->object = $object;
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
                    ->with(['object' => $this->object]);
    }
}
