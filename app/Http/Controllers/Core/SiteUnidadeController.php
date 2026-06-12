<?php

namespace App\Http\Controllers\Core;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SiteUnidade;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class SiteUnidadeController extends Controller
{
    public function listar(Request $request)
    {
        $validator = Validator::make(
        [
            'idunidade' => $request->idunidade,
        ]
        , [
            'idunidade' => 'required|integer|min:1',
        ],
        [
            'idunidade.required' => 'Necessário Informar a Unidade',
            'idunidade.integer' => 'Necessário Informar a Unidade',
            'idunidade.min' => 'Necessário Informar a Unidade',
        ]);

        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            $gestor = Tools::getGestor();
            if($gestor > 0 ){
                $query = SiteUnidade::where('fkidunidade', $request->idunidade);
                $query->with('unidade');
                $query->orderBy('suversao','desc')->groupBy('id');
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
        $reg = SiteUnidade::find($id);
        try {
            if($reg->delete()) {
                return Tools::msgpadrao(true, 'delete');
            }else{
                return Tools::msgpadrao(false, 'delete');
            }
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::msgpadrao(false, 'delete');
        } 
    }

    public function getId($id)
    {
        $registro = SiteUnidade::find($id);
        return $registro;
    }


    /*
    public function remover($id)
    {
        $reg = SiteUnidade::find($id);
        if(is_file(public_path(env('PATH_FILES_DEFAULT').$reg->dppath))){
            try {
                //unlink(public_path($reg->uapath));
                File::Delete(public_path(env('PATH_FILES_DEFAULT').$reg->dppath));

            } catch (\Exception $e) {
                $r = 0;
            }
                if($reg->delete()){
                    return 'true';
                }else{
                    return 'false';
                }
        }else{
            if($reg->delete()){
                return 'true';
            }else{
                return 'false';
            }
        }        
    }*/ 

    public function salvar(Request $request)
    {
        //Log::debug('APagarl='.$request->aptextopublicacao);
        $validator = Validator::make(
        [
            'fkidunidade' => $request->fkidunidade,
            'suanotacao' => $request->suanotacao,
        ]
        , [
            'fkidunidade' => 'required|integer|min:1',
            'suanotacao' => 'required|string|min:1',
        ],
        [
            'fkidunidade.required' => 'Necessário Informar a Unidade',
            'fkidunidade.integer' => 'Necessário Informar a Unidade',
            'fkidunidade.min' => 'Necessário Informar a Unidade',
            'suanotacao.required' => 'Necessário Informar o Texto da Publicação',
            'suanotacao.min' => 'Necessário Informar o Texto da Publicação',
            'suanotacao.string' => 'Necessário Informar o Texto da Publicação',
        ]);


        if ($validator->fails()) {
            //return redirect()->route('registrar')->withInput()->withErrors($validator)->with('message', $validator->errors()->first());
            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            if($request->id > 0)
            {
                $reg = SiteUnidade::find($request->id);
            }
            else
            {
                $reg = new SiteUnidade;
                //$reg->apdatacadastro = Carbon::now()->toDateTimeString();
            }

            $reg->fkidunidade = $request->fkidunidade;
            $reg->suanotacao = strlen($request->suanotacao) > 0 ? $request->suanotacao : '';
            $reg->suendereco = strlen($request->suendereco) > 0 ? $request->suendereco : '';
            $reg->suversao = Carbon::now()->toDateTimeString();
            $reg->flagexibe = $request->flagexibe;
            $reg->flagdelete = 0;
            $reg->flagatualiza = 1;
            $reg->flaguser = Session::get('user')->id;
            $reg->fkidgestor = Tools::getGestor();

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
            $reg = SiteUnidade::find($request->idregistro);
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

            $reg->suversao = Carbon::now()->toDateTimeString();
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
