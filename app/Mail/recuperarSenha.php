<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use stdClass;

/**
 * Recovery recuperacao de senha  
 * 
 */
class recuperarSenha extends Mailable
{
    use Queueable, SerializesModels;
    private $emaildata;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(stdClass $emaildata)
    {
        $this->emaildata = $emaildata;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->emaildata->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        Log::info('Envio de E-mail pare Recuperação de Senha - E-mail do Usuário='.$this->emaildata->emaildestino.' Link='.$this->emaildata->urlsistema);
        $this->subject($this->emaildata->subject);
        return new Content(
            markdown: 'mail.recuperarSenhaEmail',
            with: [
                'emaildata' => $this->emaildata,
            ],
        );

    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
