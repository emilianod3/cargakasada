<?php

namespace App\Http\Controllers\Core;

use App\Models\Unidade;
use App\Models\UnidadeContrato;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UnidadeContratoController extends Controller
{
    public function listar(Request $request)
    {
        $validator = Validator::make(
        [
            'fkidunidade' => $request->fkidunidade,
        ]
        , [
            'fkidunidade' => 'required|integer|min:1',
        ],
        [
            'fkidunidade.required' => 'Necessário Informar a Unidade',
            'fkidunidade.integer' => 'Necessário Informar a Unidade',
            'fkidunidade.min' => 'Necessário Informar a Unidade',
        ]);

        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            $gestor = Tools::getGestor();
            if($gestor > 0 ){
                $query = UnidadeContrato::where('fkidunidade', $request->fkidunidade);
                $query->orderBy('ucdatainiciovigencia','desc')->groupBy('id');
                try {
                    $registros = $query->paginate($request->regPg);
                    return Tools::setResponse('success', $registros, '');
                } catch (Exception $e) {
                    $except = $e->getMessage();
                    return Tools::setResponse('fail', null, 'Falha ao obter dados');
                }
            }else{
                return Tools::setResponse('fail', null, 'Falha ao obter dados');
            }
        }
    }

    public function removerId($id)
    {
        $reg = UnidadeContrato::find($id);
        try {
            if(strlen($reg->ucpath) > 0 && Tools::deleteFile($reg->ucpath)) {
                $reg->delete();
                return Tools::msgpadrao(true, 'delete');
            }else{
                if($reg->delete()){
                    return Tools::msgpadrao(true, 'delete');
                }else{
                    return Tools::msgpadrao(false, 'delete');
                }
            }            
        }catch(Exception $e){
            $except = $e->getMessage();
            return Tools::msgpadrao(false, 'delete');
            //return Tools::setResponse('fail', null, $except);
        }
    }    

    public function getId($id)
    {
        $registro = UnidadeContrato::find($id);
        return $registro;
    }

    public function salvar(Request $request)
    {
        $validator = Validator::make(
        [
            'fkidunidade' => $request->fkidunidade,
            'ucnumerocontrato' => $request->ucnumerocontrato,
            'ucdatainiciovigencia' => $request->ucdatainiciovigencia,
        ]
        , [
            'fkidunidade' => 'required|integer|min:1',
            'ucnumerocontrato' => 'required|string|min:1',
            'ucdatainiciovigencia' => 'required|string|min:1',
        ],
        [
            'fkidunidade.required' => 'Necessário Informar a Unidade',
            'fkidunidade.integer' => 'Necessário Informar a Unidade',
            'fkidunidade.min' => 'Necessário Informar a Unidade',
            'ucnumerocontrato.required' => 'Necessário Informar o Número do Contrato',
            'ucnumerocontrato.string' => 'Necessário Informar o Número do Contrato',
            'ucnumerocontrato.min' => 'Necessário Informar o Número do Contrato',
            'ucdatainiciovigencia.required' => 'Necessário Informar o Início de Vigência',
            'ucdatainiciovigencia.min' => 'Necessário Informar o Início de Vigência',
            'ucdatainiciovigencia.string' => 'Necessário Informar o Início de Vigência',
        ]);


        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            if($request->id > 0)
            {
                $reg = UnidadeContrato::find($request->id);
            }
            else
            {
                $reg = new UnidadeContrato;
                $reg->ucdatacadastro = Carbon::now()->toDateTimeString();
            }
            
            if($request->file)
            {
                $file = $request->file;
                $saveType = Tools::getSaveType();
                $reg->ucextensao = $file->getClientOriginalExtension();
                //BLOB up to 64KB     MEDIUMBLOB up to 16MB      LONGBLOB up to 4GB
                $reg->ucmyme = Tools::returnMimeType($file);
                $reg->ucnomearquivo = $file->getClientOriginalName();
                $fileNameNoExtension = preg_replace("/\.[^.]+$/", "", $file->getClientOriginalName());
                $reg->uctamanho = $file->getSize(); //tamanho em bytes
                if($saveType == 3 || $saveType == 2)
                {
                    $contents = $file->openFile()->fread($file->getSize());
                    $reg->ucarq = $contents;
                }

                if($saveType == 1 || $saveType == 3){
                    $newname = 'UnidadeContrato_'.$request->id.'_'.rand() . '.' . $file->getClientOriginalExtension();
                    $file->move(storage_path('app/public/arquivos/unidades/'), $newname); //Input::file('file')->move($destinationPath, $filename);
                    $path = '/arquivos/unidades/'. $newname;
                    $reg->ucpath = $path;
                }
                $reg->ucsavetype = $saveType;
                $reg->uctexto = '';
                $reg->fkidtipoarq = $request->fkidtipoarq != null ? $request->fkidtipoarq : 7;
            }

            $reg->ucidentificacao = strlen($request->ucidentificacao) > 0 ? $request->ucidentificacao : '';
            $reg->fkidunidade = $request->fkidunidade;
            $reg->ucnumerocontrato = strlen($request->ucnumerocontrato) > 0 ? $request->ucnumerocontrato : '';
            $reg->ucdatainiciovigencia = $request->ucdatainiciovigencia != null ? Carbon::parse($request->ucdatainiciovigencia)->toDateTimeString() : null;
            $reg->ucdatafinavigencia = $request->ucdatafinavigencia != null ? Carbon::parse($request->ucdatafinavigencia)->toDateTimeString() : null;
            $reg->ucversao = Carbon::now()->toDateTimeString();
            $reg->flagexibe = $request->flagexibe;
            $reg->fkidgestor = Tools::getGestor();
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
            $reg = UnidadeContrato::find($request->idregistro);
            /*if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
            }*/

            /*
            if($request->campo == 'status'){
                $reg->cstatus = ($reg->cstatus > 0 ? 0 : 1);
            }*/
            if($request->campo == 'flagexibe'){
                $reg->flagexibe = ($reg->flagexibe > 0 ? 0 : 1);
            }

            $reg->ucversao = Carbon::now()->toDateTimeString();
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
   

}
