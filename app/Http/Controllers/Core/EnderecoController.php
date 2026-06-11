<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Core\Tools;
use App\Models\Endereco;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EnderecoController extends Controller
{
    public function inicio()
    {
        $gestor = Tools::getGestor();
        return view('auxiliar.endereco', ['cliente' => $gestor]);
    }


    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()) {
            if(Tools::getGrupoGeral()) {
                if($gestor > 0) {
                    $qry = Endereco::where('endereco.fkidgestor', $gestor);
                    
                } else {
                    return Tools::setResponse('fail', null, 'Impossível Processar');
                }
            } else {
                if($gestor > 0) {
                    $qry = Endereco::where('fkidgestor', $gestor);
                } else {
                    return Tools::setResponse('fail', null, 'Impossível Processar');
                }
            }
        }
        return $qry;
    }

    public function get($id)
    {
        $reg = Endereco::find($id);
        return $reg;
    }

    public function remover($id)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR'); /*Desativa ou remove do banco */
        if($sistemadesativar > 0) {
            $reg = Endereco::find($id);
            if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
            }            
            Tools::setAtividade(Tools::getUser(), 3, $id, 'Desativar o Registro', 'Registro '.$id);
            //$reg->cstatus = 0;
            return Tools::msgpadrao($reg->save(), 'desativar');
        } else {
            $reg = Endereco::find($id);
            if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
            }
            Tools::setAtividade(Tools::getUser(), 3, $id, 'Remover o Registro', 'Registro '.$id);
            return Tools::msgpadrao($reg->delete(), 'delete');
        }
    }

    public function removerLote(Request $request)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR');
        if($sistemadesativar > 0) {
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = Endereco::whereIn('id', $ids);
            $qtd = $regs->count();
            Tools::setAtividade(Tools::getUser(), 3, 0, 'Desativar Registros em Lote', 'Registros '.implode(" ", $ids));
            //$regs->cstatus = 0;
            return Tools::msgpadrao($regs->save(), 'desativar', $qtd);
        } else {
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = Endereco::whereIn('id', $ids);
            $qtd = $regs->count();
            Tools::setAtividade(Tools::getUser(), 3, 0, 'Remover Registros em Lote', 'Registros '.implode(" ", $ids));
            return Tools::msgpadrao($regs->delete(), 'delete', $qtd);
        }
    }

    public function getall()
    {

        $query = Endereco::leftjoin('estado', 'cidade.fkidestado', '=', 'estado.id')->select([DB::raw('cdidentificacao, cidade.id, cidade.fkidestado, etidentificacao, estado.etsigla, CONCAT(cdidentificacao,"-",etsigla) AS estado')]);
        $query->orderBy('cdidentificacao', 'asc');
        //$registros = $query->paginate(300);
        $registros = $query->get();

        try {
            return json_encode($registros);
        } catch (Exception $e) {
            $except = $e->getMessage();
            return [];
        }
    }


    public function lista(Request $request)
    {

        $query = self::initQuery();

        if($request->statusfiltro == 0) {
            $query->where('endstatus','>=', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('endstatus', 1);
        }else if($request->statusfiltro == 2) {
            $query->where('endstatus', 0);
        }

        if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('endversao', '>=', $request->datainiciofiltro.' 00:00:00');
        }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('endversao', '>=', $request->datainiciofiltro.' 00:00:00');
            $query->where('endversao', '<=', $request->datafinalfiltro.' 23:59:59');                
        }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('endversao', '<=', $request->datafinalfiltro.' 23:59:59'); 
        }

        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';


        if(strlen($request->campoPesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('endereco.endlogradouro', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('endereco.endbairro', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('endereco.endtipologradouro', 'like', '%' . $request->campoPesquisa . '%');
        }else if(strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('endereco.endlogradouro', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('endereco.endbairro', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('endereco.endtipologradouro', 'like', '%' . $request->campoPesquisa . '%');
        }

        //$query->with('zeladoria','reservaitem','formapagamento','unico');
        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc');
        $query->with('cidade');
        $query->groupBy('id');

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
                'cidade' => $request->fkidcidade,
                'tipo' => $request->endtipologradouro,
            ],
                [
                'cidade' => 'required|integer|min:1',
                'tipo' => 'required|integer|min:1',
            ],
                [
                'cidade.required' => 'Necessário Informar a Identificação',
                'identificacao.min' => 'Necessário Informar a Identificação',
                'identificacao.integer' => 'Necessário Informar a Identificação',
                'tipo.required' => 'Necessário Informar o Tipo',
                'tipo.min' => 'Necessário Informar o Tipo',
                'tipo.integer' => 'Necessário Informar o Tipo',
            ]
            );

            if($validator->fails()) {
                return Tools::setResponse('fail', null, $validator->errors()->first());
            }


            try {
                $reg = null;
                if($request->get('id') > 0) {
                    $reg = Endereco::find($request->id);
                    if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                        return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                    }                    
                } else {
                    $reg = new Endereco();
                }
                $reg->fkidcidade = strlen($request->fkidcidade) > 0 ? $request->fkidcidade : '';
                $reg->endcep = strlen($request->endcep) > 0 ? $request->endcep : '';
                $reg->endtipologradouro = strlen($request->endtipologradouro) > 0 ? $request->endtipologradouro : '';
                $reg->endlogradouro = strlen($request->endlogradouro) > 0 ? $request->endlogradouro : '';
                $reg->endbairro = strlen($request->endbairro) > 0 ? $request->endbairro : '';
                $reg->endstatus = $request->endstatus > 0 ? $request->endstatus : 0;
                $reg->endversao = Carbon::now()->toDateTimeString();
                $reg->fkidgestor = Tools::getGestor(0);
                //$reg->flagexibe = $request->flagexibe == 'true' ? 1 : 0;
                $reg->flagdelete = 0;
                $reg->flagatualiza = 1;
                $reg->flaguser = Session::get('user')->id;

                if($reg->save()) {
                return Tools::setResponse('success', $reg, 'Registro Realizado com Sucesso');
                } else {
                    Tools::setAtividade(0, 9, 0, 'Tentativa Registro de Lista de Espera', '');
                    return Tools::setResponse('fail', [], 'Falha no Salvamento de Registro');
                }
            } catch (Exception $e) {
                $except = $e->getMessage();
                return Tools::setResponse('fail', [], 'Falha ao obter dados');

            }
        } else {
            return response()->json([
                'status' => 'fail',
                'data' => [],
                'message' => 'Falha no Salvamento de Registro'
            ]);

        }
    }    
}