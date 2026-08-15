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
use Inertia\Inertia;

class MenusController extends Controller
{
    public function inicio()
    {
        $gestor = Tools::getGestor();
        return Inertia::render('Controle/Menus', [
            'gestor' => $gestor,
            'cal' => Cals::CALMENUS
        ]);
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

    public function get(mixed $id)
    {
        $reg = Menu::find($id);
        return $reg;
    }


    public function removerId(mixed $id)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR'); /* Desativa ou remove do banco */

        if ($sistemadesativar > 0) {
            $reg = Menu::find($id);

            if (!$reg || !$reg->exists) {
                return response()->json(Tools::setResult('fail', null, 'Registro não encontrado no sistema'));
            }

            if (($reg->flagcontrole ?? 0) == 1 && (Session::get('user')->grupo->id ?? 0) != 1) {
                return response()->json(Tools::setResult('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte'));
            }

            $reg->mnstatus = 0;

            if ($reg->save()) {
                return response()->json(Tools::setResult('success', $reg, 'Registro Desativado com Sucesso'));
            } else {
                return response()->json(Tools::setResult('fail', null, 'Falha no Processamento'));
            }
        } else {
            $reg = Menu::find($id);

            if (!$reg || !$reg->exists) {
                return response()->json(Tools::setResult('fail', null, 'Registro não encontrado no sistema'));
            }

            if (($reg->flagcontrole ?? 0) == 1 && (Session::get('user')->grupo->id ?? 0) != 1) {
                return response()->json(Tools::setResult('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte'));
            }

            if ($reg->delete()) {
                return response()->json(Tools::setResult('success', $reg, 'Registro removido com Sucesso'));
            } else {
                return response()->json(Tools::setResult('fail', null, 'Falha no Processamento'));
            }
        }
    }    

    public function removerLote(Request $request)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR');
        if($sistemadesativar > 0){
            $ids = explode(',', $request->ids);
            $regs = Menu::whereIn('id', $ids);
            $qtd = $regs->count();
            
            if($regs->update(['mnstatus' => 0])){
                return response()->json(Tools::setResult('success', $regs, ($qtd > 1 ? 'Registros Desativados com Sucesso' : 'Registro Desativado com Sucesso')));
            }else{
                return response()->json(Tools::setResult('fail', null, ($qtd > 1 ? 'Impossível executar nos Registros Selecionados' : 'Impossível executar no Registro Selecionado')));
            }
        }else{
            $ids = explode(',', $request->ids);
            $regs = Menu::whereIn('id', $ids);
            $qtd = $regs->count();
            
            if($regs->delete()){
                return response()->json(Tools::setResult('success', $regs, ($qtd > 1 ? 'Registros Deletados com Sucesso' : 'Registro Deletado com Sucesso')));
            }else{
                return response()->json(Tools::setResult('fail', null, ($qtd > 1 ? 'Impossível executar nos Registros Selecionados' : 'Impossível executar no Registro Selecionado')));
            }
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

        // 1. Filtro por Status
        if ((int)$request->statusfiltro === 0) {
            $query->where('menu.mnstatus', '>=', 0);
        } else if ((int)$request->statusfiltro === 1) {
            $query->where('menu.mnstatus', 1);
        } else if ((int)$request->statusfiltro === 2) {
            $query->where('menu.mnstatus', 0);
        }

        // 2. Filtro por Período de Datas (mnversao)
        if (strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('menu.mnversao', '>=', $request->datainiciofiltro . ' 00:00:00');
        } else if (strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('menu.mnversao', '>=', $request->datainiciofiltro . ' 00:00:00');
            $query->where('menu.mnversao', '<=', $request->datafinalfiltro . ' 23:59:59');
        } else if (strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('menu.mnversao', '<=', $request->datafinalfiltro . ' 23:59:59');
        }

        // 3. Campo de Ordenação Sanitizado
        $campoordenar = ($request->campoordem && $request->campoordem !== 'undefined' && $request->campoordem !== 'null') 
            ? $request->campoordem 
            : 'id';

        // 4. Busca Ampla Sanitizada com Quebra de Termos (Palavra por Palavra)
        if (strlen($request->campoPesquisa) > 0 && $request->campoPesquisa !== 'undefined') {
            $termos = array_filter(explode(' ', trim($request->campoPesquisa)));
            $query->where(function ($queryGeral) use ($termos) {
                foreach ($termos as $termo) {
                    // Para cada palavra, cria um subgrupo AND que busca em qualquer uma das colunas da tabela Menu (OR)
                    $queryGeral->where(function ($qSub) use ($termo) {
                        $qSub->where('menu.mnidentificacao', 'like', '%' . $termo . '%')
                            ->orWhere('menu.mnnumeracao', 'like', '%' . $termo . '%')
                            ->orWhere('menu.mnicone', 'like', '%' . $termo . '%');
                    });
                }
            });
        } else if (strlen($request->campoPesquisa) > 0 && $request->tipofiltro === 'exato') {
            $query->where('menu.mnidentificacao', 'like', '%' . $request->campoPesquisa . '%')
                ->where('menu.mnnumeracao', 'like', '%' . $request->campoPesquisa . '%')
                ->where('menu.mnicone', 'like', '%' . $request->campoPesquisa . '%');
        }

        // Relacionamentos específicos da tabela Menu mantidos
        $query->with('cal', 'menuacima');

        // Ordenação e Agrupamento
        $direcaoOrdem = (strlen($request->ordem) > 0 && $request->ordem !== 'undefined') ? $request->ordem : 'desc';
        $query->orderBy('menu.' . $campoordenar, $direcaoOrdem)->groupBy('menu.id');

        try {
            $registros = $query->paginate($request->regPg);
            
            // Registro de Auditoria e Retorno no Padrão do Modelo (Inertia Session Flash)
            Tools::setAtividade(0, 8, 0, 'Listagem de Menus', 'Listado com Sucesso');
            return back()->with(Tools::setResult('success', $registros, 'Dados Enviados com Sucesso'));

        } catch (Exception $e) {
            $except = $e->getMessage();
            
            // Registro de Erro na Auditoria e Retorno via Bag de Erros
            Tools::setAtividade(0, 8, 0, 'Listagem de Menus', 'Falha na Listagem de Menus - ' . $except);
            $resp = Tools::setResult('fail', null, 'Falha no Processamento - ' . $except);
            return back()->withErrors($resp);
        }
    }

    public function salvar(Request $request)
    {
        $gestor = Tools::getGestor();
        if ($gestor > 0) {
            $validator = Validator::make(
                [
                    'identificacao' => $request->mnidentificacao,
                ], 
                [
                    'identificacao' => 'required|string|min:5|max:198',
                ],
                [
                    'identificacao.required' => 'Necessário Informar a Identificação',
                    'identificacao.min'      => 'Necessário Informar a Identificação',
                    'identificacao.string'   => 'Necessário Informar a Identificação',
                    'identificacao.max'      => 'Necessário Informar a Identificação',
                ]
            );

            if ($validator->fails()) {
                return back()->withErrors($validator->errors()->first());
            }

            try {
                $reg = null;
                if ($request->id > 0) {
                    $reg = Menu::find($request->id);
                    if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                        return back()->withErrors('Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                    }
                } else {
                    $reg = new Menu();
                }

                $reg->mnidentificacao    = strlen($request->mnidentificacao) > 0 ? $request->mnidentificacao : '';
                $reg->mnicone            = strlen($request->mnicone) > 0 ? $request->mnicone : '';
                $reg->mnnumeracao        = strlen($request->mnnumeracao) > 0 ? $request->mnnumeracao : '';
                $reg->fkidmenunivelacima = $request->fkidmenunivelacima > 0 ? $request->fkidmenunivelacima : 0;
                $reg->fkidcal            = $request->fkidcal > 0 ? $request->fkidcal : 0;
                $reg->mnsequencia        = $request->mnsequencia > 0 ? $request->mnsequencia : 0;
                $reg->mndestaque         = '';
                $reg->mnskin             = '';
                $reg->mnstatus           = $request->mnstatus;
                $reg->mnversao           = Carbon::now()->toDateTimeString();
                $reg->flagdelete         = 0;
                $reg->flagatualiza       = 1;
                $reg->flaguser           = Session::get('user')->id;

                if ($reg->save()) {
                    return back()->with(Tools::setResult('success', $reg, 'Processamento Realizado com Sucesso'));
                } else {
                    Tools::setAtividade(0, 9, 0, 'Menu', 'Falha na Tentativa Salvamento Registro');
                    return back()->withErrors('Falha no Processamento');
                }
            } catch (Exception $e) {
                $except = $e->getMessage();
                Tools::setAtividade(0, 9, 0, 'Menu', 'Falha na Tentativa Salvamento Registro - ' . $except);
                return back()->withErrors('Falha no Processamento');
            }
        } else {
            Tools::setAtividade(0, 9, 0, 'Menu', 'Falha na Tentativa Salvamento Registro');
            return back()->withErrors('Falha no Processamento');
        }
    }

    public function update(Request $request)
    {
        $gestor = Tools::getGestor();
        if ($gestor > 0 || Tools::getGrupoGeral()) {
            $validator = Validator::make(
                [
                    'idregistro' => $request->idregistro,
                ],
                [
                    'idregistro' => 'required|integer|min:1',
                ],
                [
                    'idregistro.required' => 'Dados Inválidos',
                    'idregistro.integer'  => 'Dados Inválidos',
                    'idregistro.min'      => 'Dados Inválidos',
                ]
            );

            if ($validator->fails()) {
                return back()->withErrors($validator->errors()->first());
            }

            $reg = Menu::find($request->idregistro);

            if (!$reg || !$reg->exists) {
                return back()->withErrors('Registro Não Encontrado');
            }

            // Validação de Trava de Controle de Sistema
            if ($reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                return back()->withErrors('Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
            }

            // Alteração dinâmica de status conforme o parâmetro enviado
            if ($request->campo == 'status') {
                $reg->mnstatus = ($reg->mnstatus > 0 ? 0 : 1);
            }

            /* Exemplo para expansão de novos toggles pontuais no Menu
            if ($request->campo == 'flagexibe') {
                $reg->flagexibe = ($reg->flagexibe > 0 ? 0 : 1);
            }
            */

            $reg->mnversao     = Carbon::now()->toDateTimeString();
            $reg->flaguser     = Session::get('user')->id;
            $reg->flagatualiza = 1;
            $reg->flagdelete   = 0;

            if ($reg->save()) {
                return back()->with(Tools::setResult('success', $reg, 'Processamento Realizado com Sucesso'));
            } else {
                Tools::setAtividade(0, 9, 0, 'Menu', 'Falha na Tentativa de Alterar Registro');
                return back()->withErrors('Falha no Processamento');
            }
        } else {
            Tools::setAtividade(0, 9, 0, 'Menu', 'Falha na Tentativa de Alterar Registro');
            return back()->withErrors('Falha no Processamento');
        }
    }

    public function relatorio(Request $request)
    {
        $query = self::initQuery();

        // 1. Filtro por Status
        if ($request->statusfiltro == 0) {
            $query->where('menu.mnstatus', '>=', 0);
        } else if ($request->statusfiltro == 1) {
            $query->where('menu.mnstatus', 1);
        } else if ($request->statusfiltro == 2) {
            $query->where('menu.mnstatus', 0);
        }

        // 2. Filtro por Período de Datas (mnversao)
        if (strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) <= 0) {
            $query->where('menu.mnversao', '>=', $request->datainiciofiltro . ' 00:00:00');
        } else if (strlen($request->datainiciofiltro) > 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('menu.mnversao', '>=', $request->datainiciofiltro . ' 00:00:00');
            $query->where('menu.mnversao', '<=', $request->datafinalfiltro . ' 23:59:59');
        } else if (strlen($request->datainiciofiltro) <= 0 && strlen($request->datafinalfiltro) > 0) {
            $query->where('menu.mnversao', '<=', $request->datafinalfiltro . ' 23:59:59');
        }

        // 3. Campo de Ordenação Sanitizado
        $campoordenar = 'id';
        $campoordenar = ($request->campoordem && $request->campoordem !== 'undefined' && $request->campoordem !== 'null') 
            ? $request->campoordem 
            : 'id';

        // 4. Busca Ampla Sanitizada por Subgrupos com Quebra de Termos
        if (strlen($request->campoPesquisa) > 0 && $request->campoPesquisa !== 'undefined') {
            $termos = array_filter(explode(' ', trim($request->campoPesquisa)));
            $query->where(function ($queryGeral) use ($termos) {
                foreach ($termos as $termo) {
                    $queryGeral->where(function ($qSub) use ($termo) {
                        $qSub->where('menu.mnidentificacao', 'like', '%' . $termo . '%')
                            ->orWhere('menu.mnnumeracao', 'like', '%' . $termo . '%')
                            ->orWhere('menu.mnicone', 'like', '%' . $termo . '%')
                            ->orWhere('menu.id', 'like', '%' . $termo . '%');
                    });
                }
            });
        } else if (strlen($request->campoPesquisa) > 0 && $request->tipofiltro == 'exato') {
            $query->where('menu.mnidentificacao', 'like', '%' . $request->campoPesquisa . '%')
                ->where('menu.mnnumeracao', 'like', '%' . $request->campoPesquisa . '%')
                ->where('menu.mnicone', 'like', '%' . $request->campoPesquisa . '%')
                ->where('menu.id', 'like', '%' . $request->campoPesquisa . '%');
        }

        $MAX_REGISTROS_RELATORIO = (int) (env('MAX_REGISTROS_RELATORIO') ?? 100000);

        try {
            $totalEncontrado = $query->count('menu.id');
            if ($totalEncontrado > $MAX_REGISTROS_RELATORIO) {
                \Log::warning("Geração de relatório excedeu o limite. Total: {$totalEncontrado}. Limitando a: {$MAX_REGISTROS_RELATORIO}");
                $query->take($MAX_REGISTROS_RELATORIO);
            }

            // Traz o relacionamento com o menu pai (menuacima)
            $query->with('menuacima');
            $query->orderBy('menu.' . $campoordenar, strlen($request->ordem) > 0 ? $request->ordem : 'desc')->groupBy('menu.id');
            $registros = $query->get();

            // 5. Definição Unificada de Colunas Solicitadas
            $colunasRelatorio = [
                'id'                  => ['label' => 'Código',          'width' => '8%',  'align' => 'center'],
                'mnidentificacao'     => ['label' => 'Identificação',   'width' => '30%', 'align' => 'left'],
                'mnnumeracao'         => ['label' => 'Numeração',       'width' => '12%', 'align' => 'center'],
                'fkidmenunivelacima'  => ['label' => 'Menu Superior',   'width' => '25%', 'align' => 'left'],
                'mnsequencia'         => ['label' => 'Sequência',       'width' => '10%', 'align' => 'center'],
                'mnstatus'            => ['label' => 'Situação',        'width' => '15%', 'align' => 'center'],
            ];

            $theadHtml = '<tr>';
            foreach ($colunasRelatorio as $coluna) {
                $theadHtml .= sprintf(
                    '<th width="%s" class="text-%s">%s</th>',
                    $coluna['width'],
                    $coluna['align'],
                    htmlspecialchars($coluna['label'], ENT_QUOTES, 'UTF-8')
                );
            }
            $theadHtml .= '</tr>';

            $tituloRelatorio = $request->titulorelatorio ?? 'Relação de Menus';

            // =========================================================================
            // EXPORTAÇÃO 1: CSV (Ponto e Vírgula / UTF-8 BOM)
            // =========================================================================
            if ($request->extensao == 'csv') {
                $headers = array_column($colunasRelatorio, 'label');
                $delimiter = ';';
                $output = fopen('php://temp', 'r+');
                fwrite($output, "\xEF\xBB\xBF");
                fputcsv($output, $headers, $delimiter);

                foreach ($registros as $p) {
                    $versaodatahr = $p->mnversao ? Carbon::parse($p->mnversao)->format('d/m/Y H:i') : '';
                    $status = ($p->mnstatus ?? 0) == 1 ? 'ATIVO' : 'INATIVO';
                    $menuPaiNome = $p->menuacima->mnidentificacao ?? 'Nenhum (Raiz)';

                    $row = [
                        $p->id,
                        $p->mnidentificacao ?? '',
                        $p->mnnumeracao ?? '',
                        $menuPaiNome,
                        $p->mnsequencia ?? 0,
                        $status,
                        $versaodatahr,
                    ];

                    fputcsv($output, $row, $delimiter);
                }

                rewind($output);
                $csvContent = stream_get_contents($output);
                fclose($output);

                $filename = 'Relatorio_' . ($request->modulo ?? 'Menus') . '_' . now()->format('dmYHis') . '.csv';

                return \Response::make($csvContent, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Expires' => '0',
                    'Pragma' => 'public',
                ]);
            }

            // =========================================================================
            // EXPORTAÇÃO 2: WORD (DOC)
            // =========================================================================
            else if ($request->extensao == 'doc') {
                $html = '<!DOCTYPE html><html><head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <title>Relação de Menus</title>
                    <style>
                        body { font-family: sans-serif; }
                        .table-report { width: 100%; border-collapse: collapse; }
                        .table-report th, .table-report td {
                            border: 1px solid #333333;
                            padding: 6px 8px;
                            vertical-align: top;
                            font-size: 10px;
                            line-height: 1.3;
                        }
                        .table-report th { text-align: center; font-weight: bold; background-color: #f2f2f2; }
                        .table-report td { text-align: left; }
                        .main-title { display: block; margin: 0; padding: 0; line-height: 1.3; }
                        .text-center { text-align: center; }
                        .text-left { text-align: left; }
                        .text-right { text-align: right; }
                        .status-ativo { color: #22c55e; font-weight: bold; }
                        .status-inativo { color: #ef4444; font-weight: bold; }
                    </style>
                    </head><body>';

                $html .= '<h1 style="color: #1a426f; text-align: center; padding-bottom: 10px; font-size: 18pt;">' . $tituloRelatorio . '</h1>';
                $html .= '<table class="table-report"><thead>' . $theadHtml . '</thead><tbody>';

                if ($registros->isEmpty()) {
                    $html .= '<tr><td colspan="6" style="text-align: center; font-size: 10pt; color: #343a40;">Nenhum resultado encontrado para o relatório.</td></tr>';
                } else {
                    foreach ($registros as $p) {
                        $versaodatahr = $p->mnversao ? Carbon::parse($p->mnversao)->format('d/m/Y H:i') : '';
                        $isAtivo = ($p->mnstatus ?? 0) == 1;
                        $statusHtml = $isAtivo 
                            ? '<span class="status-ativo">ATIVO</span>' 
                            : '<span class="status-inativo">INATIVO</span>';

                        $menuPaiNome = htmlspecialchars($p->menuacima->mnidentificacao ?? 'Nenhum (Raiz)', ENT_QUOTES, 'UTF-8');

                        $html .= '<tr class="text-left">';
                        $html .= '<td width="' . $colunasRelatorio['id']['width'] . '" class="text-center"><b>' . htmlspecialchars($p->id) . '</b></td>';
                        $html .= '<td width="' . $colunasRelatorio['mnidentificacao']['width'] . '"><span class="main-title"><b>' . htmlspecialchars($p->mnidentificacao ?? '', ENT_QUOTES, 'UTF-8') . '</b></span></td>';
                        $html .= '<td width="' . $colunasRelatorio['mnnumeracao']['width'] . '" class="text-center">' . htmlspecialchars($p->mnnumeracao ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                        $html .= '<td width="' . $colunasRelatorio['fkidmenunivelacima']['width'] . '">' . $menuPaiNome . '</td>';
                        $html .= '<td width="' . $colunasRelatorio['mnsequencia']['width'] . '" class="text-center">' . htmlspecialchars($p->mnsequencia ?? 0) . '</td>';
                        $html .= '<td width="' . $colunasRelatorio['mnstatus']['width'] . '" class="text-center">';
                        $html .= $statusHtml . '<br><small style="font-size: 8px; color: #555555;"> ' . $versaodatahr . '</small>';
                        $html .= '</td>';
                        $html .= '</tr>';
                    }
                }

                $html .= '</tbody></table></body></html>';
                $filename = 'Relatorio_' . ($request->modulo ?? 'Menus') . '_' . now()->format('dmYHis') . '.doc';

                return \Response::make($html, 200, [
                    'Content-Type' => 'application/msword',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Expires' => '0',
                    'Pragma' => 'public',
                ]);
            }

            // =========================================================================
            // EXPORTAÇÃO 3: PDF (TCPDF / PDFRELATORIO)
            // =========================================================================
            else {
                $html = '';
                if ($registros->isEmpty()) {
                    $html .= '<tr><td colspan="6" style="text-align: center; font-size: 10pt; color: #343a40; padding: 10px;">Nenhum resultado encontrado para o relatório.</td></tr>';
                } else {
                    foreach ($registros as $p) {
                        $versaodatahr = $p->mnversao ? Carbon::parse($p->mnversao)->format('d/m/Y H:i') : '';
                        $isAtivo = ($p->mnstatus ?? 0) == 1;
                        $statusHtml = $isAtivo 
                            ? '<small><span class="status-ativo">ATIVO</span></small>' 
                            : '<small><span class="status-inativo">INATIVO</span></small>';

                        $menuPaiNome = htmlspecialchars($p->menuacima->mnidentificacao ?? 'Nenhum (Raiz)', ENT_QUOTES, 'UTF-8');

                        $html .= '<tr>';
                        $html .= '<td width="' . $colunasRelatorio['id']['width'] . '" class="text-center"><b>' . htmlspecialchars($p->id) . '</b></td>';
                        $html .= '<td width="' . $colunasRelatorio['mnidentificacao']['width'] . '"><b>' . htmlspecialchars($p->mnidentificacao ?? '', ENT_QUOTES, 'UTF-8') . '</b></td>';
                        $html .= '<td width="' . $colunasRelatorio['mnnumeracao']['width'] . '" style="text-align: center;">' . htmlspecialchars($p->mnnumeracao ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
                        $html .= '<td width="' . $colunasRelatorio['fkidmenunivelacima']['width'] . '">' . $menuPaiNome . '</td>';
                        $html .= '<td width="' . $colunasRelatorio['mnsequencia']['width'] . '" style="text-align: center;">' . htmlspecialchars($p->mnsequencia ?? 0) . '</td>';
                        $html .= '<td width="' . $colunasRelatorio['mnstatus']['width'] . '" style="text-align: center;">' . $statusHtml;
                        $html .= '<br><small style="font-size: 8px; color: #555;"><b>Alt: </b>' . $versaodatahr . '</small>';
                        $html .= '</td>';
                        $html .= '</tr>';
                    }
                }

                $fileName = 'Relatorio_' . ($request->modulo ?? 'Menus') . '_' . time() . '.pdf';

                // Renderiza a view container repassando os blocos montados
                $htmlContent = view('relatorios.cal_relatorio', [
                    'html1' => $html,
                    'titulo1' => $tituloRelatorio,
                    'head1' => $theadHtml,
                ])->render();

                // Inicialização da classe TCPDF customizada
                $pdf = new PDFRELATORIO(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $pdf->SetCreator(env('CLIENT_DATA_NAME', env('APP_NAME')));
                $pdf->SetAuthor(env('CLIENT_DATA_NAME', env('APP_NAME')));
                $pdf->SetTitle($tituloRelatorio);
                $pdf->SetSubject('Impressão de ' . $tituloRelatorio);

                // Configuração de Margens
                $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(true);

                // Fonte padrão TCPDF
                $pdf->SetFont('helvetica', '', 9);
                $pdf->AddPage();
                $pdf->writeHTML($htmlContent, true, false, true, false, '');
                $pdfBinary = $pdf->Output($fileName, 'S');

                return response($pdfBinary, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"',
                    'Cache-Control' => 'private, max-age=0, must-revalidate',
                    'Pragma' => 'public'
                ]);
            }
        } catch (\Exception $e) {
            $except = $e->getMessage();
            Tools::setAtividade(0, 8, 0, 'Relatório de ' . ($request->modulo ?? 'Menus'), 'Falha no Processamento - ' . $except);
            $resperr = Tools::setResult('fail', null, 'Falha no Processamento');
            return back()->withErrors($resperr);
        }
    }

}
