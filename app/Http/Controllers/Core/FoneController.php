<?php

namespace App\Http\Controllers\Core;

use App\Models\Fone;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;

class FoneController extends Controller
{
    public function setFone(Request $request)
    {
        $validator = Validator::make(
            [
                'fonenumero' => $request->fone,
                'fkidtipofone' => $request->fkidtipofone,
            ],
            [
                'fonenumero' => 'required|string|unique:fone,fnnumero,'.$request->id.'|min:14|max:180',
                'fkidtipofone' => 'required|integer|min:1',
            ],
            [
                'fonenumero.required' => 'Necessário Informar o Número',
                'fonenumero.min' => 'Necessário Informar o Número',
                'fonenumero.unique' => 'Informe um Número Válido',
                'fonenumero.max' => 'Informe um Conteúdo Válido',
                'fkidtipofone.required' => 'Necessário Informar o Tipo de Telefone',
                'fkidtipofone.min' => 'Necessário Informar o Tipo de Telefone',
                'fkidtipofone.integer' => 'Necessário Informar o Tipo de Telefone',     
            ]
        );

        if ($validator->fails()) {
            return array('status' => 'fail', 'message' => $validator->errors()->first());
        } else {
            if($request->get('id') > 0) {
                $reg = Fone::find($request->id);
            } else {
                $reg = new Fone();
            }
            $reg->fnnumero = $request->fone;
            $reg->fnanotacao = $request->anotacao;
            $reg->fkidtipofone = $request->fkidtipofone;//self::getTipoFone($request->fone);
            $reg->fnversao = Carbon::now()->toDateTimeString();
            $reg->fkidunico = $request->fkidunico;
            $reg->flagatualiza = 1;
            $reg->flagdelete = 0;
            if($reg->save()) {
                return array('status' => 'success', 'message' => 'Registro salvo com Sucesso');
            } else {
                return array('status' => 'fail', 'message' => 'Não foi possível cadastrar, entre em contato com o Suporte');
            }
        }
    }

    public function getFone($fone)
    {
        $reg = Fone::where('fnnumero', 'like', '%' . $fone . '%')->first();
        return $reg;
    }

    public function getFoneId($id)
    {
        $reg = Fone::where('id', $id)->first();
        return $reg;
    }

    /**
     * 1 - Casa
     * 2 - Particular
     * 3 - Celular
     * 4 - Comercial
     */
    private function getTipoFone($fone)
    {
        if(strlen($fone) > 14) {
            return 3; //Celular
        } else {
            return 2;
        }
    }



    public function getFones(Request $request)
    {
        $query = Fone::where('fkidunico', $request->fkidunico)->with('tipofone')->orderBy(strlen($request->campoOrdem) > 0 ? $request->campoOrdem : 'fnversao', strlen($request->ordem) > 0 ? $request->ordem : 'desc');
        $registros = $query->get();
        try {
            return response()->json([
                'status' => 'success',
                'data' => $registros,
                'message' => ''
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'fail',
                'data' => [],
                'message' => 'Falha ao obter dados'
            ]);
        }
    }


    public function remover($id)
    {
        $reg = Fone::find($id);
        Tools::setAtividade(Tools::getUser(), 3, $id, 'Remover o Registro', 'Registro '.$id);
        return Tools::msgpadrao($reg->delete(), 'delete');

        /*
        $sistemadesativar = env('SISTEMA_DESATIVAR'); /*Desativa ou remove do banco *
        if($sistemadesativar > 0) {
            $reg = Fone::find($id);
            Tools::setAtividade(Tools::getUser(), 3, $id, 'Desativar o Registro', 'Registro '.$id);
            //$reg->cstatus = 0;
            return Tools::msgpadrao($reg->save(), 'desativar');
        } else {
            $reg = Cidade::find($id);
            Tools::setAtividade(Tools::getUser(), 3, $id, 'Remover o Registro', 'Registro '.$id);
            return Tools::msgpadrao($reg->delete(), 'delete');
        }*/
    }
}
