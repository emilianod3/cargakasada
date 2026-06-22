<?php

namespace App\Http\Controllers\Core;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use stdClass;
use App\Http\Controllers\Core\Tools;
use App\Mail\envioEmailContato;
use Inertia\Inertia;

class MailController extends Controller
{
    public function view()
    {  
        if(Tools::getUser() > 0){
            //return view('ajuda.contato'); 
            return Inertia::render('Ajuda/Contato');
        }else{
            //return view('ajuda.contato'); 
            return Inertia::render('Ajuda/Contato');
        }    
    }

    public function send(Request $request)
    {
        $validator = Validator::make(
        [
            'contact_name' => $request->contact_name,
            'contact_email' => $request->contact_email,             
            'contact_assunto' => $request->contact_assunto,
            'contact_message' => $request->contact_message,
        ]
        , [
            'contact_name' => 'required|string|min:7',
            'contact_email' => 'required|email|min:5',
            'contact_assunto' => 'required|string|min:15',
            'contact_message' => 'required|string|min:20',
            
        ],
        [
            'contact_name.required' => 'Necessário Informar um Nome',
            'contact_name.string' => 'Necessário Informar um Nome Válido',
            'contact_name.min' => 'Necessário Informar um Nome Válido',
            'contact_email.required' => 'Necessário Informar o E-mail',
            'contact_email.email' => 'Necessário Informar um e-mail Válido',
            'contact_email.min' => 'Necessário Informar um e-mail Válido',       
            'contact_assunto.required' => 'Necessário Informar o Assunto',
            'contact_assunto.string' => 'Necessário Informar o Assunto',
            'contact_assunto.min' => 'Necessário Informar o Assunto',
            'contact_message.required' => 'Necessário Informar a Mensagem',
            'contact_message.string' => 'Necessário Informar a Mensagem',
            'contact_message.min' => 'Necessário Informar a Mensagem',                       
        ]);
        
        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        } else {
                $emaildata = new stdClass();
                $emaildata->subject = 'Formulário de Contato '.ENV('SISTEMA_TITULO');
                $emaildata->dados = 'Mensagem Originada de '.ENV('SISTEMA_TITULO');
                $emaildata->emaildestino = env('EMAIL_FALECONOSCO');
                $emaildata->emailorigem = env('EMAIL_FALECONOSCO');
                $emaildata->contact_name = $request->contact_name;
                $emaildata->contact_email = $request->contact_email;
                $emaildata->contact_assunto = $request->contact_assunto;
                $emaildata->contact_message = $request->contact_message;
                Mail::to(env('EMAIL_FALECONOSCO'))->send(new envioEmailContato($emaildata));
                //return response()->json($this->resource->setResponse('success', [], 'Agradecemos seu Contato'));
                Tools::setAtividade(Tools::getUser(), 1, 0,  'Formulário de Contato', 'Formulário de Contato do Sistema');
                return Tools::setResponse('success', null, 'Formulário de Contato'); 
        }
    }

}
