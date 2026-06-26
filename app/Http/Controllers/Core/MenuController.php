<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Core\Tools;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Menus;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function getMenus(Int $idNivel = 0, Int $idGrupo)
    {
        return Menu::leftjoin('cal', 'menu.fkidcal', '=', 'cal.id')->leftjoin('calpermissao', 'menu.fkidcal', '=', 'calpermissao.fkidcal')->select([DB::raw('menu.mnidentificacao, menu.mnnumeracao, menu.id, menu.mnicone, menu.mndestaque, menu.mnskin, menu.fkidcal, cal.clrota, cal.clidentificacao, calpermissao.cppermissao')])->where('fkidmenunivelacima',$idNivel)->where('mnstatus', 1)->where('fkidgrupo', $idGrupo)->groupBy('menu.id')->orderBy('mnsequencia', 'asc')->get();
    }



    public function montaMenu(Int $idGrupo)
    {
        $url = url('');
        $html = '';
        $primeiro = true;
        $menus = $this->getMenus(0,$idGrupo);
        if (isset($menus) && count($menus) > 0) { // menus de primeiro nivel
            //$html .= $this->getItem($menus, $this->primeiro);
            foreach ($menus as $menu) {
                //verifica se tem menu com esse id como nível acima teste 1
                if (substr($menu->cppermissao, 0, 2) == '11')
                {
                    $menus1 = $this->getMenus($menu->id, $idGrupo);// teste 1
                //caso exista monta o main e traz os menus e faz o teste 1 denovo
                if (isset($menus1) && count($menus1) > 0) {
                    $html .= '<li><a aria-expanded="false" class="has-arrow waves-effect waves-dark menu1" href="'.$url . ($menu->fkidcal > 0 ? $menu->clrota : '/dashboard') . '"><i class="'.($menu->mnicone != '' ? $menu->mnicone : 'icon-speedometer').'"></i><span class="hide-menu"><span class="badge badge-pill badge-cyan ml-auto">' . ($menu->mnnumeracao != '' ? $menu->mnnumeracao : $menu->id )  . '</span>' . $menu->mnidentificacao . '</span></a>';
                    $primeiro = false;
                    $html .= '<ul aria-expanded="false" class="collapse in">';
                    foreach ($menus1 as $menu1) {
                        if (substr($menu1->cppermissao, 0, 2) == '11')
                        {
                            $menus2 = $this->getMenus($menu1->id, $idGrupo);
                        if (isset($menus2) && count($menus2) > 0) {
                            $html .= '<li><a aria-expanded="false" class="has-arrow waves-effect waves-dark menu2" href="javascript:void(0)"><small>' . ($menu1->mnnumeracao != '' ? $menu1->mnnumeracao : $menu1->id )  . '-' . $menu1->mnidentificacao . '</small></a>';
                            $html .= '<ul aria-expanded="false" class="collapse">';
                            foreach ($menus2 as $menu2) {
                                if (substr($menu2->cppermissao, 0, 2) == '11')
                                {
                                $menus3 = $this->getMenus($menu2->id, $idGrupo);
                                if (isset($menus3) && count($menus3) > 0) {
                                    $html .= '<li><a aria-expanded="false" class="has-arrow waves-effect waves-dark menu3" href="'.$url . ($menu2->fkidcal > 0 ? $menu2->clrota : '/dashboard') . '"><i class="icon-speedometer"></i><span class="hide-menu"><span class="badge badge-pill badge-cyan ml-auto">' . ($menu2->mnnumeracao != '' ? $menu2->mnnumeracao : $menu2->id )  . '</span>' . $menu2->mnidentificacao . '</span></a>';
                                    $html .= '<ul aria-expanded="false" class="collapse">';
                                    foreach ($menus3 as $menu3) {
                                        if (substr($menu3->cppermissao, 0, 2) == '11')
                                        {
                                            $menus4 = $this->getMenus($menu3->id, $idGrupo);
                                            if (isset($menus4) && count($menus4) > 0) {
                                                $html .= '<li><a aria-expanded="false" class="has-arrow waves-effect waves-dark" href="' .$url. ($menu3->fkidcal > 0 ? $menu3->clrota : '/dashboard') . '"><i class="icon-speedometer"></i><span class="hide-menu"><span class="badge badge-pill badge-cyan ml-auto">' . ($menu3->mnnumeracao != '' ? $menu3->mnnumeracao : $menu3->id )  . '</span>' . $menu3->mnidentificacao . '</span></a>';
                                                $html .= '<ul aria-expanded="false" class="collapse">';
                                                foreach ($menus4 as $menu4) {
                                                    if (substr($menu4->cppermissao, 0, 2) == '11')
                                                    {
                                                        $menus5 = $this->getMenus($menu4->id, $idGrupo);
                                                        if (isset($menus5) && count($menus5) > 0) {
                                                            $html .= '<li><a aria-expanded="false" class="has-arrow waves-effect waves-dark" href="' .$url. ($menu4->fkidcal > 0 ? $menu4->clrota : '/dashboard') . '"><i class="icon-speedometer"></i><span class="hide-menu"><span class="badge badge-pill badge-cyan ml-auto">' . ($menu4->mnnumeracao != '' ? $menu4->mnnumeracao : $menu4->id )  . '</span>' . $menu4->mnidentificacao . '</span></a>';
                                                            $html .= '<ul aria-expanded="false" class="collapse">';
                                                            foreach ($menus5 as $menu5) {

                                                                $menus6 = $this->getMenus($menu5->id, $idGrupo);
                                                                if (isset($menus6) && count($menus6) > 0) {
                                                                    $html .= '<li><a aria-expanded="false" class="has-arrow waves-effect waves-dark" href="' .$url. ($menu5->fkidcal > 0 ? $menu5->clrota : '/dashboard') . '"><i class="icon-speedometer"></i><span class="hide-menu"><span class="badge badge-pill badge-cyan ml-auto">' . ($menu5->mnnumeracao != '' ? $menu5->mnnumeracao : $menu5->id )  . '</span>' . $menu5->mnidentificacao . '</span></a>';
                                                                    $html .= '<ul aria-expanded="false" class="collapse">';
                                                                } else {
                                                                    $html .= '<li><a aria-expanded="false" class="has-arrow waves-effect waves-dark" href="'.$url . ($menu5->fkidcal > 0 ? $menu5->clrota : '/dashboard') . '"><i class="icon-speedometer"></i><span class="hide-menu"><span class="badge badge-pill badge-cyan ml-auto">' . ($menu5->mnnumeracao != '' ? $menu5->mnnumeracao : $menu5->id )  . '</span>' . $menu5->mnidentificacao . '</span></a>';
                                                                }
                                                                if (isset($menus6) && count($menus6) > 0) {
                                                                    $html .= '</ul>';
                                                                }

                                                            }
                                                            $html .= '</ul>';

                                                        } else {
                                                            $html .= '<li><a href="' .$url. ($menu4->fkidcal > 0 ? $menu4->clrota : '/dashboard') . '">' . ($menu4->mnnumeracao != '' ? $menu4->mnnumeracao : $menu4->id )  . '-' . $menu4->mnidentificacao . '</a>';
                                                        }
                                                    }
                                                }
                                                $html .= '</ul>';

                                            } else {
                                                $html .= '<li><a href="' .$url. ($menu3->fkidcal > 0 ? $menu3->clrota : '/dashboard') . '">' . ($menu3->mnnumeracao != '' ? $menu3->mnnumeracao : $menu3->id )  . '-' . $menu3->mnidentificacao . '</a>';
                                            }
                                        }
                                }
                                    $html .= '</ul>';
                                } else {
                                    $html .= '<li class="menu113"><a href="' .$url. ($menu2->fkidcal > 0 ? $menu2->clrota : '/dashboard') . '">'. $menu2->mnidentificacao . '</a>';
                                }
                            }
                        }
                            $html .= '</ul>';

                        } else {
                            $html .= '<li class="menu112"><a href="'.$url. ($menu1->fkidcal > 0 ? $menu1->clrota : '/dashboard') . '"><small>'. ($menu1->mnnumeracao != '' ? $menu1->mnnumeracao : $menu1->id )  . '-' . $menu1->mnidentificacao . '</small></a>';
                        }
                    }
                    }

                    $html .= '</ul>';
                } else {
                    $html .= '<li class="menu111"><a href="' .$url. ($menu->fkidcal > 0 ? $menu->clrota : '/dashboard') . '"><i class="'.($menu->mnicone != '' ? $menu->mnicone : 'icon-speedometer').'"></i><span class="hide-menu"><span class="badge badge-pill badge-cyan ml-auto">' . ($menu->mnnumeracao != '' ? $menu->mnnumeracao : $menu->id )  . '</span>' . $menu->mnidentificacao . '</span></a>';
                    $primeiro = false;
                }
                $html .= '</li>';
            }
            }
        }
        Tools::setAtividade(0, 8, 0,  'Menu Montado', 'Montagem de Menu de Grupo por Permissão');
        return $html;
    }
    
    

    /**
     * Ponto de entrada que busca o primeiro nível e dispara a montagem do JSON
     */
    public function montaMenu2(Int $idGrupo)
    {
        // 1. Busca os menus de nível zero (raiz)
        $menusRaiz = $this->getMenus(0, $idGrupo);
        $arvoreMenu = [];

        if ($menusRaiz && count($menusRaiz) > 0) {
            foreach ($menusRaiz as $menu) {
                // Aplica sua regra de permissão '11'
                if (substr($menu->cppermissao, 0, 2) == '11') {
                    // Dispara a função inteligente que vai descendo os níveis automaticamente
                    $arvoreMenu[] = $this->construirArvoreMenu($menu, $idGrupo);
                }
            }
        }

        // Registra sua atividade como você já fazia
        Tools::setAtividade(0, 8, 0, 'Menu Montado', 'Montagem de Menu de Grupo por Permissão');
        return $arvoreMenu;
    }

    /** Exemplo de Arry de Menu
     *             $menusPermitidos = [
                [
                    'id' => 'dashboard',
                    'nome' => 'Dashboard 33',
                    'icone' => 'fas fa-chart-pie',
                    'submenus' => [
                        ['nome' => 'Visão Geral', 'url' => '/teste-vue'],
                        ['nome' => 'Estatísticas', 'url' => '/analytics']
                    ]
                ],
                [
                    'id' => 'configuracoes',
                    'nome' => 'Configurações',
                    'icone' => 'fas fa-cog',
                    'submenus' => [
                        ['nome' => 'Geral', 'url' => '/config-geral']
                    ]
                ]
            ];
     * 
     * 
     */

    /**
     * Função RECURSIVA: Ela se chama novamente para buscar submenus, 
     * não importa se tem 2, 5 ou 10 níveis de profundidade.
     */
    private function construirArvoreMenu($menu, $idGrupo)
    {
        // Formata os dados do item atual de forma limpa para o Vue
        $item = [
            'id'           => $menu->id,
            'nome'         => $menu->mnidentificacao,
            'numeracao'    => $menu->mnnumeracao != '' ? $menu->mnnumeracao : $menu->id,
            'icone'        => $menu->mnicone != '' ? $menu->mnicone : 'fa fa-square',
            'rota'         => $menu->fkidcal > 0 ? $menu->clrota : '/dashboard',
            'submenus'     => [] // Começa vazio
        ];

        // Busca se existem filhos para este item específico
        $filhos = $this->getMenus($menu->id, $idGrupo);

        if ($filhos && count($filhos) > 0) {
            foreach ($filhos as $filho) {
                if (substr($filho->cppermissao, 0, 2) == '11') {
                    // MÁGICA DA RECURSIVIDADE: A função chama ela mesma para mapear o próximo nível
                    $item['submenus'][] = $this->construirArvoreMenu($filho, $idGrupo);
                }
            }
        }

        return $item;
    }


}
