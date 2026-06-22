<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\Tools;
use App\Mail\reportarProblema;
use App\Models\Tipodoc;
use App\Models\Processo;
use App\Models\ProcessoTramite;
use App\Models\ProcessoTramiteDestino;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mail;
use stdClass;

class ProblemaController extends Controller
{

    public function reporteproblema(Request $request)
    {
        $validator = Validator::make(
        [
            'nome' => $request->contact_name,
            'email' => $request->contact_email,
            'assunto' => $request->contact_assunto,
            'mensagem' => $request->contact_message,
        ]
        , [
            'nome' => 'required|min:1',
            'email' => 'required|min:1',
            'assunto' => 'required|string|min:1',
            'mensagem' => 'required|min:1',
        ],
        [
            'nome.required' => 'Necessário Informar o Nome',
            'nome.min' => 'Necessário Informar o Nome',
            'email.required' => 'Necessário Informar o E-mail',
            'email.min' => 'Necessário Informar o E-mail',
            'assunto.required' => 'Necessário Informar o Assunto',
            'assunto.string' => 'Necessário Informar o Assunto',
            'assunto.min' => 'Necessário Informar o Assunto',
            'mensagem.required' => 'Necessário Informar a Mensagem',
            'mensagem.min' => 'Necessário Informar a Mensagem',
        ]);

        if ($validator->fails()) {
            //return Tools::setResponse('fail', null, $validator->errors()->first());
            return false;
        } else {
            $result = true;
            if(ENV('REPORTAR_PROBLEMA_ABRE_OUVIDORIA') == 1){            
                try {
                    $reg = new Processo;
                    $reg->pdatacadastro = Carbon::now()->toDateTimeString();
                    $reg->pidentificacao = $request->contact_assunto != null ? $request->contact_assunto : '';
                    //$reg->fkidtiposituacao = $request->fkidsituacao != null ? $request->fkidsituacao : 0;
                    $reg->fkidtipodoc = 20000; // ordem de serviço
                    $reg->pstatus = 0;
                    $reg->prascunho = 0;
                    $reg->pconteudo = $request->contact_message != null ? $request->contact_message : '';
                    $reg->fkiddivisaoorigem = 3;//Externo
                    //$reg->fkiduserorigem = $request->userorigem;//buscar dados de origem
                    if($reg->iniciotramitacao == null) {
                        $tipodoc = self::getTipoDoc(20000);
                        $reg->iniciotramitacao = Carbon::now()->toDateTimeString();
                        $reg->pnumero = self::getProximoNumeroProcesso($tipodoc);
                        $reg->ptiponumeracao = $tipodoc->tpdcnumeracao;
                        $reg->pnumeracao = Tools::geraNumeracao($reg->pnumero, $tipodoc->tpdclocalcaractere, $tipodoc->tpdcopcional, $tipodoc->tpdcnumerocasas, $tipodoc->tpdccaractere, $tipodoc->tpdcnumeracao, $tipodoc->tpdcseparador, $reg->iniciotramitacao, null);
                    }
                    
                    $reg->fkidgestor = ENV('GESTOR');
                    $reg->pversao = Carbon::now()->toDateTimeString();
                    $reg->flagexibe = 0;
                    $reg->flagdelete = 0;
                    $reg->flagatualiza = 1;
                    $reg->flaguser = 0;
                    $reg->save();
                    
                    if($reg->id > 0){
                        $reg1 = new ProcessoTramite();
                        $reg1->ptdatacadastro = Carbon::now()->toDateTimeString();
                        $reg1->fkidtipodoc = $reg->fkidtipodoc;
                        $reg1->fkidprocesso = $reg->id;
                        $reg1->ptassunto = $reg->pidentificacao;
                        $reg1->ptexto = $reg->pconteudo;
                        $reg1->ptordem = self::getProximoOrdemTramite($reg->id);
                        /*
                        if($reg->iniciotramitacao == null) {
                            $reg1->ptordem = self::getProximoOrdemTramite($reg->id);
                        }*/
                        $reg1->pttiporegistro = 0;
                        $reg1->ptrascunho = 0;
                        $reg1->fkiddivisaoorigem = $reg->fkiddivisaoorigem;
                        //$reg1->fkiduserorigem = $request->userorigem;
                        $reg1->fkidgestor = $reg->fkidgestor;
                        $reg1->ptversao = Carbon::now()->toDateTimeString();
                        $reg1->flagexibe = $reg->flagexibe;
                        $reg1->flagdelete = 0;
                        $reg1->flagatualiza = 1;
                        $reg1->flaguser = $reg->flaguser;
                        $reg1->save();
                        if($reg1->id > 0 ){
                            $reg2 = new ProcessoTramiteDestino();
                            $reg2->fkidprocessotramite = $reg1->id;
                            $reg2->fkiddivisaodestino = ENV('DIVISAORECEBEOUVIDORIA');
                            //$reg2->fkiduserdestino = $request->membrodestino > 0 ? $request->membrodestino : 0;
                            $reg2->fkiddivisaoorigem = $reg->fkiddivisaoorigem;
                            //$reg2->fkiduserorigem = $request->userorigem;
                            $reg2->ptdsigilo = 0;
                            //$reg2->ptdprioridade = $request->prioridade;
                            //$reg2->ptdprazoaguardandoresposta = $request->prazoresposta;
                            $reg2->ptddatacadastro = Carbon::now()->toDateTimeString();
                            $reg2->fkidgestor = $reg->fkidgestor;
                            $reg2->ptdversao = Carbon::now()->toDateTimeString();
                            $reg2->flagexibe = $reg->flagexibe;
                            $reg2->flagdelete = 0;
                            $reg2->flagatualiza = 1;
                            $reg2->flaguser = $reg->flaguser;
                            $reg2->save();
                        }
                    }
                        Tools::setAtividade(0, 8, 0, 'Reportar Problema', 'Criação de Ouvidoria ao Reportar Problema Nome:'.$request->contact_name.' e-Mail: '.$request->contact_email);
                        //$result = true;
                } catch (\Exception $e) {
                    $except = $e->getMessage();
                    //return Tools::setResponse('fail', null, 'Não foi possível Processar');
                    Tools::setAtividade(0, 8, 0, 'Reportar Problema', 'Falha na Criação de Ouvidoria - Reportar Problema');
                    $result = false;
                }
            }


            if(ENV('ENVIOEMAIL') == 1 && strlen(ENV('EMAIL_REPORTAR_PROBLEMA')) > 4){
                try {
                    $emaildata = new stdClass();
                    $emaildata->subject = 'Formulário de Comunicado de Problema '.ENV('APP_NAME');
                    $emaildata->dados = 'Comunicado de Problema Originado de '.ENV('APP_NAME');
                    $emaildata->emaildestino = env('EMAIL_REPORTAR_PROBLEMA');
                    $emaildata->emailorigem = env('EMAIL_REPORTAR_PROBLEMA');
                    $emaildata->contact_name = $request->contact_name;
                    $emaildata->contact_email = $request->contact_email;
                    $emaildata->contact_assunto = $request->contact_assunto;
                    $emaildata->contact_message = $request->contact_message;
                    Mail::to(env('EMAIL_REPORTAR_PROBLEMA'))->send(new reportarProblema($emaildata));
                    //return response()->json($this->resource->setResponse('success', [], 'Agradecemos seu Contato'));
                    Tools::setAtividade(0, 8, 0, 'Reportando Problema de Sistema', 'Formulário de Contato "Reportar Problema" Nome:'.$request->contact_name.' e-Mail: '.$request->contact_email);
                    //return Tools::setResponse('success', null, 'Formulário de Contato');
                } catch (\Exception $e) {
                    $except = $e->getMessage();
                    //return Tools::setResponse('fail', null, 'Não foi possível Processar');
                    Tools::setAtividade(0, 8, 0, 'Reportar Problema', 'Falha no Envio de E-mail - '.$except);
                    $result = false;
                }
            }

            if($result == false){
                return back()->withErrors(Tools::setResult('fail', null, 'Falha no Processamento de Envio'));
            }else{
                return back()->with(Tools::setResult('success', null, 'Dados Enviados com Sucesso'));
            }            
        }
    }



    private function getTipoDoc($idtipodoc){
        $tipodoc = Tipodoc::where('id', $idtipodoc)->first();
        return $tipodoc;
    }


    private function getProximoOrdemTramite($idprocesso){
        $gestor = ENV('GESTOR');
        $ordemtramite = ProcessoTramite::where('fkidprocesso', $idprocesso)->where('fkidgestor', $gestor)->max('ptordem');
        if($ordemtramite > 0 && $ordemtramite != null){
            $num = $ordemtramite;
            return $num+1;
        }else{
            return 0;
        }
    }

    private function getProximoNumeroProcesso($tipodoc){
        
        //$tipodoc = self::getTipoDoc($idtipodoc);
        $gestor = ENV('GESTOR');
        /*switch ($num) {
            case 1:
                return "Sequencial ao Infinito";
                break;
            case 2:
                return "Sequencial Zerando todo Ano";
                break;
            case 3:
                return "Sequencial ao Infinito + Ano";
                break;
            case 4:
                return "Sequencial Zerando todo Ano + Ano";
                break;
            case 5:
                return "Sequencial ao Infinito + Legislatura";
                break;
            case 6:
                return "Sequencial Zerando na Legislatura";
                break;
            case 7:
                return "Sequencial Zerando na Legislatura + Legislatura";
                break;
            default:
                return "Sequencial ao Infinito";
        }*/
        $from = date(Carbon::now()->year.'-01-01');
        $to = date(Carbon::now()->year.'-12-31');

        $processo = null;
        if($tipodoc->tpdcnumeracao == 1 || $tipodoc->tpdcnumeracao == 3 || $tipodoc->tpdcnumeracao == 5){
            $processo = Processo::where('fkidtipodoc', $tipodoc->id)->where('fkidgestor', $gestor)->where('prascunho', 0)->max('pnumero');
        }else if($tipodoc->tpdcnumeracao == 2 || $tipodoc->tpdcnumeracao == 4){
            //$processo = Processo::where('fkidtipodoc', $idtipodoc)->where('fkidgestor', $gestor)->where('iniciotramitacao', '>=', $from)->where('iniciotramitacao', '<=', $to)->where('prascunho', 0)->max('pnumero');
            $processo = Processo::where('fkidtipodoc', $tipodoc->id)->where('fkidgestor', $gestor)->whereBetween('iniciotramitacao', [$from, $to])->where('prascunho', 0)->max('pnumero');
        }else{
            $processo = Processo::where('fkidtipodoc', $tipodoc->id)->where('fkidgestor', $gestor)->where('prascunho', 0)->max('pnumero');
        }

        
        if($processo > 0){
            $num = $processo;
            return $num+1;
        }else{
            return 1;
        }
    }    

}
