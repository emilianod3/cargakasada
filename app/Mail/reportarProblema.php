<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Core\Tools;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use stdClass;

class reportarProblema extends Mailable
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
        //Tools::setLog(Tools::getUser(), 1, 0, 'Reportando Problema de Sistema', $this->emaildata->dados.' '.$this->emaildata->contact_name.' '.$this->emaildata->contact_email.' '.$this->emaildata->contact_assunto);
        Tools::setAtividade(0, 8, 0, 'Reportando Problema de Sistema',  $this->emaildata->dados.' '.$this->emaildata->contact_name.' '.$this->emaildata->contact_email.' '.$this->emaildata->contact_assunto);        
        Log::info('Envio de E-mail Reportando Problema de Sistema', ['Dados' => $this->emaildata->dados, 'Nome Contato' => $this->emaildata->contact_name, 'E-mail Contato' => $this->emaildata->contact_email, 'Assunto' => $this->emaildata->contact_assunto]);
        $this->subject($this->emaildata->subject);
        return new Content(
            markdown: 'mail.reportarProblemaEmail',
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
        //Log::channel('slack')->info($this->emaildata->dados.' '.$this->emaildata->contact_name.' '.$this->emaildata->contact_email.' '.$this->emaildata->contact_assunto);
        //$this->to($this->emaildata->emaildestino, $this->emaildata->dados);
        //return $this->view('mail.formularioContatoEmail')->with(['emaildata' => $this->emaildata]);
        Tools::setLog(Tools::getUser(), 1, 0, 'Reportando Problema de Sistema', $this->emaildata->dados.' '.$this->emaildata->contact_name.' '.$this->emaildata->contact_email.' '.$this->emaildata->contact_assunto);
        return $this->markdown('mail.reportarProblemaEmail')->with(['emaildata' => $this->emaildata]);
    }*/
}
