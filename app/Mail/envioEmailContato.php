<?php

namespace App\Mail;

use App\Http\Controllers\Core\Tools;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use stdClass;

class envioEmailContato extends Mailable
{
    use Queueable;
    use SerializesModels;
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
        /*Tools::setLog(Tools::getUser(), 1, 0, 'Formulário de Contato do Sistema', $this->emaildata->dados.' '.$this->emaildata->contact_name.' '.$this->emaildata->contact_email.' '.$this->emaildata->contact_assunto);*/
        Tools::setAtividade(0, 8, 0, 'Formulário de Contato',  $this->emaildata->dados.' '.$this->emaildata->contact_name.' '.$this->emaildata->contact_email.' '.$this->emaildata->contact_assunto);
        Log::info('Envio de E-mail do Formulário de Contato do Sistema', ['Dados' => $this->emaildata->dados, 'Nome Contato' => $this->emaildata->contact_name, 'E-mail Contato' => $this->emaildata->contact_email, 'Assunto' => $this->emaildata->contact_assunto]);
        $this->subject($this->emaildata->subject);
        return new Content(
            markdown: 'mail.formularioContatoEmail',
            /*html: 'mail.formularioContatoEmail',
            markdown: 'core::mail.formularioContatoEmail',
            view: 'emails.orders.shipped',
            text: 'emails.orders.shipped-text',*/
            with: [
                'emaildata' => $this->emaildata,
            ],
        );

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    /*
    public function build()
    {
        $this->subject($this->emaildata->subject);
        Log::channel('slack')->info($this->emaildata->dados.' '.$this->emaildata->contact_name.' '.$this->emaildata->contact_email.' '.$this->emaildata->contact_assunto);
        //$this->to($this->emaildata->emaildestino, $this->emaildata->dados);
        //return $this->view('mail.reportarProblemaEmail')->with(['emaildata' => $this->emaildata]);
        return $this->markdown('mail.formularioContatoEmail')->with(['emaildata' => $this->emaildata]);
    }*/
}
