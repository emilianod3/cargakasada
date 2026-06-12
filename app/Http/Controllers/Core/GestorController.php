<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Core\Tools;
use Facade\FlareClient\Http\Client;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Gestor;
use Exception;

class GestorController extends Controller
{
    public function inicio()
    {
        $gestor = Tools::getGestor();
        return view('sistema.cliente', ['cliente' => $gestor]);
    }


    public function getClienteCnpj($cnpj)
    {
        $reg = Gestor::where('cnpj', 'like', '%' . $cnpj . '%')->first();
        return $reg;
    }

    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()) {
            if(Tools::getGrupoGeral()) {
                if($gestor > 0) {
                    $qry = Gestor::where('cliente.fkidgestor', $gestor);
                } else {
                    $qry = Gestor::where('cliente.fkidgestor', '=', 0);
                }
            } else {
                if($gestor > 0) {
                    $qry = Gestor::where('cliente.fkidgestor', $gestor);
                } else {
                    return Tools::setResponse('fail', null, 'Impossível Listar');
                }
            }
        } else {
            $qry = Gestor::where('cliente.id', '<=', 0);
        }
        return $qry;
    }


    public static function montaQuery($qry = null, $request = null)
    {

        if($qry != null) {
            if(strlen($request->campoPesquisa) > 0) {
                $qry->where('identificacao', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('nomefantasia', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('lema', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('cnpj', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('endereco', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('cep', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('numero', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('complemento', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('inscrestadual', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('datacadastro', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('obs', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('bairro', 'like', '%' . $request->campoPesquisa . '%');
            }

            /*
            if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
                $qry->where('datacadastro', '>=', $request->datainiciofiltro);
            }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
                $qry->where('datacadastro', '>=', $request->datainiciofiltro);
                $qry->where('datacadastro', '<=', $request->datafinalfiltro);
            }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
                $qry->where('datacadastro', '<=', $request->datafinalfiltro);
            }

            if($request->unidademedidafiltro > 0) {
                $qry->where('estprunidademedida', $request->unidademedidafiltro);
            }

            if($request->valorfiltro > 0 && $request->valorfiltrofinal <= 0) {
                $qry->where('estprqtdatual', '>=', $request->valorfiltro);
            }else if($request->valorfiltro > 0 && $request->valorfiltrofinal > 0) {
                $qry->where('estprqtdatual', '>=', $request->valorfiltro);
                $qry->where('estprqtdatual', '<=', $request->valorfiltrofinal);
            }else if($request->valorfiltro <= 0 && $request->valorfiltrofinal > 0) {
                $qry->where('estprqtdatual', '<=', $request->valorfiltrofinal);
            }

            if($request->fkidfintipodespesafiltro > 0) {
                $qry->where('fkidfintipodespesa',  $request->fkidfintipodespesafiltro );
            }

            if($request->fkidfinvinculodespesafiltro > 0) {
                $qry->where('fkidfinvinculodespesa',  $request->fkidfinvinculodespesafiltro );
            }

            if($request->fkidfinmarcadorfiltro > 0) {
                $qry->where('fkidfinmarcador',  $request->fkidfinmarcadorfiltro );
            }

            if($request->filtroperiodo == 2) {
                $qry->where('estprinttipo', 0);
            }else if($request->filtroperiodo == 1) {
                $qry->where('estprinttipo', 1);
            }*/
        }
        return $qry;
    }

    public function lista(Request $request)
    {
        //$query = self::initQuery();
        //$qry1 = clone $query;
        $qry = Gestor::where('id', '>', 0);
        //$qry = self::montaQuery($qry1, $request);

        /*
        $qry->leftjoin('cliente', 'cidade.id', '=', 'cliente.fkidcidade')->leftjoin('ramoatividade', 'ramoatividade.id', '=', 'cliente.fkidramoatividade')->select('cliente.*')->with('cidade')
        ->groupBy('cliente.id')->orderBy($request->campoordem != 'null' ? $request->campoordem : 'identificacao', strlen($request->ordem) > 0 ? $request->ordem : 'desc');*/

        /*$qry->with('cidade')
        ->groupBy('cliente.id')->orderBy($request->campoordem != 'null' ? $request->campoordem : 'identificacao', strlen($request->ordem) > 0 ? $request->ordem : 'desc');*/

        $registros = $qry->paginate($request->regPg);
        //$registros = $qry1->paginate($request->regPg);
        //->offset($offset)->limit($limit)->get();
        return Tools::setResponse('success', $registros, '');
    }


    public function removerId($id)
    {
        $reg = Gestor::find($id);
        if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
            return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
        }else{     
            if($reg->delete()) {
                return Tools::setResponse('success', $reg, 'Registro Salvo com Sucesso');
            } else {
                return Tools::setResponse('fail', null, 'Impossível Processar, entre em contato com o Suporte');                
            }
        }
    }


    public function getAll()
    {
        $query = Gestor::where('id', '>', 0);
        $query->orderBy('id', 'asc');
        $registros = $query->get();
        try {
            return json_encode($registros);
        } catch (Exception $e) {
            $except = $e->getMessage();
            return [];
        }
        
    }


    
    public function getGestores()
    {
        $query = Gestor::where('id', '>', 0);
        $query->orderBy('id', 'asc');
        $registros = $query->get();
        try {
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', [], 'Falha ao Obter Dados');
        }
        
    }

}
