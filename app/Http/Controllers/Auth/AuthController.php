<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\ConfigController;
use App\Http\Controllers\Core\MenuController;
use App\Http\Controllers\Core\Tools;
use App\Models\Grupo;
use App\Models\Usuario;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return 
     */
    public function index()
    {
        /*if(Session::has('user')) {
            $rememberuserlogincheck = Cache::get('rememberuserlogincheck', '');
            return view('login')->with('rememberuserlogincheck', $rememberuserlogincheck);
        } else {
            return self::logoff();
        }*/
        /*return Inertia::render('Auth/Login', [
            'rememberuserlogincheck' => Cookie::has('rememberusercookie') ? Cookie::get('rememberusercookie') : ''
        ]);*/
        $cookieValor = request()->cookie('rememberusercookie') ?? '';

        return Inertia::render('Auth/Login', [
            'rememberuserlogincheck' => $cookieValor
        ]);
    }

    public function telaesquecisenha()
    {
        $cookieValor = request()->cookie('rememberusercookie') ?? '';

        return Inertia::render('Auth/RecuperacaoSenha', [
            'rememberuserlogincheck' => $cookieValor
        ]);
    }    

    /*
    public static function logoff()
    {
        self::setForget();
        return redirect()->route('acesso');
    }

    public static function setForget()
    {
        //setcookie("rememberuserlogincheck", "", time() - 3600);
        // Remover o cookie "rememberuserlogincheck"
        //Cookie::forget('rememberuserlogincheck');
        //Cache::forget('rememberuserlogincheck');
    }*/

    public function autenticar(Request $request)
    {
        // 1. Sua validação original (perfeita)
        $validator = Validator::make(
            [
                'username' => $request->username,
                'password' => $request->password,
            ],
            [
                'username' => 'required|string|min:4|max:255',
                'password' => 'required|string|min:6',
            ],
            [
                'username.required' => 'Usuário Inválido',
                'username.string'   => 'Usuário Inválido',
                'username.min'      => 'Usuário Inválido',
                'username.max'      => 'Usuário Inválido',
                'password.string'   => 'Senha Inválida',
                'password.min'      => 'Senha Inválida',
                'password.required' => 'Senha Inválida',
            ]
        );

        // 🚨 ADAPTAÇÃO 1: Se falhar a validação de caracteres, joga o erro para o useForm do Vue capturar no 'onError'
        if ($validator->fails()) {
            throw ValidationException::withMessages([
                'message' => $validator->errors()->first()
            ]);
        }

        $credentials = ['username' => $request->username, 'password' => $request->password];
        
        // Sua busca original de Usuário
        $user = Usuario::where('ulogin', $credentials['username'])
            ->where('ustatus', 1)
            ->whereNotIn('fkidgrupo', [5])
            ->with('unico', 'gestor', 'emailprincipal', 'fone')
            ->first();

        $ativo = false;
        $grupo = null;

        if ($user) {
            if (Hash::check($credentials['password'], $user->upassword)) {
                $grupo = Grupo::where('id', $user->fkidgrupo)->where('gstatus', 1)->with('permissoes')->first();
                if ($grupo) {
                    $ativo = true;
                    // 🔥 Se você mudar para o Auth nativo do Laravel depois, usaria Auth::login($user);
                    // Por enquanto, mantendo suas Sessions manuais que você já usa:
                    Session::put('user', $user);
                    Session::put('grupo', $grupo);
                    //self::setRemember($request, $user);
                }
            } else {
                $ativo = false;
                Tools::setAtividade($user->id, 4, $user->id, 'Login com Senha Incorreta', 'Tentativa de Ingresso no Sistema com Usuário:'.$request->username.' Senha:'.$request->password);
            }
        } else {
            $ativo = false;
            Tools::setAtividade(0, 4, 0, 'Login com Usuário Inativo ou Bloqueado', 'Tentativa de Ingresso no Sistema com Usuário:'.$request->username.' Senha:'.$request->password);
        }

        // 2. Validação do Sucesso ou Falha do login após checar banco
        if ($ativo && $grupo) {
            //Auth::login($user, $request->remember);
            /*
            if ($request->remember) {
                // Cria um cookie seguro chamado 'rememberusercookie' guardando o username por 30 dias (43200 minutos)
                cookie()->queue('rememberusercookie', $request->username, 21600);
            } else {
                // Se o usuário desmarcou a caixinha, removemos o cookie antigo do navegador dele
                cookie()->queue(cookie()->forget('rememberusercookie'));
            }*/
            if ($request->remember) {
                // Nome, Valor, Minutos (30 dias), Path, Domínio, Secure, HttpOnly (false para o Vue conseguir ler!)
                cookie()->queue('rememberusercookie', $request->username, 21600, '/', null, false, false);
            } else {
                // Se desmarcou, limpa o cookie do navegador
                cookie()->queue(cookie()->forget('rememberusercookie', '/'));
            }            
            Session::put('permissoes', $grupo->permissoes);

            
            $menuctrl = new MenuController();
            $menus = $menuctrl->montaMenu2($grupo->id);
            Session::put('menus', $menus);
            /*
            $configUserCal = new CfgUserCalController();
            Session::put('regporpagina', $configUserCal->loadConfigNumRegPorPagina($user->id));
            */

        $menunn = '<div>'.
            '<button @click="alternarSubmenu(\'dashboard\')" v-tooltip.right="!sidebarAberta ? \'Dashboard\' : null" class="w-full flex items-center justify-between px-1 py-2 rounded-lg hover:bg-texto-claro/10 text-texto-claro transition-all group text-sm cursor-pointer">'.
                '<div class="flex items-center gap-1">'.
                    '<span class="w-6 text-center text-texto-claro/40 group-hover:text-primary text-base transition-colors">'.
                        '<i class="fas fa-chart-pie"></i>'.
                    '</span>'.
                    '<span class="font-medium whitespace-nowrap transition-all" :class="sidebarAberta ? \'opacity-100 translate-x-0\' : \'opacity-0 -translate-x-4 pointer-events-none\'">Dashboard33</span>'.
                '</div>'.
                '<i v-if="sidebarAberta" class="fas fa-chevron-right text-[10px] text-texto-claro/30 transition-transform duration-200" :class="submenusAbertos.dashboard ? \'rotate-90 text-primary\' : \'\'"></i>'.
            '</button>'.

            '<div class="overflow-hidden transition-all duration-300 ease-in-out pl-5 flex flex-col gap-1" :class="submenusAbertos.dashboard && sidebarAberta ? \'max-h-40 mt-1 pb-1\' : \'max-h-0\'">'.
                '<Link href="/teste-vue" class="text-xs text-texto-claro/90 hover:text-primary py-1.5 transition-colors block"><i class="fas fa-circle text-[6px] mr-2 opacity-40"></i> Visão Geral</Link>'.
                '<Link href="/analytics" class="text-xs text-texto-claro/90 hover:text-primary py-1.5 transition-colors block"><i class="fas fa-circle text-[6px] mr-2 opacity-40"></i> Estatísticas</Link>'.
            '</div>'.
        '</div>'.

        '<div>'.
            '<button @click="alternarSubmenu(\'dashboard1\')" v-tooltip.right="!sidebarAberta ? \'Dashboard 1\' : null" class="w-full flex items-center justify-between px-1 py-2 rounded-lg hover:bg-texto-claro/10 text-texto-claro transition-all group text-sm cursor-pointer">'.
                '<div class="flex items-center gap-1">'.
                    '<span class="w-6 text-center text-texto-claro/40 group-hover:text-primary text-base transition-colors">'.
                        '<i class="fas fa-chart-pie"></i>'.
                    '</span>'.
                    '<span class="font-medium whitespace-nowrap transition-all" :class="sidebarAberta ? \'opacity-100 translate-x-0\' : \'opacity-0 -translate-x-4 pointer-events-none\'">Dashboard 222</span>'.
                '</div>'.
                '<i v-if="sidebarAberta" class="fas fa-chevron-right text-[10px] text-texto-claro/30 transition-transform duration-200" :class="submenusAbertos.dashboard1 ? \'rotate-90 text-primary\' : \'\'"></i>'.
            '</button>'.

            '<div class="overflow-hidden transition-all duration-300 ease-in-out pl-5 flex flex-col gap-1" :class="submenusAbertos.dashboard1 && sidebarAberta ? \'max-h-40 mt-1 pb-1\' : \'max-h-0\'">'.
                '<Link href="/teste-vue" class="text-xs text-texto-claro/90 hover:text-primary py-1.5 transition-colors block"><i class="fas fa-circle text-[6px] mr-2 opacity-40"></i> Visão Geral</Link>'.
                '<Link href="/analytics" class="text-xs text-texto-claro/90 hover:text-primary py-1.5 transition-colors block"><i class="fas fa-circle text-[6px] mr-2 opacity-40"></i> Estatísticas</Link>'.
            '</div>'.
        '</div>';



            $menusPermitidos = [
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

            //Session::put('menus1', $menusPermitidos);

            self::loadDadosSessao($user);

            /*
            if (strlen($menus) < 400) {
                Tools::setAtividade($user->id, 9, $user->id, 'Falha na Geração do Menu', 'Erro 500 Menu');
                
                // 🚨 ADAPTAÇÃO 2: Em vez de return view(), o Inertia exige renderizar um componente Vue de erro
                return Inertia::render('Errors/MenuError', [
                    'titulo' => 'Falha no Carregamento de Parâmetros'
                ]);
            } else {
                //self::setAcesso($user);
                
                // 🚨 ADAPTAÇÃO 3: LOGIN BEM-SUCEDIDO! 
                // O Inertia EXIGE um redirecionamento para o painel. O Vue vai entender e trocar a tela.
                return redirect()->route('dashboard');
            }
                */
            return redirect()->route('dashboard');
        } else {
            Tools::setAtividade($user ? $user->id : 0, 9, 0, 'Login com Grupo ou usuário inativo', 'Não foi possível Ingressar no Sistema com Usuário:'.$request->username.' Senha:'.$request->password);
            
            // 🚨 ADAPTAÇÃO 4: Se o usuário/senha estiverem errados no banco, devolve o erro pro 'onError' do Vue
            throw ValidationException::withMessages([
                'message' => 'Não foi possível Ingressar no Sistema, Verifique seu Usuário e Senha'
            ]);
        }
    }


    //public function logout(Request $request)
    public function logout()
    {
        Tools::limpaSessao();
        //$rememberuserlogincheck = Cache::get('rememberuserlogincheck', '');
        $rememberuserlogincheck = Cookie::has('rememberusercookie') ? Cookie::get('rememberusercookie') : '';
        Tools::setAtividade(0, 5, 0, 'Deslogando do Sistema', 'Deslogando do Sistema');
        return redirect()->route('login')->with('rememberuserlogincheck', $rememberuserlogincheck);
    }

    public function lockscreen()
    {
        $userid = Session::has('user') ? Session::get('user')->id : 0;
        $userlogin = Session::has('user') ? Session::get('user')->ulogin : '';
        $nomeusuario = Session::has('user') ? Session::get('user')->unico->unidentificacao : '';
        $usuario = Session::has('user') ? Session::get('user') : null;
        Tools::limpaSessao();
        $rememberuserlogincheck = Cookie::has('rememberusercookie') ? Cookie::get('rememberusercookie') : '';
        Tools::setAtividade($userid, 5, 0, 'Bloqueando por Inatividade', 'Sessão Expirou por Inatividade');
        try{
            if($userid > 0)
            {
                return Inertia::render('Auth/Lockscreen', [
                    'userlogado' => $userlogin,
                    'nomeusuario' => $nomeusuario,
                    'usuario' => $usuario,
                ]);
            }else{
                return redirect()->route('login')->with('rememberuserlogincheck', $rememberuserlogincheck);
            }
        } catch (Exception $e) {
            return redirect()->route('login')->with('rememberuserlogincheck', $rememberuserlogincheck);
        }
    }    


    private function loadDadosSessao(Usuario $usuario)
    {
        try{
            Session::put('tempmens', 7000);
            //Session::put('user', $user);
            //Session::put('grupo', $grupo);                    
            //Session::put('permissoes', $permissoes);

            //$tipodoc = new TipodocController();
            //$columsCal = new ColumnCalController();
            //$menus = new MenusController();
            //$tipofone = new TipoFoneController();
            //$tipocadastro = new TipoCadastroController();
            //$siteconteudocategoria = new SiteConteudoCategoriaController();
            //$noticiacategoria = new NoticiaCategoriaController();
            //$cal = new CalController();

            //$unidade = new UnidadeController();
            //$unico = new UnicoController();
            //$configUser = new ConfigUserController();
            $config = new ConfigController();
            //$cfgsist = new CfgSistController();
            //$cfgUserCal = new CfgUserCalController();
            //$estado = new EstadoController();
            //$cidade = new CidadeController();
            //$tiposacao = new TipoAcaoController();
            //$tipocategoria = new TipoCategoriaController();
            //$categoria = new CategoriaController();
            //Session::put('colunasCal', $columsCal->loadColunasCals());
            //Session::put('tipofone', $tipofone->getAll());
            //Session::put('tipocadastro', $tipocadastro->getAll());
            //Session::put('unicoslist', $unico->getAllSession());
            //Session::put('estados', $estado->getAllSession());
            //Session::put('cidadesqtd', $cidade->getcount());
            //Session::put('menus1', $menus->getall());
            //Session::put('cidades', $cidade->getAll());
            //Session::put('tipocategoria', $tipocategoria->getAll());
            //Session::put('categoria', $categoria->getAlljson());
            //Session::put('tiposacao', $tiposacao->getAlljson());
            //Session::put('unicosJuridica', $unico->getJuridica());
            //Session::put('config', $config->getAllSession());
            //Session::put('cfgsist', $cfgsist->getAllSession());
            //Session::put('configuser', $configUser->getAllSession());
            //Session::put('cfgusercal', $cfgUserCal->getAllSession($usuario->id));
            //Session::put('cals', $cal->getAll());

            // 🚀 Criamos uma chave única: 'configuracoes_sistema_usuario_12'
            $cacheKeyconfig = 'config_' . $usuario->id;
            // O Laravel guarda isso. Colocamos um tempo longo (ex: 1 dia), 
            Cache::remember($cacheKeyconfig, 86400, function () use ($config) {
                return $config->getAllSession(); 
            });
            // Guardamos o nome da chave na sessão para o logout saber quem apagar
            //Session::put('config_', $cacheKey);
            //Log::channel('slack')->info('Carregando dados e colocando na sessão');
            Tools::setAtividade($usuario->id, 8, $usuario->id, 'Carregando dados de Sessão de Login', 'Carregando dados e colocando na sessão '.' Usuário:'.$usuario->id.'-'.$usuario->ulogin);
        } catch (Exception $e) {
            Tools::setAtividade($usuario->id, 8, $usuario->id, 'Falha no Carregamento de dados de Sessão de Login', 'Falha :'.$e->getMessage().' Usuário:'.$usuario->id.'-'.$usuario->ulogin);
        }
    } 
    
}