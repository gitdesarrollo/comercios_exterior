<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTracingMailModel extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $message_to;
    public $actual;
    public $traslada;
    public $correlativo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message,$actual,$traslada,$correlativo)
    {
        $this->message_to = $message;
        $this->actual = $actual;
        $this->traslada = $traslada;
        $this->correlativo = $correlativo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $message_to = $this->message_to; 
        $actual = $this->actual; 
        $traslada = $this->traslada; 
        $correlativo = $this->correlativo; 
        $subject_to = 'Seguimiento de Expediente';
        return $this->markdown('Mail.tracingMail', compact($message_to,$actual,$traslada,$correlativo))->subject($subject_to);
    }
}
