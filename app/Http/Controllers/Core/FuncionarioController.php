<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Funcionario;
use App\Models\FuncionarioArq;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FuncionarioController extends Controller
{
    public function inicio()
    {
        $gestor = Tools::getGestor();
        return view('geral.funcionario', ['cliente' => $gestor]);
    }


    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    $qry = Funcionario::where('fkidgestor', $gestor);
                }else{
                    $qry = Funcionario::where('id','>',0);
                }
            }else{
                if($gestor > 0){
                    $qry = Funcionario::where('fkidgestor', $gestor);
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
                $q->where('fcmatricula', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('fcseriecarteira', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('fcnumcarteira', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('fccodigocarteria', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('fclinkcarteira', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('fcobs', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('fcvigenciacarteirainicio', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('fcdataentrada', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('fcvigenciacarteirafinal', 'like', '%' . $request->campoPesquisa . '%')
                ->orWhere('fcdatasaida', 'like', '%' . $request->campoPesquisa . '%');
                
                $q->orWhereRaw("DATE_FORMAT(fcdataentrada, '%d/%m/%Y') LIKE ?", '%' . $request->campoPesquisa . '%');
                $q->orWhereRaw("DATE_FORMAT(fcdatasaida, '%d/%m/%Y') LIKE ?", '%' . $request->campoPesquisa . '%');
                $q->orWhereRaw("DATE_FORMAT(fcvigenciacarteirainicio, '%d/%m/%Y') LIKE ?", '%' . $request->campoPesquisa . '%');
                $q->orWhereRaw("DATE_FORMAT(fcvigenciacarteirafinal, '%d/%m/%Y') LIKE ?", '%' . $request->campoPesquisa . '%');
                // Pesquisa em Relacionamentos (sub-consultas)
                $q->orWhereHas('unico', function ($q_unico) use ($request) {
                    $q_unico->where('unidentificacao', 'like', $request->campoPesquisa)
                    ->orWhere('unapelido', 'like', '%' . $request->campoPesquisa . '%')
                    ->orWhere('uncpf', 'like', '%' . $request->campoPesquisa . '%');
                });
                $q->orWhereHas('unidade', function ($q_unidade) use ($request) {
                    $q_unidade->where('undidentificacao', 'like', $request->campoPesquisa)
                    ->orWhere('unnomefantasia', 'like', '%' . $request->campoPesquisa . '%')
                    ->orWhere('undobs', 'like', '%' . $request->campoPesquisa . '%');
                });
                $q->orWhereHas('cargo', function ($q_cargo) use ($request) {
                    $q_cargo->where('cgidentificacao', 'like', $request->campoPesquisa);
                });                              
                $q->orWhereHas('emails', function ($q_email) use ($request) {
                    $q_email->where('euemail', 'like', $request->campoPesquisa);
                })
                ->orWhereHas('fones', function ($q_fone) use ($request) {
                    $q_fone->where('funumero', 'like', $request->campoPesquisa);
                });
            });

        }
 
        if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('fcdataentrada', '>=', $request->datainiciofiltro);
        }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('fcdataentrada', '>=', $request->datainiciofiltro);
            $query->where('fcdataentrada', '<=', $request->datafinalfiltro);  
        }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('fcdataentrada', '<=', $request->datafinalfiltro); 
        }

        if($request->statusfiltro == 2) { 
            $query->where('fcstatus', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('fcstatus', '>', 0);
        }

        $query->with('arquivos','unico','unidade','cargo');
        $query->groupBy('funcionario.id');
        //$query->WhereNotIn('id',[1]);
        $query->orderBy(strlen($request->campoOrdem) > 0 ? $request->campoOrdem : 'fcdataentrada', strlen($request->ordem) > 0 ? $request->ordem : 'desc');

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
        $reg = Funcionario::find($id1);
        if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
			return false;
		}

        $arquivos = FuncionarioArq::where('fkidfuncionario', $id1)->get();
        $deletearquivos = false;
        foreach ($arquivos as $arq) {
            try{
                Tools::deleteFile($arq->fapath);
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
        $registro = Funcionario::where('id', $id)->with('unico')->first();
        return $registro;
    }    

    public function salvar(Request $request)
    {
        $validator = Validator::make(
        [
            'fkidunidade' => $request->fkidunidade,
            'fkidunico' => $request->fkidunico,
            'fcdataentrada' => $request->fcdataentrada,
            'fcmatricula' => $request->fcmatricula,
        ]
        , [
            'fkidunidade' => 'required|integer|min:1',
            'fkidunico' => 'required|integer|min:1',
            'fcdataentrada' => 'required|string|min:5',
            'fcmatricula' => 'required|string|min:1',
        ],
        [      
            'fcmatricula.required' => 'Informe a Matrícula',
            'fcmatricula.string' => 'Informe a Matrícula',
            'fcmatricula.min' => 'Informe a Matrícula',
            'fcdataentrada.required' => 'Informe a Data de Entrada',
            'fcdataentrada.string' => 'Informe a Data de Entrada',
            'fcdataentrada.min' => 'Informe a Data de Entrada',
            'fkidunico.required' => 'Informe a Pessoa',
            'fkidunico.integer' => 'Informe a Pessoa',
            'fkidunico.min' => 'Informe a Pessoa',
            'fkidunidade.required' => 'Informe a Unidade',
            'fkidunidade.integer' => 'Informe a Unidade',
            'fkidunidade.min' => 'Informe a Unidade',
        ]);

        if ($validator->fails()) {

            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            if($request->get('id') > 0)
            {
                $reg = Funcionario::find($request->get('id'));
                /*if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                    return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                }  */              
            }
            else
            {
                $reg = new Funcionario;
                $reg->fcdatacadastro = Carbon::now()->toDateTimeString();
            }

            $reg->fkidunico = $request->fkidunico > 0 ? $request->fkidunico : 0;
            $reg->fkidunidade = $request->fkidunidade > 0 ? $request->fkidunidade : 0;
            $reg->fkidcargo = $request->fkidcargo > 0 ? $request->fkidcargo : 0;
            $reg->fcdataentrada = strlen($request->fcdataentrada) > 0 ? Carbon::parse($request->fcdataentrada)->toDateTimeString() : null;
            $reg->fcdatasaida = strlen($request->fcdatasaida) > 0 ? Carbon::parse($request->fcdatasaida)->toDateTimeString() : null;
            $reg->fcvigenciacarteirainicio = strlen($request->fcvigenciacarteirainicio) > 0 ? Carbon::parse($request->fcvigenciacarteirainicio)->toDateTimeString() : null;
            $reg->fcvigenciacarteirafinal = strlen($request->fcvigenciacarteirafinal) > 0 ? Carbon::parse($request->fcvigenciacarteirafinal)->toDateTimeString() : null;
            $reg->fcmatricula = strlen($request->fcmatricula) > 0 ? $request->fcmatricula : '';
            $reg->fcseriecarteira = strlen($request->fcseriecarteira) > 0 ? $request->fcseriecarteira : '';
            $reg->fcnumcarteira = strlen($request->fcnumcarteira) > 0 ? $request->fcnumcarteira : '';
            $reg->fccodigocarteria = strlen($request->fccodigocarteria) > 0 ? $request->fccodigocarteria : '';            
            $reg->fclinkcarteira = strlen($request->fclinkcarteira) > 0 ? $request->fclinkcarteira : '';
            $reg->fcobs = strlen($request->fcobs) > 0 ? $request->fcobs : '';
            $reg->fcstatus = $request->status;
            $reg->fkidgestor = Tools::getGestor();
            $reg->fcversao = Carbon::now()->toDateTimeString();
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

    public function updateregistro(Request $request)
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
            $reg = Funcionario::find($request->idregistro);
            if($request->campo == 'status'){
                $reg->fcstatus = ($reg->fcstatus > 0 ? 0 : 1);
            }
            if($request->campo == 'flagexibe'){
                $reg->flagexibe = ($reg->flagexibe > 0 ? 0 : 1);
            }

            $reg->fcversao = Carbon::now()->toDateTimeString();
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

    public function getAll()
    {
        $gestor = Tools::getGestor();
        $query = Funcionario::where('fkidgestor', $gestor)
        ->join('unicos', 'funcionario.fkidunico', '=', 'unicos.id')
        ->orderBy('unicos.unidentificacao', 'asc')
        ->select('funcionario.*')->with('unico');
        $registros = $query->get();
        return $registros;
    }    








    public function arquivos(Request $request)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            $validator = Validator::make(
            [
                'idprincipal' => $request->idprincipal,
            ]
            , [
                'idprincipal' => 'required|integer|min:1',
            ],
            [
                'idprincipal.required' => 'Dados Inválidos',
                'idprincipal.integer' => 'Dados Inválidos',
                'idprincipal.min' => 'Dados Inválidos', 
            ]);
            if($validator->fails()){
                return Tools::setResponse('fail', null, $validator->errors()->first());
            }

            $query = FuncionarioArq::where('id','>', 0)->with('tipo');
            $query->where('fkidfuncionario', $request->idprincipal);
            $query->orderBy(strlen($request->campoOrdem) > 1 ? $request->campoOrdem : 'id', strlen($request->ordem) > 0 ? $request->ordem : 'desc');
            $qry2 = FuncionarioArq::where('id','>', 0)->with('tipo')->where('fkidfuncionario', $request->idprincipal)->groupBy('fkidtipoarq');
            $query->groupBy('id');

            try {
                $tipos = $qry2->get();
                $registros = $query->paginate($request->regPg != 'undefined' ? $request->regPg : 20 );
                return Tools::setResponse('success', [$registros, $tipos], '');
            } catch (Exception $e) {
                $except = $e->getMessage();
                return Tools::setResponse('fail', null, 'Falha ao obter dados');
            }
        }
    }    



    public function uploadarquivo(Request $request)
    {
        if($request->idprincipal > 0){
            $validator = Validator::make(
            [
                'idprincipal' => $request->idprincipal,
                'file' => $request->files,
            ],
            [   //'file' => 'required|mimes:doc,docx,pdf,jpg,png,jpeg  'max:500000',',
                'idprincipal' => 'required|integer|min:1',
                //'arq' => 'min:0,1|max:8000',
                'file' => 'required',
                'file.*' => 'required|mimes:jpg,png,jpeg,bmp,doc,docx,xls,xlsx,pdf,ppt,pptx,txt,adt,ods,odg,xml,rtf,gif,ico,tiff,svg|min:0,1|max:5024',  
                //'arq' => 'required|file|mimes:jpg,png,jpeg',
                //'image' => 'required|image',
                //'image' => 'required|image',
                //'image' => 'image|mimes:png',
                //'image' => 'file|mimes:jpg,png',
                //'image' => 'image|size:2048|dimensions:ratio=3/2',
                //'image' => 'required|image|size:1024||dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000',
                //'file' => 'required|file|mimes:ppt,pptx,doc,docx,pdf,xls,xlsx|max:204800',
                //'video' => 'mimes:m4v,avi,flv,mp4,mov',
                //'image' => 'mimetypes:jpg,png,jpeg'

            ],
            [
                'idprincipal.required' => 'Necessário Selecionar um Cadastro',
                'idprincipal.integer' => 'Registro Selecionado Inválido',
                'idprincipal.min' => 'Registro Selecionado Inválido',
                //'image.required' => 'Necessário enviar uma imagem',
                //'image.image' => 'Arquivo enviado Inválido',
                'file.required' => 'Necessário enviar um arquivo',
                'file.mimes' => 'Arquivo não Aceito',
                'file.min' => 'Arquivo possivelmente corrompido',
                'file.max' => 'Arquivo muito grande',

            ]);

            if($validator->fails()){
                return Tools::setResponse('fail', [], $validator->errors()->first());
            }else{
                $result = true;
                if ($request->file('files')){
                    foreach($request->file('files') as $key => $file)
                    {
                        $reg = new FuncionarioArq();
                        //$file = $request->file;
                        $saveType = Tools::getSaveType();
                        $reg->faextensao = $file->getClientOriginalExtension();
                        //$reg->pamyme = $file->getMimeType();
                        $reg->famyme = Tools::returnMimeType($file);
                        $reg->fatexto = $file->getClientOriginalName();
                        $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $file->getClientOriginalName());
                        $reg->faidentificacao =  $fileNameNoExtension;
                        $reg->fatamanho = $file->getSize(); //tamanho em bytes
                        if($saveType == 3 || $saveType == 2)
                        {
                            $contents = $file->openFile()->fread($file->getSize());
                            $reg->faarq = $contents;
                        }

                        if($saveType == 1 || $saveType == 3){
                            $newname = 'Funcionario_'.$request->idprincipal.'_'.rand() . '.' . $file->getClientOriginalExtension();
                            $file->move(storage_path('app/public/arquivos/funcionarios/'), $newname); //Input::file('file')->move($destinationPath, $filename);
                            $path = '/arquivos/funcionarios/'. $newname;
                            $reg->fapath = $path;
                        }
                        $reg->fasavetype = $saveType;
                        $reg->fkidtipoarq = $request->tipoarquivo != null ? $request->tipoarquivo : 7;
                        $reg->fkidfuncionario = $request->idprincipal;
                        $reg->faversao = Carbon::now()->toDateTimeString();
                        $reg->flagexibe = 1; //$request->flagexibe_arquivo == 'on' ? 1 : 0;
                        $reg->flagdelete = 0;
                        $reg->flagatualiza = 1;
                        $reg->flaguser = Session::get('user')->id;
                        if(!$reg->save()) {
                            $result = false;
                        }
                    }
                }
                if($result) {
                    return Tools::setResponse('success', null, 'Registro Salvo com Sucesso');
                } else {
                    //DB::rollBack();
                    return Tools::setResponse('fail', null, 'Não é possível Processar.');
                }
            }
        }
    }



    
    public function arquivoremover($id)
    {
        $reg = FuncionarioArq::find($id);
        $path = $reg->fapath;
        if($reg->delete()){
            Tools::deleteFile($path);
            Tools::setAtividade(Tools::getUser(), 3, $id,  'Remover Registro de Arquivo de Unidade ', 'Registro '.$id);
            return Tools::msgpadrao(true, 'delete');
        }else{
            return Tools::msgpadrao(false, 'delete');
        }
    }



    public function uploadarquivos(Request $request)
    {
        if($request->idprincipal > 0){
            $validator = Validator::make(
            [
                'idprincipal' => $request->idprincipal,
                'file' => $request->files,
            ],
            [   //'file' => 'required|mimes:doc,docx,pdf,jpg,png,jpeg  'max:500000',',
                'idprincipal' => 'required|integer|min:1',
                //'arq' => 'min:0,1|max:8000',
                'file' => 'required',
                'file.*' => 'required|mimes:jpg,png,jpeg,bmp,doc,docx,xls,xlsx,pdf,ppt,pptx,txt,adt,ods,odg,xml,rtf,gif,ico,tiff,svg|min:0,1|max:5024',  
                //'arq' => 'required|file|mimes:jpg,png,jpeg',
                //'image' => 'required|image',
                //'image' => 'required|image',
                //'image' => 'image|mimes:png',
                //'image' => 'file|mimes:jpg,png',
                //'image' => 'image|size:2048|dimensions:ratio=3/2',
                //'image' => 'required|image|size:1024||dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000',
                //'file' => 'required|file|mimes:ppt,pptx,doc,docx,pdf,xls,xlsx|max:204800',
                //'video' => 'mimes:m4v,avi,flv,mp4,mov',
                //'image' => 'mimetypes:jpg,png,jpeg'

            ],
            [
                'idprincipal.required' => 'Necessário Selecionar um Cadastro',
                'idprincipal.integer' => 'Registro Selecionado Inválido',
                'idprincipal.min' => 'Registro Selecionado Inválido',
                //'image.required' => 'Necessário enviar uma imagem',
                //'image.image' => 'Arquivo enviado Inválido',
                'file.required' => 'Necessário enviar um arquivo',
                'file.mimes' => 'Arquivo não Aceito',
                'file.min' => 'Arquivo possivelmente corrompido',
                'file.max' => 'Arquivo muito grande',

            ]);

            if($validator->fails()){
                return Tools::setResponse('fail', [], $validator->errors()->first());
            }else{
                $result = true;
                if ($request->file('files')){
                    foreach($request->file('files') as $key => $file)
                    {
                        $reg = new FuncionarioArq();
                        //$file = $request->file;
                        $saveType = Tools::getSaveType();
                        $reg->faextensao = $file->getClientOriginalExtension();
                        //$reg->pamyme = $file->getMimeType();
                        $reg->famyme = Tools::returnMimeType($file);
                        $reg->fatexto = $file->getClientOriginalName();
                        $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $file->getClientOriginalName());
                        $reg->faidentificacao =  $fileNameNoExtension;
                        $reg->fatamanho = $file->getSize(); //tamanho em bytes
                        if($saveType == 3 || $saveType == 2)
                        {
                            $contents = $file->openFile()->fread($file->getSize());
                            $reg->faarq = $contents;
                        }

                        if($saveType == 1 || $saveType == 3){
                            $newname = 'Funcionario_'.$request->idprincipal.'_'.rand() . '.' . $file->getClientOriginalExtension();
                            $file->move(storage_path('app/public/arquivos/funcionarios/'), $newname); //Input::file('file')->move($destinationPath, $filename);
                            $path = '/arquivos/funcionarios/'. $newname;                
                            $reg->fapath = $path;
                        }
                        $reg->fasavetype = $saveType;
                        $reg->fkidtipoarq = $request->tipoarquivo != null ? $request->tipoarquivo : 7;
                        $reg->fkidfuncionario = $request->idprincipal;
                        $reg->faversao = Carbon::now()->toDateTimeString();
                        $reg->flagexibe = 1; //$request->flagexibe_arquivo == 'on' ? 1 : 0;
                        $reg->flagdelete = 0;
                        $reg->flagatualiza = 1;
                        $reg->flaguser = Session::get('user')->id;
                        if(!$reg->save()) {
                            $result = false;
                        }
                    }
                }
                if($result) {
                    return Tools::setResponse('success', null, 'Registro Salvo com Sucesso');
                } else {
                    //DB::rollBack();
                    return Tools::setResponse('fail', null, 'Não é possível Processar.');
                }
            }

        }
    }    



    public function update(Request $request)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            $validator = Validator::make(
            [
                'idprincipal' => $request->idprincipal,
                'idregistro' => $request->idregistro,
            ]
            , [
                'idprincipal' => 'required|integer|min:1',
                'idregistro' => 'required|integer|min:1',
            ],
            [
                'idprincipal.required' => 'Dados Inválidos',
                'idprincipal.integer' => 'Dados Inválidos',
                'idprincipal.min' => 'Dados Inválidos',
                'idregistro.required' => 'Dados Inválidos',
                'idregistro.integer' => 'Dados Inválidos',
                'idregistro.min' => 'Dados Inválidos',
            ]);
    
            if($validator->fails()){
                return Tools::setResponse('fail', [], $validator->errors()->first());
            }

            //$request->acao
            $reg = null;
            $qry = FuncionarioArq::where('fkidfuncionario', $request->idprincipal)->where('id', $request->idregistro);
            $reg = $qry->first();
            if($request->value > 0){
                if($request->campo == 'fatexto'){
                    $reg->fatexto = $request->value;
                }
                if($request->campo == 'faidentificacao'){
                    $reg->faidentificacao = $request->value;
                }
                if($request->campo == 'fkidtipoarq'){
                    $reg->fkidtipoarq = $request->value;
                }
            }

            if($reg->save()){
                return Tools::msgpadrao(true, 'salvar');
            }else{
                return Tools::msgpadrao(false, 'salvar');
            }
            
        }
    }  
    
    


    public function uploadarquivostratados(Request $request)
    {
        if($request->idprincipal > 0){
            $validator = Validator::make(
            [
                'idprincipal' => $request->idprincipal,
                'file' => $request->files,
            ],
            [   //'file' => 'required|mimes:doc,docx,pdf,jpg,png,jpeg  'max:500000',',
                'idprincipal' => 'required|integer|min:1',
                //'arq' => 'min:0,1|max:8000',
                'file' => 'required',
                'file.*' => 'required|mimes:jpg,png,jpeg,bmp,doc,docx,xls,xlsx,pdf,ppt,pptx,txt,adt,ods,odg,xml,rtf,gif,ico,tiff,svg|min:0,1|max:5024',  
                //'arq' => 'required|file|mimes:jpg,png,jpeg',
                //'image' => 'required|image',
                //'image' => 'required|image',
                //'image' => 'image|mimes:png',
                //'image' => 'file|mimes:jpg,png',
                //'image' => 'image|size:2048|dimensions:ratio=3/2',
                //'image' => 'required|image|size:1024||dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000',
                //'file' => 'required|file|mimes:ppt,pptx,doc,docx,pdf,xls,xlsx|max:204800',
                //'video' => 'mimes:m4v,avi,flv,mp4,mov',
                //'image' => 'mimetypes:jpg,png,jpeg'

            ],
            [
                'idprincipal.required' => 'Necessário Selecionar um Cadastro',
                'idprincipal.integer' => 'Registro Selecionado Inválido',
                'idprincipal.min' => 'Registro Selecionado Inválido',
                //'image.required' => 'Necessário enviar uma imagem',
                //'image.image' => 'Arquivo enviado Inválido',
                'file.required' => 'Necessário enviar um arquivo',
                'file.mimes' => 'Arquivo não Aceito',
                'file.min' => 'Arquivo possivelmente corrompido',
                'file.max' => 'Arquivo muito grande',

            ]);

            if($validator->fails()){
                return Tools::setResponse('fail', [], $validator->errors()->first());
            }else{
                $result = true;
                if ($request->file('files')){
                    foreach($request->file('files') as $key => $file)
                    {
                        $reg = new FuncionarioArq();
                        //$file = $request->file;
                        $saveType = Tools::getSaveType();
                        $reg->faextensao = $file->getClientOriginalExtension();
                        //$reg->pamyme = $file->getMimeType();
                        $reg->famyme = Tools::returnMimeType($file);
                        $reg->fatexto = $file->getClientOriginalName();
                        if($request->identificaarquivo > 0 ){
                            $func = self::obteriddoarquivo($reg->fatexto);
                            if($func){
                                $reg->fkidfuncionario = $func->id;
                            }else{
                                $reg->fkidfuncionario = $request->idprincipal;
                            }
                        }else{
                            $reg->fkidfuncionario = $request->idprincipal;
                        }

                        if($request->ano > 0 ){
                            $reg->faano = $request->ano;
                        }

                        if($request->mes > 0 ){
                            $reg->fames = $request->mes;
                        }

                        $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $file->getClientOriginalName());
                        $reg->faidentificacao =  $fileNameNoExtension;
                        $reg->fatamanho = $file->getSize(); //tamanho em bytes
                        if($saveType == 3 || $saveType == 2)
                        {
                            $contents = $file->openFile()->fread($file->getSize());
                            $reg->faarq = $contents;
                        }

                        if($saveType == 1 || $saveType == 3){
                            $newname = 'Funcionario_'.$request->idprincipal.'_'.rand() . '.' . $file->getClientOriginalExtension();
                            $file->move(storage_path('app/public/arquivos/funcionarios/'), $newname); //Input::file('file')->move($destinationPath, $filename);
                            $path = '/arquivos/funcionarios/'. $newname;                
                            $reg->fapath = $path;
                        }
                        $reg->fasavetype = $saveType;
                        $reg->fkidtipoarq = $request->tipoarquivo != null ? $request->tipoarquivo : 7;
                        
                        $reg->faversao = Carbon::now()->toDateTimeString();
                        $reg->flagexibe = 1; //$request->flagexibe_arquivo == 'on' ? 1 : 0;
                        $reg->flagdelete = 0;
                        $reg->flagatualiza = 1;
                        $reg->flaguser = Session::get('user')->id;
                        if(!$reg->save()) {
                            $result = false;
                        }
                    }
                }
                if($result) {
                    return Tools::setResponse('success', null, 'Registro Salvo com Sucesso');
                } else {
                    //DB::rollBack();
                    return Tools::setResponse('fail', null, 'Não é possível Processar.');
                }
            }

        }
    }    


    private function obteriddoarquivo($nome = '')
    {
        //$numeroExtraido = preg_replace('/\D/', '', $nome);
        $numeroExtraido = '0';
        if (preg_match('/\d+/', $nome, $matches)) {
            $numeroExtraido = $matches[0];
        }        
        //$idReferencia = (int) $numeroExtraido;
        $idReferencia = $numeroExtraido;

        if (strlen($idReferencia) > 0) {
            $reg = Funcionario::where('fcmatricula', $idReferencia)->where('fkidgestor', Tools::getGestor())->first(); 
            if(isset($reg)){
                return $reg;
            }else{
                return null;
            }
        }else{
            return null;
        }
    }



    public function unidade(Request $request)
    {
        $query = self::initQuery();
        /*exato  ou amplo */
        if($request->tipofiltro == 'exato') {
        }

        if(strlen($request->campopesquisa) > 0) {
            $query->where(function ($q) use ($request) {
                $q->where('fcmatricula', 'like', '%' . $request->campopesquisa . '%')
                ->orWhere('fcseriecarteira', 'like', '%' . $request->campopesquisa . '%')
                ->orWhere('fcnumcarteira', 'like', '%' . $request->campopesquisa . '%')
                ->orWhere('fccodigocarteria', 'like', '%' . $request->campopesquisa . '%')
                ->orWhere('fclinkcarteira', 'like', '%' . $request->campopesquisa . '%')
                ->orWhere('fcobs', 'like', '%' . $request->campopesquisa . '%')
                ->orWhere('fcvigenciacarteirainicio', 'like', '%' . $request->campopesquisa . '%')
                ->orWhere('fcdataentrada', 'like', '%' . $request->campopesquisa . '%')
                ->orWhere('fcvigenciacarteirafinal', 'like', '%' . $request->campopesquisa . '%')
                ->orWhere('fcdatasaida', 'like', '%' . $request->campopesquisa . '%');
                
                $q->orWhereRaw("DATE_FORMAT(fcdataentrada, '%d/%m/%Y') LIKE ?", '%' . $request->campopesquisa . '%');
                $q->orWhereRaw("DATE_FORMAT(fcdatasaida, '%d/%m/%Y') LIKE ?", '%' . $request->campopesquisa . '%');
                $q->orWhereRaw("DATE_FORMAT(fcvigenciacarteirainicio, '%d/%m/%Y') LIKE ?", '%' . $request->campopesquisa . '%');
                $q->orWhereRaw("DATE_FORMAT(fcvigenciacarteirafinal, '%d/%m/%Y') LIKE ?", '%' . $request->campopesquisa . '%');
                // Pesquisa em Relacionamentos (sub-consultas)
                $q->orWhereHas('unico', function ($q_unico) use ($request) {
                    $q_unico->where('unidentificacao', 'like', $request->campopesquisa)
                    ->orWhere('unapelido', 'like', '%' . $request->campopesquisa . '%')
                    ->orWhere('uncpf', 'like', '%' . $request->campopesquisa . '%');
                });
                $q->orWhereHas('unidade', function ($q_unidade) use ($request) {
                    $q_unidade->where('undidentificacao', 'like', $request->campopesquisa)
                    ->orWhere('unnomefantasia', 'like', '%' . $request->campopesquisa . '%')
                    ->orWhere('undobs', 'like', '%' . $request->campopesquisa . '%');
                });
                $q->orWhereHas('cargo', function ($q_cargo) use ($request) {
                    $q_cargo->where('cgidentificacao', 'like', $request->campopesquisa);
                });
            });

        }
        
        $query->where('funcionario.fkidunidade', $request->idprincipal)->where('funcionario.fkidgestor', Tools::getGestor());
        $query->with('unico','unidade','cargo');
        $query->groupBy('funcionario.id');
        //$query->WhereNotIn('id',[1]);
        $query->orderBy(strlen($request->campoordem) > 0 ? $request->campoordem : 'fcdataentrada', strlen($request->ordem) > 0 ? $request->ordem : 'desc');

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

}
