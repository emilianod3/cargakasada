<?php

namespace App\Http\Controllers\Core;

use App\Models\CfgSist;
use App\Models\Config;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CfgSistController extends Controller
{
    public function getAllSession(){
        /*
        $query = Config::where('id', '>', 0);
        $registros = $query->get();     
        return json_encode($registros);*/
        $gestor = Tools::getGestor();
        $query = CfgSist::where('fkidgestor', $gestor)->with('config');
        $registros = $query->groupBy('fkidconfig')->get();
        //return json_encode($registros);
        return $registros;
    }


    public function getconfigs(Request $request)
    {
        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';
        $query = Config::leftjoin('cfgsist', function ($join) use ($request) {
            $join->on('config.id', '=', 'cfgsist.fkidconfig')->where('cfgsist.fkidgestor', '=', $request->idgestor); 
        })
        ->select(
            'config.id AS config_id',
            'config.identificacao',
            'config.exemplo',
            'config.tipodado',
            'config.status',
            'config.classificacao',
            'config.valor1',
            'config.valor2',
            'cfgsist.valor1 as cfgsist_valor1',
            'cfgsist.valor2 as cfgsist_valor2',
            'cfgsist.id AS cfgsist_id',
            'cfgsist.fkidgestor'
        );

        if(strlen($request->campopesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('config.identificacao', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('config.exemplo', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('config.valor1', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('config.valor2', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('config.id', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('cfgsist.valor1', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('cfgsist.valor2', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('cfgsist.id', 'like', '%' . $request->campopesquisa . '%');
        }else if(strlen($request->campopesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('config.identificacao', 'like', '%' . $request->campopesquisa . '%');
            $query->where('config.exemplo', 'like', '%' . $request->campopesquisa . '%');
            $query->where('config.valor1', 'like', '%' . $request->campopesquisa . '%');
            $query->where('config.valor2', 'like', '%' . $request->campopesquisa . '%');
            $query->where('config.id', 'like', '%' . $request->campopesquisa . '%');
            $query->where('cfgsist.valor1', 'like', '%' . $request->campopesquisa . '%');
            $query->where('cfgsist.valor2', 'like', '%' . $request->campopesquisa . '%');
            $query->where('cfgsist.id', 'like', '%' . $request->campopesquisa . '%');
        }        
        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'asc')->groupBy('config.id');

        try {
            $registros = $query->paginate($request->paginate);
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }        
    }

    public function setconfig(Request $request)
    {
        if($request->idgestor > 0){
            $reg = CfgSist::where('fkidgestor', $request->idgestor)->where('fkidconfig', $request->idconfig)->first();
            if($reg){
                $reg->delete();
                return Tools::setResponse('success', null, 'Aplicação Realizada com Sucesso');
            }else{
                $config = Config::where('id', $request->idconfig)->first();
                if($request->idgestor > 0 && $config){
                    $cfgsist = new CfgSist;
                    $cfgsist->fkidgestor = $request->idgestor;
                    $cfgsist->fkidconfig = $config->id;
                    $cfgsist->valor1 = $config->valor1;
                    $cfgsist->valor2 = $config->valor2;
                    $cfgsist->tranversao = Carbon::now()->toDateTimeString();
                    $cfgsist->transtatus = $config->status;
                    $cfgsist->flagdelete = 0;
                    $cfgsist->flagatualiza = 1;
                    $cfgsist->flagcontrole = $config->flagcontrole;
                    $cfgsist->flaguser = Session::get('user')->id;
                    if($cfgsist->save())
                    {
                        return Tools::setResponse('success', null, 'Aplicação Realizada com Sucesso');
                    }else {
                        return Tools::setResponse('fail', null, 'Não foi possível registrar, entre em contato com o Suporte');
                    }
                }
            }
        }else{
            return Tools::setResponse('fail', null, 'Não foi possível registrar, entre em contato com o Suporte');
        }    
    }

    public function updateitem(Request $request)
    {
        if($request->idregistro > 0){
            $registro = CfgSist::find($request->idregistro);
            if($registro && $request->campo == 'valor1'){
                $registro->valor1 = $request->value;
                if($registro->save())
                {
                    return Tools::setResponse('success', null, 'Aplicação Realizada com Sucesso');
                }else {
                    return Tools::setResponse('fail', null, 'Não foi possível Processar');
                }                
            }else if($registro && $request->campo == 'valor2'){
                $registro->valor2 = $request->value;
                if($registro->save())
                {
                    return Tools::setResponse('success', null, 'Aplicação Realizada com Sucesso');
                }else {
                    return Tools::setResponse('fail', null, 'Não foi possível Processar');
                }                 
            }else{
                return Tools::setResponse('fail', null, 'Não foi possível Processar');
            }
        }else{
            return Tools::setResponse('fail', null, 'Registro não Identificado');
        }
    }


    public function registraAtividade(Request $request)
    {
        $status = $request->input('status');
        $urlOrigem = $request->input('url');

        // Define a mensagem baseada no status do erro
        switch ($status) {
            case 419:
                $titulo = "Sessão Expirada (Token CSRF Inválido)";
                $descricao = "O usuário tentou interagir com o sistema, mas a sessão havia expirado na URL: {$urlOrigem}";
                $acaoId = 5;
                Log::warning("Sessão Expirada (419)", $descricao);
                break;
            case 401:
                $titulo = "Não Autenticado (401)";
                $descricao = "Usuário perdeu as credenciais de autenticação na URL: {$urlOrigem}";
                $acaoId = 9;
                Log::warning("Usuário Não Autenticado (401)", $descricao);
                break;
            case 500:
                $titulo = "Erro Interno do Servidor (500)";
                $descricao = "O sistema disparou uma falha interna (Código 500) enquanto o usuário navegava em: {$urlOrigem}";
                $acaoId = 9; // Ex: ID para Erros Críticos do Sistema
                Log::error("Erro Interno do Servidor (500)", $descricao);
                break;
            default:
                $titulo = "Erro Inesperado ({$status})";
                $descricao = "Ocorreu um erro inesperado do servidor na URL: {$urlOrigem}";
                $acaoId = 9;
                Log::notice("Erro inesperado detectado ({$status})", $descricao);
                break;
        }

        
        Tools::setAtividade(0, $acaoId, 0, $titulo, $descricao);

        return response()->json(['success' => true]);
    }    

    /*
    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    $qry = CfgSist::where('fkidgestor', $gestor);
                    //$qry = CfgSist::where('id','>',0);
                }else{
                    //$qry = Profissao::where('id','<',0);
                    return Tools::setResponse('fail', null, 'Impossível Processar');
                }
            }else{
                if($gestor > 0){
                    $qry = CfgSist::where('fkidgestor', $gestor);
                    //$qry = CfgSist::where('id','>',0);
                }else{
                    return Tools::setResponse('fail', null, 'Impossível Processar');
                }
            }
        }
        return $qry;
    }

    public function lista(Request $request)
    {
        $query = self::initQuery();

        if($request->statusfiltro == 0) {
            $query->where('transtatus','>=', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('transtatus', 1);
        }else if($request->statusfiltro == 2) {
            $query->where('transtatus', 0);
        }

        if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('versao', '>=', $request->datainiciofiltro.' 00:00:00');
        }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('versao', '>=', $request->datainiciofiltro.' 00:00:00');
            $query->where('versao', '<=', $request->datafinalfiltro.' 23:59:59');                
        }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('versao', '<=', $request->datafinalfiltro.' 23:59:59'); 
        }

        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';

        if(strlen($request->campopesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('identificacao', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('exemplo', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('valor1', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('valor2', 'like', '%' . $request->campopesquisa . '%');
        }else if(strlen($request->campopesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('identificacao', 'like', '%' . $request->campopesquisa . '%');
            $query->where('exemplo', 'like', '%' . $request->campopesquisa . '%');
            $query->where('valor1', 'like', '%' . $request->campopesquisa . '%');
            $query->where('valor2', 'like', '%' . $request->campopesquisa . '%');
        }

        $query->with('config');
        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'asc')->groupBy('id');

        try {
            $registros = $query->paginate($request->paginate);
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }


    public function getAll($campoOrdem ='identificacao', $ordem = 'asc')
    {
        $registros = $query = Config::orderBy($campoOrdem, $ordem)->get();
        return $registros;
        //return json_encode($registros);
    }

    public function getId($id)
    {
        $registro = Config::find($id);
        return $registro;
    }



    public function removerId($id)
    {
        $reg = Config::find($id);
		if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
			return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
		}        
        try {
            $reg->status = 0;
            $reg->flagexibe = 0;
            $reg->versao = Carbon::now()->toDateTimeString();
            if($reg->save()) {
                return Tools::msgpadrao(true, 'delete');
            }else{
                return Tools::msgpadrao(false, 'delete');
            }
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::msgpadrao(false, 'delete');
        } 
    }*/


}
