<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class copiesUser extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $usuarioTo;
    public $empresa_to;
    public $numero_to;
    public $asunto_to;
    public $subject;
    public $interno;
    public $copias;
    public $instrucciones;
    public $fecha;
    public $file;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$empresa,$correlativo,$asunto,$titulo,$nInterno, $copias, $instrucciones,$fecha)
    {
        $this->usuarioTo = $subject;
        $this->empresa_to = $empresa;
        $this->numero_to = $correlativo;
        $this->asunto_to = $asunto;
        $this->subject = $titulo;
        $this->interno = $nInterno;
        $this->copias = $copias;
        $this->instrucciones = $instrucciones;
        $this->fecha = $fecha;
        $this->file = 'MINECO-DACE-101-2021.pdf';


        
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject_to = $this->subject;
        

        return $this->markdown('Mail.copiesUser',[
            'usuario_to' => $this->usuarioTo,
            'empresa_to' => $this->empresa_to,
            'numero_to' => $this->numero_to,
            'asunto_to' => $this->asunto_to,
            'interno' => $this->interno,
            ['copias' => $this->copias],
            'instrucciones' => $this->instrucciones,
            'fecha' => $this->fecha
        ])->subject($subject_to);
        // ])->subject($subject_to)->attach(public_path('files\\'.$this->file));
    }
}
