<?php

namespace App\Http\Controllers\Controle;

use App\Http\Controllers\Core\Cals;
use App\Http\Controllers\Core\PDFRELATORIO;
use App\Http\Controllers\Core\Tools;
use App\Models\Cal;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Response;

class CalController extends Controller
{
    public function inicio()
    {
        $gestor = Tools::getGestor();
        return Inertia::render('Controle/Cals', [
            'gestor' => $gestor,
            'cal' => Cals::CALCONTROLE
        ]);
    }
    
    public static function initQuery($qry = null){
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    //$qry = Cidade::where('cidade.fkidgestor', $gestor);
                    $qry = Cal::where('cal.id','>',0);
                }else{
                    $qry = Cal::where('cal.id','>',0);
                }
            }else{
                if($gestor > 0){
                    //$qry = Cidade::where('cidade.fkidgestor', $gestor);
                    $qry = Cal::where('cal.id','>',0);
                }else{
                    return Tools::setResponse('fail', null, 'Impossível Processar.');
                }
            }
        }
        return $qry;
    }

    public function get(mixed $id)
    {
        $reg = Cal::find($id);
        return $reg;
    }

    public function removerId(mixed $id)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR'); /*Desativa ou remove do banco */
        if($sistemadesativar > 0){
            $reg = Cal::find($id);
            if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                //return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                //return back()->withErrors(Tools::setResult('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte')); 
                return back()->with(Tools::setResult('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte')); 
            }            
            $reg->clstatus = 0;
            //return Tools::msgpadrao($reg->save(), 'desativar');
            return back()->with(Tools::msgpadrao($reg->save(), 'desativar'));
        }else{
            $reg = Cal::find($id);
            if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                //return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                //return back()->withErrors(Tools::setResult('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte')); 
                return back()->with(Tools::setResult('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte')); 
            }            
            //return Tools::msgpadrao($reg->delete(), 'delete');
            return back()->with(Tools::msgpadrao($reg->delete(), 'delete'));
        }
    }

    public function removerLote(Request $request)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR');
        if($sistemadesativar > 0){
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = Cal::whereIn('id', $ids);
            $qtd = $regs->count();
            $regs->clstatus = 0;
            //return Tools::msgpadrao($regs->save(), 'desativar', $qtd);
            return back()->with(Tools::msgpadrao($regs->save(), 'desativar', $qtd));
        }else{
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = Cal::whereIn('id', $ids);
            $qtd = $regs->count();
            //return Tools::msgpadrao($regs->delete(), 'delete', $qtd);
            return back()->with(Tools::msgpadrao($regs->delete(), 'delete', $qtd));
        }
    }


    public function lista(Request $request)
    {

        $query = self::initQuery();

        if($request->statusfiltro == 0) {
            $query->where('cal.clstatus','>=', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('cal.clstatus', 1);
        }else if($request->statusfiltro == 2) {
            $query->where('cal.clstatus', 0);
        }


        if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('cal.clversao', '>=', $request->datainiciofiltro.' 00:00:00');
        }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('cal.clversao', '>=', $request->datainiciofiltro.' 00:00:00');
            $query->where('cal.clversao', '<=', $request->datafinalfiltro.' 23:59:59');                
        }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('cal.clversao', '<=', $request->datafinalfiltro.' 23:59:59'); 
        }

        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';

        /*
        if(strlen($request->campoPesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('cal.clidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('cal.clobserve', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('cal.clbase', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('cal.clrota', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('cal.id', 'like', '%' . $request->campoPesquisa . '%');
        }else if(strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('cal.clidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clobserve', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clbase', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clrota', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.id', 'like', '%' . $request->campoPesquisa . '%');
        }*/

        if(strlen($request->campoPesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $termos = array_filter(explode(' ', trim($request->campoPesquisa)));
            $query->where(function ($queryGeral) use ($termos) {
                foreach ($termos as $termo) {
                    // Para cada palavra, cria um subgrupo AND que busca em qualquer uma das colunas (OR)
                    $queryGeral->where(function ($qSub) use ($termo) {
                        $qSub->where('cal.clidentificacao', 'like', '%' . $termo . '%');
                        $qSub->orwhere('cal.clobserve', 'like', '%' . $termo . '%');
                        $qSub->orwhere('cal.clbase', 'like', '%' . $termo . '%');
                        $qSub->orwhere('cal.clrota', 'like', '%' . $termo . '%');
                        $qSub->orwhere('cal.id', 'like', '%' . $termo . '%');
                    });
                }
            });
        }else if(strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('cal.clidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clobserve', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clbase', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clrota', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.id', 'like', '%' . $request->campoPesquisa . '%');
        } 


        //$query->with('cal','menuacima');
        $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc')->groupBy('cal.id');

        try {
            $registros = $query->paginate($request->regPg);
            Tools::setAtividade(0, 8, 0, 'Listagem de Cals', 'Listado com Sucesso');
            //return Tools::setResponse('success', $registros, '');
            return back()->with(Tools::setResult('success', $registros, 'Dados Enviados com Sucesso'));
                
        } catch (Exception $e) {
            $except = $e->getMessage();
            //return Tools::setResponse('fail', null, 'Falha ao obter dados');
            Tools::setAtividade(0, 8, 0, 'Listagem de Cals', 'Falha na Listagem de Cals - '.$except);
            $resp = Tools::setResult('fail', null, 'Falha no Processamento - '.$except);
            return back()->withErrors($resp); 
            //return back()->withErrors(Tools::setResult('fail', null, 'Falha no Processamento - '.$except));        
        }
    }

    public function salvar(Request $request)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0){
            $validator = Validator::make(
            [
                'identificacao' => $request->clidentificacao,
                'tipo' => $request->tipo,   
            ]
            , [
                'identificacao' => 'required|string|min:5|max:198',
                'tipo' => 'required|integer|min:1',
            ],
            [
                'identificacao.required' => 'Necessário Informar a Identificação',
                'identificacao.min' => 'Necessário Informar a Identificação',
                'identificacao.string' => 'Necessário Informar a Identificação',
                'identificacao.max' => 'Necessário Informar a Identificação',
                'tipo.required' => 'Necessário Informar o Tipo',
                'tipo.min' => 'Necessário Informar o Tipo',
                'tipo.integer' => 'Necessário Informar o Tipo',
            ]);

            if($validator->fails()){
                return Tools::setResponse('fail', [], $validator->errors()->first());
            }
            
            
            try{
                $reg = null;
                if($request->get('id') > 0)
                {
                    $reg = Cal::find($request->id);
                    if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                        return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                    }                    
                }
                else
                {
                    $reg = new Cal();
                }
                $reg->clidentificacao = strlen($request->clidentificacao) > 0 ? $request->clidentificacao : '';
                $reg->clbase = strlen($request->base) > 0 ? $request->base : '';
                $reg->clrota = strlen($request->rota) > 0 ? $request->rota : '';
                $reg->cltipo = $request->tipo > 0 ? $request->tipo : 1;
                $reg->clstatus = $request->fkstatus > 0 ? $request->fkstatus : 0;
                $reg->clobserve = strlen($request->obs) > 0 ? $request->obs : '';
                $reg->clversao = Carbon::now()->toDateTimeString();
                $reg->flagdelete = 0;
                $reg->flagatualiza = 1;
                $reg->flaguser = Session::get('user')->id;
                if($reg->save()){
                    return back()->with(Tools::setResult('success', $reg, 'Registro Realizado com Sucesso'));
                }else{
                    Tools::setAtividade(0, 9, 0, 'Cals', 'Falha na Tentativa Salvamento Registro');
                    $resperr = Tools::setResult('fail', null, 'Falha no Processamento');
                    return back()->withErrors($resperr);
                }
            } catch (Exception $e) {
                $except = $e->getMessage();
                Tools::setAtividade(0, 9, 0, 'Cals', 'Falha na Tentativa Salvamento Registro - '.$except);
                $resperr = Tools::setResult('fail', null, 'Falha no Processamento');
                return back()->withErrors($resperr);
            }
        }else{
            Tools::setAtividade(0, 9, 0, 'Cals', 'Falha na Tentativa Salvamento Registro');
            $resperr = Tools::setResult('fail', null, 'Falha no Processamento');
            return back()->withErrors($resperr);
        }
    }

    public function getId(mixed $id)
    {
        $registro = Cal::find($id);
        return $registro;
    }

    public function getTiposCals()
    {
        $registros = array(
            array(
                "id" => 1,
                "tipo" => "Cadastro"
            ),
            array(
                "id" => 2,
                "tipo" => "Controle"
            ),
            array(
                "id" => 3,
                "tipo" => "SubCadastro"
            )
        );

        return $registros;
    }



    public function getForSiteConteudo()
    {
        $query = Cal::where('cltipo', '<>', 3);
        $query->orderBy('clidentificacao', 'asc');
        $registros = $query->get();
        return $registros;
    }


    public function getAll()
    {
        $query = Cal::where('id', '>', 0);
        $query->orderBy('clidentificacao', 'asc');
        //$registros = $query->paginate(300);
        $registros = $query->get();

        try {
            //Session::put('grupos', $registros);
            return json_encode($registros);
        } catch (Exception $e) {
            return [];
        }

    }


    public function getAllObj()
    {
        $query = Cal::where('id', '>', 0);
        $query->where('clstatus', '>', 0);
        $query->orderBy('clidentificacao', 'asc');
        //$registros = $query->paginate(300);
        $registros = $query->get();

        try {
            //Session::put('grupos', $registros);
            return $registros;
        } catch (Exception $e) {
            return [];
        }

    }

    /*
    public function getAll()
    {
        $query = Cal::Where('id', '>', 0);
        $query->orderBy('clidentificacao', 'asc');
        $registros = $query->get();
        return $registros;
    }*/

    public function relatorio(Request $request)
    {
        $query = self::initQuery();
        if($request->statusfiltro == 0) {
            $query->where('cal.clstatus','>=', 0);
        }else if($request->statusfiltro == 1) {
            $query->where('cal.clstatus', 1);
        }else if($request->statusfiltro == 2) {
            $query->where('cal.clstatus', 0);
        }


        if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('cal.clversao', '>=', $request->datainiciofiltro.' 00:00:00');
        }else if(strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('cal.clversao', '>=', $request->datainiciofiltro.' 00:00:00');
            $query->where('cal.clversao', '<=', $request->datafinalfiltro.' 23:59:59');                
        }else if(strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('cal.clversao', '<=', $request->datafinalfiltro.' 23:59:59'); 
        }

        $campoordenar = 'id';
        $campoordenar = $request->campoordem != 'undefined' ? $request->campoordem : 'id';

        /*
        if(strlen($request->campoPesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $query->where('cal.clidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('cal.clobserve', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('cal.clbase', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('cal.clrota', 'like', '%' . $request->campoPesquisa . '%');
            $query->orwhere('cal.id', 'like', '%' . $request->campoPesquisa . '%');
        }else if(strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('cal.clidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clobserve', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clbase', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clrota', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.id', 'like', '%' . $request->campoPesquisa . '%');
        }*/

        if(strlen($request->campoPesquisa) > 0) {  //$request->tipofiltro == 'amplo'
            $termos = array_filter(explode(' ', trim($request->campoPesquisa)));
            $query->where(function ($queryGeral) use ($termos) {
                foreach ($termos as $termo) {
                    // Para cada palavra, cria um subgrupo AND que busca em qualquer uma das colunas (OR)
                    $queryGeral->where(function ($qSub) use ($termo) {
                        $qSub->where('cal.clidentificacao', 'like', '%' . $termo . '%');
                        $qSub->orwhere('cal.clobserve', 'like', '%' . $termo . '%');
                        $qSub->orwhere('cal.clbase', 'like', '%' . $termo . '%');
                        $qSub->orwhere('cal.clrota', 'like', '%' . $termo . '%');
                        $qSub->orwhere('cal.id', 'like', '%' . $termo . '%');
                    });
                }
            });
        }else if(strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {  
            $query->where('cal.clidentificacao', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clobserve', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clbase', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.clrota', 'like', '%' . $request->campoPesquisa . '%');
            $query->where('cal.id', 'like', '%' . $request->campoPesquisa . '%');
        } 

        $MAX_REGISTROS_RELATORIO = (int) (ENV('MAX_REGISTROS_RELATORIO') ?? 100000);
        try {
            $totalEncontrado = $query->count('cal.id');
            if ($totalEncontrado > $MAX_REGISTROS_RELATORIO) {
                \Log::warning("Geração de relatório excedeu o limite. Total: {$totalEncontrado}. Limitando a: {$MAX_REGISTROS_RELATORIO}");
                $query->take($MAX_REGISTROS_RELATORIO);
            }
            $query->orderBy($campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc')->groupBy('cal.id');
            $registros = $query->get();

            if($request->extensao == 'csv'){
                $headers = [
                    'Código',
                    'Identificação',
                    'Destino',
                    'Tipo',
                    'Situação',
                    ''
                ];

                // Delimitador padrão para CSVs que abrem bem no Excel (Ponto e Vírgula)
                $delimiter = ';';
                // Cria um stream temporário na memória
                $output = fopen('php://temp', 'r+'); 
                fwrite($output, "\xEF\xBB\xBF");
                fputcsv($output, $headers, $delimiter);
                foreach ($registros as $p) {
                    $documento = '';
                    $ultimaAlteracao = $p->clversao ? Carbon::parse($p->clversao)->format('d/m/Y H:i') : '';
                    $assunto = str_replace(["\r", "\n"], ' ', $p->clobserve ?? '');
                    //$totalAnexos = isset($p->anexos) ? count($p->anexos) : 0;            
                    $status = ($p->clstatus ?? 0) == 1 ? 'ATIVO' : 'INATIVO';
                    $tipo = '';
                    //$visibilidade = ($p->flagexibe ?? 0) == 1 ? 'PÚBLICO' : 'RESTRITO';

                    if($p->cltipo == 1){
                        $tipo = "Cadastro";
                    }else if($p->cltipo == 2){
                        $tipo = "Controle";
                    }else if($p->cltipo == 3){
                        $tipo = "SubCadastro";
                    }
                    $row = [
                        $p->id,
                        $p->clidentificacao ?? '',
                        $p->clrota ?? '',
                        $tipo ?? '',
                        $status,
                        $ultimaAlteracao,
                    ];
                    
                    fputcsv($output, $row, $delimiter);
                }

                // Volta ao início do stream para ler todo o conteúdo
                rewind($output);
                $csvContent = stream_get_contents($output);
                fclose($output); // Fecha o stream temporário

                // 3. RETORNO PARA DOWNLOAD
                $filename = 'Relatorio_Cals_' . now()->format('dmYHis') . '.csv';

                // O cabeçalho 'text/csv' garante que o arquivo seja baixado corretamente.
                return Response::make($csvContent, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Expires' => '0',
                    'Pragma' => 'public',
                ]);
            }else if ($request->extensao == 'doc') {
                $html = '<!DOCTYPE html><html><head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <title>Relação de Cals</title>
                    <style>
                        body { font-family: sans-serif; }
                        .table-report { 
                            width: 100%; 
                            border-collapse: collapse; 
                        }
                        .table-report th, .table-report td {
                            border: 1px solid #333333; 
                            padding: 6px 8px;
                            vertical-align: top;
                            font-size: 10px; 
                            line-height: 1.3;
                        }
                        .table-report th {
                            text-align: center;
                            font-weight: bold;
                            background-color: #f2f2f2;
                        }
                        .table-report td {
                            text-align: left;
                        }
                        .main-title {
                            display: block;
                            margin: 0;
                            padding: 0;
                            line-height: 1.3;
                        }        
                        .text-center { text-align: center; }
                        .text-left { text-align: left; }
                        .text-right { text-align: right; }
                        .status-ativo { color: #22c55e; font-weight: bold; }
                        .status-inativo { color: #ef4444; font-weight: bold; }
                    </style>
                    </head><body>';

                $html .= '<h1 style="color: #1a426f; text-align: center; padding-bottom: 10px; font-size: 18pt;">Relação de Cals</h1>';
                $html .= '<table class="table-report"><thead>
                    <tr>
                        <th width="5%">Código</th>
                        <th width="35%">Identificação</th>
                        <th width="25%">Destino</th>
                        <th width="15%">Tipo</th>
                        <th width="15%">Situação</th>
                        <th width="5%"></th>
                    </tr></thead><tbody>';

                if ($registros->isEmpty()) {
                    $html .= '<tr><td colspan="5" style="text-align: center; font-size: 10pt; color: #343a40;">Nenhum resultado encontrado para o relatório.</td></tr>';
                } else {
                    foreach ($registros as $p) {
                        // Tratamento do tipo conforme regras da Cal
                        $tipo = '';
                        if ($p->cltipo == 1) {
                            $tipo = 'Cadastro';
                        } else if ($p->cltipo == 2) {
                            $tipo = 'Controle';
                        } else if ($p->cltipo == 3) {
                            $tipo = 'SubCadastro';
                        }

                        // Tratamento de Situação
                        $isAtivo = ($p->clstatus ?? 0) == 1;
                        $statusHtml = $isAtivo 
                            ? '<span class="status-ativo">ATIVO</span>' 
                            : '<span class="status-inativo">INATIVO</span>';

                        // Tratamento da data de última alteração
                        $ultimaAlteracao = $p->clversao ? \Carbon\Carbon::parse($p->clversao)->format('d/m/Y H:i') : '';

                        $html .= '<tr class="text-left">';
                        
                        // Coluna: Código
                        $html .= '<td width="10%" class="text-center"><b>' . htmlspecialchars($p->id) . '</b></td>';
                        
                        // Coluna: Identificação
                        $html .= '<td width="35%">';
                        $html .= '<span class="main-title"><b>' . htmlspecialchars($p->clidentificacao ?? '') . '</b></span>';
                        if (!empty($p->clobserve)) {
                            $html .= '<small style="color: #666666;"><br>' . htmlspecialchars(\Illuminate\Support\Str::limit($p->clobserve, 120)) . '</small>';
                        }
                        $html .= '</td>';

                        // Coluna: Destino / Rota
                        $html .= '<td width="25%">' . htmlspecialchars($p->clrota ?? '') . '</td>';

                        // Coluna: Tipo
                        $html .= '<td width="15%" class="text-center">' . htmlspecialchars($tipo) . '</td>';

                        // Coluna: Situação e Alteração
                        $html .= '<td width="15%" class="text-center">';
                        $html .= $statusHtml;
                        if ($ultimaAlteracao) {
                            $html .= '<br><small style="font-size: 8px; color: #555555;"><b>Alt:</b> ' . $ultimaAlteracao . '</small>';
                        }
                        $html .= '</td>';

                        $html .= '</tr>';
                    }
                }

                $html .= '</tbody></table></body></html>';
                $filename = 'Relatorio_Cals_' . now()->format('dmYHis') . '.doc';

                return \Illuminate\Support\Facades\Response::make($html, 200, [
                    'Content-Type' => 'application/msword',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Expires' => '0',
                    'Pragma' => 'public',
                ]);
            }else{
                $html = '';

                if ($registros->isEmpty()) {
                    $html .= '<tr><td colspan="5" style="text-align: center; font-size: 10pt; color: #343a40; padding: 10px;">Nenhum resultado encontrado para o relatório.</td></tr>';
                } else {
                    foreach ($registros as $p) {
                        // Tratamento do Tipo da Cal
                        $tipo = '';
                        if ($p->cltipo == 1) {
                            $tipo = 'Cadastro';
                        } else if ($p->cltipo == 2) {
                            $tipo = 'Controle';
                        } else if ($p->cltipo == 3) {
                            $tipo = 'SubCadastro';
                        }

                        // Tratamento da Situação
                        $isAtivo = ($p->clstatus ?? 0) == 1;
                        $statusHtml = $isAtivo 
                            ? '<small><span class="status-ativo">ATIVO</span></small>' 
                            : '<small><span class="status-inativo">INATIVO</span></small>';

                        // Formatação de data da última alteração
                        $ultimaAlteracao = $p->clversao 
                            ? \Carbon\Carbon::parse($p->clversao)->format('d/m/Y H:i') 
                            : '';

                        $html .= '<tr class="text-left">';
                        
                        // Coluna 1: Código
                        $html .= '<td width="10%" class="text-center"><b>' . htmlspecialchars($p->id) . '</b></td>';

                        // Coluna 2: Identificação e Observação
                        $html .= '<td width="35%">';
                        $html .= '<span class="main-title"><b>' . htmlspecialchars($p->clidentificacao ?? '') . '</b></span>';
                        if (!empty($p->clobserve)) {
                            $html .= '<small style="color: #555555;"><br><b>Obs: </b>' . htmlspecialchars(\Illuminate\Support\Str::limit($p->clobserve, 120)) . '</small>';
                        }
                        $html .= '</td>';

                        // Coluna 3: Destino / Rota
                        $html .= '<td width="25%">' . htmlspecialchars($p->clrota ?? '') . '</td>';

                        // Coluna 4: Tipo
                        $html .= '<td width="15%" class="text-center">' . htmlspecialchars($tipo) . '</td>';

                        // Coluna 5: Situação e Alteração
                        $html .= '<td width="15%" class="text-center">';
                        $html .= $statusHtml;
                        if ($ultimaAlteracao) {
                            $html .= '<br><small style="font-size: 8px; color: #555555;"><b>Alt: </b>' . $ultimaAlteracao . '</small>';
                        }
                        $html .= '</td>';

                        $html .= '</tr>';
                    }
                }

                $fileName = 'Relatorio_Cals_' . time() . '.pdf';
                $tituloRelatorio = 'Relação de Cals';

                // Renderiza a view container que inclui o cabeçalho/estilos da tabela
                $htmlContent = view('controle.cals_relatorio', [
                    'html1' => $html,
                    'titulo1' => $tituloRelatorio
                ])->render();

                // Inicialização da biblioteca de PDF customizada do sistema
                $pdf = new PDFRELATORIO(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->SetCreator(env('CLIENT_DATA_NAME', 'CargaFácil'));
                $pdf->SetAuthor(env('CLIENT_DATA_NAME', 'CargaFácil'));
                $pdf->SetTitle($tituloRelatorio);
                $pdf->SetSubject('Impressão de Relatório de Cals');

                // Configuração de Margens do Documento
                $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(true);

                // Definição da fonte padrão TCPDF
                $pdf->SetFont('helvetica', '', 9);
                $pdf->AddPage();
                $pdf->writeHTML($htmlContent, true, false, true, false, '');

                // Stream direto para o navegador
                $pdf->Output($fileName, 'I');

                return response()->make('', 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                ]);
            }
        } catch (Exception $e) {
            $except = $e->getMessage();
            return Tools::setResponse('fail', null, 'Falha ao obter dados');
        }
    }

}
