<?php

namespace App\Http\Controllers\Controle;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\Tools;
use App\Models\ConfigUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;

class ConfigUserController extends Controller
{

    /*
    public function inicio()
    {
        return view('geral.configusuario');
    }*/



    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    //$qry = Config::where('fkidgestor', $gestor);
                    $qry = ConfigUser::where('id','>',0);
                }else{
                    //$qry = Profissao::where('id','<',0);
                    return Tools::setResponse('fail', null, 'Impossível Processar');
                }
            }else{
                if($gestor > 0){
                    //$qry = Config::where('fkidgestor', $gestor);
                    $qry = ConfigUser::where('id','>',0);
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
            $query->where('status','>=', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('status', 1);
        }else if($request->statusfiltro == 2) {
            $query->where('status', 0);
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

        if($request->idprincipal > 0) {
            $query->where('fkidconfigforuser', $request->idprincipal); 
        }
        /*if(strlen($request->campopesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('identificacao', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('exemplo', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('valor1', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('valor2', 'like', '%' . $request->campopesquisa . '%');
        }else if(strlen($request->campopesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('identificacao', 'like', '%' . $request->campopesquisa . '%');
            $query->where('exemplo', 'like', '%' . $request->campopesquisa . '%');
            $query->where('valor1', 'like', '%' . $request->campopesquisa . '%');
            $query->where('valor2', 'like', '%' . $request->campopesquisa . '%');
        }*/

        $query->with('usuario', 'configforuser');
        //$query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'asc')->groupBy('id');
        if($campoordenar == 'ulogin'){
            $query->with(['usuario' => function($query) {
                $query->orderBy('usuario.ulogin', 'asc');
            }]);
        }else{
            $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc');
        }

        try {
            $registros = $query->paginate($request->regPg);
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }

    public function getConfigUser1($idUser, $idCfgForUser){
        /*$registros = $query = ConfigUser::where('status','>',0)->get();
        $config = $registros;
        Session::put('config', $config);*/

        $query = ConfigUser::leftjoin('configforuser', 'configuser.fkidconfigforuser', '=', 'configforuser.id')
        ->select([DB::raw('configforuser.id, configforuser.identificacao, configforuser.tipodado, configforuser.status,
        configforuser.classificacao, configforuser.valor1, configforuser.valor2, configuser.fkidcal,
        configuser.fkidusuario, configuser.fkidconfigforuser')]);
    
        $query->where('configforuser.status','>',0)
            ->where('configuser.status','>',0)
            ->where('configforuser.id',$idCfgForUser)
            ->where('configuser.fkidusuario',$idUser);
        $registro = $query->first(); 
        return $registro;
    }

    public function getAllSession(){

        $query = ConfigUser::leftjoin('configforuser', 'configuser.fkidconfigforuser', '=', 'configforuser.id')
        ->select([DB::raw('configforuser.id, configforuser.identificacao, configforuser.tipodado, configforuser.status,
        configforuser.classificacao, configforuser.valor1, configforuser.valor2, configuser.fkidcal,
        configuser.fkidusuario, configuser.fkidconfigforuser, configuser.id as configuserid')]);
        $registros = $query->groupBy('configuser.fkidusuario', 'configforuser.id')->get();     
        return json_encode($registros);
    }     

    public function getConfigsUser($idUser, $idCfgForUser){
        $query = ConfigUser::leftjoin('configforuser', 'configuser.fkidconfigforuser', '=', 'configforuser.id')
        ->select([DB::raw('configforuser.id, configforuser.identificacao, configforuser.tipodado, configforuser.status,
        configforuser.classificacao, configforuser.valor1, configforuser.valor2, configuser.fkidcal,
        configuser.fkidusuario, configuser.fkidconfigforuser')]);
    
        $query->where('configforuser.status','>',0)
            ->where('configuser.status','>',0)
            ->where('configforuser.id',$idCfgForUser)
            ->where('configuser.fkidusuario',$idUser);
        $registro = $query->first(); 
        return $registro;
    }   

    public function getAll($campoOrdem ='versao', $ordem = 'desc')
    {
        $registros = $query = ConfigUser::orderBy($campoOrdem, $ordem)->get();
        return $registros;
    }

    public function getconfiguser(Request $request)
    {
        try {
            if($request->idusuario > 0) {
            $query = ConfigUser::where('configuser.fkidusuario',$request->idusuario);
            $query->with('configforuser');
            $query->whereRelation('configforuser', 'status', '>', 0);

            /*$query->with(['configforuser' => function($query) {
                $query->where('configforuser.status','>', 0);
            }]);*/


            $query->orderBy('configuser.id','asc')->groupBy('id');
            $registros = $query->paginate($request->regPg);
            
            return Tools::setResponse('success', $registros, '');
            }

        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }        
        
    }

    public function getId($id)
    {
        $registro = ConfigUser::where('id', $id)->first();
        //return json_encode($registro);
        return $registro;
    }

    public function getIdCompleto($id)
    {
        $registro = ConfigUser::where('id', $id)->first();
        return $registro;
    }

    /*
    public function listar($regPg =5, $campoOrdem ='unidentificacao', $ordem = 'asc', $campoPesquisa = '')
    {
        $query = Usu::leftjoin('configforuser', 'configuser.fkidconfigforuser', '=', 'configforuser.id')
        ->leftjoin('usuario', 'configuser.fkidusuario', '=', 'usuario.id')
        ->leftjoin('unico', 'usuario.fkidunico', '=', 'unico.id')
        ->select([DB::raw('configuser.id, configuser.fkidcal, configuser.fkidusuario, configuser.fkidconfigforuser, 
        configuser.status, configuser.versao, configuser.flagdelete, configuser.flagatualiza, configuser.flagexibe, configuser.flaguser,
        configforuser.identificacao, configforuser.exemplo, configforuser.tipodado, configforuser.classificacao, configforuser.valor1, 
        configforuser.valor2, usuario.ulogin, unico.unidentificacao, unico.unapelido')]);
        if(strlen($campoPesquisa) > 0) {
            $query->where('configforuser.identificacao', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('configforuser.exemplo', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('configforuser.valor1', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('configforuser.valor2', 'like', '%' . $campoPesquisa . '%');
        }
        $query->orderBy($campoOrdem, $ordem);
        $registros = $query->paginate($regPg);
        return $registros;
    }*/

    public function getPorConfig($idCfgForUser)
    {
        $registros = null;
        if($idCfgForUser > 0) {
            $query = ConfigUser::leftjoin('configforuser', 'configuser.fkidconfigforuser', '=', 'configforuser.id')
            ->leftjoin('usuario', 'configuser.fkidusuario', '=', 'usuario.id')
            ->leftjoin('unico', 'usuario.fkidunico', '=', 'unico.id')
            ->select([DB::raw('configuser.id, configuser.fkidcal, configuser.fkidusuario, configuser.fkidconfigforuser, 
            configuser.status, configuser.versao, configuser.flagdelete, configuser.flagatualiza, configuser.flagexibe, configuser.flaguser,
            configforuser.identificacao, configforuser.exemplo, configforuser.tipodado,
            configforuser.status as statusconfforuser, configforuser.classificacao, configforuser.valor1, configforuser.valor2,
            usuario.ulogin, unico.unidentificacao, unico.unapelido')]);        
            $query->where('configuser.fkidconfigforuser', $idCfgForUser);        
            $registros = $query->orderBy('unico.unidentificacao', 'asc')->get();
        }
        return $registros;
    }

    public function getConfigForUser($idUser)
    {
        $registros = null;
        if($idUser > 0) {
            $query = ConfigUser::leftjoin('configforuser', 'configuser.fkidconfigforuser', '=', 'configforuser.id')
            ->leftjoin('usuario', 'configuser.fkidusuario', '=', 'usuario.id')
            ->leftjoin('unico', 'usuario.fkidunico', '=', 'unico.id')
            ->select([DB::raw('configuser.id, configuser.fkidcal, configuser.fkidusuario, configuser.fkidconfigforuser, 
            configuser.status, configuser.versao, configuser.flagdelete, configuser.flagatualiza, configuser.flagexibe, configuser.flaguser,
            configforuser.identificacao, configforuser.exemplo, configforuser.tipodado,
            configforuser.status as statusconfforuser, configforuser.classificacao, configforuser.valor1, configforuser.valor2,
            usuario.ulogin, unico.unidentificacao, unico.unapelido')]);        
            $query->where('configuser.fkidusuario', $idUser);        
            $registros = $query->orderBy('unico.unidentificacao', 'asc')->get();
        }
        return $registros;
    }

    public function verificarJaExiste($idCfgForUser, $idUser)
    {
        $query = ConfigUser::where('fkidconfigforuser', $idCfgForUser)
        ->where('fkidusuario', $idUser);        
        $registro = $query->first();        
        return $registro != null ? true : false;
    }



    public function removerId($id)
    {
        $reg = ConfigUser::find($id);
        try {
            if($reg->delete()) {
                return Tools::msgpadrao(true, 'delete');
            }else{
                return Tools::msgpadrao(false, 'delete');
            }
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::msgpadrao(false, 'delete');
        } 
    }



    public function salvar(Request $request)
    {
        $validator = Validator::make(
            [ 
                //'fkidcal' => $request->fkidcal,
                'fkidusuario' => $request->fkidusuario,
                'fkidconfigforuser' => $request->fkidconfigforuser,
            ]
            , [
                //'fkidcal' => 'required|integer|min:1',
                'fkidusuario' => 'required|integer|min:1',
                'fkidconfigforuser' => 'required|integer|min:1', 
             ],
            [
                //'fkidcal.required' => 'Necessário Informar a Cal',
                //'fkidcal.integer' => 'Necessário Informar a Cal',
                //'fkidcal.min' => 'Informe o Registro',
                'fkidusuario.required' => 'Necessário Informar o Usuário',
                'fkidusuario.integer' => 'Necessário Informar o Usuário',
                'fkidusuario.min' => 'Necessário Informar o Usuário',
                'fkidconfigforuser.required' => 'Necessário Informar a Configuração',
                'fkidconfigforuser.integer' => 'Necessário Informar a Configuração',
                'fkidconfigforuser.min' => 'Necessário Informar a Configuração',                
            ]);

        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            if($request->get('id') > 0)
            {
                $reg = ConfigUser::find($request->get('id'));
                $reg->fkidcal = Tools::ifnull($request->fkidcal);
                $reg->fkidusuario = Tools::ifnull($request->fkidusuario) ;
                $reg->fkidconfigforuser = Tools::ifnull($request->fkidconfigforuser);
                $reg->versao = Carbon::now()->toDateTimeString();
                $reg->flagexibe = 1;
                $reg->status = $request->status;
                $reg->flagdelete = 0;
                $reg->flagatualiza = 1;
                $reg->flaguser = Session::get('user')->id;
    
                if($reg->save())
                {
                    return Tools::setResponse('success', null, 'Registro salvo com Sucesso');
                }else {
                    return Tools::setResponse('fail', null, 'Não foi possível cadastrar, entre em contato com o Suporte');
                }
            }
            else
            {
                if(self::verificarJaExiste($request->fkidconfigforuser, $request->fkidusuario)){
                    return Tools::setResponse('fail', null, 'Configuração já existe para o Usuário');
                }else{
                    $reg = new ConfigUser;
                    $reg->fkidcal = Tools::ifnull($request->fkidcal);
                    $reg->fkidusuario = Tools::ifnull($request->fkidusuario) ;
                    $reg->fkidconfigforuser = Tools::ifnull($request->fkidconfigforuser);
                    $reg->versao = Carbon::now()->toDateTimeString();
                    $reg->flagexibe = 1;
                    $reg->status = $request->status;
                    $reg->flagdelete = 0;
                    $reg->flagatualiza = 1;
                    $reg->flaguser = Session::get('user')->id;
        
                    if($reg->save())
                    {
                        return Tools::setResponse('success', null, 'Registro salvo com Sucesso');
                    }else {
                        return Tools::setResponse('fail', null, 'Não foi possível cadastrar, entre em contato com o Suporte');
                    }
                }                
            }

        }
    }

}
