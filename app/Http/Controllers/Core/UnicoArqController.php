<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Core\Tools;
use App\Models\UnicoArq;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UnicoArqController extends Controller
{
    /*
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
                return Tools::setResponse('fail', [], $validator->errors()->first());
            }

            $query = UnicoArq::where('id','>', 0)->with('tipo');
            $query->where('fkidunico', $request->idprincipal);
            $query->orderBy(strlen($request->campoOrdem) > 1 ? $request->campoOrdem : 'id', strlen($request->ordem) > 0 ? $request->ordem : 'desc');
            $query->groupBy('id');

            try {
                $registros = $query->paginate($request->regPg != 'undefined' ? $request->regPg : 20 );
                return Tools::setResponse('success', $registros, '');
            } catch (Exception $e) {
                return Tools::setResponse('fail', [], 'Falha ao obter dados');
            }
        }
    }*/



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

            $query = UnicoArq::where('id','>', 0)->with('tipo');
            $query->where('fkidunico', $request->idprincipal);
            $query->orderBy(strlen($request->campoOrdem) > 1 ? $request->campoOrdem : 'id', strlen($request->ordem) > 0 ? $request->ordem : 'desc');
            $qry2 = UnicoArq::where('id','>', 0)->with('tipo')->where('fkidunico', $request->idprincipal)->groupBy('fkidtipoarq');
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

 
    public function salvar(Request $request)
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
                        $reg = new UnicoArq();
                        //$file = $request->file;
                        $saveType = Tools::getSaveType();
                        $reg->uaextensao = $file->getClientOriginalExtension();
                        //$reg->pamyme = $file->getMimeType();
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
                            $newname = 'Unico_'.$request->idprincipal.'_'.rand() . '.' . $file->getClientOriginalExtension();
                            $file->move(storage_path('app/public/arquivos/unicos/'), $newname); //Input::file('file')->move($destinationPath, $filename);
                            $path = '/arquivos/unicos/'. $newname;
                            $reg->uapath = $path;
                        }
                        $reg->uasavetype = $saveType;
                        $reg->fkidtipoarq = $request->tipoarquivo != null ? $request->tipoarquivo : 7;
                        $reg->fkidunico = $request->idprincipal;
                        $reg->uaversao = Carbon::now()->toDateTimeString();
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


    public function salvarfoto(Request $request)
    {
        if($request->idprincipal > 0){
            $validator = Validator::make(
            [
                'idprincipal' => $request->idprincipal,
                'file' => $request->file,
            ],
            [   //'file' => 'required|mimes:doc,docx,pdf,jpg,png,jpeg  'max:500000',',
                'idprincipal' => 'required|integer|min:1',
                //'arq' => 'min:0,1|max:8000',
                'file' => 'required|mimes:jpg,png,jpeg|min:0,1|max:5024',
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
                if($request->id > 0){
                    $reg = UnicoArq::find($request->id);
                }else{
                    $reg = new UnicoArq;
                }

                $file = $request->file;
                $saveType = Tools::getSaveType();
                $reg->uaextensao = $file->getClientOriginalExtension();
                //$reg->pamyme = $file->getMimeType();
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
                    $newname = 'Unico_'.$request->idprincipal.'_'.rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(storage_path('app/public/arquivos/unicos/'), $newname); //Input::file('file')->move($destinationPath, $filename);
                    $path = '/arquivos/unicos/'. $newname;
                    $reg->uapath = $path;
                }
                $reg->uasavetype = $saveType;
                $reg->fkidtipoarq = $request->tipoarquivo != null ? $request->tipoarquivo : 7;
                $reg->fkidunico = $request->idprincipal;
                $reg->uaversao = Carbon::now()->toDateTimeString();
                $reg->flagexibe = 1; //$request->flagexibe_arquivo == 'on' ? 1 : 0;
                $reg->flagdelete = 0;
                $reg->flagatualiza = 1;
                $reg->flaguser = Session::get('user')->id;
                if($reg->save())
                {
                    return response()->json( [
                        'status' => 'success',
                        'data' => $reg,
                        'message' => 'Registro salvo com Sucesso'
                    ]);
                }else {
                    return response()->json( [
                        'status' => 'fail',
                        'data' => [],
                        'message' => 'Não foi possível cadastrar, entre em contato com o Suporte'
                    ]); 
                }
            }

        }
    }

    public function arquivoremover($id)
    {
        $reg = UnicoArq::find($id);
        $path = $reg->uapath;
        if($reg->delete()){
            Tools::deleteFile($path);
            Tools::setAtividade(Tools::getUser(), 3, $id,  'Remover Registro de Arquivo de Unico ', 'Registro '.$id);
            return Tools::msgpadrao(true, 'delete');
        }else{
            return Tools::msgpadrao(false, 'delete');
        }
    }


    public function getfotoperfil($id)
    {
        $reg = UnicoArq::where('fkidunico', $id)->where('uastatus', 1)->where('fkidtipoarq', 8)/*->with('tipo')*/->with('unico')->orderBy('uaversao','desc')->first();
        return $reg;
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
            $qry = UnicoArq::where('fkidunico', $request->idprincipal)->where('id', $request->idregistro);
            $reg = $qry->first();
            if($request->value > 0){
                if($request->campo == 'uatexto'){
                    $reg->uatexto = $request->value;
                }
                if($request->campo == 'uaidentificacao'){
                    $reg->uaidentificacao = $request->value;
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
                        $reg = new UnicoArq();
                        //$file = $request->file;
                        $saveType = Tools::getSaveType();
                        $reg->uaextensao = $file->getClientOriginalExtension();
                        //$reg->pamyme = $file->getMimeType();
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
                            $newname = 'Unico_'.$request->idprincipal.'_'.rand() . '.' . $file->getClientOriginalExtension();
                            $file->move(storage_path('app/public/arquivos/unicos/'), $newname); //Input::file('file')->move($destinationPath, $filename);
                            $path = '/arquivos/unicos/'. $newname;                
                            $reg->uapath = $path;
                        }
                        $reg->uasavetype = $saveType;
                        $reg->fkidtipoarq = $request->tipoarquivo != null ? $request->tipoarquivo : 7;
                        $reg->fkidunico = $request->idprincipal;
                        $reg->uaversao = Carbon::now()->toDateTimeString();
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

     /*
    public function salvar(Request $request)
    {
        $validator = Validator::make(
        [
            'uaarq' => $request->file,
            'fkidunico' => $request->fkidunico,
        ],
        [   //'file' => 'required|mimes:doc,docx,pdf,jpg,png,jpeg  'max:500000',',
            'fkidunico' => 'required|integer|min:1',
            'uaarq' => 'required|mimes:gif,jpg,png,jpeg|min:0,1|max:10200',
        ],
        [
            'uaarq.required' => 'Necessário informar um arquivo de imagem',
            'uaarq.min' => 'Arquivo possívelmente corrompido',
            'uaarq.mimes' => 'Arquivo Inválido',
            'uaarq.max' => 'Arquivo muito grande',
            'fkidunico.required' => 'Necessário Selecionar um Registro para Prosseguir',
            'fkidunico.integer' => 'Registro selecionado é Inválido',
            'fkidunico.min' => 'Registro selecionado é Inválido',
        ]);

        if ($validator->fails()) {
            //return redirect()->route('registrar')->withInput()->withErrors($validator)->with('message', $validator->errors()->first());
            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else {
            if (Input::hasFile('file')) {
                $reg = new UnicoArq;
                $f = Input::file('file');
                // 'photo' => 'required|file|image|mimes:jpeg,png,gif,webp|max:2048'
                //$contents = $f->openFile()->fread($f->getSize());

                $reg->uaextensao = $f->getClientOriginalExtension();
                //$reg->uamyme = $f->getMimeType();
                $reg->uamyme = Resources::returnMimeType($f);
                $reg->uatexto = $f->getClientOriginalName();
                //$reg->uatamanho = $f->getSize(); //tamanho e bytes
                $reg->uaidentificacao = $f->getClientOriginalName();/*
                $reg->fkidunico = $request->get('id');
                $reg->fkidtipoarq = 8; 
                $reg->uaversao = Carbon::now()->toDateTimeString();
                $reg->uastatus = 1;// $request->uadstatus_arquivo == 'on' ? 1 : 0;
                $reg->flagexibe = 1; // $request->flagexibe_arquivo == 'on' ? 1 : 0;
                $reg->flaguser = Session::get('user')->id;
                $reg->flagatualiza = 1;
                $reg->flagdelete=0;*
                $filename = 'thumb_'.$request->fkidunico.'_'.rand() . '.' . $f->getClientOriginalExtension();
                $f->move(storage_path('app/public/arquivos/temporario/'), $filename); //Input::file('file')->move($destinationPath, $filename);
                $path = '/arquivos/temporario/'. $filename;
                
                $img = Image::make(public_path(env('PATH_FILES_DEFAULT')).$path)->resize(100, 100)->save(public_path(env('PATH_FILES_DEFAULT')).$path);
                $reg->uatamanho = filesize(public_path(env('PATH_FILES_DEFAULT')).$path);
                $reg->fkidunico = $request->get('fkidunico');
                $reg->fkidtipoarq = 8;
                $reg->uaversao = Carbon::now()->toDateTimeString();
                $reg->uastatus = 1;// $request->uadstatus_arquivo == 'on' ? 1 : 0;
                $reg->flagexibe = 1; // $request->flagexibe_arquivo == 'on' ? 1 : 0;
                $reg->flaguser = Session::get('user')->id;
                $reg->flagatualiza = 1;
                $reg->flagdelete = 0;
                $reg->uaarq = base64_encode($img);
                if ($reg->save()) {
                    //Storage::delete($path);
                    File::Delete($path);
                    $this->disabledFotos($reg->fkidunico, $reg->id);
                    return Tools::setResponse('success', null, 'Registro salvo com Sucesso');
                } else {
                    return Tools::setResponse('fail', null, 'Não foi possível cadastrar, entre em contato com o Suporte');
                }
            } else {
                return Tools::setResponse('fail', null, 'Não foi possível cadastrar, entre em contato com o Suporte');
            }
        }
    }

    public function disabledFotos($idUnico = 0, $idAtual=0){
        if($idAtual>0) {
            return $affected = DB::table('unicoarq')->where('fkidtipoarq', '=', 8)->where('fkidunico', '=', $idUnico)->where('id', '<>', $idAtual)->update(array('uastatus' => 0));
        }else{
            return false;
        }
        //App\User::where('id', 'like', '%')->update(['confirmed' => 'string']);
    }

    public function salvar2(Request $request)
    {
        if($request->get('idarquivounico') > 0){
            $validator = Validator::make(
                [
                    'uaarq' => $request->upload,
                    'fkidunico' => $request->fkidunicoarquivo,
                ],
                [   //'file' => 'required|mimes:doc,docx,pdf,jpg,png,jpeg  'max:500000',',
                    'fkidunico' => 'required|integer|min:1|max:51200',
                    'uaarq' => 'min:0,1',
                ],
                [
                    'uaarq.min' => 'Arquivo possívelmente corrompido',
                    'uaarq.max' => 'Arquivo muito grande',
                    'fkidunico.required' => 'Necessário Selecionar uma Pessoa para Prosseguir',
                    'fkidunico.integer' => 'Pessoa selecionada é Inválida',
                    'fkidunico.min' => 'Pessoa selecionada é Inválida',
                ]);

            if ($validator->fails()) {
                //return redirect()->route('registrar')->withInput()->withErrors($validator)->with('message', $validator->errors()->first());
                return Tools::setResponse('fail', null, $validator->errors()->first());
            }else{
                $reg= UnicoArq::find($request->get('idarquivounico'));
                if(Input::hasFile('upload')) {
                    $saveType = Resources::getSaveType();

                    $f = Input::file('upload');
                    $contents = $f->openFile()->fread($f->getSize());

                    if (strlen($request->get('uaidentificacao')) > 0) {
                        $reg->uaidentificacao = $request->get('uaidentificacao');// . '.' . $f->getClientOriginalExtension();
                    } else {
                        $reg->uaidentificacao = preg_replace('/\..+$/', '', $f->getClientOriginalName());
                    }

                    $reg->uaextensao = $f->getClientOriginalExtension();
                    //$reg->uamyme = $f->getMimeType();
                    $reg->uamyme = Resources::returnMimeType($f);
                    $reg->uatexto = $f->getClientOriginalName();
                    $reg->uatamanho = $f->getSize(); //tamanho e bytes
                    if($saveType == 3 || $saveType == 2)
                    {
                        $reg->uaarq = $contents;
                    }
                    if($saveType == 1 || $saveType == 3){
                        $newname = 'Unico_'.$request->fkidunicoarquivo.'_'.rand() . '.' . $f->getClientOriginalExtension();
                        $f->move(storage_path('app/public/arquivos/unicos/'), $newname); //Input::file('file')->move($destinationPath, $filename);
                        $path = '/arquivos/unicos/'. $newname;
                        $reg->uapath = $path;
                    }
                    $reg->uasavetype = $saveType;
                    
                }else{
                    if (strlen($request->get('uaidentificacao')) > 0) {
                        $reg->uaidentificacao = $request->get('uaidentificacao');
                    }
                }
                $reg->fkidtipoarq = $request->fkidtipoarq != null ? $request->fkidtipoarq : 7;
                $reg->fkidunico= $request->fkidunicoarquivo;
                $reg->uaversao=Carbon::now()->toDateTimeString();
                $reg->flagexibe=$request->flagexibe_arquivo == 'on' ? 1 : 0;
                $reg->uastatus=$request->uastatus_arquivo == 'on' ? 1 : 0;
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

        }else{
            $validator = Validator::make(
                [
                    'uaarq' => $request->upload,
                    'fkidunico' => $request->fkidunicoarquivo,
                ],
                [   //'file' => 'required|max:10000|mimes:doc,docx'
                    'uaarq' => 'required|min:0,1|max:51200',
                    'fkidunico' => 'required|integer|min:1',
                ],
                [
                    'uaarq.required' => 'Necessário Informar o Arquivo',
                    'uaarq.min' => 'Arquivo possívelmente corrompido',
                    'uaarq.max' => 'Arquivo muito grande',
                    'fkidunico.required' => 'Necessário Selecionar uma Pessoa para Prosseguir',
                    'fkidunico.integer' => 'Pessoa selecionada é Inválida',
                    'fkidunico.min' => 'Pessoa selecionada é Inválida',
                ]);

            if ($validator->fails()) {
                //return redirect()->route('registrar')->withInput()->withErrors($validator)->with('message', $validator->errors()->first());
                return Tools::setResponse('fail', null, $validator->errors()->first());
            }else {
                $reg = new UnicoArq;
                if(Input::hasFile('upload'))
                {
                    $saveType = Resources::getSaveType();
                    $f = Input::file('upload');
                    $contents = $f->openFile()->fread($f->getSize());

                    if( strlen($request->get('uaidentificacao')) > 0 )
                    {
                        $reg->uaidentificacao=$request->get('uaidentificacao');//'.' . $f->getClientOriginalExtension();
                    }else{
                        $reg->uaidentificacao= preg_replace('/\..+$/', '', $f->getClientOriginalName()); //$f->getClientOriginalName();
                    }
                    $reg->uaextensao=$f->getClientOriginalExtension();
                    //$reg->uamyme=$f->getMimeType();
                    $reg->uamyme = Resources::returnMimeType($f);
                    $reg->uatexto=$f->getClientOriginalName();
                    $reg->uatamanho=$f->getSize(); //tamanho e bytes
                    if($saveType == 3 || $saveType == 2)
                    {
                        $reg->uaarq = $contents;
                    }
                    if($saveType == 1 || $saveType == 3){
                        $newname = 'Unico_'.$request->fkidunicoarquivo.'_'.rand() . '.' . $f->getClientOriginalExtension();
                        $f->move(storage_path('app/public/arquivos/unicos/'), $newname); //Input::file('file')->move($destinationPath, $filename);
                        $path = '/arquivos/unicos/'. $newname;
                        $reg->uapath = $path;
                    }
                    $reg->uasavetype = $saveType;
                    //$new_name = rand() . '.' . $f->getClientOriginalExtension();
                    //$f->move(public_path('arquivostemp'), $new_name); Input::file('file')->move($destinationPath, $filename);
                    //$path =  public_path('arquivos').'\\'. $new_name;
                    //$reg->uadarq = $contents;
                    $reg->fkidtipoarq=$request->fkidtipoarq != null ? $request->fkidtipoarq : 7;
                    $reg->fkidunico= $request->fkidunicoarquivo;
                    $reg->uaversao=Carbon::now()->toDateTimeString();
                    $reg->flagexibe=$request->flagexibe_arquivo == 'on' ? 1 : 0;
                    $reg->uastatus=$request->uastatus_arquivo == 'on' ? 1 : 0;
                    $reg->flagdelete=0;
                    $reg->flagatualiza=1;
                    $reg->flaguser = Session::get('user')->id;
                    if($reg->save())
                    {
                        return Tools::setResponse('success', null, 'Registro salvo com Sucesso');
                    }else {
                        return Tools::setResponse('fail', null, 'Não foi possível cadastrar, entre em contato com o Suporte');
                    }
                }else {
                    return Tools::setResponse('fail', null, 'Não foi possível cadastrar, entre em contato com o Suporte');
                }
            }
        }
    }

    public function listar($idUnico, $campoOrdem ='uaidentificacao', $ordem = 'asc')
    {
        //Log::debug('Listar='.$idUnico);
        if($idUnico > 0) {
        $query = UnicoArq::leftjoin('tipoarq as tipoarq', 'unicoarq.fkidtipoarq', '=', 'tipoarq.id')
            ->select([DB::raw('unicoarq.id, uaidentificacao, tpaidentificacao, fkidunico, fkidtipoarq,
            uaextensao, uamyme, uatexto, uatamanho, uaversao, uastatus, unicoarq.flagexibe')]);
            $registros = $query->where('fkidunico', $idUnico)->where('fkidtipoarq','<>', 0)->orderBy($campoOrdem, $ordem)->get();
            return $registros;
            //return json_encode($registros);
        }else{
            return null;
        }
    }

    public function getId($id)
    {
        $query = UnicoArq::leftjoin('tipoarq as tipoarq', 'unicoarq.fkidtipoarq', '=', 'tipoarq.id')
            ->select([DB::raw('unicoarq.id, uaidentificacao, tpaidentificacao, fkidunico, fkidtipoarq,
            uaextensao, uamyme,uatexto,uatamanho, uaversao, uastatus, unicoarq.flagexibe')]);
        $registro = $query->where('unicoarq.id', $id)->first();
        //Log::debug($registro);
        return json_encode($registro);
    }

    public function getIdDecode($id)
    {
        $registro = $query = UnicoArq::where('fkidunico', $id)->where('uastatus', 1)->where('fkidtipoarq', 8)->first();
        if($registro){
            return $registro;
        }else{
            return null;
        }
    }

    public function getFile($id)
    {
        $reg = UnicoArq::with('tipoArquivo')->find($id);
        //$file_contents = base64_decode($reg->ucarq);
        /*
        if($reg->uasavetype == 3 || $reg->uasavetype == 2) {

            $file_contents = $reg->uaarq;
            return response($file_contents)
                ->header('Cache-Control', 'no-cache private')
                ->header('Content-Description', 'File Transfer')
                ->header('Content-Type', $reg->uamyme)
                ->header('Content-length', strlen($file_contents))
                ->header('Content-Disposition', 'inline; filename=' . $reg->uaidentificacao . '.' . $reg->uaextensao)// abre no browser
                //->header('Content-Disposition', 'attachment; filename=' . $reg->ucnomearquivo)  força o download
                ->header('Content-Transfer-Encoding', 'binary');
        }else{
            //$file = public_path().'/'. $reg->uapath;
            $file = File::get(public_path(). $reg->uapath);
            return response($file)
                ->header('Cache-Control', 'no-cache private')
                ->header('Content-Description', 'File Transfer')
                ->header('Content-Type', $reg->uamyme)
                ->header('Content-length', strlen($file))
                ->header('Content-Disposition', 'inline; filename=' . $reg->uaidentificacao . '.' . $reg->uaextensao)// abre no browser
                //->header('Content-Disposition', 'attachment; filename=' . $reg->ucnomearquivo)  força o download
                ->header('Content-Transfer-Encoding', 'binary');
        }*
        //return Response::make( $file_contents, 200, array('Content-type' => $reg->ucmyme, 'Content-length' => $file_contents->getSize()));
        return Resources::openFile($reg->uasavetype, $reg->uamyme, $reg->uaidentificacao, $reg->uaextensao, $reg->tipoArquivo->tpaidentificacao, $reg->uapath, $reg->uaarq);
    }


    public function alteraArquivo(Request $request)
    {
        $validator = Validator::make(
        [
            'id' => $request->id,
            'identificacao' => $request->identificacao,
        ],
        [   //'file' => 'required|mimes:doc,docx,pdf,jpg,png,jpeg  'max:500000',',
            'id' => 'required|integer|min:1',
            'identificacao' => 'required|string|min:1|max:220'        
        ],
        [
            'id.required' => 'Necessário Selecionar um Registro para Prosseguir',
            'id.integer' => 'Registro selecionado é Inválido',
            'id.min' => 'Registro selecionado é Inválido',
            'identificacao.required' => 'Necessário Informar a Identificação',
            'identificacao.min' => 'Necessário Informar a Identificação',
            'identificacao.max' => 'Necessário Informar a Identificação',
        ]);

        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            if($request->get('id') > 0)
            {
                $reg= UnicoArq::find($request->get('id'));
                $reg->uaidentificacao=$request->identificacao;
                $reg->uaversao=Carbon::now()->toDateTimeString();
                $reg->flagatualiza=1;
                //$reg->flagexibe=$request->flagStatus == 'true' ? 1 : 0;
                $reg->flaguser = Session::get('user')->id;
                if($reg->save())
                {
                    return Tools::setResponse('success', null, 'Alteração Realizada com Sucesso');
                }else {
                    return Tools::setResponse('fail', null, 'Não foi possível Alterar, entre em contato com o Suporte');
                }
            } else {
                return Tools::setResponse('fail', null, 'Não foi possível alterar, entre em contato com o Suporte');
            }
        }        
    }

    public function alterarItem(Request $request)
    {
        $validator = Validator::make(
        [
            'id' => $request->id,
            'acaoem' => $request->acaoem,
        ],
        [   
            'id' => 'required|integer|min:1',
            'acaoem' => 'required|string|min:1|max:220'        
        ],
        [
            'id.required' => 'Necessário Selecionar um Registro para Prosseguir',
            'id.integer' => 'Registro selecionado é Inválido',
            'id.min' => 'Registro selecionado é Inválido',            
            'acaoem.required' => 'Necessário Informar a Ação',
            'acaoem.min' => 'Necessário Informar a Ação',
            'acaoem.max' => 'Necessário Informar a Ação',
        ]);

        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            if($request->id > 0)
            {
                $reg = UnicoArq::find($request->id);
                if($request->acaoem == 'status')
                {
                    $reg->uastatus=$reg->uastatus == 0 ? 1 : 0;
                }else if($request->acaoem == 'flagexibe'){
                    $reg->flagexibe=$reg->flagexibe == 0 ? 1 : 0;
                }
                $reg->uaversao=Carbon::now()->toDateTimeString();
                //$reg->flagdelete=0;
                $reg->flagatualiza=1;
                $reg->flaguser = Session::get('user')->id;

                if($reg->save())
                {
                    return Tools::setResponse('success', null, 'Alteração Realizada com Sucesso');
                }else {
                    return Tools::setResponse('fail', null, 'Não foi possível Alterar, entre em contato com o Suporte');
                }
            } else {
                return Tools::setResponse('fail', null, 'Não foi possível alterar, entre em contato com o Suporte');
            }
        }        
    }    */

}
