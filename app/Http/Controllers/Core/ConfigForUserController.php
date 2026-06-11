<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\ConfigForUser;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ConfigForUserController extends Controller
{
    
    public function inicio()
    {
        return view('geral.configforusuario');
    }
/*
    public function geraDadosConfiguracoes(){
        $registros = $query = ConfigForUser::where('status','>',0)->get();
        $config = $registros;
        /*
        $config = array(
            'campoPesquisa' => $request->get('campoPesquisa'),
            'campoOrdem' => $request->get('campoOrdem'),
            'ordem' => $request->get('ordem'));
        array_push($searchs, ['buscaopcao' => '1']);*
        Session::put('config', $config);
    }*/

    public function getAll($campoOrdem ='identificacao', $ordem = 'asc')
    {
        $registros = $query = ConfigForUser::orderBy($campoOrdem, $ordem)->get();
        return $registros;
    }

    public function getItens()
    {
        $registros = $query = ConfigForUser::orderBy('identificacao', 'asc')->get();
        return $registros;
    }

    public function getId($id)
    {
        $registro = ConfigForUser::where('id', $id)->first();
        return json_encode($registro);
    }


    public function listar($regPg =5, $campoOrdem ='identificacao', $ordem = 'asc', $campoPesquisa = '')
    {
        $query = ConfigForUser::leftjoin('configuser', 'configuser.fkidconfigforuser', '=', 'configforuser.id')
        ->select([DB::raw('configforuser.id, configuser.fkidcal, configuser.fkidusuario, configuser.fkidconfigforuser, 
        configforuser.status, configforuser.versao, configforuser.flagdelete, configforuser.flagatualiza, configforuser.flagexibe, configforuser.flaguser,
        configforuser.identificacao, configforuser.exemplo, configforuser.tipodado,
        configforuser.classificacao, configforuser.valor1, configforuser.valor2')]);
        if(strlen($campoPesquisa) > 0) {
            $query->where('configforuser.identificacao', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('configforuser.exemplo', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('configforuser.valor1', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('configforuser.valor2', 'like', '%' . $campoPesquisa . '%');
        }
        $query->groupBy('configforuser.id')->orderBy($campoOrdem, $ordem);
        $registros = $query->paginate($regPg);
        return $registros;
    }

    public function remover($id)
    {
        //Log::debug('Saida='.$id);
        $reg = ConfigForUser::find($id);
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
                'identificacao' => $request->identificacao,
                'tipodado' => $request->tipodado,
                'classificacao' => $request->classificacao,
            ]
            , [
                'identificacao' => 'required|string|min:1|max:255',
                'tipodado' => 'required',
                'classificacao' => 'required',
             ],
            [
                'identificacao.required' => 'Necessário Informar a Identificação',
                'identificacao.string' => 'Necessário Informar a Identificação',
                'identificacao.min' => 'Registro vazio',
                'identificacao.max' => 'Informe um Conteúdo Válido',
                'tipodado.required' => 'Necessário Informar o Tipo',
                'classificacao.required' => 'Necessário Informar a Classificação',
            ]);

        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            if($request->get('id') > 0)
            {
                $reg= ConfigForUser::find($request->get('id'));
            }
            else
            {
                $reg= new ConfigForUser;
            }
            $reg->identificacao=$request->get('identificacao');
            $reg->exemplo=$request->get('exemplo');
            $reg->tipodado=$request->get('tipodado');
            $reg->classificacao=$request->get('classificacao');
            $reg->valor1=$request->valor1 == null ? '': $request->valor1;
            $reg->valor2=$request->valor2 == null ? '': $request->valor2;
            $reg->versao=Carbon::now()->toDateTimeString();
            $reg->flagexibe=$request->flagexibe == 'true' ? 1 : 0;
            $reg->status=$request->cfgstatus == 'true' ? 1 : 0;
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
