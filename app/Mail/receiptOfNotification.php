<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class receiptOfNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * variables de ingreso para correo
     */

    public $usuarioTo;
    public $internalCorrelative;
    public $externalCorrelative;
    public $receivingUser;
    public $typeDocument;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userTo, $internalCorrelative, $externalCorrelative, $receivingUser,$typeDocument)
    {
        $this->usuarioTo = $userTo;
        $this->internalCorrelative = $internalCorrelative;
        $this->externalCorrelative = $externalCorrelative;
        $this->receivingUser = $receivingUser;
        $this->typeDocument = $typeDocument;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $usuarioTo = $this->usuarioTo;
        $internalCorrelative = $this->internalCorrelative;
        $externalCorrelative = $this->externalCorrelative;
        $receivingUser = $this->receivingUser;
        $typeDocument = $this->typeDocument;
        $subject_to = "Recepción de Documento";

        return $this->view('Mail.notificationMailer', compact($usuarioTo,$internalCorrelative,$externalCorrelative,$receivingUser,$typeDocument))->subject($subject_to);
    }
}
