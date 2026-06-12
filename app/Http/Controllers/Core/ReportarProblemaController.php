<?php

namespace App\Http\Controllers\Core;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use stdClass;
use App\Mail\reportarProblema;
use App\Http\Controllers\Core\Tools;

class ReportarProblemaController extends Controller
{
    public function view()
    {
        return view('ajuda.reportarproblema');        
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
            $emaildata->subject = 'Formulário de Comunicado de Problema '.ENV('APP_NAME').' - '.ENV('SISTEMA_TITULO');
            $emaildata->dados = 'Comunicado de Problema Originado de '.ENV('APP_NAME').' - '.ENV('SISTEMA_TITULO');
            $emaildata->emaildestino = env('EMAIL_REPORTAR_PROBLEMA');
            $emaildata->emailorigem = env('EMAIL_REPORTAR_PROBLEMA');
            $emaildata->contact_name = $request->contact_name;
            $emaildata->contact_email = $request->contact_email;
            $emaildata->contact_assunto = $request->contact_assunto;
            $emaildata->contact_message = $request->contact_message;
            Mail::to(env('EMAIL_REPORTAR_PROBLEMA'))->send(new reportarProblema($emaildata));
            //return response()->json($this->resource->setResponse('success', [], 'Agradecemos seu Contato'));
            Tools::setAtividade(Tools::getUser(), 1, 0,  'Reportando Problema de Sistema', 'Formulário de Contato "Reportar Problema"');
            return Tools::setResponse('success', null, 'Formulário Enviado com Sucesso');
        }
    }




/*
    public function enviarParaEmail(Request $request)
    {
        $validator = Validator::make(
        [
            'envioemaildestino' => $request->envioemaildestino,
            'envioemailnome' => $request->envioemailnome,
            'envioemailorigem' => $request->envioemailorigem,
        ],
        [            
            'envioemaildestino'=> 'required|email',
            'envioemailnome' => 'required|string|min:5',
            'envioemailorigem'=> 'required|email',
        ],
        [
            'envioemaildestino.required' => 'Necessário Informar o E-mail',
            'envioemaildestino.email' => 'Necessário Informar um e-mail válido',
            'envioemailnome.required' => 'Necessário Informar Seu Nome',
            'envioemailnome.string' => 'Necessário Informar um Nome Válido',
            'envioemailnome.min' => 'Necessário Informar um Nome Válido',
            'envioemailorigem.required' => 'Necessário Informar o E-mail',
            'envioemailorigem.email' => 'Necessário Informar um e-mail válido',
        ]);

        if ($validator->fails()) {
            return [
                'status' => 'fail',
                'element' => $validator->errors(),
                'messagetop' => 'Falha na Validação dos dados',
                'message' => $validator->errors()->first()
            ];
        }
        
        return [
            'status' => 'success',
            'element' => $validator->errors(),
            'messagetop' => 'Formulário Enviado com Sucesso',
            'message' => 'Agradecemos seu Contato'
        ];
       
    }

     */

}
