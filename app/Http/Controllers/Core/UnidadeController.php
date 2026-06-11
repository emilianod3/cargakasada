<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\Tools;
use App\Models\EmailUnidade;
use App\Models\FoneUnidade;
use App\Models\SiteUnidade;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Unidade;
use App\Models\UnidadeArq;
use App\Models\UnidadeContrato;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;


class UnidadeController extends Controller
{

    public function inicio()
    {
        $gestor = Tools::getGestor();
        return view('geral.unidade', ['cliente' => $gestor]);
    }

    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    $qry = Unidade::where('fkidgestor', $gestor);
                }else{
                    $qry = Unidade::where('id','>',0);
                }
            }else{
                if($gestor > 0){
                    $qry = Unidade::where('fkidgestor', $gestor);
                }else{
                    return Tools::setResponse('fail', null, 'Impossível Processar.');
                }
            }
        }
        return $qry;
    }
    
    public function lista(Request $request)
    {
        $query = self::initQuery();
        /*exato  ou amplo */
        if($request->tipofiltro == 'exato') {
        }


        if(strlen($request->campoPesquisa) > 0) {
            $query->where(function ($q) use ($request) {
                $q->where('undidentificacao', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('undtexto', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unnomefantasia', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('undcep', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('undinscrmunicipal', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('undbairro', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('undcomplemento', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('undobs', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('undnumero', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('undendereco', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('uncnpj', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unddesignacao', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('undinscrestadual', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unhorariofuncionamentomanha', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unhorariofuncionamentotarde', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('undobs', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unlema', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unhistoria', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('undadosgeograficos', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unddataasscontrato', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unddatainicontrato', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unddatafimcontrato', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unddatacadastro', 'like', '%' . $request->campoPesquisa . '%');
                
                $q->orWhereRaw("DATE_FORMAT(unddatainicontrato, '%d/%m/%Y') LIKE ?", '%' . $request->campoPesquisa . '%');
                $q->orWhereRaw("DATE_FORMAT(unddatacadastro, '%d/%m/%Y') LIKE ?", '%' . $request->campoPesquisa . '%');
                $q->orWhereRaw("DATE_FORMAT(unddataasscontrato, '%d/%m/%Y') LIKE ?", '%' . $request->campoPesquisa . '%');
                $q->orWhereRaw("DATE_FORMAT(unddatafimcontrato, '%d/%m/%Y') LIKE ?", '%' . $request->campoPesquisa . '%');
                // Pesquisa em Relacionamentos (sub-consultas)
                $q->orWhereHas('emails', function ($q_email) use ($request) {
                    $q_email->where('euemail', 'like', $request->campoPesquisa);
                })
                ->orWhereHas('fones', function ($q_fone) use ($request) {
                    $q_fone->where('funumero', 'like', $request->campoPesquisa);
                });
            });

        }
 
        if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('unddatacadastro', '>=', $request->datainiciofiltro.' 00:00:00');
        }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('unddatacadastro', '>=', $request->datainiciofiltro.' 00:00:00');
            $query->where('unddatacadastro', '<=', $request->datafinalfiltro.' 23:59:59');  
        }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('unddatacadastro', '<=', $request->datafinalfiltro.' 23:59:59'); 
        }

        if($request->statusfiltro == 2) { 
            $query->where('unsiteativo', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('unsiteativo', '>', 0);
        }

        $query->with('anexos');
        $query->groupBy('unidade.id');
        $query->WhereNotIn('id',[1]);
        $query->orderBy(strlen($request->campoOrdem) > 0 ? $request->campoOrdem : 'unddatacadastro', strlen($request->ordem) > 0 ? $request->ordem : 'desc');

        try {
            $registros = $query->paginate($request->regPg);
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return response()->json([
                'status' => 'fail',
                'data' => [],
                'message' => ''
            ]);
        }
    }


    public function removerId($id1)
    {
        $reg = Unidade::find($id1);
        if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
			return false;
		}

        $contratos = UnidadeContrato::where('fkidunidade', $id1)->exists();
        $publicacoes = SiteUnidade::where('fkidunidade', $id1)->exists();
        $emails = EmailUnidade::where('fkidunidade', $id1)->exists();
        $fones = FoneUnidade::where('fkidunidade', $id1)->exists();

        if($contratos || $publicacoes || $emails || $fones){
            return false;
        }

        $arquivos = UnidadeArq::where('fkidunidade', $id1)->get();
        $deletearquivos = false;
        foreach ($arquivos as $arq) {
            try{
                Tools::deleteFile($arq->uapath);
                $arq->delete();
                $deletearquivos = true;
            }catch(Exception $e){
                $except = $e->getMessage();
                $deletearquivos = false;
                break;
            }
        }
        try {
            if($reg->delete()) {
                return true;
            }else{
                return false;
            }
        } catch (Exception $e) {
            $except = $e->getMessage();
            return false;
        }
    }

    public function getId($id)
    {
        $registro = Unidade::find($id);
        return $registro;
    }    

    public function salvar(Request $request)
    {
        $validator = Validator::make(
            [
                'undidentificacao' => $request->undidentificacao,
            ]
            , [
                'undidentificacao' => 'required|string|min:1|max:255',
            ],
            [
                'undidentificacao.required' => 'Necessário Informar a identificação',
                'undidentificacao.min' => 'Necessário Informar a identificação',
                'undidentificacao.max' => 'Informe um Conteúdo Válido',
            ]);

        if ($validator->fails()) {

            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            if($request->get('id') > 0)
            {
                $reg = Unidade::find($request->get('id'));
                if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                    return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                }                
            }
            else
            {
                $reg = new Unidade;
                $reg->unddatacadastro = Carbon::now()->toDateTimeString();
            }

            $reg->undidentificacao = strlen($request->get('undidentificacao')) > 0 ? $request->undidentificacao : '';
            $reg->undclienteprincipal = $request->undclienteprincipal;
            $reg->undtexto = strlen($request->undtexto) > 0 ? $request->undtexto : '';
            $reg->unnomefantasia = strlen($request->unnomefantasia) > 0 ? $request->unnomefantasia : '';
            $reg->undcep = strlen($request->undcep) > 0 ? $request->undcep : '';
            $reg->undinscrmunicipal = strlen($request->undinscrmunicipal) > 0 ? $request->undinscrmunicipal : '';
            $reg->unddataasscontrato = strlen($request->unddataasscontrato) > 0 ? Carbon::parse($request->unddataasscontrato)->toDateTimeString() : null;
            $reg->unddatainicontrato = strlen($request->unddatainicontrato) > 0 ? Carbon::parse($request->unddatainicontrato)->toDateTimeString() : null;
            $reg->unddatafimcontrato = strlen($request->unddatafimcontrato) > 0 ? Carbon::parse($request->unddatafimcontrato)->toDateTimeString() : null;
            $reg->uncnpj = strlen($request->uncnpj) > 0 ? $request->uncnpj : '';
            $reg->undendereco = strlen($request->undendereco) > 0 ? $request->undendereco : '';
            $reg->undbairro = strlen($request->undbairro) > 0 ? $request->undbairro : '';
            $reg->undnumero = strlen($request->undnumero) > 0 ? $request->undnumero : '';            
            $reg->undcomplemento = strlen($request->undcomplemento) > 0 ? $request->undcomplemento : '';
            $reg->unddesignacao = strlen($request->unddesignacao) > 0 ? $request->unddesignacao : '';
            $reg->undinscrestadual = strlen($request->undinscrestadual) > 0 ? $request->undinscrestadual : '';
            $reg->unhorariofuncionamentomanha = strlen($request->unhorariofuncionamentomanha) > 0 ? $request->unhorariofuncionamentomanha : '';
            $reg->unhorariofuncionamentotarde = strlen($request->unhorariofuncionamentotarde) > 0 ? $request->unhorariofuncionamentotarde : '';
            $reg->undobs = strlen($request->undobs) > 0 ? $request->undobs : '';
            $reg->unlema = strlen($request->unlema) > 0 ? $request->unlema : '';
            $reg->uncomochegar = strlen($request->uncomochegar) > 0 ? $request->uncomochegar : null;
            $reg->ungeorreferencia = strlen($request->ungeorreferencia) > 0 ? $request->ungeorreferencia : null;
            $reg->unhistoria = strlen($request->unhistoria) > 0 ? $request->unhistoria : null;
            $reg->undadosgeograficos = strlen($request->undadosgeograficos) > 0 ? $request->undadosgeograficos : null;
            $reg->untiposite = $request->untiposite;
            $reg->unsiteativo = $request->unsiteativo;
            $reg->unmodelosite = $request->unmodelosite;
            $reg->fkidramoatividade = $request->fkidramoatividade;
            $reg->fkidcidade = $request->fkidcidade > 0 ? $request->fkidcidade : 0;
            $reg->fkidgestor = Tools::getGestor();
            $reg->undversao = Carbon::now()->toDateTimeString();
            $reg->flagexibe = $request->flagexibe;
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

    public function getAll()
    {
        $gestor = Tools::getGestor();
        $query = Unidade::where('fkidgestor', $gestor)->orderBy('undidentificacao', 'asc');
        $registros = $query->get();
        return $registros;
    }

    public function getUnidadePrincipalContrato()
    {
        $query = Unidade::where('undclienteprincipal', 1);
        $registros = $query->get();
        if($registros->count() > 0)
        {
            return 1;
        }else{
            return 0;
        }
    }


    public function getAllSession()
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    $query = Unidade::where('fkidgestor', $gestor);
                }else{
                    //$query = FormaPagamento::where('id','>',0);
                    return Tools::setResponse('fail', null, 'Impossível Processar');
                }
            }else{
                if($gestor > 0){
                    $query = Unidade::where('fkidgestor', $gestor);
                }else{
                    return Tools::setResponse('fail', [], 'Impossível Listar');
                }
            }
            $query->groupBy('id');
            $query->orderBy('undidentificacao', 'asc');
            $registros = $query->get();
            Session::forget('unidades');
            Session::put('unidades', json_encode($registros));
            return json_encode($registros);
        }
    }
}
