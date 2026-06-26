<?php

namespace App\Http\Controllers\Controle;

use App\Http\Controllers\Core\Cals;
use App\Http\Controllers\Core\Tools;
use App\Models\Cal;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class CalController extends Controller
{
    public function inicio()
    {
        $gestor = Tools::getGestor();
        return Inertia::render('Controle/Cals', [
            'gestor' => $gestor,
            'cal' => Cals::CALCONTROLE
        ]);
    }
    
    public static function initQuery($qry = null){
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    //$qry = Cidade::where('cidade.fkidgestor', $gestor);
                    $qry = Cal::where('cal.id','>',0);
                }else{
                    $qry = Cal::where('cal.id','>',0);
                }
            }else{
                if($gestor > 0){
                    //$qry = Cidade::where('cidade.fkidgestor', $gestor);
                    $qry = Cal::where('cal.id','>',0);
                }else{
                    return Tools::setResponse('fail', null, 'Impossível Processar.');
                }
            }
        }
        return $qry;
    }

    public function get($id)
    {
        $reg = Cal::find($id);
        return $reg;
    }

    public function removerId($id)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR'); /*Desativa ou remove do banco */
        if($sistemadesativar > 0){
            $reg = Cal::find($id);
            if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
            }            
            $reg->clstatus = 0;
            return Tools::msgpadrao($reg->save(), 'desativar');
        }else{
            $reg = Cal::find($id);
            if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
            }            
            return Tools::msgpadrao($reg->delete(), 'delete');
        }
    }

    public function removerLote(Request $request)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR');
        if($sistemadesativar > 0){
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = Cal::whereIn('id', $ids);
            $qtd = $regs->count();
            $regs->clstatus = 0;
            return Tools::msgpadrao($regs->save(), 'desativar', $qtd);
        }else{
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = Cal::whereIn('id', $ids);
            $qtd = $regs->count();
            return Tools::msgpadrao($regs->delete(), 'delete', $qtd);
        }
    }


    public function lista(Request $request)
    {

        $query = self::initQuery();

        if($request->statusfiltro == 0) {
            $query->where('cal.clstatus','>=', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('cal.clstatus', 1);
        }else if($request->statusfiltro == 2) {
            $query->where('cal.clstatus', 0);
        }


        if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('cal.clversao', '>=', $request->datainiciofiltro.' 00:00:00');
        }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('cal.clversao', '>=', $request->datainiciofiltro.' 00:00:00');
            $query->where('cal.clversao', '<=', $request->datafinalfiltro.' 23:59:59');                
        }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('cal.clversao', '<=', $request->datafinalfiltro.' 23:59:59'); 
        }

        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';

        if(strlen($request->campoPesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('cal.clidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('cal.clobserve', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('cal.clbase', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('cal.clrota', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('cal.id', 'like', '%' . $request->campoPesquisa . '%');
        }else if(strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('cal.clidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clobserve', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clbase', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clrota', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.id', 'like', '%' . $request->campoPesquisa . '%');
        }

        //$query->with('cal','menuacima');
        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc')->groupBy('cal.id');

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
        if($gestor > 0){
            $validator = Validator::make(
            [
                'identificacao' => $request->clidentificacao,
                'tipo' => $request->tipo,   
            ]
            , [
                'identificacao' => 'required|string|min:5|max:198',
                'tipo' => 'required|integer|min:1',
            ],
            [
                'identificacao.required' => 'Necessário Informar a Identificação',
                'identificacao.min' => 'Necessário Informar a Identificação',
                'identificacao.string' => 'Necessário Informar a Identificação',
                'identificacao.max' => 'Necessário Informar a Identificação',
                'tipo.required' => 'Necessário Informar o Tipo',
                'tipo.min' => 'Necessário Informar o Tipo',
                'tipo.integer' => 'Necessário Informar o Tipo',
            ]);

            if($validator->fails()){
                return Tools::setResponse('fail', [], $validator->errors()->first());
            }
            
            
            try{
                $reg = null;
                if($request->get('id') > 0)
                {
                    $reg = Cal::find($request->id);
                    if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                        return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                    }                    
                }
                else
                {
                    $reg = new Cal();
                }
                $reg->clidentificacao = strlen($request->clidentificacao) > 0 ? $request->clidentificacao : '';
                $reg->clbase = strlen($request->base) > 0 ? $request->base : '';
                $reg->clrota = strlen($request->rota) > 0 ? $request->rota : '';
                $reg->cltipo = $request->tipo > 0 ? $request->tipo : 1;
                $reg->clstatus = $request->fkstatus > 0 ? $request->fkstatus : 0;
                $reg->clobserve = strlen($request->obs) > 0 ? $request->obs : '';
                $reg->clversao = Carbon::now()->toDateTimeString();
                $reg->flagdelete = 0;
                $reg->flagatualiza = 1;
                $reg->flaguser = Session::get('user')->id;
                if($reg->save()){
                    return Tools::setResponse('success', $reg, 'Registro Realizado com Sucesso');
                }else{
                    Tools::setLog(0, 9, 0,  'Tentativa Registro de Lista de Espera', ''); 
                    return Tools::setResponse('fail', [], 'Falha no Salvamento de Registro');
                }
            } catch (Exception $e) {
                return Tools::setResponse('fail', [], 'Falha ao obter dados');
    
            }
        }else{
            return Tools::setResponse('fail', [], 'Falha no Salvamento de Registro');
        }
    }

    public function getId($id)
    {
        $registro = Cal::find($id);
        return $registro;
    }

    public function getTiposCals()
    {
        $registros = array(
            array(
                "id" => 1,
                "tipo" => "Cadastro"
            ),
            array(
                "id" => 2,
                "tipo" => "Controle"
            ),
            array(
                "id" => 3,
                "tipo" => "SubCadastro"
            )
        );

        return $registros;
    }



    public function getForSiteConteudo()
    {
        $query = Cal::where('cltipo', '<>', 3);
        $query->orderBy('clidentificacao', 'asc');
        $registros = $query->get();
        return $registros;
    }


    public function getAll()
    {
        $query = Cal::where('id', '>', 0);
        $query->orderBy('clidentificacao', 'asc');
        //$registros = $query->paginate(300);
        $registros = $query->get();

        try {
            //Session::put('grupos', $registros);
            return json_encode($registros);
        } catch (Exception $e) {
            return [];
        }

    }


    public function getAllObj()
    {
        $query = Cal::where('id', '>', 0);
        $query->where('clstatus', '>', 0);
        $query->orderBy('clidentificacao', 'asc');
        //$registros = $query->paginate(300);
        $registros = $query->get();

        try {
            //Session::put('grupos', $registros);
            return $registros;
        } catch (Exception $e) {
            return [];
        }

    }

    /*
    public function getAll()
    {
        $query = Cal::Where('id', '>', 0);
        $query->orderBy('clidentificacao', 'asc');
        $registros = $query->get();
        return $registros;
    }*/

}
