<?php

namespace App\Http\Controllers\Controle;

use App\Http\Controllers\Core\Tools;
use App\Models\Cal;
use App\Models\CalPermissao;
use App\Models\Grupo;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ControleAcessoController extends Controller
{
    public function inicio()
    {
        $gestor = Tools::getGestor();
        return view('controle.controleacesso', ['cliente' => $gestor]);
    }

    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()) {
            if(Tools::getGrupoGeral()) {
                if($gestor > 0) {
                    //$qry = Cidade::where('cidade.fkidgestor', $gestor);
                    $qry = Grupo::where('grupo.id', '>', 0);
                } else {
                    $qry = Grupo::where('grupo.id', '>', 0);
                }
            } else {
                if($gestor > 0) {
                    //$qry = Cidade::where('cidade.fkidgestor', $gestor);
                    $qry = Grupo::where('grupo.id', '>', 0);
                } else {
                    return Tools::setResponse('fail', null, 'Impossível Processar.');
                }
            }
        }
        return $qry;
    }

    public function get($id)
    {
        $reg = Grupo::find($id);
        return $reg;
    }

    public function removerId($id)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR'); /*Desativa ou remove do banco */
        if($sistemadesativar > 0) {
            $reg = Grupo::find($id);
            if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
            } 
            $reg->gstatus = 0;
            return Tools::msgpadrao($reg->save(), 'desativar');
        } else {
            $reg = Grupo::find($id);
            //return Tools::msgpadrao($reg->delete(), 'delete');
            if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
            }     
            $reg->gstatus = 0;
            return Tools::msgpadrao($reg->save(), 'desativar');
        }
    }

    public function removerLote(Request $request)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR');
        if($sistemadesativar > 0) {
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = Grupo::whereIn('id', $ids);
            $qtd = $regs->count();
            Tools::setLog(Tools::getUser(), 3, 0, 'Desativar Registros em Lote', 'Registros '.implode(" ", $ids));
            //$regs->cstatus = 0;
            return Tools::msgpadrao($regs->save(), 'desativar', $qtd);
        } else {
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = Grupo::whereIn('id', $ids);
            $qtd = $regs->count();
            Tools::setLog(Tools::getUser(), 3, 0, 'Remover Registros em Lote', 'Registros '.implode(" ", $ids));
            //return Tools::msgpadrao($regs->delete(), 'delete', $qtd);
            return Tools::msgpadrao($regs->save(), 'desativar', $qtd);
        }
    }

    /*
    public function getall()
    {
        $query = Grupo::leftjoin('estado', 'cidade.fkidestado', '=', 'estado.id')->select([DB::raw('cdidentificacao, cidade.id, cidade.fkidestado, etidentificacao, estado.etsigla, CONCAT(cdidentificacao,"-",etsigla) AS estado')]);
        $query->orderBy('cdidentificacao', 'asc');
        //$registros = $query->paginate(300);
        $registros = $query->get();

        try {
            return json_encode($registros);
        } catch (Exception $e) {
            return [];
        }
    }*/


    public function lista(Request $request)
    {

        $query = self::initQuery();

        if($request->grupologadoid != 1){
            $query->wherenotin('grupo.id', [1]);
        }

        if($request->statusfiltro == 0) {
            $query->where('grupo.gstatus','>=', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('grupo.gstatus', 1);
        }else if($request->statusfiltro == 2) {
            $query->where('grupo.gstatus', 0);
        }


        if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('grupo.gversao', '>=', $request->datainiciofiltro.' 00:00:00');
        }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('grupo.gversao', '>=', $request->datainiciofiltro.' 00:00:00');
            $query->where('grupo.gversao', '<=', $request->datafinalfiltro.' 23:59:59');                
        }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('grupo.gversao', '<=', $request->datafinalfiltro.' 23:59:59'); 
        }

        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';

        if(strlen($request->campoPesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('grupo.gidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('grupo.ganotation', 'like', '%' . $request->campoPesquisa . '%');
        }else if(strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('grupo.gidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('grupo.ganotation', 'like', '%' . $request->campoPesquisa . '%');
        }

        //$query->with('cal','menuacima');
        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc')->groupBy('grupo.id');

        try {
            $registros = $query->paginate($request->regPg);
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }

    public function salvar(Request $request)
    {
        $gestor = Tools::getGestor();

        if($gestor > 0) {
            $validator = Validator::make(
                [
                'identificacao' => $request->gidentificacao,
            ],
                [
                'identificacao' => 'required|string|min:5|max:198',
            ],
                [
                'identificacao.required' => 'Necessário Informar a Identificação',
                'identificacao.min' => 'Necessário Informar a Identificação',
                'identificacao.string' => 'Necessário Informar a Identificação',
                'identificacao.max' => 'Necessário Informar a Identificação',
            ]
            );

            if($validator->fails()) {
                return Tools::setResponse('fail', null, $validator->errors()->first());
            }


            try {
                $reg = null;
                if($request->get('id') > 0) {
                    $reg = Grupo::find($request->id);
                    if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                        return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                    }
                } else {
                    $reg = new Grupo();
                }
                $reg->gidentificacao = strlen($request->gidentificacao) > 0 ? $request->gidentificacao : '';
                $reg->gstatus = $request->fkstatus > 0 ? $request->fkstatus : 0;
                $reg->ganotation = strlen($request->ganotation) > 0 ? $request->ganotation : '';
                $reg->gversao = Carbon::now()->toDateTimeString();
                $reg->flagdelete = 0;
                $reg->flagatualiza = 1;
                $reg->flaguser = Session::get('user')->id;

                if($request->grupologadoid != 1 && $request->id == 1){
                    return Tools::setResponse('fail', [], 'Você não tem Permissão para executar este Procedimento');
                }else{
                    if($reg->save()) {
                        return Tools::setResponse('success', $reg, 'Registro Realizado com Sucesso');
                    } else {
                        Tools::setLog(0, 9, 0, 'Tentativa Registro de Lista de Espera', '');
                        return Tools::setResponse('fail', [], 'Falha no Salvamento de Registro');
                    }
                }
                
            } catch (Exception $e) {
                return Tools::setResponse('fail', [], 'Falha ao obter dados');

            }
        } else {
            return Tools::setResponse('fail', [], 'Falha no Salvamento de Registro');

        }
    }


/*
    public function setregistro(Request $request)
    {
        $validator = Validator::make(
            [
            'idregistro' => $request->idregistro,
            'tipoalteracao' => $request->tipoalteracao,
            'valorcampo' => $request->valorcampo,
        ],
            [
            'idregistro' => 'required|integer|min:1',
            'tipoalteracao' => 'required|string|min:2',
            'valorcampo' => 'required|string|min:1',
        ],
            [
            'idregistro.required' => 'Necessário Selecionar um Cadastro',
            'idregistro.integer' => 'Registro Selecionado Inválido',
            'idregistro.min' => 'Registro Selecionado Inválido',
            'tipoalteracao.required' => 'Impossível Processar',
            'tipoalteracao.string' => 'Impossível Processar',
            'tipoalteracao.min' => 'Impossível Processar',
            'valorcampo.required' => 'Impossível Processar',
            'valorcampo.string' => 'Impossível Processar',
            'valorcampo.min' => 'Impossível Processar',
        ]
        );

        if($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'data' => [],
                'message' => $validator->errors()->first()
            ]);
        } else {
            if($request->idregistro > 0) {
                $reg = Grupo::find($request->idregistro);
                if($request->tipoalteracao == 'identificacao') {
                    $reg->gidentificacao = $request->valorcampo;
                } elseif($request->tipoalteracao == 'obs') {
                    $reg->ganotation = $request->valorcampo;
                } elseif($request->tipoalteracao == 'status') {
                    $reg->gstatus = intval($request->valorcampo);
                } elseif($request->tipoalteracao == 'exibe') {
                    //$reg->flagexibe = intval($request->valorcampo);
                }

                $reg->gversao = Carbon::now()->toDateTimeString();
                $reg->flagatualiza = 1;
                $reg->flaguser = Session::get('user')->id;

                if($reg->save()) {
                    return Tools::setResponse('success', $reg, 'Procedimento realizado com Sucesso');
                } else {
                    return Tools::setResponse('fail', [], 'Não foi possível processar, entre em contato com o Suporte');
                }
            }

            return Tools::setResponse('fail', [], 'Não foi possível processar, entre em contato com o Suporte');

        }
    }
*/





    public function membros(Request $request)
    {
        $gestor = Tools::getGestor();
        
        if($request->idprincipal > 0) {
            $query = Usuario::with(['grupo','unico']);
            $query->where('fkidgrupo', $request->idprincipal);

            if($request->grupologadoid != 1){
                $query->wherenotin('fkidgrupo', [1]);
                $query->where('fkidgestor', $gestor);
            }
    
            $query->groupBy('id');
            $query->orderBy($request->campoordem, $request->ordem);

            try {
                //$registros = $query->get();
                $registros = $query->paginate($request->regPg);
                return Tools::setResponse('success', $registros, '');
            } catch (Exception $e) {
                $except = $e->getMessage();
                return Tools::setResponse('fail', [], 'Falha ao obter dados');
            }
        }
    }

    public function getmembro($id)
    {
        try {
            $reg = Usuario::where('id', $id)->with('grupo','unico','gestor')->first();
            $id = $reg->id;
            return Tools::setResponse('success', $reg, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', [], 'Falha ao obter dados');
        }
    }


    public function membroremover($id)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR'); /*Desativa ou remove do banco */
        if($sistemadesativar > 0){
            $reg = Usuario::find($id);
            $reg->ustatus = 0;
            $reg->ulogin = $reg->ulogin;
            $reg->uversao = Carbon::now()->toDateTimeString();
            $reg->flagdelete = 1;
            $reg->flaguser = Session::get('user')->id;
            Log::notice('Desativação do Usuário - ID '.$reg->id.' '.$reg->ulogin.' Tentativa '.Carbon::now()->toDateTimeString());
            return Tools::msgpadrao($reg->save(), 'desativar');
        }else{
            $reg = Usuario::find($id);
            $reg->ustatus = 0;
            $reg->ulogin = $reg->ulogin.'_desativado';
            $reg->uanotation = 'Usuário desativado ';
            $reg->uversao = Carbon::now()->toDateTimeString();
            $reg->flagdelete = 1;
            $reg->flagatualiza = 1;
            $reg->flaguser = Session::get('user')->id;
            Log::notice('Exclusão do Usuário Solicitada, não será removido para menter Logs de Sistema - ID '.$reg->id.' '.$reg->ulogin.' Tentativa '.Carbon::now()->toDateTimeString());
            //return Tools::msgpadrao($reg->delete(), 'delete');
            return Tools::msgpadrao($reg->save(), 'desativar');
        }
    }



    public function membrosalvar(Request $request)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0) {
            $validator = Validator::make(
            [
                'fkidunico' => $request->fkidunico,
                'ulogin' => $request->ulogin,
                'fkidgrupo' => $request->fkidgrupo,
            ],
            [
                'fkidunico' => 'required|integer|min:1',
                'ulogin' => 'required|string|min:5|max:50',
                'fkidgrupo' => 'required|integer|min:1',
            ],
            [
                'fkidunico.required' => 'Informe a Pessoa que será Vinculada',
                'fkidunico.min' => 'Informe a Pessoa que será Vinculada',
                'fkidunico.integer' => 'Informe a Pessoa que será Vinculada',
                'ulogin.required' => 'Informe o Login',
                'ulogin.string' => 'Informe o Login Válido',
                'ulogin.min' => 'Informe o Login Válido',
                'ulogin.max' => 'Informe o Login Válido',
                'fkidgrupo.required' => 'Informe o Grupo de Controle',
                'fkidgrupo.min' => 'Informe o Grupo de Controle',
                'fkidgrupo.integer' => 'Informe o Grupo de Controle',
            ]
            );

            if($validator->fails()) {
                return Tools::setResponse('fail', null, $validator->errors()->first());
            } else {

                $login = Usuario::where('ulogin', $request->ulogin)->first();

                $user = null;
                $reg = null;

                if($login && ($request->id <= 0 || $login->id != $request->id)){
                    return Tools::setResponse('fail', [], 'Login Não pode ser Utilizado'); 
                }
                
                if($request->id > 0) {
                    $reg = Usuario::find($request->id);
                } else {
                    $user = Usuario::where('fkidunico', $request->fkidunico)->first();
                    if(!$user){
                        $reg = new Usuario();
                        $reg->udatacadastro = Carbon::now()->toDateTimeString();
                        //$reg->ucontadoracesso = 
                    }else{
                        Log::notice('Existe Usuário do Sistema Vinculado a Esta Pessoa - ID Unificado'.$request->fkidunico.' Tentativa '.Carbon::now()->toDateTimeString());
                        return Tools::setResponse('fail', [], 'Existe Usuário do Sistema Vinculado a Esta Pessoa');
                    }
                }

                if( strlen($request->passw) > 0){
                    $reg->upassword = bcrypt($request->passw);
                    $reg->uhash = bcrypt($request->passw);
                }

                $reg->ulogin = $request->ulogin;
                $reg->ustatus = $request->ustatus;
                $reg->usolicitalocalizacao = 1;
                $reg->uanotation = strlen($request->uanotation) > 0 ? $request->uanotation : '';
                $reg->fkidunico = $request->fkidunico;
                $reg->fkidgrupo = $request->fkidgrupo;
                $reg->uversao = Carbon::now()->toDateTimeString();

                $gestor1 = $gestor;
                if($request->grupologadoid == 1){
                    $gestor1 = $request->fkidgestor;
                }

                $reg->fkidgestor = ($gestor1 > 0 ? $gestor1 : $gestor );
                $reg->flagdelete = 0;
                $reg->flagatualiza = 1;
                $reg->flaguser = Session::get('user')->id;

                try{
                    if($reg->save()) {
                        Log::info('Inserir/Alterar Usuário no Sistema - ID Unificado '.$request->fkidunico.' '.$request->ulogin.' Registrado '.$reg->udataultimoacesso);
                        return Tools::setResponse('success', $reg, 'Registro Realizado com Sucesso');
                    } else {
                        Log::notice('Falha ao Inserir/alterar Usuário no Sistema - ID Unificado'.$request->fkidunico.' '.$request->ulogin.' Tentativa '.Carbon::now()->toDateTimeString());
                        return Tools::setResponse('fail', [], 'Falha no Salvamento de Registro');
                    }
                } catch (Exception $e) {
                    $except = $e->getMessage();
                    return Tools::setResponse('fail', null, 'Falha ao Processar Dados');
                }
            }
        } else {
            return Tools::setResponse('fail', [], 'Falha no Salvamento de Registro');
        }
    }





    public function permissoes(Request $request)
    {

        if($request->grupologadoid != 1){
            /*falta cal para tipocadastro, columncal, tipodocsubtipo, tipolei, tipoouvidoria, tipopermissao, tipoprioridade,tiporaca, buscasalva */
            $query = Cal::wherenotin('cal.id', [1,15,17,20,28,29,30,35,42,48,54,66,69,70,124,145,192,193,197]);
        }else{
            $query = Cal::where('cal.id', '>', 0);
        }
        $query->leftJoin('calpermissao as calperm', function ($join) use ($request) {
            $join->on('cal.id', '=', 'calperm.fkidcal')
                ->where('calperm.fkidgrupo', $request->idprincipal);
        });

        if($request->statusfiltro == 0) {
            $query->where('clstatus','>=', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('clstatus', 1);
        }else if($request->statusfiltro == 2) {
            $query->where('clstatus', 0);
        }


        if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('clversao', '>=', $request->datainiciofiltro.' 00:00:00');
        }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('clversao', '>=', $request->datainiciofiltro.' 00:00:00');
            $query->where('clversao', '<=', $request->datafinalfiltro.' 23:59:59');                
        }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('clversao', '<=', $request->datafinalfiltro.' 23:59:59'); 
        }

        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';

        if(strlen($request->campoPesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('clidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('clobserve', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('clbase', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('clrota', 'like', '%' . $request->campoPesquisa . '%');
        }else if(strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('clidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('clobserve', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('clbase', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('clrota', 'like', '%' . $request->campoPesquisa . '%');
        }

        $query->select([DB::raw('cal.id, clidentificacao, clobserve, clbase, clrota, clversao,
        cltipo, clstatus, cppermissao, fkidgrupo, cpversao, calperm.id as permissaoid')]);


        $query->with('permissao');
        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc')->groupBy('cal.id');

        try {
            $registros = $query->paginate($request->regPg);
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }


    public function permissaoset(Request $request)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0) {
            $validator = Validator::make(
            [
                'fkidcal' => $request->fkidcal,
                'fkidgrupo' => $request->fkidgrupo,
                'tipopermissao' => $request->tipopermissao,
            ],
            [
                'fkidcal' => 'required|integer|min:1',
                'fkidgrupo' => 'required|integer|min:1',
                'tipopermissao' => 'required|string|min:5|max:10',
            ],
            [
                'fkidcal.required' => 'Informe a Cal',
                'fkidcal.min' => 'Informe a Cal',
                'fkidcal.integer' => 'Informe a Cal',
                'fkidgrupo.required' => 'Informe a Grupo de Controle',
                'fkidgrupo.min' => 'Informe a Grupo de Controle',
                'fkidgrupo.integer' => 'Informe a Grupo de Controle',
                'tipopermissao.required' => 'Informe o Tipo de Permissão',
                'tipopermissao.string' => 'Informe o Tipo de Permissão',
                'tipopermissao.min' => 'Informe o Tipo de Permissão',
                'tipopermissao.max' => 'Informe o Tipo de Permissão',
            ]
            );

            if($validator->fails()) {
                return Tools::setResponse('fail', null, $validator->errors()->first());
            } else {
                if($request->id > 0) {
                    $reg = CalPermissao::find($request->id);
                    $reg->cppermissao = self::setPermissao($reg->cppermissao, $request->tipopermissao);
                } else {
                    $reg = CalPermissao::where('fkidcal',$request->fkidcal)->where('fkidgrupo',$request->fkidgrupo)->first();
                    if(!$reg){
                        $reg = new CalPermissao();
                        $reg->cppermissao = self::setPermissao('00:00:00:00:00', $request->tipopermissao);
                    }else{
                        $reg->cppermissao = self::setPermissao($reg->cppermissao, $request->tipopermissao);
                    }
                }

                $reg->cpversao = Carbon::now()->toDateTimeString();
                $reg->fkidcal = $request->fkidcal;
                $reg->fkidgrupo = $request->fkidgrupo;
                $reg->flagdelete = 0;
                $reg->flagatualiza = 1;
                $reg->flaguser = Session::get('user')->id;

                if($reg->save()) {
                    Log::info('Inserir/Alterar Permissão de Grupo - ID '.$reg->id.' CAL '.$reg->fkidcal.' Grupo '.$reg->fkidgrupo.' Registrado '.Carbon::now()->toDateTimeString());
                    return Tools::setResponse('success', $reg, 'Registro Realizado com Sucesso');
                } else {
                    Log::notice('Inserir/Alterar Permissão de Grupo -  CAL '.$request->fkidcal.' Grupo '.$request->fkidgrupo.' Tentativa '.Carbon::now()->toDateTimeString());
                    return Tools::setResponse('fail', [], 'Falha no Salvamento de Registro');
                }
            }
        } else {
            return Tools::setResponse('fail', [], 'Falha no Salvamento de Registro');
        }
    }

    public function setPermissao($calpermissao = '', $settipopermissao = 'consultar'){
        $result = '';
        $permissaoconsultar = '';
        $permissaoinserir = '';
        $permissaoalterar = '';
        $permissaoexcluir = '';
        $permissaoesup = '';
        if(strlen($calpermissao) > 0){
            $permissaoconsultar = substr($calpermissao, 0, 2);
            $permissaoinserir = substr($calpermissao, 3, 2);
            $permissaoalterar = substr($calpermissao, 6, 2);
            $permissaoexcluir = substr($calpermissao, 9, 2);
            $permissaoesup = substr($calpermissao, 12, 2);

            if($settipopermissao == 'consultar'){
                $permissaoconsultar = ($permissaoconsultar == '11' ? '00' : '11');
            }
            if($settipopermissao == 'inserir'){
                $permissaoinserir = ($permissaoinserir == '11' ? '00' : '11');
            }
            if($settipopermissao == 'alterar'){
                $permissaoalterar = ($permissaoalterar == '11' ? '00' : '11');
            }
            if($settipopermissao == 'excluir'){
                $permissaoexcluir = ($permissaoexcluir == '11' ? '00' : '11');
            }

            $result =  $permissaoconsultar.':'.$permissaoinserir.':'.$permissaoalterar.':'.$permissaoexcluir.':'.$permissaoesup;
            return $result;
        }else{
            return $calpermissao;
        }
    }



    public function setPermissao2($calpermissao = '', $settipopermissao = 'consultar', $permite = '11'){
        $result = '';
        $permissaoconsultar = '';
        $permissaoinserir = '';
        $permissaoalterar = '';
        $permissaoexcluir = '';
        $permissaoesup = '';
        if(strlen($calpermissao) > 0){
            $permissaoconsultar = substr($calpermissao, 0, 2);
            $permissaoinserir = substr($calpermissao, 3, 2);
            $permissaoalterar = substr($calpermissao, 6, 2);
            $permissaoexcluir = substr($calpermissao, 9, 2);
            $permissaoesup = substr($calpermissao, 12, 2);

            if($settipopermissao == 'consultar'){
                $permissaoconsultar = $permite;
            }
            if($settipopermissao == 'inserir'){
                $permissaoinserir = $permite;
            }
            if($settipopermissao == 'alterar'){
                $permissaoalterar = $permite;
            }
            if($settipopermissao == 'excluir'){
                $permissaoexcluir = $permite;
            }

            $result =  $permissaoconsultar.':'.$permissaoinserir.':'.$permissaoalterar.':'.$permissaoexcluir.':'.$permissaoesup;
            return $result;
        }else{
            return $calpermissao;
        }
    }


    public function permissaosetlote(Request $request)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0) {
            $validator = Validator::make(
            [
                'ids' => $request->ids,
                'acao' => $request->acao,
                'idgrupo' => $request->idgrupo,
            ],
            [
                'acao' => 'required|integer|min:1',
                'idgrupo' => 'required|integer|min:1',
                'ids' => 'required|string|min:1|max:180',
            ],
            [
                'acao.required' => 'Informe a Permissão',
                'acao.min' => 'Informe a Permissão',
                'acao.integer' => 'Informe a Permissão',
                'idgrupo.required' => 'Informe a Grupo de Controle',
                'idgrupo.min' => 'Informe a Grupo de Controle',
                'idgrupo.integer' => 'Informe a Grupo de Controle',
                'ids.required' => 'Informe as Cals para Atribuir as Permissões',
                'ids.string' => 'Informe as Cals para Atribuir as Permissões',
                'ids.min' => 'Informe as Cals para Atribuir as Permissões',
                'ids.max' => 'Informe as Cals para Atribuir as Permissões',
            ]
            );


            /*
            <option value="1" title="Aplicar Permissões de Consulta aos Selecionados">Aplicar Permissões de Consulta</option>
            <option value="2" title="Aplicar Permissões de Inserção aos Selecionados">Aplicar Permissões de Inserção</option>
            <option value="3" title="Aplicar Permissões de Alteração aos Selecionados">Aplicar Permissões de Alteração</option>
            <option value="4" title="Aplicar Permissões de Exclusão aos Selecionados">Aplicar Permissões de Exclusão</option>
            <option value="5" title="Remover Permissões de Consulta aos Selecionados">Remover Permissões de Consulta</option>
            <option value="6" title="Remover Permissões de Inserção aos Selecionados">Remover Permissões de Inserção</option>
            <option value="7" title="Remover Permissões de Alteração aos Selecionados">Remover Permissões de Alteração</option>
            <option value="8" title="Remover Permissões de Exclusão aos Selecionados">Remover Permissões de Exclusão</option>*/

            
            $tipopermissao = '';
            $permite = '00';
            switch ($request->acao) {
                case 1:
                    $tipopermissao = 'consultar';
                    $permite = '11';
                    break;
                case 2:
                    $tipopermissao = 'inserir';
                    $permite = '11';
                    break;
                case 3:
                    $tipopermissao = 'alterar';
                    $permite = '11';
                    break;
                case 4:
                    $tipopermissao = 'excluir';
                    $permite = '11';
                    break;
                case 5:
                    $tipopermissao = 'consultar';
                    $permite = '00';
                    break;
                case 6:
                    $tipopermissao = 'inserir';
                    $permite = '00';
                    break;
                case 7:
                    $tipopermissao = 'alterar';
                    $permite = '00';
                    break;
                case 8:
                    $tipopermissao = 'excluir';
                    $permite = '00';
                    break;                                      
            }

            if($validator->fails()) {
                return Tools::setResponse('fail', null, $validator->errors()->first());
            } else {
                $arr = explode(',', $request->ids);
                try {
                    foreach ($arr as &$id) {
                        if($id > 0){
                            $reg = CalPermissao::where('fkidcal',$id)->where('fkidgrupo',$request->idgrupo)->first();
                            if(!$reg){
                                $reg = new CalPermissao();
                                $reg->cppermissao = self::setPermissao2('00:00:00:00:00', $tipopermissao, $permite);
                            }else{
                                $reg->cppermissao = self::setPermissao2($reg->cppermissao, $tipopermissao, $permite);
                            }
                            $reg->cpversao = Carbon::now()->toDateTimeString();
                            $reg->fkidcal = $id;
                            $reg->fkidgrupo = $request->idgrupo;
                            $reg->flagdelete = 0;
                            $reg->flagatualiza = 1;
                            $reg->flaguser = Session::get('user')->id;
                            if($reg->save()) {
                                Log::info('Inserir/Alterar Permissão de Grupo - ID '.$reg->id.' CAL '.$reg->fkidcal.' Grupo '.$reg->fkidgrupo.' Registrado '.Carbon::now()->toDateTimeString());
                            } else {
                                Log::notice('Inserir/Alterar Permissão de Grupo -  CAL '.$request->fkidcal.' Grupo '.$request->fkidgrupo.' Tentativa '.Carbon::now()->toDateTimeString());
                            }
                        }
                    }
                    return Tools::setResponse('success', [], 'Procedimento Realizado com Sucesso');
                } catch (Exception $e) {
                    $except = $e->getMessage();
                    return Tools::setResponse('fail', null, 'Falha ao Executar os Procedimento');
                }
            }
        } else {
            return Tools::setResponse('fail', [], 'Falha no Salvamento de Registro');
        }
    }    

}
