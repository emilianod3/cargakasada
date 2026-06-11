<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Core\Tools;
use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\Usuario;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GrupoController extends Controller
{
    public function inicio()
    {
        return view('geral.controleacessos');
    }

    public function getGrupoVisitantes()
    {
        $query = Grupo::find(3);
        if(!isset($query)) {
            $reg = new Grupo();
            $reg->id = 3;
            $reg->gidentificacao = 'Visitantes';
            $reg->gstatus = 1;
            $reg->gversao = Carbon::now()->toDateTimeString();
            // $reg->flaguser = Session::get('user')->id;
            $reg->ganotation = 'Automaticamente Criado pelo sistema';
            $reg->flagatualiza = 1;
            $reg->flagdelete = 0;
            $reg->save();
            return $reg;
        } else {
            return $query;
        }
    }


    public function listar($regPg = 5, $campoOrdem = 'gidentificacao', $ordem = 'asc', $campoPesquisa = '')
    {
        $query = Grupo::Where('id', '>', 0);
        /*leftjoin('ramoatividade as rm', 'unidade.fkidramoatividade', '=', 'rm.id')
            ->leftjoin('cidade as cid', 'unidade.fkidcidade', '=', 'cid.id')
            ->select([DB::raw('unidade.id, undidentificacao, undclienteprincipal, undtexto, unnomefantasia, undcep,
            undinscrmunicipal, unddatacadastro, unddataasscontrato, unddatainicontrato, unddatafimcontrato, uncnpj,
            fkidramoatividade, fkidcidade,undendereco,undbairro,undnumero,undcomplemento,unddesignacao,undinscrestadual,
            unhorariofuncionamentomanha,unhorariofuncionamentotarde,undobs,unlema,undversao,unsiteativo,untiposite,
            unmodelosite,flagexibe, cid.cdidentificacao, rm.rtidentificacao')]);*/
        if(strlen($campoPesquisa) > 0) {
            $query->where('gidentificacao', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('ganotation', 'like', '%' . $campoPesquisa . '%');
        }
        $query->orderBy($campoOrdem, $ordem);
        $registros = $query->paginate($regPg);
        return $registros;
    }


    public function salvar(Request $request)
    {
        $validator = Validator::make(
            [
            'gidentificacao' => $request->gidentificacao,
        ],
            [
            'gidentificacao' => 'required|string|min:1|max:255',
        ],
            [
            'gidentificacao.required' => 'Necessário Informar a Identificação',
            'gidentificacao.string' => 'Necessário Informar a Identificação',
            'gidentificacao.min' => 'Registro vazio',
            'gidentificacao.max' => 'Informe um Conteúdo Válido',
        ]
        );

        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        } else {
            if($request->get('id') > 0) {
                $reg = Grupo::find($request->get('id'));
            } else {
                $reg = new Grupo();
            }
            $reg->gidentificacao = $request->get('gidentificacao');
            $reg->ganotation = $request->get('ganotation');
            $reg->gversao = Carbon::now()->toDateTimeString();
            $reg->gstatus = $request->gstatus == 'true' ? 1 : 0;
            $reg->flagdelete = 0;
            $reg->flagatualiza = 1;
            $reg->flaguser = Session::get('user')->id;

            if($reg->save()) {
                return Tools::setResponse('success', null, 'Registro salvo com Sucesso');
            } else {
                return Tools::setResponse('fail', null, 'Não foi possível cadastrar, entre em contato com o Suporte');
            }
        }
    }

    public function remover($id)
    {
        $reg = Grupo::find($id);
        if($reg->delete()) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function getId($id)
    {
        $registro = Grupo::find($id);
        return $registro;
    }

    public function getAll()
    {
        $query = Grupo::where('gstatus', '>', 0)->orderBy('gidentificacao', 'asc');
        $registros = $query->get();
        return $registros;

    }

    public function getListAll()
    {
        Session::forget('grupos');
        $query = Grupo::where('gstatus', '>', 0)->orderBy('gidentificacao', 'asc');
        $query->orderBy('gidentificacao', 'asc');
        //$registros = $query->paginate(300);
        $registros = $query->get();

        try {
            Session::put('grupos', $registros);
            return json_encode($registros);
        } catch (Exception $e) {
            return [];
        }

    }

/*
    public function relatorio(Request $request)
    {
        if($request->tipo == 1) {
            $query = Usuario::leftjoin('unico', 'usuario.fkidunico', '=', 'unico.id')
            ->leftjoin('grupo', 'usuario.fkidgrupo', '=', 'grupo.id')
            ->select([DB::raw('usuario.id, ulogin, fkidgrupo, ustatus, uversao, uanotation,
            fkidunico, gidentificacao, unidentificacao, unapelido, CONCAT(unidentificacao,"-",unapelido) as nomeMembro')]);
            $query->where('ustatus', '>', 0)->orderBy('unidentificacao', 'asc')->get();
            $registrostratar = $query->get();
            foreach ($registrostratar as $obj) {
                $obj->ulogin = $obj->ulogin .' ('.Tools::getStatus($obj->ustatus).')'.(strlen($obj->unidentificacao) > 0 ? '  '.$obj->nomeMembro : '').(strlen($obj->gidentificacao) > 0 ? ' - '.$obj->gidentificacao : '');
                $obj->uversao = Tools::formataData($obj->uversao);
            }
        } else {
            $query = Grupo::Where('id', '>', 0);
            if(strlen($request->campoPesquisa) > 0) {
                $query->where('gidentificacao', 'like', '%' . $request->campoPesquisa . '%')
                    ->orWhere('ganotation', 'like', '%' . $request->campoPesquisa . '%');
            }

            $query->orderBy($request->campoordem, $request->ordem)->groupBy('id');
            $registrostratar = $query->get();
            foreach ($registrostratar as $obj) {
                $obj->gidentificacao = $obj->gidentificacao .' ('.Tools::getStatus($obj->gstatus).')';
                $obj->gversao = Tools::formataData($obj->gversao);
            }

        }




        $registros = json_encode($registrostratar);
        $dir = sys_get_temp_dir();
        $tmp = tempnam($dir, "rep");
        file_put_contents($tmp, '{"dadosreport":'.$registros.'}');
        $data_file = $tmp;
        $options = [
            'format' => [$request->extensao],
            'params' => ['parametertitle' => 'Relação de '.($request->tipo == 1 ? 'Usuários' : 'Grupos'), 'parametertop' => ENV('CLIENT_DATA_NAME'), 'parameterbottom' => ENV('CLIENT_DATA_ADDRESS').' - '.ENV('CLIENT_DATA_CITY').'/'.ENV('CLIENT_DATA_STATE'),
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
            $jasper = new PHPJasper();

            try {
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

            if(file_exists($output.'.'.$request->extensao)) {
                return array('tipo' => 'ok', 'extensao' => $request->extensao, 'arquivo' => $relatorioOutFile);
            } else {
                return Tools::setResponse('fail', null, 'Impossível Gerar Relatório');
            }
        } else {
            return Tools::setResponse('fail', null, 'Impossível Gerar Relatório, referencia Inválida');
        }
    }*/
}
