<?php

namespace App\Http\Controllers\Core;

use App\Models\ColumnCal;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ColumnCalController extends Controller
{

    public function getColumnListCal(Request $request)
    {
        $query = ColumnCal::where('fkidcal', $request->idcal);
        if(strlen($request->campoPesquisa) > 0) {  
            $query->where('clheader', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('clname', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('clorder', 'like', '%' . $request->campoPesquisa . '%');
        }

        if($request->tipocampo > 0) {
            $query->where('cltipocampo', $request->tipocampo);
        }
        
        
        $query->orderBy(strlen($request->campoordem) > 0 ? $request->campoordem : 'clorder', strlen($request->ordem) > 0 ? $request->ordem : 'asc')->groupBy('id');
        //$registros = $query->get();
        $registros = $query->paginate($request->regPg);

        try {
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            return Tools::setResponse('fail', [], 'Falha ao obter dados');
        }
    }



    public function carregaColunasCal($idCal = 0)
    {
        if($idCal > 0)
        {
            $query = ColumnCal::where('fkidcal', $idCal);
            $query->orderBy('clorder', 'asc');
            //$registros = $query->paginate(Session::has('cfgcal') ? Session::get('cfgcal') : 5);
            $registros = $query->get();
            return json_encode($registros);
        } else {
            return NULL;
        }
    }

    public function carregaColunasCalTipo($idCal = 0, $tipo=0)
    { // tipo = tipo padrao = 0 - tipo secundario  do mesmo modulo = 5 - para exibir nos dois = 3
        if($idCal > 0)
        {
            $query = ColumnCal::where('fkidcal', $idCal);
            $query->where('cltipocampo', $tipo);
            $query->orWhere('cltipocampo', 3);
            $query->orderBy('clorder', 'asc');
            //$registros = $query->paginate(Session::has('cfgcal') ? Session::get('cfgcal') : 5);
            $registros = $query->get();
            return json_encode($registros);
        } else {
            return NULL;
        }
    }

    public function carregaColunas($idCal = 0)
    {
        if($idCal > 0)
        {
            $query = ColumnCal::where('fkidcal', $idCal);
            $query->orderBy('clorder', 'asc');
            $registros = $query->get();
            /*foreach ($registros as $reg){
                if($reg->clvisible == 1)
                {
                    Log::Debug(''.$reg->clname);
                    array_push($result, $reg);
                }
            }
            //var_dump($result);*/
            return $registros;
        } else {
            return null;
        }
    }

    public function setColunaOrdemAcima( $idColuna = 0)
    {
        if ($idColuna > 0) {
            $coluna = ColumnCal::findOrFail($idColuna);
            $reg = ColumnCal::where('fkidcal',$coluna->fkidcal)->where('clorder', '<=',$coluna->clorder)->first();
            if(isset($reg))
            {
                $coluna->clorder = $reg->clorder <= 0 ? 0 : $reg->clorder-1;
                $coluna->clversao=Carbon::now()->toDateTimeString();
                $coluna->flaguser = Session::get('user')->id;
                $coluna->flagatualiza=1;
                $coluna->save();
                return $reg;
            } else return $coluna;
        } else {
            return null;
        }
    }

    public function setColunaOrdemAbaixo($idColuna=0)
    {
        if($idColuna > 0)
        {
            $coluna = ColumnCal::findOrFail($idColuna);
            //Log::debug($coluna);
            $reg = ColumnCal::where('fkidcal',$coluna->fkidcal)->where('clorder', '>=',$coluna->clorder)->first();
            //Log::debug($reg);
            if(isset($reg))
            {
                $coluna->clorder = $reg->clorder+1;
                $coluna->clversao=Carbon::now()->toDateTimeString();
                $coluna->flaguser = Session::get('user')->id;
                $coluna->flagatualiza=1;
                $coluna->save();
                return $reg;
            }
            else return $coluna;

        }
        else
        {
            return null;
        }

    }

    public function setColunaVisivel($idColuna=0)
    {
        if($idColuna > 0)
        {
            $coluna = ColumnCal::findOrFail($idColuna);
            if(isset($coluna))
            {
                $coluna->clvisible = 1;
                $coluna->clversao=Carbon::now()->toDateTimeString();
                $coluna->flaguser = Session::get('user')->id;
                $coluna->flagatualiza=1;
                $coluna->save();
                Session::put('colunasCal', $this->loadColunasCals());
                return $coluna;
            }
            else return null;
        }
        else
        {
            return null;
        }
    }

    public function setColunaNaoVisivel($idColuna=0)
    {
        if($idColuna > 0)
        {
            $coluna = ColumnCal::findOrFail($idColuna);
            if(isset($coluna))
            {
                $coluna->clvisible = 0;
                $coluna->clversao=Carbon::now()->toDateTimeString();
                $coluna->flaguser = Session::get('user')->id;
                $coluna->flagatualiza=1;
                $coluna->save();
                Session::put('colunasCal', $this->loadColunasCals());
                return $coluna;
            }
            else return null;
        }
        else
        {
            return null;
        }
    }

    public function setHeaderColumn($idColuna = 0, $header)
    {
        if ($idColuna > 0 && strlen($header) > 0) {
            $coluna = ColumnCal::findOrFail($idColuna);
            if (isset($coluna)) {
                $coluna->clheader = $header;
                $coluna->clversao=Carbon::now()->toDateTimeString();
                $coluna->flaguser = Session::get('user')->id;
                $coluna->flagatualiza=1;
                $coluna->save();
                return $coluna;
            } else return null;
        } else {
            return null;
        }
    }

    public function setNomeColumn($idColuna = 0, $nome)
    {
        if ($idColuna > 0 && strlen($nome) > 0) {
            $coluna = ColumnCal::findOrFail($idColuna);
            if (isset($coluna)) {
                $coluna->clname = $nome;
                $coluna->clversao=Carbon::now()->toDateTimeString();
                $coluna->flaguser = Session::get('user')->id;
                $coluna->flagatualiza=1;
                $coluna->save();
                return $coluna;
            } else return null;
        } else {
            return null;
        }
    }

    public function loadColunasCals()
    {
        $registros = ColumnCal::orderBy('clorder', 'asc')->get();
        return json_encode($registros);
    }

    public function setStatusColumn($idColuna = 0)
    {
        if($idColuna > 0)
        {
            $coluna = ColumnCal::findOrFail($idColuna);
            if (isset($coluna)) {
                $coluna->clstatus = $coluna->clstatus == 1 ? 0 : 1;
                $coluna->clversao=Carbon::now()->toDateTimeString();
                $coluna->flaguser = Session::get('user')->id;
                $coluna->flagatualiza=1;
                $coluna->save();
                return $coluna;
            } else return null;
        } else {
            return null;
        }
    }

    public function remover($id)
    {
        $reg = ColumnCal::find($id);
        if($reg->delete()){
            return 'true';
        }else{
            return 'false';
        }

    }

    public function getId($id)
    {
        $registro = $query = ColumnCal::where('id', $id)->first();
        return $registro;
    }
    
    public function setColumnCal(Request $request)
    {
        $validator = Validator::make(
        [
            //'clheader' => $request->clheader,
            //'clname' => $request->clname,
            'clorder' => $request->clorder,
            'fkidcal' => $request->fkidcal,
        ],[
            //'clheader' => 'required|string|min:1|max:255',
            //'clname' => 'required|string',
            'clorder' => 'required|integer|min:1',
            'fkidcal' => 'required|integer|min:1',
        ],[
            //'clheader.required' => 'Necessário Informar o Rótulo da Coluna',
            //'clheader.min' => 'Rótulo da Coluna vazio',
            //'clheader.max' => 'Rótulo da Coluna muito grande',
            //'clname.required' => 'Necessário Informar o Campo de Referência da Coluna',
            //'clname.min' => 'Necessário Informar o Campo de Referência da Coluna',
            'clorder.required' => 'Necessário Informar a Ordem',
            'clorder.integer' => 'Necessário Informar a Ordem',
            'clorder.min' => 'Necessário Informar a Ordem',
            'fkidcal.required' => 'Necessário Informar a Cal',
            'fkidcal.integer' => 'Necessário Informar a Cal',
            'fkidcal.min' => 'Necessário Informar a Cal',            
        ]);

        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        }else{
            if($request->get('id') > 0)
            {
                $reg = ColumnCal::find($request->get('id'));
            }
            else
            {
                $reg = new ColumnCal;
            }
            $reg->fkidcal = $request->fkidcal;
            $reg->clsize = $request->clsize > 0 ? $request->clsize : 1;
            $reg->clalinhamento = 'left';
            $reg->clheader = strlen($request->clheader) > 0 ? $request->clheader : '';
            $reg->clname = strlen($request->clname) > 0 ? $request->clname : '';
            $reg->clorder = $request->clorder;
            $reg->clversao = Carbon::now()->toDateTimeString();
            $reg->cltipocampo = strlen($request->cltipocampo) > 0 ? $request->cltipocampo : 0;
            $reg->clstatus = $request->clstatus;
            $reg->clvisible = $request->clstatus;
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
}
