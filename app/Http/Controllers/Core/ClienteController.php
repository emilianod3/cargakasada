<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Core\Tools;
use App\Models\Cliente;
use Facade\FlareClient\Http\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ClienteController extends Controller
{
    public function inicio()
    {
        $gestor = Tools::getGestor();
        return view('sistema.cliente', ['cliente' => $gestor]);
    }

    
    public function getClienteCnpj($cnpj)
    {
        $reg = Cliente::where('cnpj','like', '%' . $cnpj . '%')->first();
        return $reg;
    }

    public static function initQuery($qry = null){
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    $qry = Cliente::where('cliente.fkidgestor', $gestor);
                }else{
                    $qry = Cliente::where('cliente.fkidgestor','=',0);
                }
            }else{
                if($gestor > 0){
                    $qry = Cliente::where('cliente.fkidgestor', $gestor);
                }else{
                    return Tools::setResponse('fail', null, 'Impossível Listar');
                }
            }
        }else{
            $qry = Cliente::where('cliente.id','<=',0);
        }
        return $qry;    
    }


    public static function montaQuery($qry = null, $request = null){

        if($qry != null){
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
        $qry = Cliente::where('id','>',0);
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
        $reg = Cliente::find($id);
        if($reg->delete()){
            return 'true';
        }else{
            return 'false';
        }
    }

}
