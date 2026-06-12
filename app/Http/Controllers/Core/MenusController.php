<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Core\Tools;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

class MenusController extends Controller
{
    public function inicio()
    {
        $gestor = Tools::getGestor();
        return view('controle.menus', ['cliente' => $gestor]);
    }

    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if ($gestor > 0 || Tools::getGrupoGeral()) {
            if (Tools::getGrupoGeral()) {
                if ($gestor > 0) {
                    //$qry = Cidade::where('cidade.fkidgestor', $gestor);
                    $qry = Menu::where('menu.id', '>', 0);
                } else {
                    $qry = Menu::where('menu.id', '>', 0);
                }
            } else {
                if ($gestor > 0) {
                    //$qry = Cidade::where('cidade.fkidgestor', $gestor);
                    $qry = Menu::where('menu.id', '>', 0);
                } else {
                    return Tools::setResponse('fail', null, 'Impossível Processar.');
                }
            }
        }
        return $qry;
    }

    public function get($id)
    {
        $reg = Menu::find($id);
        return $reg;
    }

    public function removerId($id)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR'); /*Desativa ou remove do banco */
        if ($sistemadesativar > 0) {
            $reg = Menu::find($id);
            $reg->mnstatus = 0;
            return Tools::msgpadrao($reg->save(), 'desativar');
        } else {
            $reg = Menu::find($id);
            return Tools::msgpadrao($reg->delete(), 'delete');
        }
    }

    public function removerLote(Request $request)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR');
        if ($sistemadesativar > 0) {
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = Menu::whereIn('id', $ids);
            $qtd = $regs->count();
            $regs->mnstatus = 0;
            return Tools::msgpadrao($regs->save(), 'desativar', $qtd);
        } else {
            $ids = explode(',', $request->ids);
            $regs = Menu::whereIn('id', $ids);
            $qtd = $regs->count();
            return Tools::msgpadrao($regs->delete(), 'delete', $qtd);
        }
    }

    public function getall()
    {

        $query = Menu::where('id','>',0);
        $query->orderBy('mnidentificacao', 'asc');
        //$registros = $query->paginate(300);
        $registros = $query->get();

        try {
            return json_encode($registros);
        } catch (Exception $e) {
            return [];
        }
    }

    public function lista(Request $request)
    {
        $query = self::initQuery();

        
        if($request->statusfiltro == 0) {
            $query->where('menu.mnstatus','>=', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('menu.mnstatus', 1);
        }else if($request->statusfiltro == 2) {
            $query->where('menu.mnstatus', 0);
        }


        if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('menu.mnversao', '>=', $request->datainiciofiltro.' 00:00:00');
        }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('menu.mnversao', '>=', $request->datainiciofiltro.' 00:00:00');
            $query->where('menu.mnversao', '<=', $request->datafinalfiltro.' 23:59:59');                
        }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('menu.mnversao', '<=', $request->datafinalfiltro.' 23:59:59'); 
        }

        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';

        if(strlen($request->campoPesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('menu.mnidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('menu.mnnumeracao', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('menu.mnicone', 'like', '%' . $request->campoPesquisa . '%');
        }else if(strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('menu.mnidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('menu.mnnumeracao', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('menu.mnicone', 'like', '%' . $request->campoPesquisa . '%');
        }

        $query->with('cal','menuacima');
        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc')->groupBy('menu.id');

        try {
            $registros = $query->paginate($request->regPg);
            return Tools::setResponse('success', $registros, '');
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }

    /*
    public function lista_(Request $request)
    {
        $gestor = Tools::getGestor();
        if ($gestor > 0 || Tools::getGrupoGeral()) {
            $query = self::initQuery();
            $query->with('cal');

            if (strlen($request->campoPesquisa) > 0) {
                $query->where('menu.mnidentificacao', 'like', '%' . $request->campoPesquisa . '%');
                //$query->orwhere('cal.clobserve', 'like', '%' . $request->campoPesquisa . '%');
                //$query->orwhere('clrota', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhereRelation('cal', 'clobserve', 'like', '%' . $request->campoPesquisa . '%');
                $query->orwhereRelation('cal', 'clrota', 'like', '%' . $request->campoPesquisa . '%');
                //$query->orwhereRelation('profissional.unico', 'unidentificacao', 'like', '%' . $request->campoPesquisa . '%');
                //$query->orwhereRelation('profissional.unico', 'unapelido', 'like', '%' . $request->campoPesquisa . '%');                
            }
            
            $query->groupBy('menu.id');            
            $query->orderBy(strlen($request->campoOrdem) > 1 ? $request->campoOrdem : 'id', strlen($request->ordem) > 0 ? $request->ordem : 'desc');

            try {
                $registros = $query->paginate($request->regPg);

                return response()->json([
                    'status' => 'success',
                    'data' => $registros,
                    'message' => ''
                    ]);
            } catch (Exception $e) {
                return Tools::setResponse('fail', [], 'Falha ao obter dados');

            }

            return $registros;
        }
    }*/

    public function salvar(Request $request)
    {
        $gestor = Tools::getGestor();
        if ($gestor > 0) {
            $validator = Validator::make(
                [
                'mnidentificacao' => $request->mnidentificacao,
            ],
                [
                'mnidentificacao' => 'required|string|min:5|max:198',
            ],
                [
                'mnidentificacao.required' => 'Necessário Informar a Identificação',
                'mnidentificacao.min' => 'Necessário Informar a Identificação',
                'mnidentificacao.string' => 'Necessário Informar a Identificação',
                'mnidentificacao.max' => 'Necessário Informar a Identificação',
            ]
            );

            if ($validator->fails()) {
                return Tools::setResponse('fail', null, $validator->errors()->first());
            }


            try {
                $reg = null;
                if ($request->get('id') > 0) {
                    $reg = Menu::find($request->id);
                } else {
                    $reg = new Menu();
                }
                $reg->mnidentificacao = strlen($request->mnidentificacao) > 0 ? $request->mnidentificacao : '';
                $reg->mnicone = strlen($request->mnicone) > 0 ? $request->mnicone : '';
                //$reg->mndestaque = strlen($request->mndestaque) > 0 ? $request->mndestaque : '';
                //$reg->mnskin = strlen($request->mnskin) > 0 ? $request->mnskin : '';
                $reg->mnnumeracao = strlen($request->mnnumeracao) > 0 ? $request->mnnumeracao : '';
                $reg->fkidmenunivelacima = $request->fkidmenunivelacima > 0 ? $request->fkidmenunivelacima : 0;
                $reg->fkidcal = $request->fkidcal > 0 ? $request->fkidcal : 0;
                $reg->mnsequencia = $request->mnsequencia > 0 ? $request->mnsequencia : 0;
                $reg->mnstatus = $request->fkstatus > 0 ? $request->fkstatus : 0;
                $reg->mnversao = Carbon::now()->toDateTimeString();
                $reg->flagdelete = 0;
                $reg->flagatualiza = 1;
                $reg->flaguser = Session::get('user')->id;
                if ($reg->save()) {
                return Tools::setResponse('success', $reg, 'Registro Realizado com Sucesso');
                } else {
                    Tools::setAtividade(0, 9, 0, 'Tentativa Registro de Lista de Espera', '');
                    return Tools::setResponse('fail', [], 'Falha no Salvamento de Registro');
                }
            } catch (Exception $e) {
                return Tools::setResponse('fail', [], 'Falha ao obter dados');

            }
        } else {
            return response()->json([
                'status' => 'fail',
                'data' => [],
                'message' => 'Falha no Salvamento de Registro'
            ]);

        }
    }
}
