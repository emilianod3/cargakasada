<?php

namespace App\Http\Controllers\Core;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    public function setEmail(Request $request)
    {
        $validator = Validator::make(
            [
                'email' => $request->euemail,
            ],
            [
                'email' => 'required|email|unique:email,euemail,'.$request->id.'|min:5|max:190',
            ],
            [
                'email.required' => 'Necessário Informar o E-mail',
                'email.min' => 'Necessário Informar um E-mail Válido',
                'email.max' => 'Necessário Informar um E-mail Válido',
                'email.unique' => 'E-mail Inválido ou já Utilizado',
                'email.email' => 'Informe um E-mail Válido',
            ]
        );

        if ($validator->fails()) {
            return array('status' => 'fail', 'message' => $validator->errors()->first());
        } else {
            if($request->get('id') > 0) {
                $reg = Email::find($request->id);
            } else {
                $reg = new Email();
                $reg->emusarcomoprincipal = 0;
            }
            $reg->euemail = $request->euemail;
            $reg->euanotacao = $request->euanotacao;
            $reg->emversao = Carbon::now()->toDateTimeString();
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



    /*
    public function setEmail($email, $idUnico)
    {
        $query = Email::where('euemail', '=', $email)->get();
        if($query->isNotEmpty()) {
            return false;
        } else {
            $reg = new Email();
            $reg->euemail = $email;
            $reg->euanotacao = '';
            $reg->emversao = Carbon::now()->toDateTimeString();
            // $reg->flaguser = Session::get('user')->id;
            $reg->fkidunico = $idUnico;
            $reg->flagatualiza = 1;
            $reg->flagdelete = 0;
            $reg->emusarcomoprincipal = 1;
            $reg->save();
            return true;
        }
    }*/

    public function getEmail($email)
    {
        $reg = Email::where('euemail', $email)->first();
        return $reg;
        /*if($reg->isNotEmpty())
        {
            return $reg;
        }else{
            return $reg;
        }*/
    }

    public function getEmailId($id)
    {
        $reg = Email::where('id', $id)->first();
        return $reg;
    }


    public function getEmailUnico($email)
    {
        $reg = Email::where('euemail', $email)->with('unico')->first();
        return $reg;
    }

    public function removerId($id)
    {
        $reg = Email::find($id);
        if($reg->delete()) {
            return 'true';
        } else {
            return 'false';
        }
    }

    public function getEmails(Request $request)
    {
        $query = Email::where('fkidunico', $request->fkidunico)->orderBy(strlen($request->campoOrdem) > 0 ? $request->campoOrdem : 'emusarcomoprincipal', strlen($request->ordem) > 0 ? $request->ordem : 'desc');
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
        $reg = Email::find($id);
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


    public function setPrincipal($id, $principal, $unicoid)
    {
        $regs = Email::where('fkidunico', $unicoid)->where('id', '<>', $id);
        if($regs->count() > 0){
            Email::where('fkidunico', $unicoid)->where('id', '<>', $id)->where('emusarcomoprincipal', 1)->update(['emusarcomoprincipal' => 0]);
            Tools::setAtividade(Tools::getUser(), 3, 0,  'Desativando e-mails Principais', 'Registros de Unico ID'.$unicoid);
        }

        $reg = Email::find($id);
        $reg->emusarcomoprincipal = $principal;
        $reg->emversao = Carbon::now()->toDateTimeString();
        $reg->flagatualiza = 1;
        $reg->flagdelete = 0;
        if($reg->save()) {
            return array('status' => 'success', 'message' => 'Registro salvo com Sucesso');
        } else {
            return array('status' => 'fail', 'message' => 'Não foi possível executar, entre em contato com o Suporte');
        }
    }

}
