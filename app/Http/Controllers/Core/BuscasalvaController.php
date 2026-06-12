<?php

namespace App\Http\Controllers\Core;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Buscasalva;
use App\Http\Controllers\Core\Tools;
use App\Models\Config;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class BuscasalvaController extends Controller
{

    public function getBuscas(Request $request)
    {
        if($request->user > 0 && $request->idcal > 0){
            $qry = Buscasalva::where('fkidusuario', $request->user)->where('fkidcal', $request->idcal)->orwhere('bspublico', 1);
            $registros = $qry->orderBy('bsdescricao', 'asc')->get();
            return Tools::setResponse('success', $registros, '');            
        }else{
            return Tools::msgpadrao(false, 'fail');          
        }
    }

    public function buscas($idcal, $iduser = 0)
    {
        if($idcal > 0){
            $qry = Buscasalva::where('fkidusuario', Tools::getUser())->where('fkidcal', $idcal)->where('bspublico', 0);
            $registros = $qry->orderBy('bsdescricao', 'asc')->get();
            return Tools::setResponse('success', $registros, '');            
        }else{
            return Tools::msgpadrao(false, 'fail');
        }
    }    

    public function buscasaux($idcal, $subaux = 1, $iduser = 0)
    {
        if($idcal > 0){
            $qry = Buscasalva::where('fkidusuario', Tools::getUser())->where('fkidcal', $idcal)->where('subcalaux1', $subaux)->where('bspublico', 0);
            $registros = $qry->orderBy('bsdescricao', 'asc')->get();
            return Tools::setResponse('success', $registros, '');            
        }else{
            return Tools::msgpadrao(false, 'fail');
        }
    }       

    public function get($id)
    {
        $reg = Buscasalva::find($id);
        return $reg;
    }

    public function remover($id)
    {
        $reg = Buscasalva::find($id);
        if($reg){
            return Tools::msgpadrao($reg->delete(), 'delete');
        }
    }

    public function salvar(Request $request)
    {

        $validator = Validator::make(
        [
            'calid' => $request->calid,
            'userid' => Tools::getUser(),
        ],
        [
            'calid' => 'required|integer|min:1',
            'userid' => 'required|integer|min:1',
        ],
        [
            'calid.required' => 'Necessário Informação do Módulo',
            'calid.min' => 'Necessário Informação do Módulo',
            'calid.integer' => 'Necessário Informação do Módulo',
            'userid.required' => 'Dados Incompletos',
            'userid.min' => 'Dados Incompletos',
            'userid.integer' => 'Dados Incompletos',          
            
        ]);    
        if ($validator->fails()){
            return response()->json([
                'status' => 'fail',
                'data' => [],
                'message' => $validator->errors()->first()
            ]);    
        }else{
            try {
                if($request->id > 0)
                {
                    $reg = Buscasalva::find($request->id);
                    //$reg->bsfixada = $request->fixada > 0 ? 1 : 0;
                }else{
                    $reg = new Buscasalva();
                    $reg->bsfixada = $request->fixada > 0 ? 1 : 0;
                }
                $reg->bsdescricao = $request->bsdescricao != '' ? $request->bsdescricao : '';
                $reg->bscampos = $request->dados != '' ? $request->dados : '';
                $reg->subcalaux = $request->subcalaux != '' ? $request->subcalaux : '';
                $reg->subcalaux1 = $request->subcal > 0 ? $request->subcal : 0;
                $reg->fkidusuario = Tools::getUser();
                $reg->fkidcal = $request->calid > 0 ? $request->calid : 0;
                $reg->bspublico = $request->bspublico > 0 ? $request->bspublico : 0;
                $reg->bsversao = Carbon::now()->toDateTimeString();
                $reg->flagdelete=0;
                $reg->flagatualiza=1;
                $reg->flaguser = Tools::getUser();
                return Tools::msgpadrao($reg->save(), 'salvar');
                
            } catch (Exception $e) {
                return Tools::msgpadrao(false, 'fail');
            }              
        }
    }


    public function fixarBuscaPadrao(Request $request)
    {
        Buscasalva::where('fkidcal', $request->idcal)->where('fkidusuario', $request->usuario)
        ->update([
            'bsversao' => Carbon::now()->toDateTimeString(),
            'bsfixada' => 0,
            'flagdelete' => 0, 
            'flagatualiza' => 1,
            'flaguser' => Tools::getUser(),
            'flagatualiza' => 1
        ]);
        //Tools::msgpadrao($result, 'salvar', sizeof($ids));

        $reg = Buscasalva::find($request->id);
        $reg->bsfixada = 1;
        $reg->bsversao = Carbon::now()->toDateTimeString();
        $reg->flagdelete=0;
        $reg->flagatualiza=1;
        $reg->flaguser = Tools::getUser(); 
        return Tools::msgpadrao($reg->save(), 'salvar');

    }

}
