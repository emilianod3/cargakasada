<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Core\Tools;
use App\Models\CfgUserCal;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CfgUserCalController extends Controller
{
    public function getNumRegPorPagina($idCal, $idUser)
    {
        $config = $this->getConfig($idCal, $idUser);
        if (isset($config) ) {
            return $config->ucregporpagina;
        } else {
            return 5;
        }
    }

    public function getConfig($idCal, $idUser)
    {
        return CfgUserCal::where('fkidcal', $idCal)->where('fkidusuario', $idUser)->first();
    }

    public function loadConfigNumRegPorPagina($idUser)
    {
        $registros = CfgUserCal::where('fkidusuario', $idUser)->get();
        return $registros;
    }


    public function getNumRegPorPaginaSession($idCal, $idUser) {
        $campo='ucregporpagina';
        if (Session::has('regporpagina')) {
            $configsUserCals = Session::get('regporpagina');
            if (isset($configsUserCals)) { 
                foreach ($configsUserCals as $cfgUserCal) {
                    if ($cfgUserCal->fkidcal == $idCal && $cfgUserCal->fkidusuario == $idUser) {
                    /*$dados = array(
                        'consultar' => $this->trataPermissao(substr($permissao->{$campo}, 0,2)),
                        'inserir' => $this->trataPermissao(substr($permissao->{$campo}, 3,2)),
                        'alterar' => $this->trataPermissao(substr($permissao->{$campo}, 6,2)),
                        'apagar' => $this->trataPermissao(substr($permissao->{$campo}, 9,2)),
                        'idcal' => $permissao->{'fkidcal'}
                    );*/
                        return $cfgUserCal->{$campo};
                        break;
                    }
                }
            } else {
                return 10;
            }
        } else {
            return 10;
        }
    } 

    public function setNumRegPorPagina(Request $request)
    {
        $validator = Validator::make(
            [
                'idcal' => $request->idcal,
                'iduser' => $request->iduser,
                'numReg' => $request->numReg,
            ],
            [
                'idcal' => 'required|numeric|min:1',
                'iduser' => 'required|numeric|min:1',
                'numReg' => 'required|numeric|min:1',
            ],
            [
                'idcal.required' => 'Impossível Continuar',
                'idcal.min' => 'Impossível Continuar',
                'idcal.numeric' => 'Impossível Continuar',
                'iduser.required' => 'Impossível Continuar',
                'iduser.numeric' => 'Impossível Continuar',
                'iduser.min' => 'Impossível Continuar',   
                'numReg.required' => 'Impossível Continuar',
                'numReg.min' => 'Impossível Continuar',
                'numReg.numeric' => 'Impossível Continuar',                            
        ]);
        if ($validator->fails()){
            return response()->json( [
                'status' => 'fail',
                'data' => [],
                'message' => $validator->errors()->first()
            ]);                
        }else{
            $config = $this->getConfig($request->get('idcal'), $request->get('iduser'));
            if (isset($config)) {
                $config->ucregporpagina = $request->numReg;
                $config->ucversao=Carbon::now()->toDateTimeString();
                $config->flaguser =  $request->iduser;
                $config->flagatualiza = 1;
                $config->uctipo = 1;
                $config->ucstatus = 1;

                if($config->save()){
                    Session::forget('cfgusercal');
                    Session::put('cfgusercal', self::getAllSession($request->iduser));
                    Tools::setAtividade(0, 1, $config->id,  'Altera registro de Configuração de quantidade de Registros Exibidos por Página na Listagem', '');                
                    return response()->json( [
                        'status' => 'success',
                        'data' => [],
                        'message' => 'Registro Salvo com Sucesso'
                    ]);
                }else{
                    Tools::setAtividade(0, 9, 0,  'Tentativa Registro de Configuração de quantidade de Registros Exibidos por Página na Listagem', '');               
                    return response()->json( [
                        'status' => 'fail',
                        'data' => [],
                        'message' => 'Falha no Salvamento do Registro'
                    ]);
                }  
            } else {
                $config = new CfgUserCal();
                $config->ucregporpagina = $request->numReg;
                $config->fkidusuario = $request->iduser;
                $config->fkidcal = $request->idcal;
                $config->ucoptsearch = '';
                $config->uctipo = 1;
                $config->ucstatus = 1;
                $config->flaguser = Session::get('user')->id;
                $config->flagatualiza = 1;
                $config->ucversao=Carbon::now()->toDateTimeString();

                if($config->save()){
                    Session::forget('cfgusercal');
                    Session::put('cfgusercal', self::getAllSession($request->iduser));                    
                    Tools::setAtividade(0, 1, $config->id,  'Novo registro de Configuração de quantidade de Registros Exibidos por Página na Listagem', '');                
                    return response()->json( [
                        'status' => 'success',
                        'data' => [],
                        'message' => 'Registro Salvo com Sucesso'
                    ]);
                }else{
                    Tools::setAtividade(0, 9, 0,  'Tentativa Configuração de quantidade de Registros Exibidos por Página na Listagem', '');
                    return response()->json( [
                        'status' => 'fail',
                        'data' => [],
                        'message' => 'Falha no Salvamento do Registro'
                    ]);
                }  
            }
        }
    }

    public function setNumRegPorPg($idCal = 0, $idUser = 0, $nRegPag)
    {
        if($idCal > 0 && $idUser>0 && $nRegPag>0) {
            $config = $this->getConfig($idCal, $idUser);
            if (isset($config)) {
                $config->ucregporpagina = $nRegPag;
                $config->ucversao = Carbon::now()->toDateTimeString();
                $config->flaguser = $idUser;
                $config->flagatualiza = 1;
                $config->save();
            } else {
                $config = new CfgUserCal();
                $config->ucregporpagina = $nRegPag;
                $config->fkidusuario = $idUser;
                $config->fkidcal = $idCal;
                $config->ucoptsearch = '';
                $config->flaguser = Session::get('user')->id;
                $config->flagatualiza = 1;
                $config->ucversao = Carbon::now()->toDateTimeString();
                $config->save();
            }
            return $config;
        } else {
            return null;
        }
    }

    public function setNumRegPorPgSession($idCal = 0, $idUser = 0, $nRegPag = 5)
    {
        if ($idCal > 0 && $idUser>0 && $nRegPag>0) {
            $config = $this->getConfig($idCal, $idUser);
            if (isset($config)) {
                $config->ucregporpagina = $nRegPag;
                $config->ucversao = Carbon::now()->toDateTimeString();
                $config->flaguser = $idUser;
                $config->flagatualiza = 1;
                $config->save();
            } else {
                $config = new CfgUserCal();
                $config->ucregporpagina = $nRegPag;
                $config->fkidusuario = $idUser;
                $config->fkidcal = $idCal;
                $config->ucoptsearch = '';
                $config->flaguser = Session::get('user')->id;
                $config->flagatualiza = 1;
                $config->ucversao = Carbon::now()->toDateTimeString();
                $config->save();
            }
            Session::put('regporpagina', $this->loadConfigNumRegPorPagina($idUser));
            return 'true';
        } else {
            return 'false';
        }
    }

    public function setConfigSaveFavorito($idCfg = 0, $valor)
    {
        if($reg = CfgUserCal::findOrFail($idCfg))
        {
            if(isset($reg))
            {
                $reg->ucsavefavorito = $valor === 'true' ? 1 : 0;
                $reg->flaguser = Session::get('user')->id;
                $reg->flagatualiza = 1;
                $reg->ucversao=Carbon::now()->toDateTimeString();
                $reg->save();
                return $reg;
            }
            else return null;
        }
        else
        {
            return null;
        }
    }

    public function setConfigUseFavorito($idCfg = 0, $valor)
    {
        if($reg = CfgUserCal::findOrFail($idCfg))
        {
            if(isset($reg))
            {
                $reg->ucusefavorito = $valor === 'true' ? 1 : 0;
                $reg->flaguser = Session::get('user')->id;
                $reg->flagatualiza = 1;
                $reg->ucversao=Carbon::now()->toDateTimeString();
                $reg->save();
                return $reg;
            }
            else return null;
        }
        else
        {
            return null;
        }
    }

    public function setConfigRelativeJanela($idCfg = 0, $valor)
    {
        if ($reg = CfgUserCal::findOrFail($idCfg)) {
            if (isset($reg)) {
                $reg->ucsizerelative = $valor === 'true' ? 1 : 0;
                $reg->flaguser = Session::get('user')->id;
                $reg->flagatualiza = 1;
                $reg->ucversao=Carbon::now()->toDateTimeString();
                $reg->save();
                return $reg;
            } else 
                return null;
        } else {
            return null;
        }
    }

    public function setConfigsPadrao($idCfg = 0)
    {
        if ($reg = CfgUserCal::findOrFail($idCfg)) {
            if (isset($reg))
            {
                $reg->ucsizerelative = 1;
                $reg->ucmaximizado = 0;
                $reg->ucsizefixo = 0;
                $reg->ucusefavorito = 1;
                $reg->ucsavefavorito = 0;
                $reg->ucusesizecolumnsrelative = 1;
                $reg->ucwidth = 100;
                $reg->ucheight = 100;
                $reg->ucuseteclaatalho = 1;
                $reg->ucstatus = 1;
                $reg->flagatualiza = 1;
                $reg->flagdelete = 0;
                $reg->flaguser = Session::get('user')->id;
                $reg->ucversao=Carbon::now()->toDateTimeString();
                $reg->save();
                return $reg;
            } else 
                return null;
        } else {
            return null;
        }
    }

    public function getAllSession($idUser = 0){
        if($idUser > 0){
            $query = CfgUserCal::where('fkidusuario', $idUser)->where('ucstatus', '>', 0);
            $registros = $query->groupBy('id')->get();
            return json_encode($registros);
        }else{
            return null;
        }
    }

}
