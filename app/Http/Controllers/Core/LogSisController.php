<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Core\Tools;
use App\Models\LogSis;
use App\Models\Unico;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Support\Facades\DB;

class LogSisController extends Controller
{
    public function inicio()
    {
        $gestor = Tools::getGestor();
        return view('controle.logsis', ['cliente' => $gestor]);
    }
    
    public static function initQuery($qry = null){
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    //$qry = Cidade::where('cidade.fkidgestor', $gestor);
                    $qry = LogSis::where('logsis.id','>',0);
                }else{
                    $qry = LogSis::where('logsis.id','>',0);
                }
            }else{
                if($gestor > 0){
                    //$qry = Cidade::where('cidade.fkidgestor', $gestor);
                    $qry = LogSis::where('logsis.id','>',0);
                }else{
                    return Tools::setResponse('fail', null, 'Impossível Processar.');
                }
            }
        }

        return $qry;
    }

    public function getId($id)
    {
        $reg = logsis::find($id);
        return $reg;
    }

    public function remover($id)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR'); /*Desativa ou remove do banco */
        if($sistemadesativar > 0){
            $reg = logsis::find($id);
            Tools::setAtividade(Tools::getUser(), 3, $id,  'Desativar o Registro', 'Registro '.$id);
            //$reg->cstatus = 0;
            return Tools::msgpadrao($reg->save(), 'desativar');
        }else{
            $reg = logsis::find($id);
            Tools::setAtividade(Tools::getUser(), 3, $id,  'Remover o Registro', 'Registro '.$id);
            return Tools::msgpadrao($reg->delete(), 'delete');
        }
    }

    public function removerLote(Request $request)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR');
        if($sistemadesativar > 0){
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = logsis::whereIn('id', $ids);
            $qtd = $regs->count();
            Tools::setAtividade(Tools::getUser(), 3, 0,  'Desativar Registros em Lote', 'Registros '.implode(" ",$ids));
            //$regs->cstatus = 0;
            return Tools::msgpadrao($regs->save(), 'desativar', $qtd);
        }else{
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = logsis::whereIn('id', $ids);
            $qtd = $regs->count();
            Tools::setAtividade(Tools::getUser(), 3, 0,  'Remover Registros em Lote', 'Registros '.implode(" ",$ids));
            return Tools::msgpadrao($regs->delete(), 'delete', $qtd);
        }
    }

    public function getall()
    {
        
        $query = logsis::leftjoin('logs.fktipoacao', 'logs.fkidusuario', '=', 'usuario.id')->select([DB::raw('fkidusuario, lgonde, fktipoacao, idregistro, lgtexto, CONCAT(fkidusuario,"-",etsigla) AS fkidusuario')]);
        $query->orderBy('fkidusuario', 'asc');
        //$registros = $query->paginate(300);
        $registros = $query->get();

        try {
            return json_encode($registros);
        } catch (Exception $e) {
            return [];
        }
    }


    public function lista(Request $request)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            $query = self::initQuery();
            $query->selectRaw('logsis.lgtexto, logsis.lgonde, logsis.idregistro, logsis.id, logsis.lgversao, logsis.fktipoacao, logsis.fkidusuario, tipoa.tpidentificacao as tipoacao, unico.unidentificacao as usuario, usuario.fkidunico');
            if(strlen($request->campoPesquisa) > 0) {
                $query->where('logsis.lgonde', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('logsis.lgtexto', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhere('logsis.id', 'like', '%' . $request->campoPesquisa . '%');
            }
            
            $query->leftjoin('tipoacao as tipoa', 'logsis.fktipoacao', '=', 'tipoa.id');
            $query->leftjoin('usuario', 'logsis.fkidusuario', '=', 'usuario.id');
            $query->leftjoin('unico', 'unico.id', '=', 'usuario.fkidunico');
            //->where('tipoa.tpstatus' > 0);
            
            $query->groupBy('logsis.id');
            $query->orderBy(strlen($request->campoOrdem) > 1 ? 'logsis.'.$request->campoOrdem : 'logsis.id', strlen($request->ordem) > 0 ? $request->ordem : 'desc');
    
            try {
                $registros = $query->paginate($request->regPg);

            return response()->json([
                'status' => 'success',
                'data' => $registros,
                'message' => '' 
                ]);
            } catch (Exception $e) {
                return Tools::setResponse('fail', [], 'Falha ao obter dados');
    
            }
    
          return $registros;
        }
    }

    public function listaLogs(Request $request)
    {
        $email = false;
        $user = false;
        $fone = false;
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    $query = Unico::where('fkidgestor', $gestor);
                }else{
                    $query = Unico::where('id','>',0);
                }
            }else{
                if($gestor > 0){
                    $query = Unico::where('fkidgestor', $gestor);
                }else{
                    return Tools::setResponse('fail', [], 'Impossível Listar');
                }
            }

            /*exato  ou amplo */
            if(strlen($request->tipofiltro) == 'exato') {
            }

            if(strlen($request->campoPesquisa) > 0) {
                $email = true;
                $fone = true;
                $user = true;
                $query->where('unidentificacao', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unendereco', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unbairro', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unnumero', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('uncomplemento', 'like', '%' . $request->campoPesquisa . '%')
                //->orWhere('fnnumero', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unobs', 'like', '%' . $request->campoPesquisa . '%');
                //$query->whereRelation('pacote','pobs','like', '%' . $request->campoPesquisa . '%');
                $query->orwhereRelation('email','euemail','like', '%' . $request->campoPesquisa . '%');
                $query->orwhereRelation('fone','fnnumero','like', '%' . $request->campoPesquisa . '%');
                /*$query->with(['fone' => function($q) use ($request) {
                    return $q->where('fnnumero', 'like', '%'.$request->campoPesquisa.'%');
                }]);
                $query->with(['email' => function($q) use ($request) {
                    return $q->where('euemail', 'like', '%'.$request->campoPesquisa.'%');
                             /*->orWhere('that_too', '=', 1);*
                }]);*/
            }else{
                $query = Tools::setClassWith($query, 'email', $email);
                $email = true;

                $query = Tools::setClassWith($query, 'fone', $fone);
                $fone = true;

                $query = Tools::setClassWith($query, 'usuario', $user);
                $user = true;
                
            }

            if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
                $query->where('undatacadastro', '>=', $request->datainiciofiltro);
            }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
                $query->where('undatacadastro', '>=', $request->datainiciofiltro);
                $query->where('undatacadastro', '<=', $request->datafinalfiltro);  
            }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
                $query->where('undatacadastro', '<=', $request->datafinalfiltro); 
            }

            /*
            if($request->loginfiltro == 1) {
                $query = Tools::setClassWith($query, 'email', $email);
                $email = true;
                //$query->whereNull('email');
                //$query->whereColumn('euemail');
                $query->withCount('email')->having('email_count','>',0);
            }

            if($request->loginfiltro > 1) { 
                $query = Tools::setClassWith($query, 'email', $email);
                $email = true;
                $query->withCount('email')->having('email_count','<=',0);
            }*/

            if($request->loginfiltro == 1) {
                $query = Tools::setClassWith($query, 'usuario', $user);
                $user = true;
                //$query->whereNull('email');
                //$query->whereColumn('euemail');
                $query->withCount('usuario')->having('usuario_count','>',0);
            }

            if($request->loginfiltro > 1) { 
                $query = Tools::setClassWith($query, 'usuario', $user);
                $user = true;
                $query->withCount('usuario')->having('usuario_count','<=',0);
            }




            if($request->statusfiltro == 2) { 
                $query->where('unstatus', 0);
            }else if($request->statusfiltro == 1) {
                $query->where('unstatus', '>', 0);
            }

            $query->with('avatar');
            $query->groupBy('unico.id');
            $query->WhereNotIn('id',[1]);
            $query->Where('fkidtipocadastro',1);
            //$query->with('usuario');
            //$query->whereRelation('usuario','ulogin','like', '%cunha@teste.com%');

            $query->orderBy(strlen($request->campoOrdem) > 0 ? $request->campoOrdem : 'unidentificacao', strlen($request->ordem) > 0 ? $request->ordem : 'desc');
            try {
                $registros = $query->paginate($request->regPg);
                //$registros = $query->groupBy('unico.id')->paginate(10);
                //->offset($offset)->limit($limit)->get(); 
                return Tools::setResponse('success', $registros, '');
            } catch (Exception $e) {
                return response()->json( [
                    'status' => 'success',
                    'data' => [],
                    'message' => ''
                ]);
            }
        
        }else{
            //return Tools::setResponse('fail', null, 'Falha no Salvamento do Registro');
            return response()->json( [
                'status' => 'fail',
                'data' => [],
                'message' => 'Falha na Listagem'
            ]);            
        }
    }

}
