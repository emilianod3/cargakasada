<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\Tools;
use App\Mail\recuperarSenha;
use App\Models\Cidade;
use App\Models\Email;
use App\Models\Fone;
use App\Models\PassRecovery;
use Modules\Core\Http\Controllers\TipoCadastroController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\TipoCadastro;
use App\Models\Unico;
use App\Models\UnicoArq;
use App\Models\Usuario;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use PHPJasper\PHPJasper;
use stdClass;

class UnicoController extends Controller
{
    public function inicio()
    {
        $gestor = Tools::getGestor();
        return view('geral.unico', ['cliente' => $gestor]);
    }


    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    $qry = Unico::where('fkidgestor', $gestor);
                }else{
                    $qry = Unico::where('id','>',0);
                }
            }else{
                if($gestor > 0){
                    $qry = Unico::where('fkidgestor', $gestor);
                }else{
                    return Tools::setResponse('fail', null, 'Impossível Processar.');
                }
            }
        }
        return $qry;
    }

    public function inserirUnicoPessoaFisica($nome, $cpf)
    {
        if (strlen($nome) > 0 && !self::verificaUnicoCPF($cpf)) {
            $tipoCad = new TipoCadastro();
            $reg = new Unico();
            $reg->unidentificacao = trim($nome);
            $reg->unapelido = trim($nome);
            $reg->untiposanguineo = '';
            $reg->unnomefantasia = '';
            $reg->uncep = '';
            $reg->uninscrmunicipal = '';
            $reg->unserie = '';
            $reg->undatacadastro = Carbon::now()->toDateString();
            $reg->undatanasc = null;
            $reg->unrg = '';
            $reg->untituloeleitor = '';
            $reg->unnumcarttrabalho = '';
            $reg->uncpf = $cpf;
            $reg->unpis = '';
            $reg->unzonaeleitoral = '';
            $reg->unsecaoeleitoral = '';
            $reg->uncnpj = '';
            $reg->fkidtipocadastro = $tipoCad->getPessoaFisica()->id;
            $reg->fkidclassesocial = null;
            $reg->fkidprofissao = null;
            $reg->fkidramoatividade = null;
            $reg->fkidescolaridade = null;
            $reg->fkidtratamento = null;
            $reg->fkidestadocivil = null;
            $reg->fkidcidade = null;
            $reg->fkidraca = 0;
            $reg->unendereco = '';
            $reg->unbairro = '';
            $reg->unnumero = '';
            $reg->uncomplemento = '';
            $reg->unsexo = '';
            $reg->undesignacao = '';
            $reg->uninscrestadual = '';
            $reg->unobs = '';
            $reg->unoptasimples = 0;
            $reg->unversao = Carbon::now()->toDateTimeString();
            // $reg->flaguser = Session::get('user')->id;
            $reg->flagatualiza = 1;
            $reg->flagexibe = 1;
            $reg->flagdelete = 0;
            $reg->save();
            return $reg;
        } else {
            return null;
        }
    }


    public function verificaUnicoCPF($cpf)
    {
        $gestor = Tools::getGestor();
        if (strlen($cpf) > 0) {
            $query = Unico::where('uncpf', '=', $cpf);//->where('ustatus', 1);
            if(strlen($gestor) > 0){
                $query->where('fkidgestor', '=', $gestor);
            }
            $result = $query->get();
            if ($result->isEmpty()) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function verificaUnicoCPF2($cpf)
    {
        $gestor = Tools::getGestor();
        if (strlen($cpf) > 0) {
            $query = Unico::where('uncpf', '=', $cpf);//->where('ustatus', 1);
            if(strlen($gestor) > 0){
                $query->where('fkidgestor', '=', $gestor);
            }
            $result = $query->get();         
            return $result->count();
        }
    }
    

    public function verificaUnicoNome($nome)
    {
        $gestor = Tools::getGestor();
        if (strlen($nome) > 0) {
            $query = Unico::where('unidentificacao', '=', trim($nome));
            if(strlen($gestor) > 0){
                $query->where('fkidgestor', '=', $gestor);
            }
            $query->get();         
            return $query->count();
        }
    }    

    public function removerId($id)
    {
        $reg = Unico::find($id);
        if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
			return false;
		}

        $emails = Email::where('fkidunico', $id)->exists();
        $fones = Fone::where('fkidunico', $id)->exists();

        if($emails || $fones){
            return false;
        }

        $arquivos = UnicoArq::where('fkidunico', $id)->get();
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


    public function getUnicoId($id)
    {
        return $reg = Unico::with(['foneprincipal', 'foneprincipalcelular', 'emailprincipal','cidade','avatar','usuario','anexos'])->find($id);
    }

    public function getUnico($id)
    {
        try {
            $reg = Unico::with(['foneprincipal', 'foneprincipalcelular', 'emailprincipal','cidade','avatar','usuario','anexos'])->find($id);
            return Tools::setResponse('success', $reg, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha no Processamento');
        }
    }



    public function getFisica()
    {
        $query = Unico::leftjoin('tipocadastro', 'unico.fkidtipocadastro', '=', 'tipocadastro.id')->select([DB::raw('tipocadastro.id, unidentificacao, unapelido, unico.id, tpcidentificacao')]);
        $query->where('tpcidentificacao', 'like', '%Física%');
        //$query->where('tipocadastro.id',2);
        $query->orderBy('unidentificacao', 'asc');
        $registros = $query->get();
        return json_encode($registros);
    }

    public function getJuridica()
    {
        $query = Unico::leftjoin('tipocadastro', 'unico.fkidtipocadastro', '=', 'tipocadastro.id')->select([DB::raw('tipocadastro.id, unidentificacao, unapelido, unico.id, tpcidentificacao, unnomefantasia')]);
        $query->where('tpcidentificacao', 'like', '%Juridic%');
        //$query->where('tipocadastro.id',2);
        $query->orderBy('unnomefantasia', 'asc');
        $registros = $query->get();
        return json_encode($registros);
    }

    public function listaCPF(Request $request)
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

            $query->with('avatar', 'anexos');
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

    public function loadListagem($tipocad = 1, $regPg = 5, $campoOrdem = 'dpidentificacao', $ordem = 'asc', $campoPesquisa = '')
    {
        $query = Unico::leftjoin('tipocadastro', 'unico.fkidtipocadastro', '=', 'tipocadastro.id')
            ->leftjoin('classesocial', 'unico.fkidclassesocial', '=', 'classesocial.id')
            ->leftjoin('profissao', 'unico.fkidprofissao', '=', 'profissao.id')
            ->leftjoin('ramoatividade as rm', 'unico.fkidramoatividade', '=', 'rm.id')
            ->leftjoin('escolaridade as esc', 'unico.fkidescolaridade', '=', 'esc.id')
            ->leftjoin('tratamento as tra', 'unico.fkidtratamento', '=', 'tra.id')
            ->leftjoin('estadocivil as est', 'unico.fkidestadocivil', '=', 'est.id')
            ->leftjoin('cidade as cid', 'unico.fkidcidade', '=', 'cid.id')
            //->leftjoin('unicoarq as uniarq', 'unico.id', '=', 'uniarq.fkidunico')
            ->leftJoin('unicoarq as uniarq', function ($join) {
                $join->on('unico.id', '=', 'uniarq.fkidunico')
                    ->where('uniarq.fkidtipoarq', '=', 8)
                    ->where('uniarq.uastatus', '=', 1);
            })
            ->select([DB::raw('unico.id, unidentificacao, fkidtipocadastro, unapelido, unnomefantasia, uncep,
            uninscrmunicipal, unserie,  undatacadastro, undatanasc, unrg, untituloeleitor, unnumcarttrabalho, uncpf, unpis,
            unsecaoeleitoral, unzonaeleitoral, uncnpj, fkidclassesocial, fkidprofissao, fkidramoatividade, fkidescolaridade,
            fkidtratamento, fkidestadocivil, fkidcidade, fkidraca, unendereco, unbairro, unnumero, uncomplemento, unsexo,
            undesignacao, uninscrestadual, unobs, unversao, unoptasimples, unico.flagexibe, tipocadastro.tpcidentificacao,
            classesocial.clscidentificacao, profissao.pfidentificacao, rm.rtidentificacao, esc.ecidentificacao,
            tra.ttidentificacao, est.ecidentificacao, cid.cdidentificacao, uniarq.uamyme, uniarq.uaarq ')]);
        $query->where('fkidtipocadastro', $tipocad);
        if(strlen($campoPesquisa) > 0) {
            $query->where('unidentificacao', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('unnomefantasia', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('uncpf', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('uncomplemento', 'like', '%' . $campoPesquisa . '%');
        }
        $query->orderBy($campoOrdem, $ordem);
        $registros = $query->paginate($regPg);
        return $registros;
    }

    public function listarUnicos($regPg = 5, $campoOrdem = 'unidentificacao', $ordem = 'asc', $campoPesquisa = '')
    {
        $query = Unico::leftjoin('tipocadastro', 'unico.fkidtipocadastro', '=', 'tipocadastro.id')
            ->select([DB::raw('unico.id, unidentificacao, fkidtipocadastro, unapelido, unnomefantasia, uncep,
            uninscrmunicipal, unserie,  undatacadastro, undatanasc, unrg, untituloeleitor, unnumcarttrabalho, uncpf, unpis,
            unsecaoeleitoral, unzonaeleitoral, uncnpj, fkidclassesocial, fkidprofissao, fkidramoatividade, fkidescolaridade,
            fkidtratamento, fkidestadocivil, fkidcidade, fkidraca, unendereco, unbairro, unnumero, uncomplemento, unsexo,
            undesignacao, uninscrestadual, unobs, unversao, unoptasimples, unico.flagexibe, tipocadastro.tpcidentificacao')]);

        if(strlen($campoPesquisa) > 0) {
            $query->where('unidentificacao', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('unnomefantasia', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('uncpf', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('uncomplemento', 'like', '%' . $campoPesquisa . '%');
        }
        $query->orderBy($campoOrdem, $ordem);
        $registros = $query->paginate($regPg);
        return $registros;
    }


    public function autoriaspessoafisica(Request $request)
    {
        $query = self::initQuery();
        $query->where('fkidtipocadastro', 1);
        $query->where('unstatus', 1);
        $query->whereNotIn('id', [1]);

        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';

        if(strlen($request->campopesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('unidentificacao', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('unapelido', 'like', '%' . $request->campopesquisa . '%');
            //$query->orwhere('rtcnaesecao', 'like', '%' . $request->campopesquisa . '%');
        }else if(strlen($request->campopesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('unidentificacao', 'like', '%' . $request->campopesquisa . '%');
            $query->where('unapelido', 'like', '%' . $request->campopesquisa . '%');
            //$query->where('rtcnaeversao', 'like', '%' . $request->campopesquisa . '%');
            //$query->where('rtcnaesecao', 'like', '%' . $request->campopesquisa . '%');
        }

        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc');
        $query->groupBy('id');

        try {
            $registros = $query->paginate($request->regPg);
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }



    public function pessoafisica()
    {
        $query = self::initQuery();
        $query->where('fkidtipocadastro', 1);
        $query->where('unstatus', 1);
        $query->whereNotIn('id', [1]);

        $query->orderBy('unidentificacao','asc');
        $query->groupBy('id');

        try {
            $registros = $query->get();
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }

    public function pessoajuridica()
    {
        $query = self::initQuery();
        $query->where('fkidtipocadastro', 2);
        $query->where('unstatus', 1);
        $query->whereNotIn('id', [1]);

        $query->orderBy('unidentificacao','asc');
        $query->groupBy('id');

        try {
            $registros = $query->get();
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }

    public function autoriaspessoajuridica(Request $request)
    {
        $query = self::initQuery();
        $query->where('fkidtipocadastro', 2);
        $query->where('unstatus', 1);
        $query->whereNotIn('id', [1]);

        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';

        if(strlen($request->campopesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('unidentificacao', 'like', '%' . $request->campopesquisa . '%');
            $query->orwhere('unapelido', 'like', '%' . $request->campopesquisa . '%');
            //$query->orwhere('rtcnaesecao', 'like', '%' . $request->campopesquisa . '%');
        }else if(strlen($request->campopesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('unidentificacao', 'like', '%' . $request->campopesquisa . '%');
            $query->where('unapelido', 'like', '%' . $request->campopesquisa . '%');
            //$query->where('rtcnaeversao', 'like', '%' . $request->campopesquisa . '%');
            //$query->where('rtcnaesecao', 'like', '%' . $request->campopesquisa . '%');
        }

        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc');
        $query->groupBy('id');

        try {
            $registros = $query->paginate($request->regPg);
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }

    /*
    public function getAutorias($idDoc, $idTipoDoc, $regPg = 5, $campoOrdem = 'unidentificacao', $ordem = 'asc', $campoPesquisa = '')
    {
        $query1 = Autoria::select([DB::raw('auidmembro')]);
        $ids = $query1->where('auiddocumento', $idDoc)
        ->where('autipoautor', 7)
        ->where('auidtipodoc', $idTipoDoc)->get();
        $data[] = 0;
        foreach ($ids as $id) {
            $data[] = $id->auidmembro;
        }

        $query = Unico::leftjoin('tipocadastro', 'unico.fkidtipocadastro', '=', 'tipocadastro.id')
        ->select([DB::raw('unico.id, unidentificacao, fkidtipocadastro, unapelido, unnomefantasia, uncep,
      uninscrmunicipal, unserie,  undatacadastro, undatanasc, unrg, untituloeleitor, unnumcarttrabalho, uncpf, unpis,
      unsecaoeleitoral, unzonaeleitoral, uncnpj, fkidclassesocial, fkidprofissao, fkidramoatividade, fkidescolaridade,
      fkidtratamento, fkidestadocivil, fkidcidade, fkidraca, unendereco, unbairro, unnumero, uncomplemento, unsexo,
      undesignacao, uninscrestadual, unobs, unversao, unoptasimples, unico.flagexibe, tipocadastro.tpcidentificacao')]);
        $query->whereNotIn('unico.id', $data);
        if(strlen($campoPesquisa) > 0) {
            $query->where('unidentificacao', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('unnomefantasia', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('uncpf', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('uncnpj', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('unapelido', 'like', '%' . $campoPesquisa . '%')
                ->orWhere('uncomplemento', 'like', '%' . $campoPesquisa . '%');
        }
        $query->orderBy($campoOrdem, $ordem);
        $registros = $query->paginate($regPg);
        return $registros;
    }

*/

    /*

        public function getAutoriasParecer($idParecer, $regPg = 5, $campoOrdem = 'unidentificacao', $ordem = 'asc', $campoPesquisa = '')
        {
            $query1 = ParecerAutoria::select([DB::raw('paidmembro')]);
            $ids = $query1->where('paidparecer', $idParecer)
            ->where('patipoautor', 7)->get();
            $data[] = 0;
            foreach ($ids as $id) {
                $data[] = $id->paidmembro;
            }

            $query = Unico::leftjoin('tipocadastro', 'unico.fkidtipocadastro', '=', 'tipocadastro.id')
            ->select([DB::raw('unico.id, unidentificacao, fkidtipocadastro, unapelido, unnomefantasia, uncep,
          uninscrmunicipal, unserie,  undatacadastro, undatanasc, unrg, untituloeleitor, unnumcarttrabalho, uncpf, unpis,
          unsecaoeleitoral, unzonaeleitoral, uncnpj, fkidclassesocial, fkidprofissao, fkidramoatividade, fkidescolaridade,
          fkidtratamento, fkidestadocivil, fkidcidade, fkidraca, unendereco, unbairro, unnumero, uncomplemento, unsexo,
          undesignacao, uninscrestadual, unobs, unversao, unoptasimples, unico.flagexibe, tipocadastro.tpcidentificacao')]);
            $query->whereNotIn('unico.id', $data);
            if(strlen($campoPesquisa) > 0) {
                $query->where('unidentificacao', 'like', '%' . $campoPesquisa . '%')
                    ->orWhere('unnomefantasia', 'like', '%' . $campoPesquisa . '%')
                    ->orWhere('uncpf', 'like', '%' . $campoPesquisa . '%')
                    ->orWhere('uncnpj', 'like', '%' . $campoPesquisa . '%')
                    ->orWhere('unapelido', 'like', '%' . $campoPesquisa . '%')
                    ->orWhere('uncomplemento', 'like', '%' . $campoPesquisa . '%');
            }
            $query->orderBy($campoOrdem, $ordem);
            $registros = $query->paginate($regPg);
            return $registros;
        }
    */


    public function update(Request $request)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            $validator = Validator::make(
            [
                'idregistro' => $request->idregistro,
            ]
            , [
                'idregistro' => 'required|integer|min:1',
            ],
            [
                'idregistro.required' => 'Dados Inválidos',
                'idregistro.integer' => 'Dados Inválidos',
                'idregistro.min' => 'Dados Inválidos',
            ]);
    
            if($validator->fails()){
                return Tools::setResponse('fail', null, $validator->errors()->first());
            }

            $reg = null;
            $reg = Unico::find($request->idregistro);
            if($request->campo == 'unstatus'){
                $reg->unstatus = ($reg->unstatus > 0 ? 0 : 1);
            }
            if($request->campo == 'flagexibe'){
                $reg->flagexibe = ($reg->flagexibe > 0 ? 0 : 1);
            }
            
            $reg->unversao = Carbon::now()->toDateTimeString();
            $reg->flaguser = Session::get('user')->id;
            $reg->flagatualiza = 1;
            $reg->flagdelete = 0;

            if($reg->save()){
                return Tools::msgpadrao(true, 'salvar');
            }else{
                return Tools::msgpadrao(false, 'salvar');
            }
        }
    }



    public function unicos(Request $request)
    {
        $gestor = Tools::getGestor();

        $query = Unico::leftjoin('tipocadastro', 'unico.fkidtipocadastro', '=', 'tipocadastro.id')
        ->leftjoin('fone', 'unico.id', '=', 'fone.fkidunico')
        ->leftJoin('email', function ($join) {
            $join->on('unico.id', '=', 'email.fkidunico')
                ->where('email.emusarcomoprincipal', '=', 1);
        })
        ->select([DB::raw('unico.id, unidentificacao, fkidtipocadastro, unapelido, unnomefantasia, uncep, uninscrmunicipal, unserie,  undatacadastro, undatanasc, unrg, untituloeleitor, unnumcarttrabalho, uncpf, unpis, unsecaoeleitoral, unzonaeleitoral, uncnpj, fkidclassesocial, fkidprofissao, fkidramoatividade, fkidescolaridade, fkidtratamento, fkidestadocivil, fkidcidade, fkidraca, unendereco, unbairro, unnumero, uncomplemento, unsexo, undesignacao, uninscrestadual, unobs, unversao, unoptasimples, unico.flagexibe, tipocadastro.tpcidentificacao, fone.fnnumero, email.euemail')])->with('email');

        if(strlen($request->campoPesquisaUnico) > 0) {
            $query->where('unidentificacao', 'like', '%' . $request->campoPesquisaUnico . '%')
                ->orWhere('unendereco', 'like', '%' . $request->campoPesquisaUnico . '%')
                ->orWhere('unbairro', 'like', '%' . $request->campoPesquisaUnico . '%')
                ->orWhere('unnumero', 'like', '%' . $request->campoPesquisaUnico . '%')
                ->orWhere('uncomplemento', 'like', '%' . $request->campoPesquisaUnico . '%')
                ->orWhere('fnnumero', 'like', '%' . $request->campoPesquisaUnico . '%')
                ->orWhere('euemail', 'like', '%' . $request->campoPesquisaUnico . '%')
                ->orWhere('unrg', 'like', '%' . $request->campoPesquisaUnico . '%')
                ->orWhere('uncpf', 'like', '%' . $request->campoPesquisaUnico . '%')
                ->orWhere('unobs', 'like', '%' . $request->campoPesquisaUnico . '%');
        }
        $query->groupBy('unico.id');
        $query->orderBy('unidentificacao', 'asc');


        try {
            $registros = $query->get();
            return response()->json([
                'status' => 'success',
                'data' => $registros,
                'message' => ''
            ]);
        } catch (Exception $e) {
            $except = $e->getMessage();
            return response()->json([
                'status' => 'fail',
                'data' => [],
                'message' => 'Falha ao obter dados'
            ]);

        }

        return $registros;
    }


    public function getAllSession()
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()) {
            if(Tools::getGrupoGeral()) {
                if($gestor > 0) {
                    $query = Unico::where('fkidgestor', $gestor);
                } else {
                    $query = Unico::where('id', '>', 0);
                }
            } else {
                if($gestor > 0) {
                    $query = Unico::where('fkidgestor', $gestor);
                } else {
                    return json_encode(null);
                }
            }
            $query = $query->leftjoin('tipocadastro', 'unico.fkidtipocadastro', '=', 'tipocadastro.id')->where('fkidtipocadastro', 1)
            ->select([DB::raw('unico.id, unidentificacao, unapelido, unnomefantasia, tipocadastro.tpcidentificacao')])->where('unico.id', '>', 0);
            $registros = $query->orderBy('unidentificacao', 'asc')->get();
            return json_encode($registros);
        } else {
            return json_encode(null);
        }
    }

    public function getAll()
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()) {
            if(Tools::getGrupoGeral()) {
                if($gestor > 0) {
                    $query = Unico::where('fkidgestor', $gestor);
                } else {
                    $query = Unico::where('id', '>', 0);
                }
            } else {
                if($gestor > 0) {
                    $query = Unico::where('fkidgestor', $gestor);
                } else {
                    return response()->json([
                        'status' => 'fail',
                        'data' => [],
                        'message' => 'Impossível Listar'
                    ]);
                }
            }
            $query->where('unstatus', '>', '0')->with('usuario');
            $query->groupBy('id');
            $query->orderBy('unidentificacao', 'asc');
            //$registros = json_encode($query->get());
            $registros = $query->get();

            try {
                return Tools::setResponse('success', $registros, '');
            } catch (Exception $e) {
                return Tools::setResponse('fail', [], 'Falha ao obter dados');
            }
            return $registros;
        }
    }

    /*
    public function getAll()
    {
      $query = Unico::leftjoin('tipocadastro', 'unico.fkidtipocadastro', '=', 'tipocadastro.id')
      ->select([DB::raw('unico.id, unidentificacao, fkidtipocadastro, unapelido, unnomefantasia, uncep,
      uninscrmunicipal, unserie,  undatacadastro, undatanasc, unrg, untituloeleitor, unnumcarttrabalho, uncpf, unpis,
      unsecaoeleitoral, unzonaeleitoral, uncnpj, fkidclassesocial, fkidprofissao, fkidramoatividade, fkidescolaridade,
      fkidtratamento, fkidestadocivil, fkidcidade, fkidraca, unendereco, unbairro, unnumero, uncomplemento, unsexo,
      undesignacao, uninscrestadual, unobs, unversao, unoptasimples, unico.flagexibe, tipocadastro.tpcidentificacao')]);
      $query->orderBy('unidentificacao', 'asc');
      $registros = $query->get();
      return json_encode($registros);
        //return $registros;
    }
    */


    public function salvarcpf(Request $request)
    {
        
        $validagestor = Tools::validaGestor();
        if($validagestor <= 0){
            return array('status' => 'fail', 'message' => 'Impossível Prosseguir, entre em contato com o Gestor');
        }
        

        $validator = Validator::make(
            [
            'unidentificacao' => $request->unidentificacao,
            'fkidtipocadastro' => $request->fkidtipocadastro,
        ],
            [
            'unidentificacao' => 'required|string|min:1|max:255',
            'fkidtipocadastro' => 'required|integer|min:1',
        ],
            [
            'unidentificacao.required' => 'Necessário Informar a identificação',
            'unidentificacao.min' => 'Necessário Informar a identificação',
            'unidentificacao.max' => 'Informe um Conteúdo Válido',
            'fkidtipocadastro.required' => 'Necessário Informar o tipo de cadastro',
            'fkidtipocadastro.integer' => 'Necessário Informar o tipo de cadastro',
            'fkidtipocadastro.min' => 'Necessário Informar o tipo de cadastro',
        ]
        );

        if ($validator->fails()) {
            //return redirect()->route('registrar')->withInput()->withErrors($validator)->with('message', $validator->errors()->first());
            return array('status' => 'fail', 'message' => $validator->errors()->first());
        } else {            

            if($request->get('id') > 0) {
                if(self::verificaUnicoNome($request->unidentificacao) > 1){
                    return array('status' => 'fail', 'message' => 'Necessário Informar uma Identificação Válida');
                }else if(strlen($request->uncpf) > 0){
                    if(self::verificaUnicoCPF2($request->uncpf) > 1){
                        return array('status' => 'fail', 'message' => 'CPF Não pode ser Utilizado');
                    }
                }                
                $reg = Unico::find($request->get('id'));
            } else {
                if(self::verificaUnicoNome($request->unidentificacao) > 0){
                    return array('status' => 'fail', 'message' => 'Necessário Informar uma Identificação Válida');
                }else if(strlen($request->uncpf) > 0){
                    if(self::verificaUnicoCPF2($request->uncpf) > 0){
                        return array('status' => 'fail', 'message' => 'CPF Não pode ser Utilizado');
                    }
                }
                $reg = new Unico();
                $reg->undatacadastro = Carbon::now()->toDateTimeString();
            }
            $reg->unidentificacao = trim($request->get('unidentificacao'));
            $reg->unapelido = $request->unapelido != null ? $request->unapelido : '';
            $reg->untiposanguineo = $request->untiposanguineo != null ? $request->untiposanguineo : '';
            $reg->uncep = $request->uncep != null ? $request->uncep : '';
            $reg->unserie = $request->unserie != null ? $request->unserie : '';
            $reg->undatanasc = $request->undatanasc != null ? Carbon::parse($request->undatanasc)->toDateTimeString() : null;
            $reg->unrg = $request->unrg != null ? $request->unrg : '';
            $reg->untituloeleitor = $request->untituloeleitor != null ? $request->untituloeleitor : '';
            $reg->unnumcarttrabalho = $request->unnumcarttrabalho != null ? $request->unnumcarttrabalho : '';
            $reg->uncpf = $request->uncpf != null ? $request->uncpf : '';
            $reg->unpis = $request->unpis != null ? $request->unpis : '';
            $reg->unzonaeleitoral = $request->unzonaeleitoral != null ? $request->unzonaeleitoral : '';
            $reg->unsecaoeleitoral = $request->unsecaoeleitoral != null ? $request->unsecaoeleitoral : '';
            $reg->uncnpj = $request->uncnpj != null ? $request->uncnpj : '';
            $reg->fkidtipocadastro = $request->fkidtipocadastro != null ? $request->fkidtipocadastro : 0;
            $reg->fkidclassesocial = $request->fkidclassesocial > 0 ? $request->fkidclassesocial : 0;
            $reg->fkidprofissao = $request->fkidprofissao > 0 ? $request->fkidprofissao : 0;
            $reg->fkidramoatividade = $request->fkidramoatividade > 0 ? $request->fkidramoatividade : 0;
            $reg->fkidescolaridade = $request->fkidescolaridade > 0 ? $request->fkidescolaridade : 0;
            $reg->fkidtratamento = $request->fkidtratamento > 0 ? $request->fkidtratamento : 0;
            $reg->fkidestadocivil = $request->fkidestadocivil > 0 ? $request->fkidestadocivil : 0;
            $reg->fkidraca = $request->fkidraca != null ? $request->fkidraca : 0;
            $reg->fkidcidade = $request->fkidcidade != null ? $request->fkidcidade : 0;
            $reg->unendereco = $request->unendereco != null ? $request->unendereco : '';
            $reg->unbairro = $request->unbairro != null ? $request->unbairro : '';
            $reg->unnumero = $request->unnumero != null ? $request->unnumero : '';
            $reg->uncomplemento = $request->uncomplemento != null ? $request->uncomplemento : '';
            $reg->unsexo = $request->unsexo != null ? $request->unsexo : '';
            $reg->unobs = $request->unobs != null ? $request->unobs : '';
            $reg->unversao = Carbon::now()->toDateTimeString();
            $reg->unstatus = $request->fkstatus > 0 ? $request->fkstatus : 0;
            $reg->flagexibe = $request->flagexibe > 0 ? $request->flagexibe : 0;
            $reg->flagdelete = 0;
            $reg->flagatualiza = 1;
            $reg->flaguser = Session::get('user')->id;
            $reg->fkidgestor = Session::get('user')->fkidgestor;
            $reg->fkidunidade = 0;

            if($reg->save()) {
                Session::forget('unicoslist');
                Session::put('unicoslist', self::getAllSession());
                if($request->origem != null) {
                    return $reg;
                } else {
                    return array('status' => 'success', 'message' => 'Registro salvo com Sucesso');
                }
            } else {
                return array('status' => 'fail', 'message' => 'Não foi possível cadastrar, entre em contato com o Suporte');
            }
        }
    }



    public function salvar(Request $request)
    {
        $validator = Validator::make(
            [
            'unidentificacao' => $request->unidentificacao,
            'fkidtipocadastro' => $request->fkidtipocadastro,
        ],
            [
            'unidentificacao' => 'required|string|min:1|max:255',
            'fkidtipocadastro' => 'required|integer|min:1',
        ],
            [
            'unidentificacao.required' => 'Necessário Informar a identificação',
            'unidentificacao.min' => 'Necessário Informar a identificação',
            'unidentificacao.max' => 'Informe um Conteúdo Válido',
            'fkidtipocadastro.required' => 'Necessário Informar o tipo de cadastro',
            'fkidtipocadastro.integer' => 'Necessário Informar o tipo de cadastro',
            'fkidtipocadastro.min' => 'Necessário Informar o tipo de cadastro',
        ]
        );

        if ($validator->fails()) {
            //return redirect()->route('registrar')->withInput()->withErrors($validator)->with('message', $validator->errors()->first());
            return Tools::setResponse('fail', null, $validator->errors()->first());
        } else {
            if($request->get('id') > 0) {
                $reg = Unico::find($request->get('id'));
                if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                    return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                }

            } else {
                $reg = new Unico();
                $reg->undatacadastro = Carbon::now()->toDateTimeString();
            }
            $reg->unidentificacao = trim($request->get('unidentificacao'));
            $reg->unapelido = $request->unapelido != null ? $request->unapelido : '';
            $reg->untiposanguineo = $request->untiposanguineo != null ? $request->untiposanguineo : '';
            $reg->unnomefantasia = $request->unnomefantasia != null ? $request->unnomefantasia : '';
            $reg->uncep = $request->uncep != null ? $request->uncep : '';
            $reg->uninscrmunicipal = $request->uninscrmunicipal != null ? $request->uninscrmunicipal : '';
            $reg->unserie = $request->unserie != null ? $request->unserie : '';
            $reg->undatacadastro = Carbon::now()->toDateString();
            $reg->undatanasc = $request->undatanasc != null ? Carbon::parse($request->undatanasc)->toDateTimeString() : null;
            $reg->unrg = $request->unrg != null ? $request->unrg : '';
            $reg->untituloeleitor = $request->untituloeleitor != null ? $request->untituloeleitor : '';
            $reg->unnumcarttrabalho = $request->unnumcarttrabalho != null ? $request->unnumcarttrabalho : '';
            $reg->uncpf = $request->uncpf != null ? $request->uncpf : '';
            $reg->unpis = $request->unpis != null ? $request->unpis : '';
            $reg->unzonaeleitoral = $request->unzonaeleitoral != null ? $request->unzonaeleitoral : '';
            $reg->unsecaoeleitoral = $request->unsecaoeleitoral != null ? $request->unsecaoeleitoral : '';
            $reg->uncnpj = $request->uncnpj != null ? $request->uncnpj : '';
            $reg->fkidtipocadastro = $request->fkidtipocadastro != null ? $request->fkidtipocadastro : 0;
            $reg->fkidclassesocial = $request->fkidclassesocial > 0 ? $request->fkidclassesocial : null;
            $reg->fkidprofissao = $request->fkidprofissao > 0 ? $request->fkidprofissao : null;
            $reg->fkidramoatividade = $request->fkidramoatividade > 0 ? $request->fkidramoatividade : null;
            $reg->fkidescolaridade = $request->fkidescolaridade > 0 ? $request->fkidescolaridade : null;
            $reg->fkidtratamento = $request->fkidtratamento > 0 ? $request->fkidtratamento : null;
            $reg->fkidestadocivil = $request->fkidestadocivil > 0 ? $request->fkidestadocivil : null;
            $reg->fkidcidade = $request->fkidcidade != null ? $request->fkidcidade : null;
            $reg->fkidraca = 0;
            $reg->unendereco = $request->unendereco != null ? $request->unendereco : '';
            $reg->unbairro = $request->unbairro != null ? $request->unbairro : '';
            $reg->unnumero = $request->unnumero != null ? $request->unnumero : '';
            $reg->uncomplemento = $request->uncomplemento != null ? $request->uncomplemento : '';
            $reg->unsexo = $request->unsexo != null ? $request->unsexo : '';
            $reg->undesignacao = $request->undesignacao != null ? $request->undesignacao : '';
            $reg->uninscrestadual = $request->uninscrestadual != null ? $request->uninscrestadual : '';
            $reg->unobs = $request->unobs != null ? $request->unobs : '';
            $reg->unoptasimples = $request->unoptasimples != null ? $request->unoptasimples : '';
            $reg->unversao = Carbon::now()->toDateTimeString();
            $reg->flagexibe = $request->flagexibe == 'true' ? 1 : 0;
            $reg->flagdelete = 0;
            $reg->flagatualiza = 1;
            $reg->flaguser = Session::get('user')->id;

            if($reg->save()) {
                Session::forget('unicoslist');
                Session::put('unicoslist', self::getAllSession());
                if($request->origem != null) {
                    return $reg;
                } else {
                    return Tools::setResponse('success', null, 'Registro salvo com Sucesso');
                }
            } else {
                return Tools::setResponse('fail', null, 'Não foi possível cadastrar, entre em contato com o Suporte');
            }
        }
    }





    public function salvarperfil(Request $request)
    {
        $validator = Validator::make(
        [
            'idprincipal' => $request->idprincipal,
            'iduser' => $request->iduser,
        ],
        [
            'idprincipal' => 'required|integer|min:1',
            'iduser' => 'required|integer|min:1',
        ],
        [
            'idprincipal.required' => 'Informe a Pessoa',
            'idprincipal.min' => 'Informe a Pessoa',
            'idprincipal.integer' => 'Informe a Pessoa',
            'iduser.required' => 'Informe o Login de Usuário',
            'iduser.min' => 'Informe o Login de Usuário',
            'iduser.integer' => 'Informe o Login de Usuário',
        ]
        );

        if ($validator->fails()) {
            //return redirect()->route('registrar')->withInput()->withErrors($validator)->with('message', $validator->errors()->first());
            return Tools::setResponse('fail', null, $validator->errors()->first());
        } else {
            if($request->idprincipal > 0 && $request->iduser > 0) {
                $uni = Unico::find($request->idprincipal);
                $reg = Usuario::find($request->iduser);
                
                if(strlen($request->passw) > 4){
                    $reg->upassword = bcrypt($request->passw);
                    $reg->uhash = bcrypt($request->passw);
                }

                /*
                $reg->unidentificacao = $request->get('unidentificacao');
                $reg->unapelido = $request->unapelido != null ? $request->unapelido : '';
                $reg->untiposanguineo = $request->untiposanguineo != null ? $request->untiposanguineo : '';
                $reg->unnomefantasia = $request->unnomefantasia != null ? $request->unnomefantasia : '';
                $reg->uncep = $request->uncep != null ? $request->uncep : '';
                $reg->uninscrmunicipal = $request->uninscrmunicipal != null ? $request->uninscrmunicipal : '';
                $reg->unserie = $request->unserie != null ? $request->unserie : '';
                $reg->undatacadastro = Carbon::now()->toDateString();
                $reg->undatanasc = $request->undatanasc != null ? Carbon::parse($request->undatanasc)->toDateTimeString() : null;
                $reg->unrg = $request->unrg != null ? $request->unrg : '';
                $reg->untituloeleitor = $request->untituloeleitor != null ? $request->untituloeleitor : '';
                $reg->unnumcarttrabalho = $request->unnumcarttrabalho != null ? $request->unnumcarttrabalho : '';
                $reg->uncpf = $request->uncpf != null ? $request->uncpf : '';
                $reg->unpis = $request->unpis != null ? $request->unpis : '';
                $reg->unzonaeleitoral = $request->unzonaeleitoral != null ? $request->unzonaeleitoral : '';
                $reg->unsecaoeleitoral = $request->unsecaoeleitoral != null ? $request->unsecaoeleitoral : '';
                $reg->uncnpj = $request->uncnpj != null ? $request->uncnpj : '';
                $reg->fkidtipocadastro = $request->fkidtipocadastro != null ? $request->fkidtipocadastro : 0;
                $reg->fkidclassesocial = $request->fkidclassesocial > 0 ? $request->fkidclassesocial : null;
                $reg->fkidprofissao = $request->fkidprofissao > 0 ? $request->fkidprofissao : null;
                $reg->fkidramoatividade = $request->fkidramoatividade > 0 ? $request->fkidramoatividade : null;
                $reg->fkidescolaridade = $request->fkidescolaridade > 0 ? $request->fkidescolaridade : null;
                $reg->fkidtratamento = $request->fkidtratamento > 0 ? $request->fkidtratamento : null;
                $reg->fkidestadocivil = $request->fkidestadocivil > 0 ? $request->fkidestadocivil : null;
                $reg->fkidcidade = $request->fkidcidade != null ? $request->fkidcidade : null;
                $reg->fkidraca = 0;
                $reg->unendereco = $request->unendereco != null ? $request->unendereco : '';
                $reg->unbairro = $request->unbairro != null ? $request->unbairro : '';
                $reg->unnumero = $request->unnumero != null ? $request->unnumero : '';
                $reg->uncomplemento = $request->uncomplemento != null ? $request->uncomplemento : '';
                $reg->unsexo = $request->unsexo != null ? $request->unsexo : '';
                $reg->undesignacao = $request->undesignacao != null ? $request->undesignacao : '';
                $reg->uninscrestadual = $request->uninscrestadual != null ? $request->uninscrestadual : '';
                $reg->unobs = $request->unobs != null ? $request->unobs : '';
                $reg->unoptasimples = $request->unoptasimples != null ? $request->unoptasimples : '';*/

                $reg->uversao = Carbon::now()->toDateTimeString();
                $reg->flagdelete = 0;
                $reg->flagatualiza = 1;
                $reg->flaguser = Session::get('user')->id;                                                                 

                try {
                    if($reg->save()) {
                        if($request->origem != null) {
                            return $reg;
                        } else {
                            return Tools::setResponse('success', $reg, 'Processamento Realizado com Sucesso');
                        }
                    }
                } catch (Exception $e) {
                    $except = $e->getMessage();
                    return Tools::setResponse('fail', null, 'Impossível Processar Solicitação');
                }

            } else {
                return Tools::setResponse('fail', null, 'Impossível Processar Solicitação');
            }
        }
    }


    public function updatetermouso($idUser = 0, $tipo = 1)
    {
        if ($idUser > 0) {
            if ($user = Usuario::find($idUser)) {
                $user->uversao = Carbon::now()->toDateTimeString();
                $user->uaceitetermosuso = $tipo;
                $user->udataaceitetermosuso = ($tipo > 0 ? Carbon::now()->toDateTimeString() : null);
                $user->flagatualiza = 1;
                $user->flagdelete = 0;
                $user->save();
                if($tipo > 0){
                    Tools::setAtividade(0, 8, 0, 'Aceite dos Termos de Uso', 'Atualização via Sistema');
                }else{
                    Tools::setAtividade(0, 8, 0, 'Declina dos Termos de Uso', 'Atualização via Sistema');
                }
                //return true;
                //return Redirect::back()->with('result', true);
                return response()->json(['result' => true, 'usuario' => $user]);
            } else {
                //return false;
                //return Redirect::back()->with('result', false);
                return response()->json(['result' => false, 'usuario' => null]);
            }
        } else {
            //return false;
            //return Redirect::back()->with('result', false);
            return response()->json(['result' => false, 'usuario' => null]);
        }
    }

    public function updatetermousoobj($idUser, $tipo = 1)
    {
        if ($idUser > 0) {
            if ($user = Usuario::find($idUser)) {
                $user->uversao = Carbon::now()->toDateTimeString();
                $user->uaceitetermosuso = $tipo;
                $user->udataaceitetermosuso = ($tipo > 0 ? Carbon::now()->toDateTimeString() : null);
                $user->flagatualiza = 1;
                $user->flagdelete = 0;
                $user->save();
                if($tipo > 0){
                    Tools::setAtividade(0, 8, 0, 'Aceite dos Termos de Uso', 'Atualização via Sistema');
                }else{
                    Tools::setAtividade(0, 8, 0, 'Declina dos Termos de Uso', 'Atualização via Sistema');
                }
                return $user;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }


    public function cadastronovousuario(Request $request)
    {
        /*&& self::verificaUnicoNome($request->unidentificacao) <= 0*/ 
        try{
        if (strlen($request->unidentificacao) > 0 && self::verificaUnicoCPF($request->uncpf) == false) {
            $reg = new Unico();
            $reg->unidentificacao = trim($request->unidentificacao);
            $reg->unapelido = trim($request->unapelido);
            $reg->untiposanguineo = '';
            $reg->unnomefantasia = '';
            $reg->uncep = (strlen($request->uncep) > 0 ? $request->uncep : '');
            $reg->uninscrmunicipal = '';
            $reg->unserie = '';
            $reg->undatacadastro = Carbon::now()->toDateString();
            $reg->undatanasc = $request->undatanasc;
            $reg->unrg = $request->unrg;
            $reg->untituloeleitor = '';
            $reg->unnumcarttrabalho = '';
            $reg->uncpf = $request->uncpf;
            $reg->unpis = '';
            $reg->unzonaeleitoral = '';
            $reg->unsecaoeleitoral = '';
            $reg->uncnpj = '';
            $reg->fkidtipocadastro = $request->tipopessoa;
            $reg->fkidclassesocial = 0;
            $reg->fkidprofissao = 0;
            $reg->fkidramoatividade = 0;
            $reg->fkidescolaridade = 0;
            $reg->fkidtratamento = 0;
            $reg->fkidestadocivil = 0;
            $reg->fkidcidade = self::setcidade($request->fkidcidade, $request->unidadefederativa);
            $reg->fkidraca = 0;
            $reg->unendereco = (strlen($request->unendereco) > 0 ? $request->unendereco : '');
            $reg->unbairro = (strlen($request->unbairro) > 0 ? $request->unbairro : '');
            $reg->unnumero = (strlen($request->unnumero) > 0 ? $request->unnumero : '');
            $reg->uncomplemento = (strlen($request->uncomplemento) > 0 ? $request->uncomplemento : '');
            $reg->unsexo = $request->unsexo;
            $reg->undesignacao = '';
            $reg->uninscrestadual = '';
            $reg->unobs = '';
            $reg->unoptasimples = 0;
            $reg->unstatus = 1;
            $reg->fkidgestor = ENV('GESTOR');;
            $reg->unversao = Carbon::now()->toDateTimeString();
            // $reg->flaguser = Session::get('user')->id;
            $reg->flagatualiza = 1;
            $reg->flagexibe = 1;
            $reg->flagdelete = 0;
            if($reg->save()){
                $emailok = false;
                $userok = false;
                if(self::verificaUnicoEmail($request->euemail, $reg) <= 0){
                    $emailok = true;
                }else{
                    $reg->delete();
                    return Tools::setResponse('fail', null, 'Impossível Prosseguir, Informações Inconsistentes');
                }

                if(self::verificaUsuario($request->euemail, $reg) <= 0){
                    $userok = true;
                }else{
                    $reg->delete();
                    return Tools::setResponse('fail', null, 'Impossível Prosseguir, Informações Inconsistentes');
                }

                try{
                    if($emailok && $userok){
                        $regemail = new Email();
                        $regemail->euemail = $request->euemail;
                        $regemail->euanotacao = '';
                        $regemail->fkidunico = $reg->id;
                        $regemail->emusarcomoprincipal = 1;
                        $regemail->flagdelete = 0;
                        $regemail->flaguser = 0;
                        $regemail->flagatualiza = 1;
                        $regemail->emversao = Carbon::now()->toDateTimeString();
                        if($regemail->save()){
                            $reguser = new Usuario();
                            $reguser->ulogin = $request->euemail;
                            $reguser->uhash = '';
                            $reguser->ustatus = 0;
                            $reguser->upassword = Hash::make($request->senha);
                            $reguser->uaceitetermosuso = 1;
                            $reguser->udataaceitetermosuso = Carbon::now()->toDateTimeString();
                            $reguser->udatatermosuso = Carbon::now()->toDateTimeString();
                            $reguser->usolicitalocalizacao = 1;
                            $reguser->uanotation = 'Criado pelo cadastro do Site';                            
                            $reguser->fkidgrupo = 3;//visitante
                            $reguser->fkidunico = $reg->id;
                            $reguser->fkidgestor = $reg->fkidgestor;
                            $reguser->flagdelete = 0;
                            $reguser->flaguser = 0;
                            $reguser->flagatualiza = 1;
                            $reguser->udatacadastro = Carbon::now()->toDateTimeString();
                            $reguser->uversao = Carbon::now()->toDateTimeString();
                            if($reguser->save()){
                                $arquivos = true;
                                if ($request->file('files')){
                                    $arquivos = self::uploads($reg->id, $reguser->id, $request->file('files'));
                                }
                                $regfone = new Fone();
                                $regfone->fnnumero = $request->fnnumero;
                                $regfone->fkidtipofone = 2;
                                $regfone->fnanotacao = '';
                                $regfone->fkidunico = $reg->id;
                                $regfone->flagdelete = 0;
                                $regfone->flaguser = $reguser->id;
                                $regfone->flagatualiza = 1;
                                $regfone->fnversao = Carbon::now()->toDateTimeString();
                                if($regfone->save()){
                                    return Tools::setResponse('success', $reg, 'Usuário Cadastrado com Sucesso');
                                }else{
                                    if($request->file('files') && $arquivos == false){
                                        self::removerarquivos($reg->id);
                                    }
                                    $reg->delete();
                                    $regemail->delete();
                                    $reguser->delete();
                                    return Tools::setResponse('fail', null, 'Impossível Prosseguir, Informações Inconsistentes');
                                }
                            }else{
                                $reg->delete();
                                $regemail->delete();
                                return Tools::setResponse('fail', null, 'Impossível Prosseguir, Informações Inconsistentes');
                            }                      
                        }else{
                            $reg->delete();
                            return Tools::setResponse('fail', null, 'Impossível Prosseguir, Informações Inconsistentes');
                        }
                    }
                } catch (Exception $e) {
                    $except = $e->getMessage();
                    $reg->delete();
                    $regemail->delete();
                    $reguser->delete();
                    $regfone->delete();
                    return Tools::setResponse('fail', null, 'Impossível Prosseguir, Informações Inconsistentes');
                }
            }else{
                return Tools::setResponse('fail', null, 'Impossível Prosseguir, Informações Inconsistentes');
            }
        } else {
            return Tools::setResponse('fail', null, 'Impossível Prosseguir, Informações Inconsistentes');
        }
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Impossível Prosseguir, Informações Inconsistentes');
        }
    }

    /**
     * verifica se já existe o e-mail cadastrado para o mesmo gestor
     * 
     */
    public function verificaUnicoEmail($email, $unico)
    {
        $emails = DB::table('email')
            ->where('email.euemail', '=', trim($email))
            ->join('unico', function ($join) use ($unico) {
                $join->on('email.fkidunico', '=', 'unico.id')
                    ->where('unico.fkidgestor', '=', $unico->fkidgestor);
            })
            ->groupBy('email.id')
            ->get();
        return $emails->count();
    }
    
    /**
     * verifica se ja existe login para o gestor
     * 
     */
    public function verificaUsuario($email, $unico)
    {
        try{
            if (strlen($email) > 0) {
                $query = Usuario::where('ulogin', '=', trim($email))->where('fkidgestor', '=', $unico->fkidgestor);
                $query->get();  
                return $query->count();
            }else{
                return 1;
            }
        } catch (Exception $e) {
            return 1;
        }
    }


    /**
     * Se a cidade existe retorna o id, caso contrario cadastro a cidade e retorna o id
     * 
     */
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

    
    public function checktipoarquivo($extensao = ''){
        $extensoes_permitidas = ['jpg', 'png', 'jpeg', 'bmp'];
        // Verifique se a extensão passada está no array de extensões 
        if (in_array(strtolower($extensao), $extensoes_permitidas)) {
            return 8; // thumb de perfil
        }else{
            return 7;
        }
    }

    public function uploads($idprincipal = 0, $iduser = 0, $files)
    {
        if($idprincipal > 0){
            $result = true;
            try{
                if ($files){
                    foreach($files as $key => $file)
                    {
                        $reg = new UnicoArq();
                        $saveType = Tools::getSaveType();
                        $reg->uaextensao = $file->getClientOriginalExtension();
                        //$reg->eamyme = $file->getMimeType();
                        $reg->uamyme = Tools::returnMimeType($file);
                        $reg->uatexto = $file->getClientOriginalName();
                        $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $file->getClientOriginalName());
                        $reg->uaidentificacao =  $fileNameNoExtension;
                        $reg->uatamanho = $file->getSize(); //tamanho em bytes
                        if($saveType == 3 || $saveType == 2)
                        {
                            $contents = $file->openFile()->fread($file->getSize());
                            $reg->uaarq = $contents;
                        }

                        if($saveType == 1 || $saveType == 3){
                            $newname = 'Unico_'.$idprincipal.'_'.rand() . '.' . $file->getClientOriginalExtension();
                            $file->move(storage_path('app/public/arquivos/unicos/'), $newname); //Input::file('file')->move($destinationPath, $filename);
                            $path = '/arquivos/unicos/'. $newname;
                            $reg->uapath = $path;
                        }

                        $reg->uastatus = 1;
                        $reg->uasavetype = $saveType;
                        $reg->fkidtipoarq = self::checktipoarquivo($reg->uaextensao);
                        $reg->fkidunico = $idprincipal;
                        $reg->uaversao = Carbon::now()->toDateTimeString();
                        $reg->flagexibe = 1; //$request->flagexibe_arquivo == 'on' ? 1 : 0;
                        $reg->flagdelete = 0;
                        $reg->flagatualiza = 1;
                        $reg->flaguser = $iduser;
                        if(!$reg->save()) {
                            $result = false;
                        }
                    }
                }
                return $result;
            } catch (Exception $e) {
                return false;
            }
            
        }else{
            return true;
        }
    }
    
    
    public function removerarquivos($id = 0)
    {
        $arquivos = UnicoArq::where('fkidunico', $id)->get();
        $deleted = true;
        try {
            if ($arquivos){
                foreach($arquivos as $arquivo)
                {
                    Tools::deleteFile($arquivo->uapath);
                    if($arquivo->delete()){
                        
                    }else{
                        $deleted = false;
                    }
                }
            }
            return $deleted;
        } catch (Exception $e) {
            $except = $e->getMessage();
            return false;
        } 
    }


    public function gerartokenrecuperarsenha(Request $request)
    {
        $validator = Validator::make(
        [
            'unidentificacao' => $request->unidentificacao,
            'undatanasc' => $request->undatanasc,             
            'uncpf' => $request->uncpf,
            'euemail' => $request->euemail,
            'aceitatermos' => $request->aceitatermos,
        ]
        , [
            'unidentificacao' => 'required|string|min:5',
            'undatanasc' => 'required|string|min:8',
            'uncpf' => 'required|string|min:13',
            'euemail' => 'required|email|min:4',
            'aceitatermos' => 'required|integer|min:0',
            
        ],
        [
            'unidentificacao.required' => 'Informe o Nome',
            'unidentificacao.string' => 'Informe o Nome',
            'unidentificacao.min' => 'Informe o Nome',
            'undatanasc.required' => 'Informe a Data de Nascimento',
            'undatanasc.email' => 'Informe a Data de Nascimento',
            'undatanasc.min' => 'Informe a Data de Nascimento',      
            'uncpf.required' => 'Informe um CPF Válido',
            'uncpf.string' => 'Informe um CPF Válido',
            'uncpf.min' => 'Informe um CPF Válido',
            'euemail.required' => 'Informe um e-mail Válido',
            'euemail.string' => 'Informe um e-mail Válido',
            'euemail.min' => 'Informe um e-mail Válido',
            'aceitatermos.required' => 'Aceite os Termos de Uso para Prosseguir',
            'aceitatermos.string' => 'Aceite os Termos de Uso para Prosseguir',
            'aceitatermos.min' => 'Aceite os Termos de Uso para Prosseguir',
        ]);
        
        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        } else {
            try{
                $unico = Unico::where('unidentificacao',trim($request->unidentificacao))->where('undatanasc',$request->undatanasc)
                ->where('unstatus',1)->where('uncpf', $request->uncpf)->where('fkidgestor', env('GESTOR'))
                ->whereHas('email', function ($query) use ($request) {
                    $query->where('euemail', $request->euemail);
                })->first();

                $email = self::getEmailUnico($request->euemail);

                if (empty($unico) || empty($email)) {
                    return Tools::setResponse('fail', null, 'Impossível Prosseguir, Informe Dados Reais');
                }else{
                    if($unico->id == $email->unico->id){
                        $user = Usuario::where('fkidunico', $unico->id)->where('ustatus', 1)->with('unico', 'gestor')->first();
                        if (empty($user)) {
                            return Tools::setResponse('fail', null, 'Impossível Prosseguir com a Recuperação, Tente Novamente');
                        }else{
                            self::updatetermouso($user->id, 1);
                        }
                        $passrecovery = self::gerarToken($user->id, $email->id);
                        if (empty($passrecovery)) {
                            return Tools::setResponse('fail', null, 'Impossível Prosseguir com a Recuperação de Senha, Tente Novamente');
                        }
                        $tokenrecovery = self::getToken($passrecovery->id);
                        $emaildata = new stdClass();
                        $emaildata->subject = 'Recuperação de Senha - '.ENV('CLIENT_DATA_NAME');
                        $emaildata->dados = 'Acesse o link de Redefinição de Senha';
                        $emaildata->emaildestino = $email->euemail;
                        $emaildata->urlsistema = route('redefinir.senha', $tokenrecovery);
                        //$emaildata->urlsistema = url().'/redefinir/senha/'.$tokenrecovery;  
                        Mail::to($email->euemail)->send(new recuperarSenha($emaildata));
                        return Tools::setResponse('success', null, 'Acesse seu e-mail para Finalizar o Procedimento');
                    }else{
                        return Tools::setResponse('fail', null, 'Impossível Prosseguir, Tente Novamente');
                    }
                }
            } catch (Exception $e) {
                $except = $e->getMessage();
                return Tools::setResponse('fail', null, 'Impossível Prosseguir, Tente Novamente');
            }
        }
    }


    protected function getEmailUnico($email)
    {
        $reg = Email::where('euemail', $email)->with('unico')->first();
        return $reg;
    }    

    protected function gerarToken($idUser, $idEmail)
    {
        $reg = new PassRecovery();
        $reg->fkidusuario = $idUser;
        $reg->fkidemail = $idEmail;
        $reg->prip = '';
        $reg->token = '';
        $reg->prdtrecovery = null;
        $reg->prdtregistro = Carbon::now()->addHour(1)->toDateTimeString();
        $reg->prstatus = 1;
        $reg->prversao = Carbon::now()->toDateTimeString();
        // $reg->flaguser = Session::get('user')->id;
        $reg->flagatualiza = 1;
        $reg->flagdelete = 0;
        $reg->save();
        return $reg;
    }

    protected function getToken($idPassRec)
    {
        return Crypt::encrypt($idPassRec);
    }

    public function showRecoveryForm($token)
    {
        if (strlen($token)>15) {
            $idPass = self::checkToken($token);
            if ($idPass)
            {
                $passRecovery = self::validaRecovery($idPass);
                if ($passRecovery && $passRecovery->prstatus == 1) {
                    if(self::setRecovery($token, $passRecovery->id))
                    {
                        return view('auth.novasenha', ['recoverytoken' => $token]);
                    }else {
                        return redirect()->route('esqueci.senha');
                    }
                }else {
                    return redirect()->route('esqueci.senha');
                }
            }
            else {
                return redirect()->route('esqueci.senha');
            }
        } else {
            return redirect()->route('esqueci.senha');
        }
    }

    protected function checkToken($token)
    {
        if (strlen($token)>15) {
            $resultado = Crypt::decrypt($token);
            return $resultado;
        } else {
            return null;
        }
    }
    
    protected function validaRecovery($id)
    {
        $reg = PassRecovery::where('id', $id)->where('prdtregistro', '>', Carbon::now()->toDateTimeString())->where('prstatus', 1)->first();
        return $reg;
    }

    protected function validaForSenha($id)
    {
        $reg = PassRecovery::where('id', $id)->where('prdtregistro', '>', Carbon::now()->toDateTimeString())->first();
        return $reg;
    }

    protected function getPassRecoveryForId($id)
    {
        $reg = PassRecovery::find($id);
        return $reg;
    }
    
    protected function setRecovery($token, $id)
    {
        $passRecovery = PassRecovery::find($id);
        if($passRecovery) {
            $passRecovery->token = $token;
            $passRecovery->prdtrecovery = Carbon::now()->toDateTimeString();
            $passRecovery->prstatus = 0;
            $passRecovery->save();
            return $passRecovery;
        }else{
            return null;
        }
    }

    public function salvarnovasenha(Request $request)
    {
        try{
            $idtoken = Crypt::decrypt($request->token);
            $passrecovery = self::validaForSenha($idtoken);
            if (empty($passrecovery)) {
                return Tools::setResponse('fail', null, 'Impossível Prosseguir, Verifique os Requisitos de Senha');
            }
            $reguser = Usuario::where('id', $passrecovery->fkidusuario)->where('ustatus', 1)->first();
            $reguser->upassword = Hash::make($request->senha);
            $reguser->uaceitetermosuso = 1;
            $reguser->udataaceitetermosuso = Carbon::now()->toDateTimeString();
            //$reguser->udatatermosuso = Carbon::now()->toDateTimeString();
            $reguser->flagdelete = 0;
            $reguser->flaguser = 0;
            $reguser->flagatualiza = 1;
            $reguser->uversao = Carbon::now()->toDateTimeString();
            if($reguser->save()){
                return Tools::setResponse('success', null, 'Nova Senha Registrada com Sucesso');
            }else{
                return Tools::setResponse('fail', null, 'Impossível Prosseguir, Verifique os Requisitos de Senha');
            }
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Impossível Prosseguir, Verifique os Requisitos de Senha');
        }
    }    



    public function meusdados()
    {
        return view('geral.meusdados');
    }


    
    public function updatecadastro(Request $request)
    {
        $validagestor = Tools::validaGestor();
        if($validagestor <= 0){
            return array('status' => 'fail', 'message' => 'Impossível Prosseguir');
        }
        
        $validator = Validator::make(
            [
            'unidentificacao' => $request->unidentificacao,
        ],
            [
            'unidentificacao' => 'required|string|min:1|max:255',
        ],
            [
            'unidentificacao.required' => 'Necessário Informar a identificação',
            'unidentificacao.min' => 'Necessário Informar a identificação',
            'unidentificacao.max' => 'Informe um Conteúdo Válido',
        ]
        );

        if ($validator->fails()) {
            return array('status' => 'fail', 'message' => $validator->errors()->first());
        } else {            

            if($request->id > 0) {
                if(strlen($request->uncpf) > 0){
                    if(self::verificaUnicoCPFUpdate($request->uncpf, $request->id) > 1){
                        return array('status' => 'fail', 'message' => 'CPF Não pode ser Utilizado');
                    }
                }                
                $reg = Unico::find($request->get('id'));
            } else {
                if(strlen($request->uncpf) > 0){
                    if(self::verificaUnicoCPF2($request->uncpf) > 0){
                        return array('status' => 'fail', 'message' => 'CPF Não pode ser Utilizado');
                    }
                }
                $reg = new Unico();
                $reg->undatacadastro = Carbon::now()->toDateTimeString();
            }
            $reg->unidentificacao = trim($request->unidentificacao);
            $reg->unapelido = strlen($request->unapelido) > 0 ? $request->unapelido : '';
            $reg->untiposanguineo = $request->untiposanguineo > 0 ? $request->untiposanguineo : 0;
            $reg->uncep = strlen($request->uncep) > 0 ? $request->uncep : '';
            //$reg->unserie = strlen($request->unserie) > 0 ? $request->unserie : '';
            $reg->undatanasc = strlen($request->undatanasc) > 8 ? Carbon::parse($request->undatanasc)->toDateTimeString() : null;
            $reg->unrg = strlen($request->unrg) > 0 ? $request->unrg : '';
            //$reg->untituloeleitor = strlen($request->untituloeleitor) > 0 ? $request->untituloeleitor : '';
            $reg->unnumcarttrabalho = strlen($request->unnumcarttrabalho) > 0 ? $request->unnumcarttrabalho : '';
            $reg->uncpf = strlen($request->uncpf) > 0 ? $request->uncpf : '';
            $reg->unpis = strlen($request->unpis) > 0 ? $request->unpis : '';
            //$reg->unzonaeleitoral = strlen($request->unzonaeleitoral) > 0 ? $request->unzonaeleitoral : '';
            //$reg->unsecaoeleitoral = strlen($request->unsecaoeleitoral) > 0 ? $request->unsecaoeleitoral : '';
            $reg->uncnpj = strlen($request->uncnpj) > 0 ? $request->uncnpj : '';
            //$reg->fkidtipocadastro = $request->fkidtipocadastro > 0 ? $request->fkidtipocadastro : 0;
            $reg->fkidclassesocial = $request->fkidclassesocial > 0 ? $request->fkidclassesocial : 0;
            //$reg->fkidprofissao = $request->fkidprofissao > 0 ? $request->fkidprofissao : 0;
            //$reg->fkidramoatividade = $request->fkidramoatividade > 0 ? $request->fkidramoatividade : 0;
            $reg->fkidescolaridade = $request->fkidescolaridade > 0 ? $request->fkidescolaridade : 0;
            $reg->fkidtratamento = $request->fkidtratamento > 0 ? $request->fkidtratamento : 0;
            $reg->fkidestadocivil = $request->fkidestadocivil > 0 ? $request->fkidestadocivil : 0;
            //$reg->fkidraca = $request->fkidraca > 0 ? $request->fkidraca : 0;
            $reg->fkidcidade = $request->fkidcidade > 0 ? $request->fkidcidade : 0;
            $reg->unendereco = strlen($request->unendereco) > 0 ? $request->unendereco : '';
            $reg->unbairro = strlen($request->unbairro) > 0 ? $request->unbairro : '';
            $reg->unnumero = strlen($request->unnumero) > 0 ? $request->unnumero : '';
            $reg->uncomplemento = strlen($request->uncomplemento) > 0 ? $request->uncomplemento : '';
            $reg->unsexo = $request->unsexo;
            //$reg->unobs = strlen($request->unobs) > 0 ? $request->unobs : '';
            $reg->unversao = Carbon::now()->toDateTimeString();
            //$reg->unstatus = $request->fkstatus > 0 ? $request->fkstatus : 0;
            //$reg->flagexibe = $request->flagexibe > 0 ? $request->flagexibe : 0;
            $reg->flagdelete = 0;
            $reg->flagatualiza = 1;
            $reg->flaguser = Session::get('user')->id;
            //$reg->fkidgestor = Session::get('user')->fkidgestor;
            //$reg->fkidunidade = 0;

            if($reg->save()) {
                if($request->origem != null) {
                    return $reg;
                } else {
                    return array('status' => 'success', 'message' => 'Registro salvo com Sucesso');
                }
            } else {
                return array('status' => 'fail', 'message' => 'Não foi possível cadastrar, entre em contato com o Suporte');
            }
        }
    }    

    public function verificaUnicoCPFUpdate($cpf, $id)
    {
        $gestor = Tools::getGestor();
        if (strlen($cpf) > 0) {
            $query = Unico::where('uncpf', '=', $cpf)->where('id', '!=', $id);//->where('ustatus', 1);
            if(strlen($gestor) > 0){
                $query->where('fkidgestor', '=', $gestor);
            }
            $result = $query->get();         
            return $result->count();
        }
    }






/*


    public function relatorio(Request $request)
    {
        $query = Unico::leftjoin('tipocadastro', 'unico.fkidtipocadastro', '=', 'tipocadastro.id')
        ->leftjoin('classesocial', 'unico.fkidclassesocial', '=', 'classesocial.id')
        ->leftjoin('profissao', 'unico.fkidprofissao', '=', 'profissao.id')
        ->leftjoin('ramoatividade as rm', 'unico.fkidramoatividade', '=', 'rm.id')
        ->leftjoin('escolaridade as esc', 'unico.fkidescolaridade', '=', 'esc.id')
        ->leftjoin('tratamento as tra', 'unico.fkidtratamento', '=', 'tra.id')
        ->leftjoin('estadocivil as est', 'unico.fkidestadocivil', '=', 'est.id')
        ->leftjoin('cidade as cid', 'unico.fkidcidade', '=', 'cid.id')
        //->leftjoin('unicoarq as uniarq', 'unico.id', '=', 'uniarq.fkidunico')
        ->leftJoin('unicoarq as uniarq', function ($join) {
            $join->on('unico.id', '=', 'uniarq.fkidunico')
                ->where('uniarq.fkidtipoarq', '=', 8)
                ->where('uniarq.uastatus', '=', 1);
        })
        ->select([DB::raw('unico.id, unidentificacao, fkidtipocadastro, unapelido, unnomefantasia, uncep,
        uninscrmunicipal, unserie,  undatacadastro, undatanasc, unrg, untituloeleitor, unnumcarttrabalho, uncpf, unpis,
        unsecaoeleitoral, unzonaeleitoral, uncnpj, fkidclassesocial, fkidprofissao, fkidramoatividade, fkidescolaridade,
        fkidtratamento, fkidestadocivil, fkidcidade, fkidraca, unendereco, unbairro, unnumero, uncomplemento, unsexo,
        undesignacao, uninscrestadual, unobs, unversao, unoptasimples, unico.flagexibe, tipocadastro.tpcidentificacao,
        classesocial.clscidentificacao, profissao.pfidentificacao, rm.rtidentificacao, esc.ecidentificacao,
        tra.ttidentificacao, est.ecidentificacao, cid.cdidentificacao, uniarq.uamyme, uniarq.uaarq ')]);
        $query->where('fkidtipocadastro', $request->tipocadastro);
        if(strlen($request->campoPesquisa) > 0) {
            $query->where('unidentificacao', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('unnomefantasia', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('uncpf', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('uncomplemento', 'like', '%' . $request->campoPesquisa . '%');
        }

        $query->orderBy($request->campoordem, $request->ordem)->groupBy('id');
        $registrostratar = $query->get();
        foreach ($registrostratar as $obj) {
            if($request->tipocadastro == 2) {
                $obj->unidentificacao = $obj->unidentificacao.(strlen($obj->unapelido) > 1 ? ' - '.$obj->unapelido : '').(strlen($obj->unnomefantasia) > 1 ? ' - '.$obj->unnomefantasia : '').(strlen($obj->uncpf) > 0 ? ' CPF:'.$obj->uncpf.' ' : '').
                (strlen($obj->unendereco) > 0 ? ' ('.$obj->unendereco.' ' : '').(strlen($obj->unnumero) > 0 ? ', '.$obj->unnumero.' ' : '').(strlen($obj->uncomplemento) > 0 ? ', '.$obj->uncomplemento.' ' : '').(strlen($obj->unbairro) > 0 ? ' - '.$obj->unbairro.' ' : '').(strlen($obj->uncep) > 0 ? '  CEP:'.$obj->uncep.' ' : '').')';
            } else {
                $obj->unidentificacao = $obj->unidentificacao.(strlen($obj->unapelido) > 1 ? ' - '.$obj->unapelido : '').(strlen($obj->uncnpj) > 0 ? ' CNPJ.:'.$obj->uncnpj.' ' : '').
                (strlen($obj->unendereco) > 0 ? ' ('.$obj->unendereco.' ' : '').(strlen($obj->unnumero) > 0 ? ', '.$obj->unnumero.' ' : '').(strlen($obj->uncomplemento) > 0 ? ', '.$obj->uncomplemento.' ' : '').(strlen($obj->unbairro) > 0 ? ' - '.$obj->unbairro.' ' : '').(strlen($obj->uncep) > 0 ? '  CEP:'.$obj->uncep.' ' : '').')';
            }
            $obj->undatacadastro = Tools::formataData($obj->undatacadastro);
        }

        $registros = json_encode($registrostratar);
        $dir = sys_get_temp_dir();
        $tmp = tempnam($dir, "rep");
        file_put_contents($tmp, '{"dadosreport":'.$registros.'}');
        $data_file = $tmp;
        $options = [
            'format' => [$request->extensao],
            'params' => ['parametertitle' => 'Relação de '.($request->tipocadastro == 2 ? 'Pessoas Jurídicas' : 'Pessoas Físicas'), 'parametertop' => ENV('CLIENT_DATA_NAME'), 'parameterbottom' => ENV('CLIENT_DATA_ADDRESS').' - '.ENV('CLIENT_DATA_CITY').'/'.ENV('CLIENT_DATA_STATE'),
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
