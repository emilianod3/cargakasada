<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Core\Tools;
use App\Models\Cidade;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use PHPJasper\PHPJasper;

class CidadeController extends Controller
{
    public function inicio()
    {
        $gestor = Tools::getGestor();
        return view('auxiliar.cidade', ['cliente' => $gestor]);
    }

    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()) {
            if(Tools::getGrupoGeral()) {
                if($gestor > 0) {
                    //$qry = Cidade::where('cidade.fkidgestor', $gestor);
                    $qry = Cidade::where('cidade.id', '>', 0);
                } else {
                    $qry = Cidade::where('cidade.id', '>', 0);
                }
            } else {
                if($gestor > 0) {
                    //$qry = Cidade::where('cidade.fkidgestor', $gestor);
                    $qry = Cidade::where('cidade.id', '>', 0);
                } else {
                    return Tools::setResponse('fail', null, 'Impossível Processar.');
                }
            }
        }
        return $qry;
    }

    public function get($id)
    {
        $reg = Cidade::find($id);
        return $reg;
    }

    public function remover($id)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR'); /*Desativa ou remove do banco */
        if($sistemadesativar > 0) {
            $reg = Cidade::find($id);
            if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
            }

            Tools::setAtividade(Tools::getUser(), 3, $id, 'Desativar o Registro', 'Registro '.$id);
            //$reg->cstatus = 0;
            return Tools::msgpadrao($reg->save(), 'desativar');
        } else {
            $reg = Cidade::find($id);
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
            $regs = Cidade::whereIn('id', $ids);
            $qtd = $regs->count();
            Tools::setAtividade(Tools::getUser(), 3, 0, 'Desativar Registros em Lote', 'Registros '.implode(" ", $ids));
            //$regs->cstatus = 0;
            return Tools::msgpadrao($regs->save(), 'desativar', $qtd);
        } else {
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = Cidade::whereIn('id', $ids);
            $qtd = $regs->count();
            Tools::setAtividade(Tools::getUser(), 3, 0, 'Remover Registros em Lote', 'Registros '.implode(" ", $ids));
            return Tools::msgpadrao($regs->delete(), 'delete', $qtd);
        }
    }


    public function getcount()
    {
        $total_counts = Cidade::count();

        //$query = Cidade::leftjoin('estado', 'cidade.fkidestado', '=', 'estado.id')->select([DB::raw('cdidentificacao, cidade.id, cidade.fkidestado, etidentificacao, estado.etsigla, CONCAT(cdidentificacao,"-",etsigla) AS estado')]);
        //$query->orderBy('cdidentificacao', 'asc');
        //$registros = $query->paginate(300);
        //$registros = $query->get();

        try {
            return $total_counts;
            //return $registros;
        } catch (Exception $e) {
            return 0;
        }

    }

    public function setcidade($cidade = '', $estado = 0)
    {
        if(strlen($cidade) > 1){
            $cidadeexiste = Cidade::where('cdidentificacao', '=', trim($cidade))->first();
            if($cidadeexiste){
                return $cidadeexiste->id;
            }else{
                try {
                    $reg = new Cidade();
                    $reg->cdidentificacao = trim($cidade);
                    $reg->fkidestado = $estado;
                    $reg->flagdelete = 0;
                    $reg->flaguser = 0;
                    $reg->flagatualiza = 1;
                    $reg->cdversao = Carbon::now()->toDateTimeString();
                    $reg->save();
                    return $reg->id;
                } catch (Exception $e) {
                    $except = $e->getMessage();
                    return 0;
                }            
            }
        }else{
            return 0;
        }
    }
    

    public function getall()
    {
        /*$gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    $query = Cidade::where('fkidgestor', $gestor);
                }else{
                    $query = Cidade::where('id','>',0);
                }
            }else{
                if($gestor > 0){
                    $query = Cidade::where('fkidgestor', $gestor);
                }else{
                    return Tools::setResponse('fail', [], 'Impossível Listar');
                }
            }
            //$query->where('cstatus','>','0');
            $query->groupBy('id');
            $query->orderBy('cdidentificacao', 'asc');
            //$registros = json_encode($query->get());
            $registros = $query->get();

            try {
                return Tools::setResponse('success', $registros, '');
            } catch (Exception $e) {
                return Tools::setResponse('fail', [], 'Falha ao obter dados');
            }
        return $registros;
        }*/
        Session::forget('cidades');
        $query = Cidade::leftjoin('estado', 'cidade.fkidestado', '=', 'estado.id')->select([DB::raw('cdidentificacao, cidade.id, cidade.fkidestado, etidentificacao, estado.etsigla, CONCAT(cdidentificacao,"-",etsigla) AS estado')]);
        $query->orderBy('cdidentificacao', 'asc');
        //$registros = $query->paginate(300);
        $registros = $query->get();

        try {
            Session::put('cidades', $registros);
            return json_encode($registros);
        } catch (Exception $e) {
            return [];
        }

        /*try {
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            return response()->json( [
                'status' => 'fail',
                'data' => [],
                'message' => 'Falha ao obter dados'
            ]);
        }*/
    }

    /*
    public function getAll()
    {
        //$registros = Cidade::leftjoin('estado', 'cidade.fkidestado', '=', 'estado.id')->select([DB::raw('cdidentificacao, cidade.id, cidade.fkidestado, etidentificacao, estado.etsigla, CONCAT(etidentificacao,"-",etsigla) AS estado')]);
        //$registros = Cidade::all(); ->orderBy('etidentificacao', 'asc');
        //return json_encode($registros);
        //return $registros;

        $query = Cidade::leftjoin('estado', 'cidade.fkidestado', '=', 'estado.id')->select([DB::raw('cdidentificacao, cidade.id, cidade.fkidestado, etidentificacao, estado.etsigla, CONCAT(cdidentificacao,"-",etsigla) AS estado')]);
        $query->orderBy('cdidentificacao', 'asc');
        //$registros = $query->paginate(300);
        $registros = $query->get();
        return json_encode($registros);
    }*/


    public function lista(Request $request)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()) {
            $query = self::initQuery();
            if(strlen($request->campoPesquisa) > 0) {
                $query->where('cidade.cdidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            }
            $query->with('estado');
            $query->groupBy('id');
            $query->orderBy(strlen($request->campoOrdem) > 1 ? $request->campoOrdem : 'id', strlen($request->ordem) > 0 ? $request->ordem : 'desc');

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

        $query = self::initQuery();

        /*
        if($request->statusfiltro == 0) {
            $query->where('endstatus','>=', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('endstatus', 1);
        }else if($request->statusfiltro == 2) {
            $query->where('endstatus', 0);
        }*/

        if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('cdversao', '>=', $request->datainiciofiltro.' 00:00:00');
        }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('cdversao', '>=', $request->datainiciofiltro.' 00:00:00');
            $query->where('cdversao', '<=', $request->datafinalfiltro.' 23:59:59');                
        }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('cdversao', '<=', $request->datafinalfiltro.' 23:59:59'); 
        }

        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';


        if(strlen($request->campoPesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('cidade.cdidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            //$query->orwhere('endereco.endbairro', 'like', '%' . $request->campoPesquisa . '%');
            //$query->orwhere('endereco.endtipologradouro', 'like', '%' . $request->campoPesquisa . '%');
        }else if(strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('cidade.cdidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            //$query->where('endereco.endbairro', 'like', '%' . $request->campoPesquisa . '%');
            //$query->where('endereco.endtipologradouro', 'like', '%' . $request->campoPesquisa . '%');
        }

        //$query->with('zeladoria','reservaitem','formapagamento','unico');
        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc');
        $query->with('estado');
        $query->groupBy('id');

        try {
            $registros = $query->paginate($request->regPg);
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }

    public function existente($value, $estado)
    {
        if (strlen($value) > 0) {
            $query = Cidade::where('cdidentificacao', '=', $value)->where('fkidestado', '=', $estado)->get();//->where('ustatus', 1);
            if ($query->isEmpty()) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function salvar(Request $request)
    {
        $validator = Validator::make(
            [
                'identificacao' => $request->cdidentificacao,
                'estado' => $request->estado,
            ],
            [
                'identificacao' => 'required|string|min:1|max:255',
                'estado' => 'required|integer|min:1',
            ],
            [
                'identificacao.required' => 'Necessário Informar a Identificação',
                'identificacao.min' => 'Registro vazio',
                'identificacao.max' => 'Informe um Conteúdo Válido',
                'estado.required' => 'Necessário Informar o Estado',
                'estado.integer' => 'Necessário Informar o Estado',
                'estado.min' => 'Necessário Informar o Estado',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'data' => [],
                'message' => $validator->errors()->first()
            ]);
        } else {
            if($request->id > 0) {
                $reg = Cidade::find($request->get('id'));
                if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                    return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                }          
            } else {
                $reg = new Cidade();
            }
            if(self::existente($request->cdidentificacao, $request->estado)) {
                return response()->json([
                    'status' => 'fail',
                    'data' => [],
                    'message' => 'Falha no Salvamento de Registro'
                ]);
            }

            $reg->cdidentificacao = $request->get('cdidentificacao');
            $reg->cdversao = Carbon::now()->toDateTimeString();
            $reg->fkidestado = $request->estado;
            $reg->flagdelete = 0;
            $reg->flagatualiza = 1;
            $reg->flaguser = Session::get('user')->id;

            if($reg->save()) {
                return response()->json([
                'status' => 'success',
                'data' => $reg,
                'message' => 'Registro Realizado com Sucesso'
                ]);
            } else {
                Tools::setAtividade(0, 9, 0, 'Tentativa Registro de Registro', '');
                return response()->json([
                    'status' => 'fail',
                    'data' => [],
                    'message' => 'Falha no Salvamento de Registro'
                ]);
            }

        }
    }



























    /*
    public function editar($id, $page)
    {
        $registro = Cidade::find($id);
        Session::put('page', $page);
        return view('geral.cidades', compact( 'registro'));
    }



    public function salvar(Request $request)
    {
        $validator = Validator::make(
            [
                'identificacao' => $request->edtidentificacao,
                'estado' => $request->estado,
            ]
            , [
                'identificacao' => 'required|string|min:1|max:255',
                'estado' => 'required',
            ],
            [
                'identificacao.required' => 'Necessário Informar a Identificação',
                'identificacao.min' => 'Registro vazio',
                'identificacao.max' => 'Informe um Conteúdo Válido',
                'estado.required' => 'Necessário Informar o Estado',
            ]);

        if ($validator->fails()) {
            //return redirect()->route('registrar')->withInput()->withErrors($validator)->with('message', $validator->errors()->first());
            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            if($request->get('id') > 0)
            {
                $reg= Cidade::find($request->get('id'));
            }
            else
            {
                $reg= new Cidade;
            }
            $reg->cdidentificacao=$request->get('edtidentificacao');
            $reg->cdversao=Carbon::now()->toDateTimeString();
            $reg->fkidestado=$request->estado;
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

    public function loadListagem($regPg =5, $campoOrdem ='cdidentificacao', $ordem = 'asc', $campoPesquisa = '')
    {
        //$query = Cidade::where('id','>', 0);
        $query = Cidade::leftjoin('estado', 'cidade.fkidestado', '=', 'estado.id')->select([DB::raw('cdidentificacao, cidade.id, cidade.fkidestado, etidentificacao, estado.etsigla, CONCAT(etidentificacao,"-",etsigla) AS estado')]);
        if (strlen($campoPesquisa) > 0) {
            $query->where('cdidentificacao', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('etidentificacao', 'like', '%' . $campoPesquisa . '%');
        }
        $query->orderBy($campoOrdem, $ordem);
        $registros = $query->paginate($regPg);
        return $registros;
    }

    public function getId($id)
    {
        $registro = Cidade::find($id);
        return $registro;
    }

    public function getAll()
    {
        //$registros = Cidade::leftjoin('estado', 'cidade.fkidestado', '=', 'estado.id')->select([DB::raw('cdidentificacao, cidade.id, cidade.fkidestado, etidentificacao, estado.etsigla, CONCAT(etidentificacao,"-",etsigla) AS estado')]);
        //$registros = Cidade::all(); ->orderBy('etidentificacao', 'asc');
        //return json_encode($registros);
        //return $registros;

        $query = Cidade::leftjoin('estado', 'cidade.fkidestado', '=', 'estado.id')->select([DB::raw('cdidentificacao, cidade.id, cidade.fkidestado, etidentificacao, estado.etsigla, CONCAT(cdidentificacao,"-",etsigla) AS estado')]);
        $query->orderBy('cdidentificacao', 'asc');
        //$registros = $query->paginate(300);
        $registros = $query->get();
        return json_encode($registros);
    }

    public function relatorio(Request $request)
    {
        $query = Cidade::leftjoin('estado', 'cidade.fkidestado', '=', 'estado.id')->select([DB::raw('cdidentificacao, cidade.id, cidade.fkidestado, etidentificacao, estado.etsigla, CONCAT(etidentificacao,"-",etsigla) AS estado')]);
        if (strlen($request->campopesquisa) > 0) {
            $query->where('cdidentificacao', 'like', '%' . $request->campopesquisa . '%')
                ->orWhere('etidentificacao', 'like', '%' . $request->campopesquisa . '%');
        }

        $query->orderBy($request->campoordem, $request->ordem)->groupBy('id');
        $registrostratar = $query->get();
        foreach ($registrostratar as $obj) {
            $obj->cdidentificacao = $obj->id .' - '.$obj->cdidentificacao .'      -       '.($obj->fkidestado > 0 ? $obj->etidentificacao.' - '.$obj->etsigla : '');
            $obj->cdversao = Resources::formataData($obj->cdversao);
        }

        $registros = json_encode($registrostratar);
        $dir = sys_get_temp_dir();
        $tmp = tempnam($dir, "rep");
        file_put_contents ($tmp, '{"dadosreport":'.$registros.'}');
        $data_file = $tmp;
        $options = [
            'format' => [$request->extensao],
            'params' => ['parametertitle' => 'Relação de Cidades', 'parametertop' => ENV('CLIENT_DATA_NAME'), 'parameterbottom' => ENV('CLIENT_DATA_ADDRESS').' - '.ENV('CLIENT_DATA_CITY').'/'.ENV('CLIENT_DATA_STATE'), 
            'parameterbottom2' => ENV('CLIENT_DATA_TELEPHONE').' - '.ENV('CLIENT_DATA_EMAIL').' - '.ENV('CLIENT_DATA_SITE'), 'parameterlogo' => ENV('APP_URL').ENV('IMGRELTOPO')],
            'locale' => 'pt_BR',
            'db_connection' => [
                'driver' => 'json',
                'data_file' => $data_file,
                'json_query' => 'dadosreport'
            ]
        ];

        $relatorioFile = $request->report;
        $relatorioPath = storage_path('app/reports/').$relatorioFile;  
        $relatorioOutFile = rand().date("YmdHis");
        $relatorioOutPath = public_path('storage/cache/relatorios/');

        if(file_exists($relatorioPath)) {
            if(!file_exists($relatorioOutPath)) { // Cria pasta para o projeto, caso não já exista uma
                File::makeDirectory($relatorioOutPath);
            }

            $input = $relatorioPath;
            $output = $relatorioOutPath.$relatorioOutFile;
            $jasper = new PHPJasper;

            try{
                $jasper->process(
                    $input,
                    $output,
                    $options
                )->execute();
                unlink($tmp);
            } catch (Exception $e) {
                unlink($tmp);
                return Tools::setResponse('fail', null, 'Impossível Gerar Relatório');
            }

            if(file_exists($output.'.'.$request->extensao))
            {
                return array('tipo' => 'ok', 'extensao' => $request->extensao, 'arquivo' => $relatorioOutFile);
            }else {
                return Tools::setResponse('fail', null, 'Impossível Gerar Relatório');
            }                
        }else{
            return Tools::setResponse('fail', null, 'Impossível Gerar Relatório, referencia Inválida');
        }
    }*/
















    /*
    public function relatorio(Request $request)
    {
        $query = Cidade::leftjoin('estado', 'cidade.fkidestado', '=', 'estado.id')->select([DB::raw('cdidentificacao, cidade.id, cidade.fkidestado, etidentificacao, estado.etsigla, CONCAT(etidentificacao,"-",etsigla) AS estado')]);
        if (strlen($request->campopesquisa) > 0) {
            $query->where('cdidentificacao', 'like', '%' . $request->campopesquisa . '%')
                ->orWhere('etidentificacao', 'like', '%' . $request->campopesquisa . '%');
        }

        $query->orderBy($request->campoordem, $request->ordem)->groupBy('id');
        $registrostratar = $query->get();
        foreach ($registrostratar as $obj) {
            $obj->cdidentificacao = $obj->id .' - '.$obj->cdidentificacao .'      -       '.($obj->fkidestado > 0 ? $obj->etidentificacao.' - '.$obj->etsigla : '');
            $obj->cdversao = Tools::formataData($obj->cdversao);
        }

        $registros = json_encode($registrostratar);
        $dir = sys_get_temp_dir();
        $tmp = tempnam($dir, "rep");
        file_put_contents ($tmp, '{"dadosreport":'.$registros.'}');
        $data_file = $tmp;
        $options = [
            'format' => [$request->extensao],
            'params' => ['parametertitle' => 'Relação de Cidades', 'parametertop' => ENV('CLIENT_DATA_NAME'), 'parameterbottom' => ENV('CLIENT_DATA_ADDRESS').' - '.ENV('CLIENT_DATA_CITY').'/'.ENV('CLIENT_DATA_STATE'),
            'parameterbottom2' => ENV('CLIENT_DATA_TELEPHONE').' - '.ENV('CLIENT_DATA_EMAIL').' - '.ENV('CLIENT_DATA_SITE'), 'parameterlogo' => ENV('APP_URL').ENV('IMGRELTOPO')],
            'locale' => 'pt_BR',
            'db_connection' => [
                'driver' => 'json',
                'data_file' => $data_file,
                'json_query' => 'dadosreport'
            ]
        ];

        $relatorioFile = $request->report;
        $relatorioPath = storage_path('app/reports/').$relatorioFile;
        $relatorioOutFile = rand().date("YmdHis");
        $relatorioOutPath = public_path('storage/cache/relatorios/');

        if(file_exists($relatorioPath)) {
            if(!file_exists($relatorioOutPath)) { // Cria pasta para o projeto, caso não já exista uma
                File::makeDirectory($relatorioOutPath);
            }

            $input = $relatorioPath;
            $output = $relatorioOutPath.$relatorioOutFile;
            $jasper = new PHPJasper;

            try{
                $jasper->process(
                    $input,
                    $output,
                    $options
                )->execute();
                unlink($tmp);
            } catch (Exception $e) {
                unlink($tmp);
                return Tools::setResponse('fail', null, 'Impossível Gerar Relatório');
            }

            if(file_exists($output.'.'.$request->extensao))
            {
                return array('tipo' => 'ok', 'extensao' => $request->extensao, 'arquivo' => $relatorioOutFile);
            }else {
                return Tools::setResponse('fail', null, 'Impossível Gerar Relatório');
            }
        }else{
            return Tools::setResponse('fail', null, 'Impossível Gerar Relatório, referencia Inválida');
        }
    }    */
}
