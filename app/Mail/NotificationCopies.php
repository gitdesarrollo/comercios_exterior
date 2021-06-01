<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationCopies extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;


    public $usuarioTo;
    public $empresa_to;
    public $numero_to;
    public $asunto_to;
    public $subject;
    public $interno;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$empresa,$correlativo,$asunto,$titulo,$nInterno)
    {
        $this->usuarioTo = $subject;
        $this->empresa_to = $empresa;
        $this->numero_to = $correlativo;
        $this->asunto_to = $asunto;
        $this->subject = $titulo;
        $this->interno = $nInterno;
    
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject_to = $this->subject;

        return $this->markdown('Mail.copies',[
            'usuario_to' => $this->usuarioTo,
            'empresa_to' => $this->empresa_to,
            'numero_to' => $this->numero_to,
            'asunto_to' => $this->asunto_to,
            'interno' => $this->subject
        ])->subject($subject_to);
    }
}
