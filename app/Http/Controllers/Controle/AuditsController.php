<?php

namespace App\Http\Controllers\Controle;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Controllers\Core\Tools;
use App\Models\Audits;
use Illuminate\Support\Carbon;

class AuditsController extends Controller
{
    const INFOMOD = "Auditoria";

    public function inicio()
    {
        $gestor = Tools::getGestor();
        return view('controle.auditoria', ['cliente' => $gestor]);
    }


    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    //$qry = Audits::where('fkidgestor', $gestor);
                    $qry = Audits::where('id','>',0);
                }else{
                    //$qry = Audits::where('id','<',0);
                    return Tools::setResponse('fail', null, 'Impossível Processar');
                }
            }else{
                if($gestor > 0){
                    //$qry = Audits::where('fkidgestor', $gestor);
                    $qry = Audits::where('id','>',0);
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

        /*
        if($request->statusfiltro == 0) {
            $query->where('rstatus','>=', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('rstatus', 1);
        }else if($request->statusfiltro == 2) {
            $query->where('rstatus', 0);
        }*/


        if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('created_at', '>=', $request->datainiciofiltro.' 00:00:00');
        }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('created_at', '>=', $request->datainiciofiltro.' 00:00:00');
            $query->where('created_at', '<=', $request->datafinalfiltro.' 23:59:59');                
        }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('created_at', '<=', $request->datafinalfiltro.' 23:59:59'); 
        }

        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';

        if(strlen($request->campoPesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('user_type', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('user_id', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('auditable_type', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('auditable_id', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('old_values', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('new_values', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('url', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('ip_address', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('user_agent', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('tags', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('created_at', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('updated_at', 'like', '%' . $request->campoPesquisa . '%');
        }else if(strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('user_type', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('user_id', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('auditable_type', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('auditable_id', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('old_values', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('new_values', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('url', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('ip_address', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('user_agent', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('tags', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('created_at', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('updated_at', 'like', '%' . $request->campoPesquisa . '%');
        }

        //$query->with('zeladoria','reservaitem','formapagamento','unico');
        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc')->groupBy('id');

        try {
            $registros = $query->paginate($request->regPg);
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }


    
    public function auditorialistauser(Request $request)
    {
        try {
            if($request->idusuario > 0) {
            $query = self::initQuery();
            $query->where('user_id', $request->idusuario);

            /*
            if($request->statusfiltro == 0) {
                $query->where('rstatus','>=', 0);
            }else if($request->statusfiltro == 1) {
                $query->where('rstatus', 1);
            }else if($request->statusfiltro == 2) {
                $query->where('rstatus', 0);
            }*/

            if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
                $query->where('created_at', '>=', $request->datainiciofiltro.' 00:00:00');
            }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
                $query->where('created_at', '>=', $request->datainiciofiltro.' 00:00:00');
                $query->where('created_at', '<=', $request->datafinalfiltro.' 23:59:59');                
            }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
                $query->where('created_at', '<=', $request->datafinalfiltro.' 23:59:59'); 
            }

            $campoordenar = 'id';
            $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';

            if(strlen($request->campoPesquisa) > 0) {  //$request->tipofiltro == 'amplo'
                $query->where('user_type', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('user_id', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('auditable_type', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('auditable_id', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('old_values', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('new_values', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('url', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('ip_address', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('user_agent', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('tags', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('created_at', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('updated_at', 'like', '%' . $request->campoPesquisa . '%');
            }else if(strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {  
                $query->where('user_type', 'like', '%' . $request->campoPesquisa . '%');
                $query->where('user_id', 'like', '%' . $request->campoPesquisa . '%');
                $query->where('auditable_type', 'like', '%' . $request->campoPesquisa . '%');
                $query->where('auditable_id', 'like', '%' . $request->campoPesquisa . '%');
                $query->where('old_values', 'like', '%' . $request->campoPesquisa . '%');
                $query->where('new_values', 'like', '%' . $request->campoPesquisa . '%');
                $query->where('url', 'like', '%' . $request->campoPesquisa . '%');
                $query->where('ip_address', 'like', '%' . $request->campoPesquisa . '%');
                $query->where('user_agent', 'like', '%' . $request->campoPesquisa . '%');
                $query->where('tags', 'like', '%' . $request->campoPesquisa . '%');
                $query->where('created_at', 'like', '%' . $request->campoPesquisa . '%');
                $query->where('updated_at', 'like', '%' . $request->campoPesquisa . '%');
            }

            $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc')->groupBy('id');
            $registros = $query->paginate($request->regPg);
            return Tools::setResponse('success', $registros, '');

            }
        
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }  
    }
    

    public function salvar(Request $request)
    {
        $validator = Validator::make(
            [
            'user_type' => $request->user_type,
            ],
            [
            'user_type' => 'required|string|min:1|max:180',
            ],
            [
            'user_type.required' => 'Necessário Informar a identificação',
            'user_type.min' => 'Necessário Informar a identificação',
            'user_type.max' => 'Informe um Conteúdo Válido',
            ]
        );

        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        } else {
            //DB::beginTransaction();
            if($request->id > 0) {
                $reg = Audits::find($request->id);
            } else {
                $reg = new Audits();
                //$reg->rdatacadastro = Carbon::now()->toDateTimeString();
            }
            $reg->user_type = $request->user_type;
            //$reg->rtcnaesubclasse = $request->rtcnaesubclasse;
            $reg->fkidgestor = Tools::getGestor(0);
            //$reg->rstatus = $request->fkstatus > 0 ? $request->fkstatus : 0;
            $reg->created_at = Carbon::now()->toDateTimeString();
            //$reg->flagexibe = $request->flagexibe == 'true' ? 1 : 0;
            $reg->flagdelete = 0;
            $reg->flagatualiza = 1;
            $reg->flaguser = Session::get('user')->id;
            //$vv = DB::commit();
            if($reg->save()) {
                return Tools::setResponse('success', null, 'Registro Salvo com Sucesso');
            } else {
                //DB::rollBack();
                return Tools::setResponse('fail', null, 'Não foi possível cadastrar, entre em contato com o Suporte.');
            }
        }
    }

    public function get($id)
    {
        $reg = Audits::where('id',$id)->first();
        return $reg;
    }


    public function removerId($id)
    {
        $reg = Audits::find($id);
        if($reg->delete()) {
            return 'true';
        } else {
            return 'false';
        }
    }


    public function getall()
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    $query = Audits::where('fkidgestor', $gestor);
                }else{
                    //$query = FormaPagamento::where('id','>',0);
                    return Tools::setResponse('fail', null, 'Impossível Processar');
                }
            }else{
                if($gestor > 0){
                    $query = Audits::where('fkidgestor', $gestor);
                }else{
                    return Tools::setResponse('fail', [], 'Impossível Listar');
                }
            }
            //$query->where('rstatus','>','0');
            $query->groupBy('id');
            $query->orderBy('user_type', 'asc');
            //$registros = json_encode($query->get());
            $registros = $query->get();
    
            try {
                return Tools::setResponse('success', $registros, '');
            } catch (Exception $e) {
                return Tools::setResponse('fail', [], 'Falha ao obter dados');
            }
        }
    }
}
