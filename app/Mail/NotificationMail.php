<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuarioTo;
    public $empresa_to;
    public $numero_to;
    public $asunto_to;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$empresa,$correlativo,$asunto,$titulo)
    // public function __construct()
    {
        $this->usuarioTo = $subject;
        $this->empresa_to = $empresa;
        $this->numero_to = $correlativo;
        $this->asunto_to = $asunto;
        $this->subject = $titulo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $usuario_to = $this->usuarioTo;
        $empresa_to = $this->empresa_to;
        $numero_to = $this->numero_to;
        $asunto_to = $this->asunto_to;
        $subject_to = $this->subject;
        return $this->view('Mail.correo', compact("usuario_to",'empresa_to','numero_to','asunto_to'))->subject($subject_to);
    }
}