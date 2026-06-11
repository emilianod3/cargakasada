<?php

namespace App\Http\Controllers\Core;



use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\CfgUserCalController;
use App\Http\Controllers\Core\ClienteController;
use App\Http\Controllers\Core\EmailController;
use App\Http\Controllers\Core\FoneController;
use App\Http\Controllers\Core\GrupoController;
use App\Http\Controllers\Core\MenuController;
use App\Http\Controllers\Core\UnicoController;
use App\Models\Email;
use App\Models\Grupo;
use App\Models\Usuario;
use App\Http\Controllers\Core\Tools;
use App\Http\Controllers\Auxiliar\CategoriaController;
use App\Http\Controllers\Auxiliar\TipoAcaoController;
use App\Http\Controllers\Auxiliar\TipoCadastroController;
use App\Http\Controllers\Auxiliar\TipoCategoriaController;
use App\Http\Controllers\Auxiliar\TipoFoneController;
use App\Http\Controllers\Core\CalController;
use App\Http\Controllers\Core\CidadeController;
use App\Http\Controllers\Core\ColumnCalController;
use App\Http\Controllers\Core\ConfigController;
use App\Http\Controllers\Core\ConfigUserController;
use App\Http\Controllers\Core\EstadoController;
use App\Http\Controllers\Auth\PassRecoveryController;
use App\Mail\recuperaSenha;
use App\Models\PassRecovery;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use SebastianBergmann\CodeCoverage\Report\PHP;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use PHPJasper\PHPJasper;
use stdClass;

class UsuarioController extends Controller
{
    public function perfil()
    {
        $gestor = Tools::getGestor();
        return view('controle.perfilusuario', ['cliente' => $gestor, 'usuariosession' => Session::get('user')]);
    }


    public function inicio()
    {
        $gestor = Tools::getGestor();
        return view('auxiliar.usuario', ['cliente' => $gestor]);
    }



    public static function initQuery($qry = null)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            if(Tools::getGrupoGeral()){
                if($gestor > 0){
                    $qry = Usuario::where('fkidgestor', $gestor);
                }else{
                    //$qry = Administracao::where('id','<',0);
                    return Tools::setResponse('fail', null, 'Impossível Processar');
                }
            }else{
                if($gestor > 0){
                    $qry = Usuario::where('fkidgestor', $gestor);
                }else{
                    return Tools::setResponse('fail', null, 'Impossível Processar');
                }
            }
        }
        return $qry;
    }



    public function getAll()
    {
        $query = self::initQuery();
        //$query->with('unico')->orderBy('unico.unidentificacao', 'asc')->groupBy('id');
        //$query->join('unico')->select(['usuario.*', 'unico.unidentificacao as teste'])->orderBy('teste', 'asc')->groupBy('id');
        //$query->join('unico', 'usuario.fkidunico', '=', 'unico.id')->orderByRaw('unico.unidentificacao', 'asc')->groupBy('id');
        $query->with(['unico' => function($q) {
            $q->orderBy('unico.unidentificacao', 'asc');
        }]);
        $registros = $query->get();
        return $registros;
    }


    /*
    public function index()
    {
        if (Session::has('user')) {
            return view('dashboard');
        } else {
            return self::logoff();
        }
    }

    
    public static function setForget()
    {
        setcookie("rememberuserlogincheck", "", time() - 3600);
    }

    public static function logoff()
    {
        self::setForget();
        //$rememberuserlogincheck = (isset($_COOKIE['rememberuserlogincheck']) ? $_COOKIE['rememberuserlogincheck'] : '');
        //return redirect()->route('acesso')->with('rememberuserlogincheck', $rememberuserlogincheck);
        return redirect()->route('acesso');
    }

    public static function setRemember($request, $user)
    {
        //$remember = $request->remember == 'true' ? 1 : 0;
        if ($request->remember != 'false') {
            $validade = strtotime("+3 weeks"); //validade do cookie
            $domain = ($_SERVER['SERVER_NAME'] != 'localhost') ? $_SERVER['SERVER_NAME'] : false;
            //setcookie("rememberuserlogincheck", $user->ulogin, $validade, '/', $domain, false, true);
            setcookie("rememberuserlogincheck", $user->ulogin, $validade); // cookie utilizado para o sistema relembrar o usuario utilizado do sistema
        } else {
            self::setForget();
        }
    }

    private static function setatividade()
    {
        Cache::forget('ultimaatividade');
        try {
            Cache::put('ultimaatividade', Carbon::now()->addMinutes(ENV('SISTEMA_TEMPO_EXPIRA_SESSAO_MINUTOS')), (ENV('SISTEMA_TEMPO_EXPIRA_SESSAO_MINUTOS') * 60)); //600 = 10 Minutes
        } catch (Exception $e) {
        }
    }

    private static function getatividade()
    {
        $value = Cache::has('ultimaatividade');
        if ($value) {
            $expiraem = Cache::get('ultimaatividade');
            if ($expiraem >= Carbon::now()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function lockscreen()
    {
        if (Session::has('user') && self::getatividade()) {
            return view('auth.lockscreen');
        } else {
            return self::logoff();
        }
    }

    public function logout()
    {
        $rememberuserlogincheck = (isset($_COOKIE['rememberuserlogincheck']) ? $_COOKIE['rememberuserlogincheck'] : '');
        if (Session::has('user')) {
            self::setForget();
        }
        Tools::limpaSessao();
        //return view('auth.login', ['rememberuserlogincheck' => $rememberuserlogincheck]);
        Tools::setAtividade(0, 5, 0, 'Deslogado do Sistema', 'Deslogado do Sistema');
        return redirect()->route('acesso')->with('rememberuserlogincheck', $rememberuserlogincheck);
    }

    /*
    public function autenticar(Request $request)
    {
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
            'username.string' => 'Usuário Inválido',
            'username.min' => 'Usuário Inválido',
            'username.max' => 'Usuário Inválido',
            'password.string' => 'Senha Inválida',
            'password.min' => 'Senha Inválida',
            'password.required' => 'Senha Inválida',
        ]
        );
        if ($validator->fails()) {
            return Tools::setResponse('fail', null, $validator->errors()->first());
        } else {
            //set_time_limit(8000000);
            $credentials = ['username' => $request->username, 'password' => $request->password];
            $user = Usuario::where('ulogin', $credentials['username'])->where('ustatus', 1)->whereIn('fkidgrupo', [1,2,3,6])->with('unico', 'gestor', 'emailprincipal', 'fone')->first();
            $ativo = false;
            $grupo = null;
            if ($user) {
                if (Hash::check($credentials['password'], $user->upassword)) {
                    $grupo = Grupo::where('id', $user->fkidgrupo)->where('gstatus', 1)->with('permissoes')->first();
                    if ($grupo) {
                        $ativo = true;
                        //$config = new ConfigController();
                        //$config->geraDadosConfiguracoes();
                        Session::put('user', $user);
                        Session::put('grupo', $grupo);
                        //Session::put('email', $email);
                        self::setRemember($request, $user);
                        self::setatividade();
                        //$this->carregaSessao();
                        //$tempoBloqueio = Resources::getConfig(12,'valor1');
                    }
                } else {
                    $ativo = false;
                    Tools::setAtividade($user->id, 4, $user->id, 'Login com Senha Incorreta', 'Tentativa de Ingresso no Sistema');
                }
            } else {
                $ativo = false;
                Tools::setAtividade(0, 4, 0, 'Login com Usuário Inativo ou Bloqueado', 'Tentativa de Ingresso no Sistema');
            }

            if ($ativo && $grupo) {
                Session::put('permissoes', $grupo->permissoes);
                //Session::put('avatarperfil', $avatar);
                $menuctrl = new MenuController();
                $menus = $menuctrl->montaMenu($grupo->id);
                Session::put('menus', $menuctrl->montaMenu($grupo->id));
                $configUserCal = new CfgUserCalController();
                Session::put('regporpagina', $configUserCal->loadConfigNumRegPorPagina($user->id));
                /*if($grupo->id == 1){
                    self::loadUsuariosClientes($user);
                }*

                self::loadDadosSessao($user);
                if (strlen($menus) < 400) {
                    Tools::setAtividade($user->id, 9, $user->id, 'Falha na Geração do Menu', 'Erro 500 Menu');
                    return view('ajuda.mensagempublico', ['coderro' => '', 'titulo' => 'Falha no Carregamento de Parâmetros', 'mensagem1' => '', 'mensagem2' => '',]);
                } else {
                    Tools::setAtividade($user->id, 4, $user->id, 'Login bem Sucedido', 'Ingressando no Sistema');
                    return Tools::setResponse('success', null, 'Ingressado no Sistema');
                }
            } else {
                Tools::setAtividade($user->id, 9, 0, 'Login com Grupo ou usuário inativo', 'Não foi possível Ingressar no Sistema, Verifique seu Usuário e Senha');
                return Tools::setResponse('fail', null, 'Não foi possível Ingressar no Sistema, Verifique seu Usuário e Senha');
            }
        }
    }*/

    /*
    private function loadUsuariosClientes($user)
    {
        $usuariosclientes = Usuario::with('unico', 'gestor', 'emailprincipal', 'fone')->get();
        if ($usuariosclientes->count() > 0) {
            Session::put('usuariosclientes', json_encode($usuariosclientes));
            Tools::setAtividade($user->id, 7, $user->id, 'Usuário de Grupo Administrador Geral', 'Listagem de Usuários de Grupo de Administração Geral Superior');
        } else {
            Tools::setAtividade($user->id, 7, $user->id, 'Usuário de Grupo Administrador Geral', 'Falha na Listagem de Usuários de Grupo de Administração Geral Superior');
        }
    }*/

    /*
    private function loadDadosSessao(Usuario $usuario)
    {
        Session::put('tempmens', 7000);
        //$tipodoc = new TipodocController();
        $columsCal = new ColumnCalController();
        $tipofone = new TipoFoneController();
        $tipocadastro = new TipoCadastroController();
        //$siteconteudocategoria = new SiteConteudoCategoriaController();
        //$noticiacategoria = new NoticiaCategoriaController();
        $cal = new CalController();
        $grupos = new GrupoController();
        $menus = new MenusController();

        //$unidade = new UnidadeController();
        $unico = new UnicoController();
        $configUser = new ConfigUserController();
        $config = new ConfigController();
        $cfgUserCal = new CfgUserCalController();
        $estado = new EstadoController();
        //$cidade = new CidadeController();
        $tipocategoria = new TipoCategoriaController();
        $categoria = new CategoriaController();
        $tiposacao = new TipoAcaoController();


        $grupos->getListAll();
        Session::put('colunasCal', $columsCal->loadColunasCals());
        Session::put('tipofone', $tipofone->getAll());
        Session::put('tipocadastro', $tipocadastro->getAll());
        Session::put('unicoslist', $unico->getAllSession());
        Session::put('estados', $estado->getAllSession());
        //Session::put('cidades', $cidade->getAll());
        Session::put('tipocategoria', $tipocategoria->getAll());
        Session::put('categoria', $categoria->getAlljson());
        Session::put('tiposacao', $tiposacao->getAlljson());


        //Session::put('unicosJuridica', $unico->getJuridica());
        Session::put('config', $config->getAllSession());
        Session::put('configuser', $configUser->getAllSession());
        Session::put('cfgusercal', $cfgUserCal->getAllSession($usuario->id));
        Session::put('cals', $cal->getAll());
        Session::put('menus1', $menus->getall());


        //Log::channel('slack')->info('Carregando dados e colocando na sessão');
        Tools::setAtividade($usuario->id, 8, $usuario->id, 'Carregando dados de Sessão de Login', 'Carregando dados e colocando na sessão');
    }*/


    /*

    public function limpaSessao()
    {
        //Session::flush();
        Session::forget('user');
        Session::forget('grupo');
        Session::forget('grupos');
        //Session::forget('unico');
        Session::forget('tempmens');
        Session::forget('menus');
        //Session::forget('cfgcal');
        //Session::forget('permissao');
        //Session::forget('colunas');
        //Session::forget('searchs');
        //Session::forget('browser');
        //Session::forget('page');
        //Session::forget('config');
        //Session::forget('configjson');
        Session::forget('permissoes');
        //Session::forget('minutosParaBloqueio');
        //Session::forget('ematividadeate');
        //Cache::forget('ematividade_');
        //Session::forget('tipodoc');

        Session::forget('colunasCal');
        Session::forget('regporpagina');
        Session::forget('tipofone');
        Session::forget('tipocadastro');
        Session::forget('unicoslist');

        //Session::forget('unidades');
        Session::forget('cidades');
        Session::forget('estados');
        Session::forget('config');
        Session::forget('configuser');
        Session::forget('cfgusercal');
        Session::forget('tipocategoria');
        Session::forget('categoria');
        Session::forget('tiposacao');
        //Session::forget('siteconteudocategoria');
        //Session::forget('noticiacategoria');
        //Session::forget('noticiaclassificacao');
        Session::forget('cals');
        Session::forget('menus1');
        Session::forget('usuariosclientes');
        //Log::channel('slack')->info('Limpando a sessão');
        Tools::setAtividade(0, 8, 0, 'Descarregando dados de Sessão de Usuário Logado', 'Limpando a sessão');
    }*/


    /*
    public function perfilusuario()
    {
        //return redirect()->route('acesso')->with('rememberuserlogincheck', $rememberuserlogincheck);
        return view('auth.perfilusuario');
    }*/
    /*
        grecaptcha': grecaptcha.getResponse(),
        'regcpf': $("#regcpf").val(),
        'regnome': $("#regnome").val(),
        'regemail': $("#regemail").val(),
        'regfone': $("#regfone").val(),
        'regsenha': $("#regsenha").val(),
        'checktermosuso': $("#checktermosuso").prop("checked"),
        'regcnpjcheck': $("#regcnpjcheck").prop("checked"),
        'regcnpj': $("#regcnpjcheck").prop("checked") ? $("#regcnpj").val() : '',

        return view('ajuda.mensagempublico', [
                'coderro' => '',
                'titulo' => 'Registro Realizado com Sucesso',
                'mensagem1' => 'Aguarde a Aceitação de seu Cadastro por nossa Equipe',
                'mensagem2' => 'Você receberá a Confirmação pelo e-mail Informado'
            ]);
        */




    public function registrarusuario(Request $request)
    {
        $validator = Validator::make(
            [
            'regnome' => $request->regnome,
            'regcpf' => $request->regcpf,
            'euemail' => $request->regemail,
            'ulogin' => $request->regemail,
            'regfone' => $request->regfone,
            'regsenha' => $request->regsenha,
            'regsenhaconfirm' => $request->regsenhaconfirm,
            'checktermosuso' => $request->checktermosuso,
        ],
            [
            'regnome' => 'required|string|min:12|max:255',
            'regcpf' => 'required|string|min:12|max:20',
            'euemail' => 'required|unique:email|email',
            'ulogin' => 'required|min:8|unique:usuario|max:255',
            'regfone' => 'required|string|min:14|max:17',
            //'regsenha' => 'required|min:8|regex:/^[a-zA-Z0-9!$@#&*%]+$/', //|confirmed
            'regsenhaconfirm' => 'required|min:5|same:regsenha',
            'checktermosuso' => 'required',
        ],
            [
            'regnome.required' => 'Necessário Informar o Nome',
            'regnome.min' => 'Informe o Nome Completo',
            'regcpf.required' => 'Necessário Informar o CPF',
            'regcpf.min' => 'Informe um CPF Válido',
            'regemail.required' => 'Necessário Informar o E-mail',
            'regemail.email' => 'Necessário Informar um e-mail válido',
            'regemail.unique' => 'Este e-mail não pode ser usado',
            'regemail.min' => 'Necessário Informar um e-mail válido',
            'regfone.required' => 'Necessário Informar o Telefone',
            'regfone.min' => 'Informe um Telefone',
            'regsenha.required' => 'Necessário Informar uma Senha',
            'regsenha.min' => 'A senha não atende aos Requisitos de Complexidade',
            'regsenha.regex' => 'A senha não atende às boas práticas de Segurança',
            'regsenhaconfirm.required' => 'Necessário Confirmar a Senha',
            'regsenhaconfirm.min' => 'Senhas não correspondem',
            'regsenhaconfirm.same' => 'Senhas não correspondem',
            'checktermosuso.required' => 'Aceite os termos de Uso para prosseguir',
            //'regsenha.confirmed' => 'Senhas não correspondem',

        ]
        );
        if ($validator->fails()) {
            Tools::setAtividade(0, 9, 0, 'Falha no Registro de Novo Usuário', $validator->errors()->first());
            return Tools::setResponse('fail', null, $validator->errors()->first());
            //return redirect()->route('registrar')->withInput()->withErrors($validator)->with('message', $validator->errors()->first());
        } else {
            if ($request->checktermosuso == 'true') {
                $gestor = null;
                if ($request->regcnpjcheck == 'true' && strlen($request->regcnpj) > 0) {
                    $clienteCtrl =  new ClienteController();
                    $gestor = $clienteCtrl->getClienteCnpj($request->regcnpj);
                }
                $comcnpj = false;
                if ($request->regcnpjcheck == 'true') {
                    $comcnpj = ($gestor == null ? true : false);
                }

                if ($comcnpj) {
                    return Tools::setResponse('fail', null, 'Falha ao Vincular com o CNPJ Informado');
                } else {
                    $log = 'Nome:'.$request->regnome.' CPF:'.$request->regcpf.' E-mail:'.$request->regemail.' Fone:'.$request->regfone;
                    $unicoCtrl =  new UnicoController();
                    $unico = $unicoCtrl->inserirUnicoPessoaFisica($request->regnome, $request->regcpf);
                    if ($unico == null) {
                        Tools::setAtividade(0, 9, 0, 'Falha ao Cadastrar Unico de Novo Registro de Usuário', $log);
                        return Tools::setResponse('fail', null, 'Falha no Cadastro Principal');
                    } else {
                        $grupoCtrl =  new GrupoController();
                        $grupo = $grupoCtrl->getGrupoVisitantes();
                        $emailCtrl =  new EmailController();
                        $email = $emailCtrl->setEmail($request->regemail, $unico->id);
                        $foneCtrl =  new FoneController();
                        $fone = $foneCtrl->setFone($request->regfone, $unico->id);
                        if ($grupo && $email && $fone) {
                            if (self::insereUser($request->regemail, $request->regsenha, $unico->id, $grupo->id, $gestor)) {
                                return redirect()->route('mensagem', [
                                    'coderro' => '',
                                    'titulo' => 'Registro Realizado com Sucesso',
                                    'mensagem1' => 'Aguarde a Aceitação de seu Cadastro por nossa Equipe',
                                    'mensagem2' => 'Você receberá a Confirmação pelo e-mail Informado'
                                ]);
                                //return Tools::setResponse('success', null, 'Registro Realizado com Sucesso');
                            } else {
                                Tools::setAtividade(0, 9, 0, 'Falha no Registro de Novo Usuário', $log);
                                return Tools::setResponse('fail', null, 'Falha ao Cadastrar, Entre em Contato Conosco.');
                            }
                        } else {
                            Tools::setAtividade(0, 9, 0, 'Falha no Registro de Novo Usuário, e-mail e Fone', $log);
                            return Tools::setResponse('fail', null, 'Falha ao Cadastrar, Entre em Contato Conosco.');
                        }
                    }
                }
            } else {
                Tools::setAtividade(0, 9, 0, 'Falha no Registro de Novo Usuário', 'Aceite os Termos de Uso');
                return Tools::setResponse('fail', null, 'Aceite os Termos de Uso para Continuar');
            }
            //return redirect()->route('acesso');
        }
    }

    public function insereUser($login, $pass, $idUnico, $idGrupo, $gestor)
    {
        if (strlen($login) > 0 && strlen($pass) > 0) {
            if ($this->verificaUserJaExiste($login) == false) {
                $reg = new Usuario();
                $reg->ulogin = $login;
                $reg->upassword = Hash::make($pass);
                $reg->ustatus = 0;
                $reg->uaceitetermosuso = 1;
                $reg->uversao = Carbon::now()->toDateTimeString();
                $reg->udatacadastro = Carbon::now()->toDateTimeString();
                $reg->udataaceitetermosuso = Carbon::now()->toDateTimeString();
                $reg->udatatermosuso = Carbon::now()->toDateTimeString();
                $reg->flaguser = Tools::getUser(0);
                $reg->fkidunico = $idUnico;
                $reg->fkidgrupo = $idGrupo;
                $reg->usolicitalocalizacao = 1;
                $reg->fkidgestor = ($gestor != null ? $gestor->id : 0);
                $reg->flagatualiza = 1;
                $reg->flagdelete = 0;
                $reg->uanotation = 'Criado pelo cadastro de registro';
                $reg->save();
                Tools::setAtividade(0, 1, 0, 'Cadastro de Novo Usuário', 'Criado pelo cadastro de registro realizado com Sucesso, Usuário '.$reg->id.' '.$reg->ulogin, '');
                //Log::channel('slack')->info('Criado pelo cadastro de registro realizado com Sucesso, Usuário '.$reg->id.' '.$reg->ulogin);
                return true;
            } else {
                //Log::channel('slack')->warning('Falha, Tentativa de criação de Usuário com Login já Existente, Usuário '.$login);
                Tools::setAtividade(0, 9, 0, 'Falha, Tentativa de criação de Usuário com Login já Existente, Usuário '.$login, '');
                return false;
            }
        } else {
            //Log::channel('slack')->warning('Falha, Tentativa de criação de Usuário com login vazio');
            Tools::setAtividade(0, 9, 0, 'Falha, Tentativa de criação de Usuário com login vazio', '');
            return false;
        }
    }

    public function tratamentoErroCadastro($unico, $email, $fone, $user)
    {
        if ($unico) {
            $unicoCtrl =  new UnicoController();
            $unicoCtrl->removerId($unico->id);
        }

        if ($email) {
            $emailCtrl =  new EmailController();
            $emailCtrl->removerId($email->id);
        }

        if ($fone) {
            $foneCtrl =  new FoneController();
            $foneCtrl->removerId($fone->id);
        }

        if ($user) {
            $usuarioCtrl =  new UsuarioController();
            $usuarioCtrl->removerId($user->id);
        }
        Tools::setAtividade(0, 8, 0, 'Tratamento de Erro de Cadastro de Novo Usuário', 'tratamentoErroCadastro');

    }

    public function verificaUserJaExiste($login)
    {
        if (strlen($login) > 0) {
            $query = Usuario::where('ulogin', '=', $login)->get();//->where('ustatus', 1);
            if ($query->isEmpty()) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function verificaUserJaExisteComId($login, $id)
    {
        if (strlen($login) > 0) {
            $result = Usuario::where('ulogin', '=', $login)->first();//->where('ustatus', 1);

            if ($result == null) {
                return false;
            } else {
                if ($result->id == $id) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }

    public function removerId($id)
    {
        $reg = Usuario::find($id);
		if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
			return 'false';
		}

        if ($reg->delete()) {
            return 'true';
        } else {
            return 'false';
        }
    }


    public function setUserChange($id)
    {
        $reg = Usuario::with('unico', 'gestor', 'emailprincipal', 'fone')->find($id);

        if ($reg != null) {
            Session::forget('user');
            Session::put('user', $reg);
            return Tools::setResponse('success', null, 'Utilizando Usuário : '.$reg->ulogin);
        } else {
            return Tools::setResponse('fail', null, 'Não é possível Utilizar o Usuário');
        }
    }

    public function enviaSenhaRecuperacaoPorEmail(Request $request)
    {
        $emailCtrl =  new EmailController();
        $passRec =  new PassRecoveryController();
        if (strlen($request->recoveremail) > 0) {
            $email = $emailCtrl->getEmailUnico($request->recoveremail);
            if ($email) {
                $user = Usuario::where('fkidunico', $email->fkidunico)->where('ustatus', 1)->with('unico', 'gestor', 'emailprincipal', 'fone')->first();
                if ($user) {
                    $pass = $passRec->gerarToken($user->id, $email->id);
                    if ($pass) {
                        $result = self::getToken($pass->id);
                        $reset = new stdClass();
                        $reset->url = url('').'/reset/'.$result;
                        Mail::to($email->euemail)->send(new recuperaSenha($user, $email, $user->unico, $reset));
                        Tools::setAtividade(0, 8, 0, 'Recuperação de Senha de Login Bem Sucedido ', $request->recoveremail);
                        return Tools::setResponse('success', null, 'Sucesso no Procedimento');
                        //return redirect()->route('recuperacao')->with('enviado',$reset->url);
                        //return redirect()->route('recuperacao')->with('recoverylink','/reset/'.$result);
                    } else {
                        Tools::setAtividade(0, 9, 0, 'Tentativa de Recuperação de Senha de Login ', $request->recoveremail);
                        return Tools::setResponse('fail', null, 'Verifique o e-mail informado');
                    }
                } else {
                    //return redirect()->route('recuperacao');
                    Tools::setAtividade(0, 9, 0, 'Tentativa de Recuperação de Senha de Login ', $request->recoveremail);
                    return Tools::setResponse('fail', null, 'Verifique o e-mail informado');
                }
            } else {
                return Tools::setResponse('fail', null, 'Verifique o e-mail informado');
                //return redirect()->route('recuperacao');
            }
        } else {
            return Tools::setResponse('fail', null, 'Verifique o e-mail informado');
            //return redirect()->route('mensagem', ['coderro' => '', 'titulo' => 'Falha no Carregamento de Parâmetros', 'mensagem1' => '', 'mensagem2' => '']);
            //return view('ajuda.mensagempublico', ['coderro' => '', 'titulo' => 'Falha no Carregamento de Parâmetros', 'mensagem1' => '', 'mensagem2' => '',]);
        }
    }

    public function getToken($idPassRec)
    {
        return Crypt::encrypt($idPassRec);
    }

    public function checkToken($token)
    {
        if (strlen($token) > 15) {
            $resultado = Crypt::decrypt($token);
            return $resultado;
        } else {
            return null;
        }
    }

    public function gerarToken($idUser, $idEmail)
    {
        $reg = new PassRecovery();
        $reg->unidentificacao = '';
        $reg->fkidusuario = $idUser;
        $reg->fkidemail = $idEmail;
        $reg->prip = '';
        $reg->token = '';
        $reg->prdtrecovery = null;
        $reg->prdtregistro = Carbon::now()->addHour(1)->toDateTimeString();
        $reg->prstatus = 1;
        $reg->prversao = Carbon::now()->toDateTimeString();
        $reg->flagatualiza = 1;
        $reg->flagdelete = 0;
        $reg->save();
        return $reg;
    }

    public function reset($token)
    {
        if (strlen($token) > 30) {
            $idPass = self::checkToken($token);
            if ($idPass) {
                $passRec =  new PassRecoveryController();
                $passRecovery = $passRec->getForId($idPass);
                $validado = $passRec->validaRecovery($passRecovery->id);
                if ($passRecovery != null && $validado != null) {
                    if ($passRecovery) {
                        if ($passRec->setRecovery($token, $passRecovery->id)) {
                            Tools::setAtividade(0, 8, 0, 'Usando token para Recuperação de Senha', 'Token:'.$token);
                            return redirect()->route('recuperacaosenha')->with('recovery', $token);
                        } else {
                            Tools::setAtividade(0, 9, 0, 'Falha: Usando token para Recuperação de Senha', 'Token:'.$token);
                            return redirect()->route('error')->with('message', 'Falha na Recuperação de Senha, Token Inválido');
                        }
                    } else {
                        Tools::setAtividade(0, 9, 0, 'Falha na Recuperação de Senha, Token Inválido', 'Token:'.$token);
                        return redirect()->route('error')->with(['coderro' => '', 'titulo' => 'Falha na Recuperação de Senha, Token Inválido', 'mensagem1' => '', 'mensagem2' => '']);
                    }
                } else {
                    Tools::setAtividade(0, 9, 0, 'Falha na Recuperação de Senha, Token Inválido', 'Token:'.$token);
                    return redirect()->route('error')->with(['coderro' => '', 'titulo' => 'Falha na Recuperação de Senha, Prazo do Token Expirado', 'mensagem1' => '', 'mensagem2' => '']);
                }
            } else {
                Tools::setAtividade(0, 9, 0, 'Falha na Recuperação de Senha, Token Inválido', 'Token:'.$token);
                return redirect()->route('error')->with(['coderro' => '', 'titulo' => 'Falha na Recuperação de Senha, Token Inválido', 'mensagem1' => '', 'mensagem2' => '']);
            }
        } else {
            Tools::setAtividade(0, 9, 0, 'Falha na Recuperação de Senha, Token Inválido', 'Token:'.$token);
            return redirect()->route('error')->with(['coderro' => '', 'titulo' => 'Falha na Recuperação de Senha, Token Inválido', 'mensagem1' => '', 'mensagem2' => '']);
        }
    }


    public function updatesenha(Request $request)
    {
        $validator = Validator::make(
            [
            '_cifra' => $request->_cifra,
            'regsenha' => $request->regsenha,
            'regsenhaconfirm' => $request->regsenhaconfirm,
            'checktermosuso' => $request->checktermosuso,
        ],
            [
            //'regsenha' => 'required|min:8|regex:/^[a-zA-Z0-9!$@#&*%]+$/', //|confirmed
            'regsenhaconfirm' => 'required|min:5|same:regsenha',
            'checktermosuso' => 'required',
        ],
            [
            'regsenha.required' => 'Necessário Informar uma Senha',
            'regsenha.min' => 'A senha não atende aos Requisitos de Complexidade',
            'regsenha.regex' => 'A senha não atende às boas práticas de Segurança',
            'regsenhaconfirm.required' => 'Necessário Confirmar a Senha',
            'regsenhaconfirm.min' => 'Senha nã atende os requsitos de Segurança',
            'regsenhaconfirm.same' => 'Senhas não correspondem',
            'checktermosuso.required' => 'Aceite os termos de Uso para prosseguir',
            //'regsenha.confirmed' => 'Senhas não correspondem',

        ]
        );

        if ($validator->fails()) {
            Tools::setAtividade(0, 9, 0, 'Falha no Reset da Senha', 'Token:'.$request->_cifra);
            return array('tipo' => 'erro', 'titulo' => 'Falha na Recuperação da Senha', 'message' => $validator->errors()->first());
        } else {
            $passRec = new PassRecoveryController();
            $idPass = self::checkToken($request->_cifra);
            if ($idPass > 0 && strlen($request->_cifra) > 30) {
                $pass = $passRec->getForId($idPass);
                if ($pass) {
                    if (self::updateUser($pass->fkidusuario, $request->regsenha)) {
                        //LogSisController::setAtividade((Session::get('user') ? Session::get('user')->id : 0), 7, 0, 'Reset da Senha','Recuperação de Senha Realizada com Sucesso');
                        Tools::setAtividade(0, 8, 0, 'Reset da Senha', 'Recuperação de Senha Realizada com Sucesso'.$request->_cifra);
                        //return redirect()->route('senharecuperada')->with('message', 'Recuperação de Senha Realizada com Sucesso');
                        return array('tipo' => 'ok', 'titulo' => 'Sucesso', 'message' => 'Recuperação de Senha Realizada');
                    } else {
                        //LogSisController::setAtividade((Session::get('user') ? Session::get('user')->id : 0), 7, 0, 'Falha no Reset da Senha', 'Não foi possível recuperar a senha!');
                        Tools::setAtividade(0, 9, 0, 'Falha no Reset da Senha', 'Não foi possível recuperar a senha!'.$request->_cifra);
                        //return view('auth.message')->with('message','Falha na Recuperação de Senha');
                        return array('tipo' => 'erro', 'titulo' => 'Falha na Recuperação da Senha', 'message' => 'Falha na Recuperação da Senha');
                    }

                } else {
                    Tools::setAtividade(0, 9, 0, 'Falha no Reset da Senha', 'Não foi possível recuperar a senha!'.$request->_cifra);
                    //return view('auth.message')->with('message','Falha na Recuperação de Senha');
                    return array('tipo' => 'erro', 'titulo' => 'Falha na Recuperação da Senha', 'message' => 'Falha na Recuperação da Senha');
                }
            } else {
                Tools::setAtividade(0, 9, 0, 'Falha no Reset da Senha', 'Não foi possível recuperar a senha!'.$request->_cifra);
                //return view('auth.message')->with('message','Falha na Recuperação de Senha');
                return array('tipo' => 'erro', 'titulo' => 'Falha na Recuperação da Senha', 'message' => 'Falha na Recuperação da Senha');
            }
        }
    }

    public function updateUser($idUser, $pass)
    {
        if (strlen($pass) > 0 && $idUser > 0) {
            if ($user = Usuario::find($idUser)) {
                $user->upassword = Hash::make($pass);
                $user->uversao = Carbon::now()->toDateTimeString();
                $user->uaceitetermosuso = 1;
                $user->udataaceitetermosuso = Carbon::now()->toDateTimeString();
                $user->flagatualiza = 1;
                $user->flagdelete = 0;
                $user->uanotation = 'Atualizado por recuperação de senha';
                $user->save();
                Tools::setAtividade(0, 8, 0, 'Atualização de Senha de Usuário pela Recuperação de Senha', 'Atualizado por recuperação de senha');
                return $user;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function updatetermouso($idUser, $tipo = 1)
    {
        if ($idUser > 0) {
            if ($user = Usuario::find($idUser)) {
                $user->uversao = Carbon::now()->toDateTimeString();
                $user->uaceitetermosuso = $tipo;
                $user->udataaceitetermosuso = ($tipo > 0 ? Carbon::now()->toDateTimeString() : null);
                $user->flagatualiza = 1;
                $user->flagdelete = 0;
                $user->save();
                if($tipo > 0){
                    Tools::setAtividade(0, 8, 0, 'Aceite dos Termos de Uso', 'Atualização via Sistema');
                }else{
                    Tools::setAtividade(0, 8, 0, 'Declina dos Termos de Uso', 'Atualização via Sistema');
                }
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }    



    public function update(Request $request)
    {
        $gestor = Tools::getGestor();
        if($gestor > 0 || Tools::getGrupoGeral()){
            $validator = Validator::make(
            [
                'idregistro' => $request->idregistro,
            ]
            , [
                'idregistro' => 'required|integer|min:1',
            ],
            [
                'idregistro.required' => 'Dados Inválidos',
                'idregistro.integer' => 'Dados Inválidos',
                'idregistro.min' => 'Dados Inválidos',
            ]);
    
            if($validator->fails()){
                return Tools::setResponse('fail', null, $validator->errors()->first());
            }

            //$request->acao
            $reg = null;
            $qry = Usuario::where('id', $request->idregistro);
            $reg = $qry->first();
            if($request->campo == 'fkidgrupo'){
                $reg->fkidgrupo = $request->value;
            }
            if($request->campo == 'paidentificacao'){
                $reg->fkidgrupo = $request->value;
            }

            if($reg->save()){
                return Tools::msgpadrao(true, 'salvar');
            }else{
                return Tools::msgpadrao(false, 'salvar');
            }
            
        }
    }    



    public function lista(Request $request)
    {
        $gestor = Tools::getGestor();
        if ($gestor > 0 || Tools::getGrupoGeral()) {
            $query = self::initQuery();
            if (strlen($request->campoPesquisa) > 0) {
                $query->where('usuario.ulogin', 'like', '%' . $request->campoPesquisa . '%');
                $query->orWhereHas('unico', function($q) use ($request) {
                    $q->where('unico.unidentificacao', 'like', '%' . $request->campoPesquisa . '%');
                });
            }
            $query->with('unico', 'grupo', 'gestor', 'emailprincipal', 'foneprincipalcelular');
            $query->groupBy('id');
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
    }

    public function get($id)
    {
        //->with('unico', 'grupo', 'gestor', 'emailprincipal', 'foneprincipalcelular')
        $reg = Usuario::with('unico', 'grupo', 'gestor', 'emailprincipal', 'foneprincipalcelular')->find($id);
        return $reg;
    }

    public function salvar(Request $request)
    {
        $gestor = Tools::getGestor();
        $validator = Validator::make(
            [
            'login' => $request->login,
            'grupo' => $request->grupo,
            'unico' => $request->unico,
        ],
            [
            'login' => 'required|string|min:8|max:120',
            'grupo' => 'required|integer|min:1',
            'unico' => 'required|integer|min:1',
        ],
            [
            'login.required' => 'Necessário Informar o Login',
            'login.min' => 'Informe um Login Válido',
            'login.string' => 'Informe um Login Válido',
            'login.max' => 'Informe um Login Válido',
            'grupo.required' => 'Necessário Informar o Grupo Pertencente',
            'grupo.min' => 'Necessário Informar o Grupo Pertencente',
            'grupo.integer' => 'Necessário Informar o Grupo Pertencente',
            'unico.required' => 'Necessário Informar a Pessoa',
            'unico.min' => 'Necessário Informar a Pessoa',
            'unico.integer' => 'Necessário Informar a Pessoa',
        ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'data' => [],
                'message' => $validator->errors()->first()
            ]);
        } else {
            if ($this->verificaUserJaExisteComId($request->login, $request->id) == true) {
                return response()->json([
                    'status' => 'fail',
                    'data' => [],
                    'message' => 'Usuário já está em Uso'
                ]);
            }


            if ($gestor == null) {
                return response()->json([
                    'status' => 'fail',
                    'data' => [],
                    'message' => 'Impossível Prosseguir, faça login com um Gestor para Continuar'
                ]);
            }

            if (strlen($request->regsenha) > 0) {
                $validator1 = Validator::make(
                    [
                        'regsenha' => $request->regsenha,
                        'regsenhaconfirm' => $request->regsenhaconfirm,
                    ],
                    [
                        'regsenha' => 'required|min:8|regex:/^[a-zA-Z0-9!$@#&*%]+$/', //|confirmed
                        'regsenhaconfirm' => 'required|min:5|same:regsenha',
                    ],
                    [
                        'regsenha.required' => 'Necessário Informar uma Senha',
                        'regsenha.min' => 'A senha não atende aos Requisitos de Complexidade',
                        'regsenha.regex' => 'A senha não atende às boas práticas de Segurança',
                        'regsenhaconfirm.required' => 'Necessário Confirmar a Senha',
                        'regsenhaconfirm.min' => 'Senhas não correspondem',
                        'regsenhaconfirm.same' => 'Senhas não correspondem',
                    ]
                );

                if ($validator1->fails()) {
                    return response()->json([
                        'status' => 'fail',
                        'data' => [],
                        'message' => $validator1->errors()->first()
                    ]);
                }
            }

            if ($request->id > 0) {
                $reg = Usuario::find($request->get('id'));
                if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                    return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
                }
            } else {
                $reg = new Usuario();
                $reg->udatacadastro = Carbon::now()->toDateTimeString();
            }

            if (strlen($request->regsenha) > 0) {
                $reg->upassword = Hash::make($request->regsenha);
            }

            $reg->ulogin = $request->login;
            $reg->ustatus = $request->fkstatus;
            //$reg->uaceitetermosuso = 1;
            $reg->uversao = Carbon::now()->toDateTimeString();
            //$reg->udataaceitetermosuso = Carbon::now()->toDateTimeString();
            //$reg->udatatermosuso = Carbon::now()->toDateTimeString();
            $reg->flaguser = Tools::getUser(0);
            $reg->fkidunico = $request->unico;
            $reg->fkidgrupo = $request->grupo;
            //$reg->usolicitalocalizacao = 1;
            $reg->fkidgestor = ($gestor != null ? $gestor : 0);
            $reg->flagatualiza = 1;
            $reg->flagdelete = 0;
            $reg->uanotation = 'Criado pelo Cadastro de Usuário';

            if ($reg->save()) {
                if ($request->id > 0) {
                    Tools::setAtividade(0, 1, 0, 'Alteração de Usuário', 'Alterado pelo Cadastro de Usuário realizado com Sucesso, Usuário '.$reg->id.' '.$reg->ulogin, '');
                } else {
                    Tools::setAtividade(0, 1, 0, 'Cadastro de Novo Usuário', 'Criado pelo Cadastro de Usuário realizado com Sucesso, Usuário '.$reg->id.' '.$reg->ulogin, '');
                }
                return response()->json([
                'status' => 'success',
                'data' => $reg,
                'message' => 'Registro Realizado com Sucesso'
                ]);
            } else {
                Tools::setAtividade(0, 9, 0, 'Tentativa Registro de Registro', '');
                return response()->json([
                    'status' => 'fail',
                    'data' => [],
                    'message' => 'Falha no Salvamento de Registro'
                ]);
            }

        }
    }

    public function remover($id)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR'); /*Desativa ou remove do banco */
        if ($sistemadesativar > 0) {
            $reg = Usuario::find($id);
            if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
            }            
            Tools::setAtividade(Tools::getUser(), 3, $id, 'Desativar o Registro', 'Registro '.$id);
            $reg->ustatus = 0;
            return Tools::msgpadrao($reg->save(), 'desativar');
        } else {
            $reg = Usuario::find($id);
            if ($reg->exists && $reg->flagcontrole == 1 && Session::get('user')->grupo->id != 1) {
                return Tools::setResponse('fail', null, 'Registro Bloqueado pelo Sistema, para continuar entre em contato com o Suporte');
            }            
            Tools::setAtividade(Tools::getUser(), 3, $id, 'Remover o Registro', 'Registro '.$id);
            $reg->ustatus = 0;
            return Tools::msgpadrao($reg->save(), 'desativar');
            //return Tools::msgpadrao($reg->delete(), 'delete');
        }
    }

    public function removerLote(Request $request)
    {
        $sistemadesativar = env('SISTEMA_DESATIVAR');
        if ($sistemadesativar > 0) {
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = Usuario::whereIn('id', $ids);
            $qtd = $regs->count();
            Tools::setAtividade(Tools::getUser(), 3, 0, 'Desativar Registros em Lote', 'Registros '.implode(" ", $ids));
            //$regs->cstatus = 0;
            return Tools::msgpadrao($regs->save(), 'desativar', $qtd);
        } else {
            $ids = explode(',', $request->ids);
            //$update = DB::table('findespesa')->whereIn('id', $valores)->update(array('fddatapagamento' => Carbon::now()->toDateTimeString()));
            $regs = Usuario::whereIn('id', $ids);
            $qtd = $regs->count();
            Tools::setAtividade(Tools::getUser(), 3, 0, 'Remover Registros em Lote', 'Registros '.implode(" ", $ids));
            return Tools::msgpadrao($regs->delete(), 'delete', $qtd);
        }
    }

    /*
    public function updateUserCfd($id, $pass)
    {
        if (strlen($pass) > 0 && $id > 0) {
            if ($reg = CfdDados::find($id)) {
                $reg->cfddpassword=Hash::make($pass);
                $reg->cfddversao=Carbon::now()->toDateTimeString();
                $reg->cfddultimoacessoauxiliar = "Atualizado por recuperação de senha";
                // $reg->flaguser = Session::get('user')->id;
                $reg->flagatualiza = 1;
                $reg->flagdelete=0;
                $reg->save();
                //Log::channel('slack')->info('CFD Atualizado por recuperação de senha '.$id);
                Tools::setAtividade(0, 2, 0,  'Tratamento de Erro de Cadastro de Novo Usuário', 'tratamentoErroCadastro');
                return $reg;
            } else {
                //Log::channel('slack')->warning('Falha, Tentativa de recuperação de Senha não Encontrado '.$id);
                return false;
            }
        } else {
            //Log::channel('slack')->warning('Falha, Tentativa de recuperação de Senha '.$id);
            return false;
        }
    }*/

}
