<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\ConfigUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ConfigUserController extends Controller
{
    
    public function inicio()
    {
        return view('geral.configusuario');
    }

    public function getConfigUser($idUser, $idCfgForUser){
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

    public function listar($regPg =5, $campoOrdem ='unidentificacao', $ordem = 'asc', $campoPesquisa = '')
    {
        $query = ConfigUser::leftjoin('configforuser', 'configuser.fkidconfigforuser', '=', 'configforuser.id')
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
    }

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


    public function remover($id)
    {
        $reg = ConfigUser::find($id);
        if($reg->delete()){
            return 'true';
        }else{
            return 'false';
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
                $reg= ConfigUser::find($request->get('id'));
                $reg->fkidcal=$request->get('fkidcal');
                $reg->fkidusuario=$request->get('fkidusuario');
                $reg->fkidconfigforuser=$request->get('fkidconfigforuser');
                $reg->versao=Carbon::now()->toDateTimeString();
                $reg->flagexibe=$request->flagexibe;
                $reg->status=$request->configuserstatus == 'true' ? 1 : 0;
                $reg->flagdelete=0;
                $reg->flagatualiza=1;
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
                if($this->verificarJaExiste($request->get('fkidconfigforuser'), $request->get('fkidusuario'))){
                    return Tools::setResponse('fail', null, 'Configuração já existe para o Usuário');
                }else{
                    $reg= new ConfigUser;
                    $reg->fkidcal=$request->get('fkidcal');
                    $reg->fkidusuario=$request->get('fkidusuario');
                    $reg->fkidconfigforuser=$request->get('fkidconfigforuser');
                    $reg->versao=Carbon::now()->toDateTimeString();
                    $reg->flagexibe=$request->flagexibe;
                    $reg->status=$request->configuserstatus == 'true' ? 1 : 0;
                    $reg->flagdelete=0;
                    $reg->flagatualiza=1;
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
