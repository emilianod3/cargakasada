<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Core\Tools;
use App\Models\UnidadeArq;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;


class UnidadeArqController extends Controller
{
    
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

            $query = UnidadeArq::where('id','>', 0)->with('tipo');
            $query->where('fkidunidade', $request->idprincipal);
            $query->orderBy(strlen($request->campoOrdem) > 1 ? $request->campoOrdem : 'id', strlen($request->ordem) > 0 ? $request->ordem : 'desc');
            $qry2 = UnidadeArq::where('id','>', 0)->with('tipo')->where('fkidunidade', $request->idprincipal)->groupBy('fkidtipoarq');
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
                        $reg = new UnidadeArq();
                        //$file = $request->file;
                        $saveType = Tools::getSaveType();
                        $reg->uadextensao = $file->getClientOriginalExtension();
                        //$reg->pamyme = $file->getMimeType();
                        $reg->uadmyme = Tools::returnMimeType($file);
                        $reg->uadtexto = $file->getClientOriginalName();
                        $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $file->getClientOriginalName());
                        $reg->uadidentificacao =  $fileNameNoExtension;
                        $reg->uadtamanho = $file->getSize(); //tamanho em bytes
                        if($saveType == 3 || $saveType == 2)
                        {
                            $contents = $file->openFile()->fread($file->getSize());
                            $reg->uadarq = $contents;
                        }

                        if($saveType == 1 || $saveType == 3){
                            $newname = 'unidade_'.$request->idprincipal.'_'.rand() . '.' . $file->getClientOriginalExtension();
                            $file->move(storage_path('app/public/arquivos/unidades/'), $newname); //Input::file('file')->move($destinationPath, $filename);
                            $path = '/arquivos/unidades/'. $newname;
                            $reg->uapath = $path;
                        }
                        $reg->uasavetype = $saveType;
                        $reg->fkidtipoarq = $request->tipoarquivo != null ? $request->tipoarquivo : 7;
                        $reg->fkidunidade = $request->idprincipal;
                        $reg->uadversao = Carbon::now()->toDateTimeString();
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
        $reg = UnidadeArq::find($id);
        $path = $reg->uapath;
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
                        $reg = new UnidadeArq();
                        //$file = $request->file;
                        $saveType = Tools::getSaveType();
                        $reg->uadextensao = $file->getClientOriginalExtension();
                        //$reg->pamyme = $file->getMimeType();
                        $reg->uadmyme = Tools::returnMimeType($file);
                        $reg->uadtexto = $file->getClientOriginalName();
                        $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $file->getClientOriginalName());
                        $reg->udaidentificacao =  $fileNameNoExtension;
                        $reg->uadtamanho = $file->getSize(); //tamanho em bytes
                        if($saveType == 3 || $saveType == 2)
                        {
                            $contents = $file->openFile()->fread($file->getSize());
                            $reg->uadarq = $contents;
                        }

                        if($saveType == 1 || $saveType == 3){
                            $newname = 'Unidade_'.$request->idprincipal.'_'.rand() . '.' . $file->getClientOriginalExtension();
                            $file->move(storage_path('app/public/arquivos/unidades/'), $newname); //Input::file('file')->move($destinationPath, $filename);
                            $path = '/arquivos/unidades/'. $newname;                
                            $reg->uapath = $path;
                        }
                        $reg->uasavetype = $saveType;
                        $reg->fkidtipoarq = $request->tipoarquivo != null ? $request->tipoarquivo : 7;
                        $reg->fkidunidade = $request->idprincipal;
                        $reg->uadversao = Carbon::now()->toDateTimeString();
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
            $qry = UnidadeArq::where('fkidunidade', $request->idprincipal)->where('id', $request->idregistro);
            $reg = $qry->first();
            if($request->value > 0){
                if($request->campo == 'uadtexto'){
                    $reg->uadtexto = $request->value;
                }
                if($request->campo == 'udaidentificacao'){
                    $reg->udaidentificacao = $request->value;
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
}
