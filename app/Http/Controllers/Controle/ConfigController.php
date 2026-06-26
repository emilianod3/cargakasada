<?php

namespace App\Http\Controllers\Controle;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Core\Tools;
use Exception;

class ConfigController extends Controller
{

    public function inicio()
    {
        return view('controle.configsistema');
    }

    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    //$qry = Config::where('fkidgestor', $gestor);
                    $qry = Config::where('id','>',0);
                }else{
                    //$qry = Profissao::where('id','<',0);
                    return Tools::setResponse('fail', null, 'Impossível Processar');
                }
            }else{
                if($gestor > 0){
                    //$qry = Config::where('fkidgestor', $gestor);
                    $qry = Config::where('id','>',0);
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

        //$query->with('legislatura');
        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'asc')->groupBy('id');

        try {
            $registros = $query->paginate($request->paginate);
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }

    public function geraDadosConfiguracoes(){
        $registros = $query = Config::where('status','>',0)->get();
        $config = $registros;
        /*
        $config = array(
            'campoPesquisa' => $request->get('campoPesquisa'),
            'campoOrdem' => $request->get('campoOrdem'),
            'ordem' => $request->get('ordem'));
        array_push($searchs, ['buscaopcao' => '1']);*/
        Session::put('config', $config);

        $registrosjson = $query = Config::select([DB::raw('config.id, config.identificacao, config.status, config.classificacao, config.valor1, config.valor2, config.versao, config.flagexibe')])->where('status','>',0)->get();
        Session::put('configjson', json_encode($registrosjson));
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

    /*
    public function getId($id)
    {
        $registro = Config::where('id', $id)->first();
        return json_encode($registro);
    }*/


    /*
    public function listar($regPg =5, $campoOrdem ='identificacao', $ordem = 'asc', $campoPesquisa = '')
    {
        $query = Config::Where('id', '>', 0);
        if(strlen($campoPesquisa) > 0) {
            $query->where('identificacao', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('exemplo', 'like', '%' . $campoPesquisa . '%');
        }
        $query->orderBy($campoOrdem, $ordem);
        $registros = $query->paginate($regPg);
        return $registros;
    }*/

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
    }


    public function salvar(Request $request)
    {
        $validator = Validator::make(
        [
            'identificacao' => $request->identificacao,
            'tipodado' => $request->tipodado,
            'classificacao' => $request->classificacao,
            'valor1' => $request->valor1,
        ]
        , [
            'identificacao' => 'required|string|min:1|max:255',
            'tipodado' => 'required|integer|min:1',
            'classificacao' => 'required|integer|min:1',
            'valor1' => 'required|string|min:1|max:255',
            ],
        [
            'identificacao.required' => 'Necessário Informar a Identificação',
            'identificacao.string' => 'Necessário Informar a Identificação',
            'identificacao.min' => 'Registro vazio',
            'identificacao.max' => 'Informe um Conteúdo Válido',
            'tipodado.required' => 'Necessário Informar o Tipo de Tratamento',
            'tipodado.integer' => 'Necessário Informar o Tipo de Tratamento',
            'tipodado.min' => 'Necessário Informar o Tipo de Tratamento',
            'classificacao.required' => 'Necessário Informar a Classificação',
            'classificacao.integer' => 'Necessário Informar a Classificação',
            'classificacao.min' => 'Necessário Informar a Classificação',
            'valor1.required' => 'Necessário Informar o Valor Inicial de Tratamento',
            'valor1.string' => 'Necessário Informar o Valor Inicial de Tratamento',
            'valor1.min' => 'Necessário Informar o Valor Inicial de Tratamento',
            'valor1.max' => 'Necessário Informar o Valor Inicial de Tratamento',
        ]);

        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            if($request->get('id') > 0)
            {
                $reg = Config::find($request->get('id'));
                if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                    return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                }                
            }
            else
            {
                $reg = new Config;
            }
            $reg->identificacao = strlen($request->identificacao) > 0 ? $request->identificacao : '';
            $reg->exemplo = strlen($request->exemplo) > 0 ? $request->exemplo : '';
            $reg->tipodado = $request->tipodado > 0 ? $request->tipodado : 0;
            $reg->classificacao = $request->classificacao > 0 ? $request->classificacao : 0;
            $reg->valor1 = strlen($request->valor1) > 0 ? $request->valor1 : '';
            $reg->valor2 = strlen($request->valor2) > 0 ? $request->valor2 : '';
            $reg->versao = Carbon::now()->toDateTimeString();
            $reg->status = $request->fkstatus > 0 ? $request->fkstatus : 0;
            $reg->flagexibe = $request->flagexibe > 0 ? $request->flagexibe : 0;
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
