<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Auxiliares\MYPDF;
use App\Http\Controllers\Constants;
use App\Models\Legislatura;
use App\Models\LogAtividade;
use DateTime;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Database\Schema\Blueprint;
use NumberFormatter;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Crypt;
use PhpOffice\PhpWord\PhpWord;
use ZipArchive;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QROutputInterface;
use PhpOffice\PhpWord\Style\Section;
use Stevebauman\Location\Facades\Location;

class Tools{

    /**
     * @param integer $idUsuario  A Qual usuario a ação está vinculada, para validar comparar com o usuario da session se for diferente pode ser um problema
     * @param [type] $tipoAcao   7 = Indefinida 1=incluir 2-alterar 3-excluir 4-logar 5-deslogar 6-validar 8-interacao sistema - 9-falha Ver tabela TipoAcao
     * @param integer $idRegistro
     * @param [type] $origemnosistema
     * @param [type] $obs
     * @return void
     */
    public static function setAtividade($idUsuario = 0, $tipoAcao = 7, $idRegistro = 0, $origemnosistema = '', $obs = '')
    {
        if($idUsuario <= 0){
            $idUsuario = self::getUser(0);
        }

        $config = self::getConfig(1, 'status');
        $ip = '';
        $userAgent = '';
        if($config > 0 || $config == null) {
            try{
                $ip = request()->ip();
                $userAgent = request()->userAgent();
            } catch (Exception $e) {
                $except = $e->getMessage();
                if(!self::temTexto($ip)){
                    $ip = '0.0.0.0';
                }
                if(!self::temTexto($userAgent)){
                    $userAgent = 'Falha na identificação '.$except;
                }
            }
            $reg = new LogAtividade();
            $reg->fkidusuario = self::getUser($idUsuario);
            $reg->fktipoacao = $tipoAcao;
            $reg->idregistro = $idRegistro;
            $reg->lip = $ip;
            $reg->lagent = $userAgent;
            $reg->llocal = self::getDadosGeograficos($ip);
            $reg->lgonde = self::temTexto($origemnosistema) ? $origemnosistema : 'Não Informado';
            $reg->lgtexto = $obs;
            self::temTexto($obs) ? $obs : 'Não Informado';
            $reg->lgversao = Carbon::now()->toDateTimeString();
            $reg->flagatualiza = 1;
            $reg->flagdelete = 0;
            $reg->flaguser = self::getUser(0);
            if($reg->save()) {
                return true;
            } else {
                return false;
            }
        }
    }


    public static function getDadosGeograficos($ip = '')
    {
        try{
            if ($dadosGeo = Location::get($ip)) {
                $cidade = $dadosGeo->cityName;     // Nome da cidade
                $estado = $dadosGeo->regionName;   // Nome do estado / província
                $pais   = $dadosGeo->countryName;  // Nome do país
                
                $textoFinalLog = "Origem: {$pais} {$cidade}/{$estado} - Coordenadas: {$dadosGeo->latitude},{$dadosGeo->longitude}";
            } else {
                $textoFinalLog = "Localização não identificada para o IP {$ip}";
            }
            return $textoFinalLog;
        } catch (Exception $e) {
            return "Localização não identificada para o IP {$ip} - {$e}";
        }
    }


    public static function getUser($idUser = 0)
    {
        if($idUser > 0) {
            return $idUser;
        } else {
            return Session::has('user') ? Session::get('user')->id : 0;
            /*if(Session::has('user')){
                return Session::get('user')->id;
            }else{
                return 0;
            }*/

        }
    }



    public static function getGrupoGeral($idUser = 0)
    {
        if($idUser > 0 && Session::get('user')->fkidgrupo == 1) {
            return true;
        } else {
            $idgrupogeral = Session::has('user') ? Session::get('user')->fkidgrupo : 0;
            return $idgrupogeral == 1 ? true : false;
        }
    }

    public static function getGestor($idGestor = 0)
    {
        if($idGestor > 0) {
            return $idGestor;
        } else {
            return Session::has('user') ? Session::get('user')->fkidgestor : 0;
            /*if(Session::has('user')){
                return Session::get('user')->id;
            }else{
                return 0;
            }*/
        }
    }


    public static function validaGestor()
    {
        $idGestor = Session::has('user') ? Session::get('user')->fkidgestor : 0;
        return $idGestor;
    }

    public static function formatMoeda($valor = 0)
    {
        $formatter = new NumberFormatter('pt_BR', $valor);
        return $formatter;
    }

   

    /**
     * value true or false
     * tipo delete  alterar  salvar fail
     */
    public static function msgpadrao($value = false, $tipo = 'salvar', $qtdregs = 1)
    {
        if($tipo == 'delete') {
            if($value) {
                return response()->json([
                    'status' => 'success',
                    'data' => [],
                    'message' => ($qtdregs > 1 ? 'Registros Deletados com Sucesso' : 'Registro Deletado com Sucesso')
                ]);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'data' => [],
                    'message' => ($qtdregs > 1 ? 'Impossível executar nos Registros Selecionados' : 'Impossível executar no Registro Selecionado')
                ]);
            }
        } elseif($tipo == 'desativar') {
            if($value) {
                return response()->json([
                    'status' => 'success',
                    'data' => [],
                    'message' => ($qtdregs > 1 ? 'Registros Desativado com Sucesso' : 'Registro Desativado com Sucesso')
                ]);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'data' => [],
                    'message' => ($qtdregs > 1 ? 'Impossível executar nos Registros Selecionados' : 'Impossível executar no Registro Selecionado')
                ]);
            }
        } elseif($tipo == 'salvar') {
            if($value) {
                return response()->json([
                    'status' => 'success',
                    'data' => [],
                    'message' => ($qtdregs > 1 ? 'Registros Salvos com Sucesso' : 'Registro Salvo com Sucesso')
                ]);
            } else {
                return response()->json([
                    'status' => 'fail',
                    'data' => [],
                    'message' =>   ($qtdregs > 1 ? 'Falha nos Salvamentos dos Registros' : 'Falha no Salvamento do Registro')
                ]);
            }
        } elseif($tipo = 'fail') {
            return response()->json([
                'status' => 'fail',
                'data' => [],
                'message' =>  ($qtdregs > 1 ? 'Falha nos Execução das Tarefas' : 'Falha na Execução da Tarefa')
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'data' => [],
                'message' =>  ($qtdregs > 1 ? 'Falha nos Execução das Tarefas, Entre em contato com o Administrador' : 'Falha na Execução da Tarefa, Entre em contato com o Administrador')
            ]);
        }

    }

    public static function temTexto($value)
    {
        if(strlen($value) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function ifnull($value)
    {
        if($value == 0 || $value == null || $value == "null") {
            return null;
        } else {
            return $value;
        }
    }

    public static function setClassWith($query, $relation, $status = false)
    {
        if(!$status) {
            return $query->with($relation);
        } else {
            return $query;
        }
    }

    public static function getCor(): String
    {
        $items = array("red", "blue", "green", "yellow", "white", "aqua");
        return $items[array_rand($items)];
    }

    public static function limpaSessao()
    {
        try{
            $iduser = Session::has('user') ? Session::get('user')->id : 0;
            $userlogin = Session::has('user') ? Session::get('user')->ulogin : '';
            //Session::flush();
            Session::forget('user');
            Session::forget('grupo');
            Session::forget('permissoes');
            //Session::forget('unico');
            Session::forget('tempmens');
            //Session::forget('menus');
            Session::forget('menus');
            //Session::forget('cfgcal');
            //Session::forget('permissao');
            //Session::forget('colunas');
            //Session::forget('searchs');
            //Session::forget('browser');
            //Session::forget('page');
            //Session::forget('config');
            //Session::forget('configjson');        
            //Session::forget('minutosParaBloqueio');
            //Session::forget('ematividadeate');
            //Cache::forget('ematividade_');
            //Session::forget('tiposacao');
            //Session::forget('tiposituacao');
            //Session::forget('colunasCal');
            //Session::forget('regporpagina');
            //Session::forget('tipocadastro');
            //Session::forget('unicoslist');
            //Session::forget('tiposparentescos');
            //Session::forget('tipocategoria');
            //Session::forget('categoria');
            //Session::forget('origem');
            //Session::forget('cidades');
            //Session::forget('cidadesqtd');
            //Session::forget('estados');
            //Session::forget('config');
            //Session::forget('configuser');
            //Session::forget('cfgsist');
            //Session::forget('cfgusercal');
            //Session::forget('categoria');
            //Session::forget('siteconteudocategoria');
            //Session::forget('noticiacategoria');
            //Session::forget('noticiaclassificacao');
            //Session::forget('cals');
            //Session::forget('usuariosclientes');
            //Log::channel('slack')->info('Limpando a sessão');
            //Session::forget('tipofone');
            //Session::forget('ramoatividade');
            //Session::forget('tratamentos');
            //Session::forget('estadocivil');
            //Session::forget('tiporaca');
            //Session::forget('unidades');
            //Session::forget('divisoes');
            $cacheKeyconfig = 'config_' . $iduser;
            if ($cacheKeyconfig) {
                Cache::forget($cacheKeyconfig);
            }

            // 3. Limpa o resto da sessão com os comandos nativos
            Session::invalidate();
            Session::regenerateToken();  
        
            if($iduser > 0){
                Tools::setAtividade($iduser, 8, $iduser, 'Descarregando dados de Sessão de Usuário Logado', 'Limpando a sessão Usuário: '.$iduser.'-'.$userlogin);
            }else{
                Tools::setAtividade($iduser, 8, $iduser, 'Dados de Sessão Inexistentes', '');
            }
        } catch (Exception $e) {
            Tools::setAtividade(0, 8, 0, 'Falha na Limpeza de Dados de Sessão', 'Falha :'.$e->getMessage());
        }
    }

    public static function getDay($date)
    {
        //$date = DateTime::createFromFormat("Y-m-d", $date);
        $date = DateTime::createFromFormat("Y-n-j", $date);
        return $date->format("j");
    }

    public static function getMonth($date)
    {
        $date = DateTime::createFromFormat("Y-m-d", $date);
        return $date->format("n");
    }

    public static function getYear($date)
    {
        $date = DateTime::createFromFormat("Y-m-d", $date);
        return $date->format("Y");
    }

    public static function calcularDiasEntreDatas($dataInicio, $dataFim) {
        // Cria objetos DateTime a partir das strings de data
        $data1 = new DateTime($dataInicio);
        $data2 = new DateTime($dataFim);

        // Usa o método diff() para obter a diferença
        $intervalo = $data1->diff($data2);

        // Retorna a propriedade 'days' do objeto DateInterval
        return $intervalo->days;
    }


    public static function deleteFile($file)
    {
        $path = env('PATH_PUBLIC_ADMIN');

        if(!is_dir($path.$file) && file_exists($path.$file)) {
            //File::Delete($path.$file);
            unlink($path.$file);
            return true;
        } else {
            return false;
        }

    }

    public static function convertTimeToSecond(string $time): int
    {
        $d = explode(':', $time);
        return ($d[0] * 3600) + ($d[1] * 60) + $d[2];
    }



    public static function getDateTimeForSQL($date)
    {
        $time = date("i:s", strtotime($date));
        //substr($date,0,2)."-".substr($date,3,2)."-".substr($date,6,4);
        //$date = date("Y-d-m", strtotime($date));
        if ($time == '00:00') {
            $time = date("H:i:s", time());
        }
        $result = substr($date, 6, 4).'-'.substr($date, 3, 2).'-'.substr($date, 0, 2).' '.$time;
        return $result;
    }

    public static function getDateForSQL($date)
    {
        $result = substr($date, 6, 4).'-'.substr($date, 3, 2).'-'.substr($date, 0, 2);
        return $result;
    }

    public static function getConfig($id, $campo)
    {
        if (Session::has('config')) {
            //$configs = Session::get('config');
            $configs = json_decode(Session::get('config')); 
            if(isset($configs)) {
                try {
                    foreach($configs as $cfg) {
                        if($cfg->id == $id) {
                            return $cfg->{$campo};
                        }
                    }
                } catch (Exception $e) {
                    $except = $e->getMessage();
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getCfgSist($id, $campo)
    {
        if (Session::has('cfgsist')) {
            //$configs = Session::get('config');
            $cfgsist = json_decode(Session::get('cfgsist')); 
            if(isset($cfgsist)) {
                try {
                    foreach($cfgsist as $cfg) {
                        if($cfg->fkidconfig == $id) {
                            return $cfg->{$campo};
                        }
                    }
                } catch (Exception $e) {
                    $except = $e->getMessage();
                    return null;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }    

    public static function setResponse(string $status, $data = null, string $message = null)
    {
        return [
            'status' => $status,
            'data' => $data,
            'message' => $message
        ];
        /*
        return response()->json( [
            'status' => 'success',
            'data' => $registros,
            'message' => ''
        ]);*/
    }

    public static function setResponseRel(string $status, string $extensao, string $arquivo, $data = null, string $message = null)
    {
        return [
            'status' => $status,
            'extensao' => $extensao,
            'arquivo' => $arquivo,
            'data' => $data,
            'message' => $message
        ];
        /*
        return response()->json( [
            'status' => 'success',
            'extensao' => $request->extensao, 'arquivo' => $relatorioOutFile,
            'data' => $registros,
            'message' => ''
        ]);*/
    }

    public static function trataDiretorio($pathfile)
    {
        if(!file_exists($pathfile)) { // Cria pasta caso não exista
            File::makeDirectory($pathfile);
            return true;
        } else {
            return false;
        }
    }


    public static function primeiraMaiuscula($string)
    {
        $string = mb_strtolower(trim(preg_replace("/\s+/", " ", $string)));//transformo em minuscula toda a sentença
        $palavras = explode(" ", $string);//explodo a sentença em um array
        $t =  count($palavras);//conto a quantidade de elementos do array
        for ($i = 0; $i < $t; $i++) { //entro em um for limitando pela quantidade de elementos do array
            $retorno[$i] = ucfirst($palavras[$i]);//altero a primeira letra de cada palavra para maiuscula
            if($retorno[$i] == "Dos" || $retorno[$i] == "De" || $retorno[$i] == "Do" || $retorno[$i] == "Da" || $retorno[$i] == "E" || $retorno[$i] == "Das"):
                $retorno[$i] = mb_strtolower($retorno[$i]);//converto em minuscula o elemento do array que contenha preposição de nome próprio
            endif;
        }
        return implode(" ", $retorno);
    }


    public static function getSaveType()
    {
        //$r = new Resources();
        $emBanco = self::getConfig(10, 'status');
        $emPasta = self::getConfig(11, 'status');
        if ($emBanco != null && $emBanco == 1 && $emPasta != null && $emPasta == 1) {
            return 3; //1=salva na pasta 2=salva no banco 3=salva no banco e na pasta
        } elseif ($emBanco != null && $emBanco == 1 && ($emPasta == null || $emPasta == 0)) {
            return 2; //1=salva na pasta 2=salva no banco 3=salva no banco e na pasta
        } else {
            return 1; //1=salva na pasta 2=salva no banco 3=salva no banco e na pasta
        }
    }

    public static function returnMimeType($file)
    {
        if($file->getClientOriginalExtension() == 'doc') {
            return 'application/msword';
        } elseif($file->getClientOriginalExtension() == 'odt') {
            return 'application/vnd.oasis.opendocument.text';
        } else {
            return $file->getMimeType();
        }
    }

    public static function get_mime_type($filename)
    {
        $idx = explode('.', $filename);
        $count_explode = count($idx);
        $idx = strtolower($idx[$count_explode - 1]);

        $mimet = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',


            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        if (isset($mimet[$idx])) {
            return $mimet[$idx];
        } else {
            return 'application/octet-stream';
        }
    }


    public static function getUnidadeMedida($tipo)
    {
        switch ($tipo) {
            case 0:
                return "Não Definida";
                break;
            case 1:
                return "Unidade(s) (UN)";
                break;
            case 2:
                return "Cartela(s) (CT)";
                break;
            case 3:
                return "Caixa(s) (CX)";
                break;
            case 4:
                return "Duzia(s) (DZ)";
                break;
            case 5:
                return "Grosa(s) (GS)";
                break;
            case 6:
                return "Par(es) (PA)";
                break;
            case 7:
                return "Peça(s) (PÇ)";
                break;
            case 8:
                return "Pacote(s) (PT)";
                break;
            case 9:
                return "Rolo(s) (RL)";
                break;
            case 10:
                return "Kilograma(s) (kg)";
                break;
            case 11:
                return "Grama(s) (g)";
                break;
            case 12:
                return "Saca(s) (SC60Kg)";
                break;
            case 13:
                return "Litro(s) (l)";
                break;
            case 14:
                return "Metro(s) (m²)";
                break;
            case 15:
                return "Mililitro(s) (ml)";
                break;
            case 16:
                return "Metro(s) (m³)";
                break;
            case 17:
                return "Centímetro(s)";
                break;
            case 18:
                return "Centímetro(s) (cm²)";
                break;
            default:
                return 'Não Definida';
        }
    }

    public function getImageTipoFile($extensao)
    {
        switch ($extensao) {
            case 'pdf':
                echo "iconepdf";
                break;
            case 'pdfx':
                echo "iconepdf";
                break;
            case 'doc':
                echo "iconedoc";
                break;
            default:
                echo "icone padrao";
        }

    }

    public static function getTypeSize($tamanho)
    {

        $tipo = 'Mb';
        if(strpos($tamanho, 'K')) {
            $tipo = 'Kb';
        } elseif(strpos($tamanho, 'G')) {
            $tipo = 'Gb';
        } else {
            $tipo = 'Mb';
        }

        return $tipo;

    }

    public static function convSize($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function removeStringValor($valor)
    {
        $str = str_replace('R$ ', '', $valor);
        $str = str_replace(',', '', $str);
        $str = str_replace('.', '', $str);
        return $str;
    }

    public static function tipoDocumentoClassificacao($num)
    {
        switch ($num) {
            case 1:
                return "Projeto";
                break;
            case 2:
                return "Parecer";
                break;
            case 3:
                return "Propositura";
                break;
            case 4:
                return "Processo";
                break;
            case 5:
                return "Correspondência";
                break;
            case 6:
                return "Norma Legal";
                break;
            case 7:
                return "Sessão";
                break;
            case 8:
                return "Ata";
                break;
            case 9:
                return "Protocolo";
                break;
            case 10:
                return "Ato Oficial";
                break;
            case 11:
                return "Administrativo";
                break;
            case 12:
                return "Diverso";
                break;
            case 13:
                return "Edital";
                break;
            case 14:
                return "Ouvidoria";
                break;
            default:
                return "Projeto";
        }
    }

    public static function tipoDocumentoPosicionamento($num)
    {
        switch ($num) {
            case 1:
                return "À Esquerda";
                break;
            case 2:
                return "À Direita";
                break;
            default:
                return "À Esquerda";
        }
    }

    public static function tipoDocumentoTipoNumeracao($num)
    {
        switch ($num) {
            case 1:
                return "Sequencial ao Infinito";
                break;
            case 2:
                return "Sequencial Zerando todo Ano";
                break;
            case 3:
                return "Sequencial ao Infinito + Ano";
                break;
            case 4:
                return "Sequencial Zerando todo Ano + Ano";
                break;
            case 5:
                return "Sequencial ao Infinito + Legislatura";
                break;
            case 6:
                return "Sequencial Zerando na Legislatura";
                break;
            case 7:
                return "Sequencial Zerando na Legislatura + Legislatura";
                break;
            default:
                return "Sequencial ao Infinito";
        }
    }


    public static function gerarNumAlfaNumericoUpper($tamanho)
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $randomString = '';
        for($i = 0; $i < $tamanho; $i = $i+1){
           $randomString .= $chars[mt_rand(0,36)];
        }
        return $randomString;
    }


    public static function gerarProtocoloComData($tamanho = 6)
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $randomString = '';
        for ($i = 0; $i < $tamanho; $i++) {
            $randomString .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $randomString.Carbon::now()->format('YmdHis');

    }


    public static function gerarCodigoAcessoProtocolo() {
        $caracteres = 'abcdefghijklmnopqrstuvwxyz0123456789'; // Defina o conjunto de caracteres permitido
        $codigo = '';
        
        for ($i = 0; $i < 6; $i++) {
            $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        return $codigo;
    }



    /*
     * Exemplos de como usar esta função
        Retornar a senha com 10 caracteres como maiúsculas, minusculas, números e símbolos:
     *  echo gerar_senha(10, true, true, true, true);
     * */
    public static function gerar_senha($tamanho, $maiusculas, $minusculas, $numeros, $simbolos)
    {
        $ma = "ABCDEFGHIJKLMNOPQRSTUVYXWZ"; // $ma contem as letras maiúsculas
        $mi = "abcdefghijklmnopqrstuvyxwz"; // $mi contem as letras minusculas
        $nu = "0123456789"; // $nu contem os números
        $si = "!@#$%¨&*()_+="; // $si contem os símbolos
        $senha = '';

        if ($maiusculas) {
            // se $maiusculas for "true", a variável $ma é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($ma);
        }

        if ($minusculas) {
            // se $minusculas for "true", a variável $mi é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($mi);
        }

        if ($numeros) {
            // se $numeros for "true", a variável $nu é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($nu);
        }

        if ($simbolos) {
            // se $simbolos for "true", a variável $si é embaralhada e adicionada para a variável $senha
            $senha .= str_shuffle($si);
        }

        // retorna a senha embaralhada com "str_shuffle" com o tamanho definido pela variável $tamanho
        return substr(str_shuffle($senha), 0, $tamanho);
    }

    public static function getNumeroRegistro($localcaractere, $separador, $numero)
    {
        $mystring = $numero;
        $findme =  $separador;
        $pos = strpos($mystring, $findme);

        if ($pos) {
            if ($localcaractere == 1) {
                //return $pos.substr($mystring, 0, $pos);
                return substr($mystring, 0, $pos);
            } else {
                return substr($mystring, $pos, strlen($mystring));
            }
        } else {
            return $numero;
        }

    }


    public static function setDatabaseEngine(Blueprint $table)
    {
        if(ENV('DATABASE_ENGINE')){
            $table->engine = ENV('DATABASE_ENGINE');
        }
    }

    


    public static function str_slug($str)
    {
       $palavra = mb_ereg_replace("[^a-zA-Z0-9_]", "", strtr($str, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
       return $palavra;
    }     


    public static function removeSpecialChar($value)
    {
        //$result  = preg_replace('/[^a-zA-Z0-9_ -]/s','',$value);
        //Str::of($typeReg)->slug('-')
        $result = str_replace(array( '\'', '"', ',' , ';', ':', '\\', '/', '*', '?', '|', '<', '>' ), ' ', $value);

        return $result;
    }

    public static function openFile($saveType, $myme, $fileIdentif, $extension, $typeArq, $path, $binary = null)
    {

        if($saveType == 3 || $saveType == 2) {
            if($binary != null) {
                $file_contents = $binary;
                return response($file_contents)
                    ->header('Cache-Control', 'no-cache private')
                    ->header('Content-Description', 'File Transfer')
                    ->header('Content-Type', $myme)
                    ->header('Content-length', strlen($file_contents))
                    ->header('Content-Disposition', 'inline; filename=' . self::removeSpecialChar($typeArq).'_'.rand(). '.' . $extension)// abre no browser
                    //->header('Content-Disposition', 'attachment; filename=' . $reg->ucnomearquivo)  força o download
                    ->header('Content-Transfer-Encoding', 'binary');
            } else {
                return view('errors.naoencontradoarquivo', [
                    'message' => 'Arquivo "'.self::removeSpecialChar($fileIdentif).'" Não foi Encontrado.'
                ]);
            }
        } else {
            $filepath = env('PATH_FILES_ADMIN').$path;
            if(file_exists($filepath) && $path != null) {
                $file = File::get($filepath);
                return response($file)
                    ->header('Cache-Control', 'no-cache private')
                    ->header('Content-Description', 'File Transfer')
                    ->header('Content-Type', $myme)
                    ->header('Content-length', strlen($file))
                    ->header('Content-Disposition', 'inline; filename=' .  self::removeSpecialChar($typeArq).'_'.rand(). '.' . $extension)// abre no browser
                    //->header('Content-Disposition', 'attachment; filename=' . $reg->ucnomearquivo)  força o download
                    ->header('Content-Transfer-Encoding', 'binary');
            } else {
                return view('errors.naoencontradoarquivo', [
                    'message' => 'Arquivo "'.self::removeSpecialChar($fileIdentif).'" Não foi Encontrado.'
                ]);
            }
        }
    }

    public static function openFileFromPath($saveType, $myme, $fileIdentif, $extension, $typeArq, $path, $binary = null)
    {
        if($saveType == 3 || $saveType == 2) {
            if($binary != null) {
                $file_contents = $binary;
                return response($file_contents)
                    ->header('Cache-Control', 'no-cache private')
                    ->header('Content-Description', 'File Transfer')
                    ->header('Content-Type', $myme)
                    ->header('Content-length', strlen($file_contents))
                    ->header('Content-Disposition', 'inline; filename=' . self::removeSpecialChar($typeArq).'_'.rand(). '.' . $extension)// abre no browser
                    //->header('Content-Disposition', 'attachment; filename=' . $reg->ucnomearquivo)  força o download
                    ->header('Content-Transfer-Encoding', 'binary');
            } else {
                return view('errors.naoencontradoarquivo', [
                    'message' => 'Arquivo "'.self::removeSpecialChar($fileIdentif).'" Não foi Encontrado.'
                ]);
            }
        } else {
            if(file_exists($path) && $path != null) {
                $file = File::get($path);
                return response($file)
                    ->header('Cache-Control', 'no-cache private')
                    ->header('Content-Description', 'File Transfer')
                    ->header('Content-Type', $myme)
                    ->header('Content-length', strlen($file))
                    ->header('Content-Disposition', 'inline; filename=' .  self::removeSpecialChar($typeArq).'_'.rand(). '.' . $extension)// abre no browser
                    //->header('Content-Disposition', 'attachment; filename=' . $reg->ucnomearquivo)  força o download
                    ->header('Content-Transfer-Encoding', 'binary');
            } else {
                return view('errors.naoencontradoarquivo', [
                    'message' => 'Arquivo "'.self::removeSpecialChar($fileIdentif).'" Não foi Encontrado.'
                ]);
            }
        }
    }




    public static function formataData($data)
    {
        return date("d/m/Y", strtotime($data));
    }

    public static function formataDataHora($data)
    {
        return date("d/m/Y H:i:s", strtotime($data));
    }

    public static function getTiposClassificacao($value)
    {
        switch ($value) {
            case 1:
                return 'Projeto';
                break;
            case 2:
                return 'Parecer';
                break;
            case 3:
                return 'Propositura';
                break;
            case 4:
                return 'Processo';
                break;
            case 5:
                return 'Correspondência';
                break;
            case 6:
                return 'Norma Legal';
                break;
            case 7:
                return 'Sessão';
                break;
            case 8:
                return 'Reunião/Ata';
                break;
            case 9:
                return 'Protocolo';
                break;
            case 10:
                return 'Ato Oficial';
                break;
            case 11:
                return 'Administrativo';
                break;
            case 12:
                return 'Diversos';
                break;
            case 13:
                return 'Edital';
                break;
            case 14:
                return 'Ouvidoria';
                break;
            default:
                return 'Não Classificado';
        }
    }


    //if (!function_exists('geraNumeracao')) {

    public static function geraNumeracao($numero, $localcaractere, $opcional, $numerocasas, $caractere, $numeracao, $separador, $datadoc, $legislatura)
    {
        $texto = '';
        $number = $numero != null ? $numero : 0;
        if ($localcaractere == 1) {
            $texto .= ($opcional != null ? $opcional : '');
            for($i = 0; $i < ($numerocasas - strlen($number)); $i++) {
                $texto .= $caractere;
            }
            $texto .= $number;
            if($numeracao == 3 || $numeracao == 4) {
                if($datadoc != null && strlen($datadoc) > 4 ) {
                    $texto .= $separador . substr($datadoc, 0, 4);
                } else {
                    $texto .= $separador . Carbon::now()->year;
                }
            } elseif($numeracao == 5 || $numeracao == 7 || $numeracao == 8) {
                if($numeracao == 8) {
                    $texto .= $separador . 'Dep';
                } else {
                    if($datadoc != null) {
                        $texto .= $separador . ($legislatura != null ? $legislatura->lsigla : 'Sem Legislatura');
                    } else {
                        $texto .= $separador . ($legislatura != null ? $legislatura->lsigla : 'Sem Legislatura');
                    }
                }
            }
        } else {
            if($numeracao == 3 || $numeracao == 4) {
                $texto .= Carbon::now()->year;
            } elseif($numeracao == 5 || $numeracao == 7 || $numeracao == 8) {
                if($numeracao == 8) {
                    $texto .= 'Dep';
                } else {
                    if($datadoc != null) {
                        $texto .= $separador . ($legislatura != null ? $legislatura : 'Sem Legislatura');
                    } else {
                        $texto .= $separador . ($legislatura != null ? $legislatura : 'Sem Legislatura');
                    }
                }
            }
            $texto .= $separador;
            $texto .= $numero;
            for ($i = 0; $i < ($numerocasas - strlen($numero)); $i++) {
                $texto .= $caractere;
            }
            $texto .= $opcional;
        }
        return $texto;
    }




    public static function montaZeroEsquerda($numero, $qtdcaracteres = 7)
    {
        $texto = '';
        for($i = 0; $i < ($qtdcaracteres - strlen($numero)); $i++) {
            $texto .= '0';
        }
        $texto .= $numero;
        return $texto;
    }

    public static function getStatus($status)
    {
        if($status <= 0) {
            return 'Inativo';
        } else {
            return 'Ativo';
        }
    }



    /*

    public static function getLegislatura($data1)
    {
        $gestor = Tools::getGestor();
        $data = Carbon::now()->format('Y-m-d');
        if(strlen($data1) > 4 ){
            $data = date_format(date_create($data1),"Y-m-d");
        }
    
        $legislatura = null;
        $qry = Legislatura::where('fkidgestor', $gestor);
        $qry->whereDate('ldatainicial', '<=', $data);
        $qry->whereDate('ldatafinal', '>=', $data);
        $qry->orderby('ldatainicial','desc');
        $legislatura = $qry->first();
        return $legislatura;

    }*/


    /**
     * Mascara o CPF, deixando apenas os 3 primeiros e os 2 últimos dígitos visíveis (e.g., 123******45).
     * * Esta função primeiro limpa o CPF, removendo todos os caracteres que não são dígitos.
     * Em seguida, ela verifica se o CPF limpo possui exatamente 11 dígitos.
     * Se for válido, aplica a máscara usando uma expressão regular.
     *
     * @param string $cpfCompleto O CPF (pode incluir pontos, traços ou estar limpo).
     * @return string O CPF mascarado ou o original se não for um CPF válido de 11 dígitos.
     */
    static function cpfLGDP(string $cpfCompleto): string
    {
        // 1. Verifica se a string está vazia ou é nula
        if (empty($cpfCompleto)) {
            return '';
        }

        // 2. Remove tudo que não for dígito
        $cpfLimpo = preg_replace('/[^\d]/', '', $cpfCompleto);

        // 3. Verifica se tem 11 dígitos (tamanho padrão de um CPF)
        if (strlen($cpfLimpo) !== 11) {
            // Retorna o valor original se não for um CPF de 11 dígitos
            return $cpfCompleto; 
        }

        // 4. Aplica a Expressão Regular para Capturar e Substituir:
        // Padrão: 
        // ^(\d{3})  -> Captura os 3 primeiros dígitos (Grupo 1)
        // \d{6}    -> Ignora (mascara) os 6 dígitos do meio
        // (\d{2})$ -> Captura os 2 dígitos finais (verificadores) (Grupo 2)
        // Substituição: $1******$2 (Grupo 1 + 6 asteriscos + Grupo 2)
        return preg_replace('/^(\d{3})\d{6}(\d{2})$/', '$1******$2', $cpfLimpo);
    }






    public static function checkCertificadoPDF($pdfpath)
    {
        $pdfContent = file_get_contents($pdfpath);
        $signature = false;
        if (preg_match('/\/Type\s*\/Sig/i', $pdfContent) || preg_match('/\/Contents\s*</i', $pdfContent)) {
            $signature = true;
        }
        return $signature;

        /*
        if ($signature) {
            // Expressão regular para capturar o conteúdo da assinatura
            if (preg_match('/\/Contents\s*<([0-9A-F]+)>/i', $pdfContent, $matches)) {
                $signatureHex = $matches[1];
                $signatureBin = hex2bin($signatureHex); // Converte de HEX para binário
                /*
                return [
                    'signature_hex' => $signatureHex,
                    'signature_bin' => $signatureBin,
                    'signature_length' => strlen($signatureBin),
                ];*

                // Certificados X.509 geralmente começam com um padrão específico de bytes
                //$certStart = "-----BEGIN CERTIFICATE-----";
                //$certEnd = "-----END CERTIFICATE-----";

                // Converte o binário para base64
                //$certBase64 = chunk_split(base64_encode($signatureBin), 64, "\n");
                //return $certBase64;
                return $signatureBin;
                //return "{$certStart}\n{$certBase64}\n{$certEnd}\n";
            }
        }*/

        /*
        if ($signature) {
            $certificatePem = self::convertDerToPem($signatureBin);
            $certificateData = self::parseCertificate($certificatePem);
            print_r($certificateData);
        } else {
            echo "Nenhum certificado válido encontrado no PDF.";
        }*/
    }

    
    function convertDerToPem($derData) {
        // Converte a assinatura binária (DER) para PEM com as tags apropriadas
        // Codifica os dados DER para Base64
        $pem = "-----BEGIN CERTIFICATE-----\n";
        $pem .= chunk_split(base64_encode($derData), 64, "\n");
        $pem .= "-----END CERTIFICATE-----\n";
        return $pem;
    }

    function parseCertificate($certificatePem) {
        // Imprime o conteúdo do certificado PEM para depuração
        //echo "<pre>";
        //echo "Certificado PEM:\n";
        //echo $certificatePem; // Exibe o PEM para garantir que as tags estão corretas
        //echo "</pre>";
        // Tenta processar o certificado PEM
        $certInfo = openssl_x509_parse($certificatePem."-----END CERTIFICATE-----\n");
        
        if (!$certInfo) {
            return "Falha ao processar o certificado digital.";
        }
        
        return [
            'subject' => $certInfo['subject'], // Titular do certificado
            'issuer' => $certInfo['issuer'],   // Emissor do certificado (Autoridade Certificadora)
            'valid_from' => date('Y-m-d H:i:s', $certInfo['validFrom_time_t']), // Data de início da validade
            'valid_to' => date('Y-m-d H:i:s', $certInfo['validTo_time_t']),     // Data de expiração
            'serial_number' => $certInfo['serialNumberHex'], // Número de série
            'signature_algorithm' => $certInfo['signatureTypeSN'], // Algoritmo usado na assinatura
        ];
    }


    public static function getDadosPFX($certificado, $cert_password)
    {
        //if(file_exists(public_path('storage\certificados\18281598_out_file.pfx'))) {
        //$rawpfx = file_get_contents(public_path('storage\certificados\18281598_out_file.pfx'));
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                //var_dump($certs);
                return $certs;

            } catch (Exception $e) {
                return null;
            }
        }
    }

    public static function getVerificaSenhaCertificado($certificado, $cert_password)
    {
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                //var_dump($certs);
                return $certs;
            } catch (Exception $e) {
                return null;
            }
        }
    }

    public static function getEmissaoCertificado($certificado, $cert_password)
    {
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                $certdata = openssl_x509_parse($certs['cert'], 0);
                //$conteudo = $certdata['subject']['commonName'].' (Emitido por '.$certdata['issuer']['commonName'].')';
                $conteudo = $certdata['subject']['commonName'];
                return $conteudo;
            } catch (Exception $e) {
                return null;
            }
        }
    }

    public static function getEmissaoCommonName($certificado, $cert_password)
    {
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                $certdata = openssl_x509_parse($certs['cert'], 0);
                $conteudo = $certdata['issuer']['commonName'];
                return $conteudo;
            } catch (Exception $e) {
                return null;
            }
        }
    }

    public static function getEmissaoPais($certificado, $cert_password)
    {
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                $certdata = openssl_x509_parse($certs['cert'], 0);
                $conteudo = $certdata['issuer']['countryName'];
                return $conteudo;
            } catch (Exception $e) {
                return null;
            }
        }
    }

    public static function getEmissaoIdentificacao($certificado, $cert_password)
    {
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                $certdata = openssl_x509_parse($certs['cert'], 0);
                $conteudo = $certdata['issuer']['organizationalUnitName'];
                return $conteudo;
            } catch (Exception $e) {
                return null;
            }
        }
    }

    public static function getEmissaoTipo($certificado, $cert_password)
    {
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                $certdata = openssl_x509_parse($certs['cert'], 0);
                $conteudo = $certdata['issuer']['organizationName'];
                return $conteudo;
            } catch (Exception $e) {
                return null;
            }
        }
    }

    public static function getDataVencimento($certificado, $cert_password)
    {
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                $certdata = openssl_x509_parse($certs['cert'], 0);
                $conteudo = $certdata['validTo'];
                //echo "Data de Vencimento: " . date('Y-m-d H:i:s', $vencimento);
                return $conteudo;
            } catch (Exception $e) {
                $except = $e->getMessage();
                return null;
            }
        }
    }


    public static function getPublicKey($certificado, $cert_password)
    {
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                return $certs['cert'];

            } catch (Exception $e) {
                $except = $e->getMessage();
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getPrivateKey($certificado, $cert_password)
    {
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                return $certs['pkey'];

            } catch (Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function gerarArquivoCertificadoPublico($certificado, $cert_password, $id, $nomeFile)
    {
        //if(file_exists(public_path('storage\certificados\18281598_out_file.pfx'))) {
        //$rawpfx = file_get_contents(public_path('storage\certificados\18281598_out_file.pfx'));
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                //var_dump($certs);
                $certdata = openssl_x509_parse($certs['cert'], 0);
                //var_dump($certdata['name']);
                //var_dump($certdata);
                //var_dump($certdata['subject']['commonName']);
                //$nomeArquivo = 'Certificado_public_'.$id.'_'.rand() . '.pem';
                $nomeArquivo = $nomeFile.'_'.$id.'_publickey'. '.pem';
                $conteudo = 'Bag Attributes'.PHP_EOL.'    friendlyName: '.$certdata['subject']['commonName'].PHP_EOL.'subject=C = '.$certdata['issuer']['countryName'].', O = '.$certdata['issuer']['organizationName'].', OU = '.$certdata['subject']['organizationalUnitName'][0].', ';
                $conteudo .= 'OU = '.$certdata['subject']['organizationalUnitName'][1].', OU = '.$certdata['subject']['organizationalUnitName'][2].', OU = '.$certdata['subject']['organizationalUnitName'][3].', OU = '.$certdata['subject']['organizationalUnitName'][4].', CN = '.$certdata['subject']['commonName'];
                $conteudo .= PHP_EOL.PHP_EOL.'issuer=C = '.$certdata['issuer']['countryName'].', O = '.$certdata['issuer']['organizationName'].', OU = '.$certdata['subject']['organizationalUnitName'][2].', CN = '.$certdata['issuer']['commonName'];
                $conteudo .= PHP_EOL.PHP_EOL.$certs["cert"];

                file_put_contents(storage_path('app/public/arquivos/certificados/').$nomeArquivo, $conteudo);
                return $nomeArquivo;

                //file_put_contents(public_path('storage\certificados\certpublic2.pem'), $crts['pkey'].$crts['cert'].$str);
                //file_put_contents(public_path('storage\certificados\certpublic1.pem'), $crts['pkey'].$crts['cert'].implode('', $crts['extracerts']));

            } catch (Exception $e) {
                $except = $e->getMessage();
                return null;
            }
        }
    }

    public static function gerarArquivoCertificadoPrivate($certificado, $cert_password, $id, $nomeFile)
    {
        //if(file_exists(public_path('storage\certificados\18281598_out_file.pfx'))) {
        //$rawpfx = file_get_contents(public_path('storage\certificados\18281598_out_file.pfx'));
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                //var_dump($certs);
                $certdata = openssl_x509_parse($certs['cert'], 0);
                //$certdata = openssl_x509_parse($certs['pkey'],0);
                //var_dump($certdata['name']);
                //var_dump($certdata);
                //var_dump($certdata['subject']['commonName']);
                //$nomeArquivo = 'Certificado_private_'.$id.'_'.rand() . '.pem';
                $nomeArquivo = $nomeFile.'_'.$id.'_privatekey'. '.pem';
                $conteudo = 'Bag Attributes'.PHP_EOL.'    friendlyName: '.$certdata['subject']['commonName'].PHP_EOL.'Key Attributes: <No Attributes>'.PHP_EOL.$certs["pkey"];

                file_put_contents(storage_path('app/public/arquivos/certificados/').$nomeArquivo, $conteudo);

                return $nomeArquivo;

                //file_put_contents(public_path('storage\certificados\certpublic2.pem'), $crts['pkey'].$crts['cert'].$str);
                //file_put_contents(public_path('storage\certificados\certpublic1.pem'), $crts['pkey'].$crts['cert'].implode('', $crts['extracerts']));

            } catch (Exception $e) {
                return null;
            }
        }
    }


    

    public static function converterDOCXparaPdf($pathorigem, $pathdestino){
        // Caminho do arquivo .doc recebido
        $docFile = $pathorigem;
        try{
            // Carregar o arquivo .docx
            $phpWord = IOFactory::load($docFile);
            // Registrar o caminho da biblioteca TCPDF
            Settings::setPdfRendererName(Settings::PDF_RENDERER_TCPDF);
            Settings::setPdfRendererPath(base_path('vendor/tecnickcom/tcpdf'));
            $pdf = IOFactory::createWriter($phpWord, 'PDF');
            $pdf->save($pathdestino);
            //return response()->download($outputPdf);
            return true;
        } catch (Exception $e) {
            $except = $e->getMessage();
            return false;
        }
    }


    public static function converterDOCparaPdf($pathorigem, $pathdestino){
        $docFile = $pathorigem;
        try{
            $phpWord = IOFactory::load($docFile, 'Word2007');
            // Registrar o caminho da biblioteca TCPDF
            Settings::setPdfRendererName(Settings::PDF_RENDERER_TCPDF);
            Settings::setPdfRendererPath(base_path('vendor/tecnickcom/tcpdf'));
            $pdf = IOFactory::createWriter($phpWord, 'PDF');
            $pdf->save($pathdestino);
            //return response()->download($outputPdf);
            return true;
        } catch (Exception $e) {
            $except = $e->getMessage();
            return false;
        }
    }

    public static function converterRtfparaPdf($pathorigem, $pathdestino){
        $docFile = $pathorigem;
        try{
            // Carregar o arquivo .doc
            $phpWord = IOFactory::load($docFile, 'RTF');
            // Registrar o caminho da biblioteca TCPDF
            Settings::setPdfRendererName(Settings::PDF_RENDERER_TCPDF);
            Settings::setPdfRendererPath(base_path('vendor/tecnickcom/tcpdf'));
            $pdf = IOFactory::createWriter($phpWord, 'PDF');
            $pdf->save($pathdestino);
            //return response()->download($outputPdf);
            return true;
        } catch (Exception $e) {
            $except = $e->getMessage();
            return false;
        }
    }

    public static function converterODTparaPdf($pathorigem, $pathdestino){
        $docFile = $pathorigem;
        try{
            $phpWord = IOFactory::load($docFile, 'ODText');
            // Registrar o caminho da biblioteca TCPDF
            Settings::setPdfRendererName(Settings::PDF_RENDERER_TCPDF);
            Settings::setPdfRendererPath(base_path('vendor/tecnickcom/tcpdf'));
            $pdf = IOFactory::createWriter($phpWord, 'PDF');
            $pdf->save($pathdestino);
            //return response()->download($outputPdf);
            return true;
        } catch (Exception $e) {
            $except = $e->getMessage();
            return false;
        }
    }

    public static function converterTxtparaPdf($pathorigem, $pathdestino)
    {
        // Caminho do arquivo .doc recebido
        $txtFile = $pathorigem;
        try{
            // Ler o conteúdo do arquivo .txt
            $textContent = file_get_contents($txtFile);
            // Criar uma nova instância do TCPDF
            $pdf = new TCPDFLOCAL();
            // Configurar as margens
            $pdf->SetMargins(18, 20, 18);
            $pdf->SetHeaderMargin(5);
            $pdf->SetFooterMargin(10);
            // Cobrir o cabeçalho (geralmente 20mm do topo) e rodapé (geralmente 20mm da parte inferior)
            // Desenhando um retângulo branco sobre essas áreas
            //$pdf->SetFillColor(255, 255, 255); // Cor de fundo branca
            //$pdf->Rect(0, 0, $pdf->GetPageWidth(), 28, 'F'); // Cabeçalho
            //$pdf->Rect(0, $pdf->GetPageHeight() - 20, $pdf->GetPageWidth(), 20, 'F'); // Rodapé
            // Adicionar uma página
            $pdf->AddPage();
            // Definir a fonte do texto (opcional)
            $pdf->SetFont('helvetica', '', 12);
            // Adicionar o conteúdo do arquivo .txt ao PDF
            $pdf->MultiCell(0, 10, $textContent);

            // Definir o cabeçalho
            //$pdf->SetFont('helvetica', 'B', 12);
            //$pdf->Cell(0, 10, 'Cabeçalho do Documento', 0, 1, 'C');

            // Adicionar conteúdo
            //$pdf->SetFont('helvetica', '', 12);
            //$pdf->MultiCell(0, 10, "Conteúdo do Documento aqui...\n" . str_repeat('Texto\n', 30));

            // Definir o rodapé
            //$pdf->SetY(-15);
            //$pdf->SetFont('helvetica', 'I', 8);
            //$pdf->Cell(0, 10, 'Rodapé - Página ' . $pdf->getPageNumber(), 0, 0, 'C');

            // Salvar o PDF gerado
            $pdf->Output($pathdestino, 'F');
            return true;
        } catch (Exception $e) {
            $except = $e->getMessage();
            return false;
        }
    }
    

    public static function convertPDFtoHTML($pathorigem, $pathdestino){
        $pdf = new PDF();
        // Carrega o PDF
        $pdf->setSourceFile($pathorigem);
        $pageCount = $pdf->getNumPages();
        // Para cada página do PDF
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pdf->AddPage();
            $templateId = $pdf->importPage($pageNo);
            $pdf->useTemplate($templateId);
        }
        //$htmlOutput = $path.$filenameoutput;
        // Aqui você pode processar o conteúdo gerado com TCPDF ou exportar para HTML com o seu código
        // No entanto, para conversão completa de HTML, é necessário usar ferramentas como `pdf2htmlEX`.
        $htmlOutput = $pdf->Output('S');  // Saída em formato de string (HTML).
        // Salve ou processe o HTML gerado
        file_put_contents($pathdestino, $htmlOutput);
        return $pathdestino;
    }


    

    public static function geraArquivoTramite($certificado, $cert_password)
    {
        if($certificado != null && strlen($cert_password) > 2) {
            try {
                $rawpfx = $certificado;
                $certs = array();
                openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                //var_dump($certs);
                return $certs;
            } catch (Exception $e) {
                return null;
            }
        }
    }


     // Função para converter numeral romano para número árabe
     public static function romanizeverso($romano)
    {
        // Mapeamento de símbolos romanos para seus valores decimais
        $valores = [
            'I' => 1,
            'V' => 5,
            'X' => 10,
            'L' => 50,
            'C' => 100,
            'D' => 500,
            'M' => 1000
        ];
    
        $total = 0;
        $i = 0;
    
        // Iterar através de cada caractere do número romano
        while ($i < strlen($romano)) {
            // Se o valor do caractere atual for menor que o valor do próximo, subtraímos
            if ($i + 1 < strlen($romano) && $valores[$romano[$i]] < $valores[$romano[$i + 1]]) {
                $total += $valores[$romano[$i + 1]] - $valores[$romano[$i]];
                $i += 2;  // Avança dois caracteres
            } else {
                // Caso contrário, somamos o valor
                $total += $valores[$romano[$i]];
                $i += 1;  // Avança um caractere
            }
        }
    
        return $total;
    }


    public static function getAlineaNum($letra)
    {
        $alfabeto = 'abcdefghijklmnopqrstuvwxyz'; // O alfabeto completo
        $index = 0; // Inicializa o índice

        // Verifica se a entrada é uma única letra
        if (strlen($letra) === 1) {
            $index = strpos($alfabeto, $letra); // Encontrar o índice da letra
            if ($index !== false) {
                return $index + 1; // Exemplo: 'a' = 1, 'z' = 26
            }
        } else {
            // Caso tenha mais de uma letra (aa, ab, ac, ...)
            $length = strlen($letra);
            for ($i = 0; $i < $length; $i++) {
                $index *= 26; // Move uma "posição" à esquerda em base 26
                $index += strpos($alfabeto, $letra[$i]) + 1; // Soma o valor correspondente à letra
            }
            return $index;
        }

        return 0; // Se não for uma letra válida, retorna 'NaN'
    }



    public static function getDadosCertificado($certificado, $tipodado)
    {
        $result = '';
        if($tipodado == 'info') {
            if($certificado->ctipocertificado != null) {
                $result .= (strlen($result) > 0 ? ' - ' : '').$certificado->ctipocertificado;
            }

            if($certificado->cemissortipo != null) {
                $result .= (strlen($result) > 0 ? ' - ' : '').$certificado->cemissortipo;
            }

            if($certificado->cemissoridentificacao != null) {
                $result .= (strlen($result) > 0 ? ' - ' : '').$certificado->cemissoridentificacao;
            }

        } else { //'emissor'
            if($certificado->cemissorcommonname != null) {
                $result .= $certificado->cemissorcommonname;
            }
        }

        return $result;
    }



    public static function convertDOCtoPDF($path, $filename, $filenameoutput)
    {
        //$filename = 'teste.doc';
        //$filenameoutput = 'convertDOCtoPDF.pdf';
        $realpath = realpath(app_path('\..'));
        define('PHPWORD_BASE_DIR', $realpath);
        //DOM PDF OK

        define('DOMPDF_ENABLE_AUTOLOAD', false);
        $domPdfPath = realpath($realpath).'/vendor/dompdf/dompdf';
        //\PhpOffice\PhpWord\Settings::setPdfRendererPath(".");
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName(\PhpOffice\PhpWord\Settings::PDF_RENDERER_DOMPDF);
        //$phpWord = IOFactory::load($path.$filename, 'Word2007');
        //$phpWord->save($path.$filenameoutput, 'PDF');

        $phpWord = \PhpOffice\PhpWord\IOFactory::load($path.$filename);
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'PDF');
        $xmlWriter->save($path.$filenameoutput);

        //TCPDF OK
        /*
        $PdfPath = realpath(PHPWORD_BASE_DIR . '/vendor/tecnickcom/tcpdf');
        //$PdfPath = realpath(PHPWORD_BASE_DIR . '/vendor/setasign/fpdi/Tcpdf/Fpdi');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($PdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('TCPDF');
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($path.$filename);
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord , 'PDF');
        $xmlWriter->save($path.$filenameoutput);
        //$phpWord = IOFactory::load($path.$filename, 'Word2007');
        //$phpWord->save($path.$filenameoutput, 'PDF');
        */

        return $filenameoutput;
    }



    public static function convertTemplateToPDF(PhpWord $phpWord, string $outputPath): bool
    {
        try {
            // 1. Configura o PhpWord para usar o TCPDF
            \PhpOffice\PhpWord\Settings::setPdfRendererName(\PhpOffice\PhpWord\Settings::PDF_RENDERER_TCPDF);
            
            // Se estivesse usando DOMPDF, a configuração seria:
            // $domPdfPath = base_path('vendor/dompdf/dompdf');
            // \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
            // \PhpOffice\PhpWord\Settings::setPdfRendererName(\PhpOffice\PhpWord\Settings::PDF_RENDERER_DOMPDF);

            // 2. Cria o escritor de PDF
            $xmlWriter = IOFactory::createWriter($phpWord, 'PDF');
            
            // 3. Salva o arquivo PDF no caminho de saída
            $xmlWriter->save($outputPath);

            return File::exists($outputPath);
            
        } catch (\Exception $e) {
            error_log("Erro na conversão do Template para PDF (TCPDF): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tenta converter um arquivo DOCX para PDF usando a CLI do unoconv/LibreOffice.
     * Requer que o 'unoconv' esteja instalado e acessível no servidor.
     *
     * @param string $inputDocxPath Caminho completo para o arquivo .docx de entrada.
     * @param string $outputPdfPath Caminho completo para o arquivo .pdf de saída.
     * @return bool
     */    
    public static function convertDocxToPdf(string $inputDocxPath, string $outputPdfPath, string $orientacao = 'retrato'): bool
    {
        try {
            // 1. Define o NOME do renderizador: TCPDF
            // Esta linha avisa ao PHPWord qual motor de PDF ele deve usar.
            Settings::setPdfRendererName(Settings::PDF_RENDERER_DOMPDF);
            // 2. Define o CAMINHO para o renderizador (ABSOLUTO)
            // base_path() retorna o caminho raiz do Laravel.
            // O TCPDF é instalado pelo Composer em vendor/tecnickcom/tcpdf.
            $dompdfPath = base_path('vendor/dompdf/dompdf');

            // VERIFICAÇÃO DE SEGURANÇA: Confirma que o caminho existe.
            if (!File::isDirectory($dompdfPath)) {
                throw new \Exception(
                    "ERRO CRÍTICO: Pasta do Dompdf não encontrada em: " . $dompdfPath . 
                    ". Certifique-se de ter executado 'composer require dompdf/dompdf'."
                );
                return false;
            }

            // Aplica o caminho
            Settings::setPdfRendererPath($dompdfPath);                     

            // 4. CARREGAR o DOCX temporário em um objeto PhpWord completo
            $phpWord = IOFactory::load($inputDocxPath);

            if($orientacao != 'retrato'){
                $isLandscapeForced = false;
                foreach ($phpWord->getSections() as $index => $section) {
                    $settings = $section->getSettings(); 
                    
                    // CRUCIAL: Se o objeto de configurações (settings) não existir (for null),
                    // não podemos definir a orientação. Neste caso, pulamos esta seção.
                    if ($settings !== null) {
                        // Define a orientação de cada seção para PAISAGEM
                        $settings->setOrientation(Section::ORIENTATION_LANDSCAPE);
                        $isLandscapeForced = true;
                    } else {
                        // Logar um aviso se for uma seção sem configurações, indicando que ela foi ignorada
                        //error_log("AVISO: Seção $index no documento DOCX não possui configurações e a orientação Paisagem não pôde ser forçada. Foi ignorada para evitar falha.");
                    }
                }
                
                // Opcional: Se nenhuma seção puder ser forçada, pode ser um documento vazio ou corrompido.
                if (!$isLandscapeForced && count($phpWord->getSections()) > 0) {
                    //error_log("AVISO: Nenhuma seção no documento foi atualizada. O PDF pode não ser Paisagem.");
                } 
            }           

            // 5. Salvar o objeto PhpWord como PDF
            $xmlWriter = IOFactory::createWriter($phpWord, 'PDF');
            $xmlWriter->save($outputPdfPath);
            return File::exists($outputPdfPath);

        } catch (ProcessFailedException $exception) {
            $except = $exception->getMessage();
            error_log("DOCX to PDF Conversion Failed: " . $exception->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log("DOCX to PDF Conversion Failed (Geral): " . $e->getMessage());
            return false;
        }


        // 1. Defina o comando.
        // O comando 'unoconv -f pdf' tenta salvar o PDF no mesmo diretório do DOCX.
        // O Laravel executa comandos no diretório raiz, então precisamos ser explícitos.
        // Nota: Certifique-se de que o diretório de destino é gravável pelo usuário do servidor (geralmente www-data).

        // Usaremos a opção -o para especificar o arquivo de saída.
        /*
        $command = ['unoconv', '-f', 'pdf', '-o', dirname($outputPdfPath), $inputDocxPath];
        $process = new Process($command);
        try {
            $process->mustRun();
            $finalPath = dirname($outputPdfPath) . '/' . basename($inputDocxPath, '.docx') . '.pdf';
            
            if (File::exists($finalPath)) {
                 if ($finalPath !== $outputPdfPath) {
                    File::move($finalPath, $outputPdfPath);
                 }
                 return true;
            }
            
            return File::exists($outputPdfPath);

        } catch (ProcessFailedException $exception) {
            $except = $exception->getMessage();
            error_log("DOCX to PDF Conversion Failed: " . $exception->getMessage() . " Output: " . $process->getErrorOutput());
            return false;
        } catch (\Exception $e) {
            error_log("DOCX to PDF Conversion Failed (Geral): " . $e->getMessage());
            return false;
        }*/
    }    



    public static function convertDOCtoHTML($path, $filename, $filenameoutput)
    {
        //$filename = 'teste1.docx';
        //$filenameconvertido = 'convertDOCtoPDF.html';
        $phpWord = IOFactory::load($path.$filename, 'Word2007');
        $phpWord->save($path.$filenameoutput, 'HTML');
        return $filenameoutput;
    }



    /**
    * Remove os arquivos da pasta cache public/storage/cache/, depois de um tempo
    * 604800 milisegundos 7 dias
    * 172800 milisegundos 2 dias
    * 86400 milisegundos 1 dias
    * 60000 milisegundos 1 minuto
    */
    public static function cleanFileInCache(): void
    {
        $time = 604800; // 7 days (default)
        //$files = glob("public/storage/cache/assinaturadigital/*");
        $files = File::glob(public_path() . '/storage/cache/assinaturadigital/*.*');
        if ($files) {
            $timeAgo = time() - $time;
            foreach ($files as $file) {
                if (filemtime($file) < $timeAgo && file_exists($file) && is_file($file)) {
                    unlink($file);
                }
            }
            clearstatcache();
        }
    }

    /**
    * Remove os arquivos da pasta cache public/storage/cache/, depois de um tempo
    * 604800 milisegundos 7 dias
    * 172800 milisegundos 2 dias
    * 86400 milisegundos 1 dias
    * 60000 milisegundos 1 minuto
    */
    public static function removerarquivosantigos($path): void
    {
        $time = 1800; // 30 minutos
        //$files = glob("public/storage/cache/assinaturadigital/*");
        //$files = File::glob(storage_path() . $path);
        $files = File::files($path);
        foreach ($files as $file) {
            // Verifique se o arquivo tem mais de 30 minutos =  hora (3600 segundos)
            if (time() - filemtime($file) > $time) {
                // Excluir o arquivo
                File::delete($file);
            }
        }
    }


    public static function getHashFile($pathfile, $isFile = false)
    {
        $cfgGetHash = self::getConfig(16, 'status');
        if ($cfgGetHash != null && $cfgGetHash == 1) {
            try {
                if(!$isFile) {
                    $hash = hash_file(env('TYPEHASH'), $pathfile);
                } else {
                    $hash = hash(env('TYPEHASH'), $pathfile);
                }
                return $hash;
            } catch (Exception $e) {
                return '';
            }
        } else {
            return '';
        }
    }


    /**
     * Retorna o texto do arquivo
     * O arquivo qdo encoded pode ser file padronizado como input
     *
     */
    public static function getText($pathfile, $extensao)
    {
        if(file_exists($pathfile)) {
            if($extensao == 'pdf') {
                return self::read_pdf($pathfile);
            } elseif($extensao == 'doc') {
                return self::read_doc($pathfile);
            } elseif($extensao == 'docx') {
                return self::read_docx($pathfile);
            } elseif($extensao == 'txt') {
                return self::read_txt($pathfile);
            } elseif($extensao == 'rtf') {
                return self::read_rtf($pathfile);                
            } elseif($extensao == 'xlsx') {
                return self::read_xlsx($pathfile);
            } elseif($extensao == 'pptx') {
                return self::read_pptx($pathfile);
            } elseif($extensao == 'odt') {
                return self::read_odt($pathfile);
            } else {
                return '';
            }
        } else {
            return '';
        }
        
        /*} elseif($pathfile != "") {
            $dir = sys_get_temp_dir();
            $tmp = tempnam($dir, "etr");
            file_put_contents($tmp, $pathfile);
            $outtext = '';
            if($extensao == 'pdf') {
                $outtext = self::read_pdf($tmp);
            } elseif($extensao == 'doc') {
                $outtext = self::read_doc($tmp);
            } elseif($extensao == 'docx') {
                $outtext = self::read_docx($tmp);
            } elseif($extensao == 'txt' || $extensao == 'rtf') {
                $outtext = self::read_txt($tmp);
            } elseif($extensao == 'xlsx') {
                $outtext = self::read_xlsx($tmp);
            } elseif($extensao == 'pptx') {
                $outtext = self::read_pptx($tmp);
            } elseif($extensao == 'odt') {
                $outtext = self::read_odt($tmp);
            } else {
                $outtext = '';
            }
            unlink($tmp);
            return $outtext;
        } else {
            return '';
        }*/
    }



    /**
     * Retorna o texto do arquivo
     * O arquivo qdo encoded pode ser file padronizado como input
     *
     */
    public static function getTextFile($pathfile, $extensao, $isFile = false)
    {
        $cfgGetText = self::getConfig(4, 'status');
        if ($cfgGetText != null && $cfgGetText == 1) {
            if(!$isFile) {
                if(file_exists($pathfile)) {
                    if($extensao == 'pdf') {
                        return self::read_pdf($pathfile);
                    } elseif($extensao == 'doc') {
                        return self::read_doc($pathfile);
                    } elseif($extensao == 'docx') {
                        return self::read_docx($pathfile);
                    } elseif($extensao == 'txt' || $extensao == 'rtf') {
                        return self::read_txt($pathfile);
                    } elseif($extensao == 'xlsx') {
                        return self::read_xlsx($pathfile);
                    } elseif($extensao == 'pptx') {
                        return self::read_pptx($pathfile);
                    } elseif($extensao == 'odt') {
                        return self::read_odt($pathfile);
                    } else {
                        return '';
                    }
                } else {
                    return '';
                }
            } elseif($pathfile != "") {
                $dir = sys_get_temp_dir();
                $tmp = tempnam($dir, "etr");
                file_put_contents($tmp, $pathfile);
                $outtext = '';
                if($extensao == 'pdf') {
                    $outtext = self::read_pdf($tmp);
                } elseif($extensao == 'doc') {
                    $outtext = self::read_doc($tmp);
                } elseif($extensao == 'docx') {
                    $outtext = self::read_docx($tmp);
                } elseif($extensao == 'txt' || $extensao == 'rtf') {
                    $outtext = self::read_txt($tmp);
                } elseif($extensao == 'xlsx') {
                    $outtext = self::read_xlsx($tmp);
                } elseif($extensao == 'pptx') {
                    $outtext = self::read_pptx($tmp);
                } elseif($extensao == 'odt') {
                    $outtext = self::read_odt($tmp);
                } else {
                    $outtext = '';
                }
                unlink($tmp);
                return $outtext;
            } else {
                return '';
            }
        }

        /*
        if ($cfgGetText != null && $cfgGetText == 1){
            $parser = new Parser();
            try{
                if($isFile){
                    $pathtemp = storage_path('app/public/arquivos/extracttexttemp/');
                    if($isInput){
                        if($pathfile->getClientOriginalExtension() == 'pdf'){
                            $filename = 'tempextract_'.rand() . '.' . $pathfile->getClientOriginalExtension();
                            $pathfile->move($pathtemp, $filename);
                            $pdf = $parser->parseFile($pathtemp.$filename);
                            $text = $pdf->getText();
                            unlink($pathtemp.$filename);
                            return $text;
                        }else{
                            return '';
                        }
                    }else{
                        if($extensao == 'pdf'){
                            $filename = 'tempextract_'.rand() . $extensao;
                            file_put_contents($pathtemp.$filename, $pathfile);
                            $pdf = $parser->parseFile($pathtemp.$filename);
                            $text = $pdf->getText();
                            unlink($pathtemp.$filename);
                            return $text;
                        }else{
                            return '';
                        }
                    }
                }else{
                    if($extensao == 'pdf'){
                        $pdf = $parser->parseFile($pathfile);
                        $text = $pdf->getText();
                        return $text;
                    }else{
                        return '';
                    }
                }
            } catch (Exception $e) {
                return '';
            }
        }else{
            return '';
        }*/
    }

    private static function read_pdf($filename)
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filename);
            $text = $pdf->getText();
            return $text;
        } catch (Exception $e) {
            return '';
        }
    }

    private static function read_odt($filename)
    {
        try {
            $dataFile = "content.xml";
            //Create a new ZIP archive object
            $zip = new ZipArchive();
            // Open the archive file
            if (true === $zip->open($filename)) {
                // If successful, search for the data file in the archive
                if (($index = $zip->locateName($dataFile)) !== false) {
                    // Index found! Now read it to a string
                    $text = $zip->getFromIndex($index);
                    // Load XML from a string
                    // Ignore errors and warnings
                    //$xml = DOMDocument::loadXML($text, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                    $xml = new DOMDocument();
                    $xml->loadXML($text, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                    // Remove XML formatting tags and return the text
                    return strip_tags($xml->saveXML());
                }
                //Close the archive file
                $zip->close();
            }

            // In case of failure return a message
            return '';
        } catch (Exception $e) {
            return '';
        }
    }

    private static function read_txt($pathfile)
    {
        try {
            // Lê todo o conteúdo do arquivo .txt
            $content = file_get_contents($pathfile);
            
            // Verifica se ocorreu algum erro na leitura
            if ($content === false) {
                return '';
            }

            return $content;
        } catch (Exception $e) {
            return '';
        }
    }


    private static function read_rtf($pathfile)
    {
        try {
            // Lê o conteúdo do arquivo RTF
            $content = file_get_contents($pathfile);

            // Remove tags RTF para tentar extrair o texto
            // As tags RTF começam com uma barra (ex. \par para parágrafos, \b para negrito, etc.)
            $content = preg_replace('/\{\\.*?\}/', '', $content); // Remove as partes RTF
            $content = strip_tags($content); // Remove outras tags HTML, se houver
            $content = preg_replace('/\{\\.*?\}/', '', $content); 
            return $content;
        } catch (Exception $e) {
            return '';
        }
    }


    private static function read_xml($pathfile)
    {
        try {
            // Criar um novo objeto DOMDocument
            $dom = new DOMDocument();
            $dom->load($pathfile);

            // Criar um objeto XPath
            $xpath = new DOMXPath($dom);

            // Usar XPath para obter todos os nós de texto
            $nodes = $xpath->query('//text()'); // Seleciona todos os nós de texto no XML

            // Extrair o texto de cada nó
            $text = '';
            foreach ($nodes as $node) {
                $text .= $node->nodeValue . "\n";
            }

            return $text;
        } catch (Exception $e) {
            return '';
        }
    }




    private static function read_doc($pathfile)
    {
        try {
            // Carregar o arquivo .docx
            $phpWord = IOFactory::load($pathfile);

            $text = '';

            // Iterar sobre as seções do documento e extrair o texto
            foreach ($phpWord->getSections() as $section) {
                // Iterar sobre os elementos dentro de cada seção
                foreach ($section->getElements() as $element) {
                    // Verifica se o elemento é um parágrafo
                    if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                        // Se for um TextRun (grupo de textos com formatação)
                        foreach ($element->getElements() as $textElement) {
                            // Verifica se o elemento é um texto simples
                            if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                $text .= $textElement->getText() . "\n";
                            }
                        }
                    } elseif ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                        // Se for um elemento de texto simples
                        $text .= $element->getText() . "\n";
                    }
                }
            }

            return $text;        
            /*
            $fileHandle = fopen($pathfile, "r");
            $line = @fread($fileHandle, filesize($pathfile));
            $lines = explode(chr(0x0D), $line);
            $outtext = "";
            foreach($lines as $thisline) {
                $pos = strpos($thisline, chr(0x00));
                if (($pos !== false) || (strlen($thisline) == 0)) {
                } else {
                    $outtext .= utf8_encode($thisline)." ";
                }
            }*/
            /*
            $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$outtext);
            $outtext = str_replace(array('[\', \']'),'', $outtext);
            $outtext = preg_replace('/[.*]/U','', $outtext);
            $outtext = preg_replace('/&(amp;)?#?[a-z0–9]+;/i', '-', $outtext);
            $outtext = htmlentities($outtext, ENT_COMPAT, 'utf-8');
            $outtext = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\1', $outtext );
            $outtext = preg_replace(array('/[a-z0–9]/i', '/[-]+/') , '-', $outtext);
            $outtext = preg_replace("@([ã])@","",$outtext);
            $outtext = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $outtext);*/
            //return $outtext;
        } catch (Exception $e) {
            $except = $e->getMessage();
            return '';
        }
    }

    private static function read_docx($pathfile)
    {
        try {
            // Carregar o arquivo .docx
            $phpWord = IOFactory::load($pathfile);

            $text = '';

            // Iterar sobre as seções do documento e extrair o texto
            foreach ($phpWord->getSections() as $section) {
                // Itera sobre os elementos dentro de cada seção
                foreach ($section->getElements() as $element) {
                    // Verifica se o elemento é um parágrafo
                    if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                        foreach ($element->getElements() as $textElement) {
                            // Verifica se o elemento é de texto
                            if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                $text .= $textElement->getText() . "\n";
                            }
                        }
                    } elseif ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                        // Para elementos de texto simples
                        $text .= $element->getText() . "\n";
                    }
                }
            }
            return $text;
        } catch (Exception $e) {
            return '';
        }
        /*$striped_content = '';
        $content = '';
        try {
            $zip = zip_open($pathfile);
            if (!$zip || is_numeric($zip)) {
                return false;
            }
            while ($zip_entry = zip_read($zip)) {
                if (zip_entry_open($zip, $zip_entry) == false) {
                    continue;
                }
                if (zip_entry_name($zip_entry) != "word/document.xml") {
                    continue;
                }
                $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                zip_entry_close($zip_entry);
            }// end while
            zip_close($zip);
            $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
            $content = str_replace('</w:r></w:p>', "\r\n", $content);
            $striped_content = strip_tags($content);

            return $striped_content;
        } catch (Exception $e) {
            return '';
        }*/
    }


    private static function read_xlsx($input_file)
    {
        $xml_filename = "xl/sharedStrings.xml"; //content file name
        $zip_handle = new ZipArchive();
        $output_text = "";
        try {
            if(true === $zip_handle->open($input_file)) {
                if(($xml_index = $zip_handle->locateName($xml_filename)) !== false) {
                    $xml_datas = $zip_handle->getFromIndex($xml_index);
                    //$xml_handle = DOMDocument::loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                    $xml_handle = new DOMDocument();
                    $xml_handle->loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                    $output_text = strip_tags($xml_handle->saveXML());
                } else {
                    $output_text .= "";
                }
                $zip_handle->close();
            } else {
                $output_text .= "";
            }
            return $output_text;
        } catch (Exception $e) {
            return '';
        }
    }

    private static function read_pptx($input_file)
    {
        $zip_handle = new ZipArchive();
        $output_text = "";
        try {
            if(true === $zip_handle->open($input_file)) {
                $slide_number = 1; //loop through slide files
                while(($xml_index = $zip_handle->locateName("ppt/slides/slide".$slide_number.".xml")) !== false) {
                    $xml_datas = $zip_handle->getFromIndex($xml_index);
                    //$xml_handle = DOMDocument::loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                    $xml_handle = new DOMDocument();
                    $xml_handle->loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                    $output_text .= strip_tags($xml_handle->saveXML());
                    $slide_number++;
                }
                if($slide_number == 1) {
                    $output_text .= "";
                }
                $zip_handle->close();
            } else {
                $output_text .= "";
            }
            return $output_text;
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Retorna o sistema operacional do server
     * 1 = Windows
     * 0 = Outro
     */
    public static function getSOServer()
    {
        $phpso = explode(" ", php_uname());

        if($phpso[0] == "Windows") {
            //$so = 'Windows';
            return 1;
        } else {
            //$so = 'Outro';
            return 0;
        }
    }


    public static function convertPDFVersion($filepathorigem, $filename)
    {
        $so = self::getSOServer();
        $outputfile = storage_path('app/public/arquivos/cachetemp/').$filename;
        $inputfile = $filepathorigem;

        if($so == 0) {
            /* para LINUX */
            /* Para Ubuntu/Debian:  ver a versao instalada gs --version */
            //sudo apt-get update
            //sudo apt-get install ghostscript
            //gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH  -sOutputFile=$outputfile  $path.$filename
            $gsCmd = "gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH -sOutputFile=".$outputfile." ".$inputfile;
            $result = exec($gsCmd);
            if($result != null){
                return $outputfile;
            }else{
                return null;
            }

            /*
            $command = new GhostscriptConverterCommand();
            $filesystem = new Filesystem();
            
            $converter = new GhostscriptConverter($command, $filesystem);
            $result = $converter->convert($inputfile, '1.4');
            if($result != null){
                return $filename;
            }else{
                return null;
            }*/
        } else {
            /* para WINDOWS */
            //"C:\Program Files\gs\gs9.53.3\bin\gswin64c" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH -sOutputFile="'.$outputfile.'" "'.$path.$filename.'" 2>&1
            $result = shell_exec('"D:\Program Files\gs\gs10.04.0\bin\gswin64c" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH -sOutputFile="'.$outputfile.'" "'.$inputfile.'" 2>&1');
            if($result != null){
                return $outputfile;
            }else{
                return null;
            }
        }
    }
    

    public static function getPDFVersion($path, $filename) {
        /*$parser = new Parser();

        // Caminho para o seu PDF
        $file = 'D:\webserverx\htdocs\interdoc\public\storage\arquivos\assinaturadigital\AssinaturaDigital_1_874983365.pdf';

        // Faz a leitura do PDF
        $pdf = $parser->parseFile($file);

        // Obter o conteúdo do PDF
        $text = $pdf->getText();*/
         /*$guesser = new RegexGuesser();
        $result = $guesser->guess($path.$filename);
        $float  = floatval($result);
        if($float > 1.4) {
            return true;
        } else {
            return false;
        }*/

        $pdf = new Fpdi();

        try{
            // Abre o PDF e carrega seu conteúdo
            //$pdf->setSourceFile('D:\webserverx\htdocs\interdoc\public\storage\arquivos\assinaturadigital\AssinaturaDigital_1_874983365.pdf');

            // Obtém a versão do PDF
            $pdfFile = fopen($path, 'r');
            $header = fread($pdfFile, 10); // Lê os primeiros 5 bytes (cabeçalho do PDF)
            // Verifica se o arquivo é um PDF válido
            if (substr($header, 0, 5) == "%PDF-") {
                $version = substr($header, 5, 3); // A versão do PDF está após o "%PDF-" (por exemplo, "1.4")
                $float  = floatval($version);
                fclose($pdfFile);
                if($float > 1.4){
                    return true;
                }else{
                    return false;
                }
            }else{
                fclose($pdfFile);
                return false;
            }
        }catch (Exception $e) {
            $except = $e->getMessage();
            return false;
        }
    } 



    public function assinarPDF($pathorigem, $filename, $certificado, $posicaoassinatura = 1)
    {
        //$configQrCOde = Resources::getConfig(18, 'status');
        $configAssinaturaVisivel = Tools::getConfig(19, 'status');
        $configLinkVerificador = Tools::getConfig(20, 'status');

        if(file_exists($pathorigem)) {
            $pdfDocument = null;
            $pdfDocument = $pathorigem;
            $cert_password = Crypt::decrypt($certificado->cpassword);
            $versaoPDF = self::getPDFVersion($pathorigem, $filename);
            //$compressaoPDF = self::getCompressaoPDF1($pathorigem);
            if($versaoPDF == true){
                $pdfDocument =  self::convertPDFVersion($pathorigem, $filename);
                if($pdfDocument != null){
                    //File::Delete($pathPDF.$filename);
                    //File::Move($pathPDF.$pdfDocument, $pathPDF.$filename);
                }
                //$pdfDocument = $pathPDF.$filename;
            }else{
                //$pdfDocument = storage_path('app/public/arquivos/assinaturadigital/').$filename;
            }

            if($pdfDocument != null){
                $pdf = new PDF();

                // Desabilitar a criação de margens automáticas
                $pdf->SetAutoPageBreak(false);
                $pageWidth = $pdf->GetPageWidth();
                $pageHeight = $pdf->GetPageHeight();

                //file_put_contents($pdf_path, $pdfDocument);
                $pageCount = $pdf->setSourceFile($pdfDocument);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $pdf->AddPage();
                    $page = $pdf->importPage($i);
                    $pdf->useTemplate($page, 0, 0);
                    //$pdf->useTemplate($page, 0, 0, $pageWidth, ($pageHeight+28));
                    // Cobrir o cabeçalho (geralmente 20mm do topo) e rodapé (geralmente 20mm da parte inferior)
                    // Desenhando um retângulo branco sobre essas áreas
                    //$pdf->SetFillColor(255, 255, 255); // Cor de fundo branca
                    //$pdf->Rect(0, 0, $pdf->GetPageWidth(), 20, 'F'); // Cabeçalho
                    //$pdf->Rect(0, $pdf->GetPageHeight() - 20, $pdf->GetPageWidth(), 20, 'F'); // Rodapé
                }
                $dataassinatura = date("d/m/Y H:i:s");
                $quemassina = 'Assinado Digitalmente por '.$certificado->cidentificacao.' em '.$dataassinatura;

                $info = [
                    'Name'        => 'Assinado por '.$certificado->cidentificacao,
                    'Date'        => $dataassinatura,
                    'Reason'      => 'Assinatura de Documento Oficial',
                    'ContactInfo' => 'Entre em contato',
                ];

                $imagePath = public_path('build/assets/icons/iconcert32.jpg');
                $imgx = 50; // Posição horizontal (a partir da margem esquerda)
                $imgy = 100; // Posição vertical (a partir da margem superior)
                $imgwidth = 10; // Largura da imagem
                $imgheight = 10; // Altura da imagem
                if ($configAssinaturaVisivel != null && $configAssinaturaVisivel == 1){
                    $html = '';
                    //$pdf->setPrintHeader(false);
                    //$pdf->setPrintFooter(false);
                    //$pdf->writeHTML($html, false);

                    // Calcular a largura e altura do texto
                    $textWidth = $pdf->GetStringWidth($quemassina);
                    $textHeight = $pdf->GetStringHeight($textWidth, $quemassina);
                    // Posição do texto na página
                    $x = $pageWidth - $textHeight;
                    $y = $pageHeight / 2;
                    $rotate = 0;

                    // 1 = lateral dir horario meio 
                    // 2 = lateral esq horario meio
                    // 3 = lateral dir anthorario meio
                    // 4 = lateral esq anthorario meio
                    // 5 = Rodapé Centralizado
                    // 6 = Rodapé acima Centralizado
                    // 7 = Assinatura em nova Página
                    if($posicaoassinatura == 1){
                        $x = $pageWidth - 5;
                        $y = ($pageHeight / 2) - ($textWidth/2);
                        $rotate = 270;
                        $imgx = $pageWidth - ($imgwidth * 1.5);
                        $imgy = $y-3;
                    }else if($posicaoassinatura == 2){
                        $x = $textHeight;
                        $y = ($pageHeight / 2) - ($textWidth/2);
                        $rotate = 270;
                        $imgx = $x - ($imgwidth * 1.5);
                        $imgy = $y-3;
                    }else if($posicaoassinatura == 3){
                        $x = $pageWidth - 7;
                        $y = $pageHeight - $textWidth;
                        $rotate = 90;
                        $imgx = $pageWidth - ($imgwidth * 2);
                        $imgy = $y-3;
                    }else if($posicaoassinatura == 4){
                        $x = $textHeight - 5;
                        $y = $pageHeight - $textWidth;
                        $rotate = 90;
                        $imgx = $x - ($imgwidth * 1.5);
                        $imgy = $y-3;
                    }else if($posicaoassinatura == 5){
                        $x = ($pageWidth / 3) - ($textWidth/2);
                        $y = $pageHeight - ($textHeight*3);
                        $rotate = 0;
                        $imgx = $x+($imgwidth * 4);
                        $imgy = $y;
                    }else if($posicaoassinatura == 6){
                        $x = ($pageWidth / 3) - ($textWidth/2);
                        $y = $pageHeight - ($textHeight*8);
                        $rotate = 0;
                        $imgx = $x+($imgwidth * 4);
                        $imgy = $y;
                    }else if($posicaoassinatura == 7){
                        $pdf->AddPage();
                        $x = ($pageWidth / 3) - ($textWidth/2);
                        $y = $pageHeight - ($textHeight*8);
                        $rotate = 0;
                        $imgx = $x+($imgwidth * 4);
                        $imgy = $y;
                    }else{
                        $x = ($pageWidth / 3) - ($textWidth/2);
                        $y = $pageHeight - ($textHeight*8);
                        $rotate = 0;
                        $imgx = $x+($imgwidth * 4);
                        $imgy = $y;
                    }

                    // Definir a fonte para o texto
                    //$pdf->SetFont('Arial', 'B', 16);
                    $pdf->SetFont('helvetica','', 7);
                    if($posicaoassinatura > 0){
                        if($rotate > 0){
                            $pdf->StartTransform();

                            // Aplicar a rotação de 270 graus
                            $pdf->Rotate($rotate, $x, $y);
                            // Adicionar o texto na página
                            $pdf->Text($x, $y, $quemassina);
                            $pdf->Image($imagePath, $imgx, $imgy, $imgwidth, $imgheight);

                            //$pdf->StartTransform();
                            //$pdf->Rotate(90, 20, 250); // Rotaciona 90° na posição X Y rodape da pagina a esquerda
                            //$pdf->Rotate(90, 150, 200); // Rotaciona 90° na posição X Y rodape da pagina a direita
                            //$pdf->Rotate(90, 100, 150); // Rotaciona 90° na posição X Y meio da página a direita
                            //$pdf->Rotate(180, 100, 150); // de ponta cabeça no topo da página
                            //$pdf->MultiCell(160, 40, $quemassina, 0, 'C', false, 2, '32', '250', true, 0, false, false, 0, 'T');
                            //$pdf->Image(public_path('build/assets/icons/iconcert32.png'),  env('IMGCERTREL_X'), env('IMGCERTREL_Y'), env('IMGCERTREL_WIDTH'), env('IMGCERTREL_HEIGHT'), 'PNG');
                            //$pdf->Image('https://localhost:8282/interdoc/public/storage/arquivos/assinaturadigital/iconcert32.png', 100,100,36, 36, 'PNG');
                            //$pdf->Image(public_path(env('PATH_FILES_DEFAULT').'\signer\stock_certificate.png'),  env('IMGCERTREL_X'), env('IMGCERTREL_Y'), env('IMGCERTREL_WIDTH'), env('IMGCERTREL_HEIGHT'), 'PNG');
                            //$pdf->Image($img, env('IMGCERTREL_X'), env('IMGCERTREL_Y'), env('IMGCERTREL_WIDTH'), env('IMGCERTREL_HEIGHT'), 'PNG');
                            
                            //$pdf->StopTransform();
                            // Desfazer a rotação
                            //$pdf->Rotate(0);
                            $pdf->StopTransform();
                        }else{
                            //$pdf->Text($x, $y, $quemassina);
                            $pdf->SetXY($x, $y);
                            $pdf->Cell(0, 10, $quemassina, 0, 0, 'C');
                            $pdf->Image($imagePath, $imgx, $imgy, $imgwidth, $imgheight);
                        }
                    }
                }

                
                /*
                if ($configQrCOde != null && $configQrCOde == 1){
                    $styleqrcode = array(
                        'border' => 2,
                        'vpadding' => 'auto',
                        'hpadding' => 'auto',
                        'fgcolor' => array(0,0,0),
                        'bgcolor' => false, //array(255,255,255)
                        'module_width' => 1, // width of a single module in points
                        'module_height' => 1 // height of a single module in points
                    );
                    // QRCODE,Q : QR-CODE Better error correction
                    //$pdf->write2DBarcode( env('CLIENT_DATA_SITE').'/'.$procdestino->fkidprocessotramite.'-'.$procdestino->id, 'QRCODE,Q', env('IMGQRCODEREL_X'), env('IMGQRCODEREL_Y'), env('IMGQRCODEREL_WIDTH'), env('IMGQRCODEREL_HEIGHT'), $styleqrcode, 'N');
                    //$pdf->Text(100, 190, 'QRCODE Q1');
                }*/

                
                if ($configLinkVerificador != null && $configLinkVerificador == 1){
                    $informativo = Tools::getConfig(20, 'exemplo');
                    /*$informativo = '<br/><span style="font-size: x-small; padding-bottom:40px; padding-top:40px; text-align:right;">Para validar a Assinatura do Documento acesse o <b color="#006600">'.
                    '<a href="'.env('URL_CERT_VERIFICADOR').'">Verificador</a></b> que detem a <i>Lista de Certificados Confiáveis</i> para validação. </span><br/>';*/
                    $pdf->SetY($pageHeight - 25);
                    $pdf->writeHTML($informativo, true, false, true, false, 'R');
                }
                //$pdf->Image(public_path('storage\signer\stock_certificate.png'), 20, 252, 15, 18, 'PNG');
                //$publickey = File::get(public_path('storage\certificados\antoniopubliccert.pem'));
                //$privatekey = File::get(public_path('storage\certificados\antonioprivatekey.pem'));            
                //$publickey = File::get(public_path(env('PATH_FILES_DEFAULT').'/'.$certificado->cpathpublic));
                //$privatekey = File::get(public_path(env('PATH_FILES_DEFAULT').'/'.$certificado->cpathprivate));     
                $publickey = $certificado->cconteudopublic;
                $privatekey = $certificado->cconteudoprivate;       
                $pdf->setSignature($publickey, $privatekey, $cert_password, '', 2, $info, "A");
                //$pdf->addEmptySignatureAppearance(180, 60, 15, 15);
                $array = explode('.', $filename);
                $extension = end($array);
                $namefinal = basename(rand(), '.'.$extension);
                $filesave =  $namefinal.'_Assinado.'.$extension;
                $pp = storage_path('app/public/arquivos/assinados/').$filesave;
                /*
                I : send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.
                D : send to the browser and force a file download with the name given by name.
                F : save to a local server file with the name given by name.
                S : return the document as a string (name is ignored).
                FI : equivalent to F + I option
                FD : equivalent to F + D option
                E : return the document as base64 mime multi-part email attachment (RFC 2045)*/
                //$n = $pdf->Output($filesave, 'D');
                $n = $pdf->Output($pp, 'F');
                Tools::deleteFile('/arquivos/cachetemp/'.$filename);
                if($n == ''){
                    return $filesave;
                }else{
                    return null;
                }
                //$pdf->Output('Assinado_'.date("YmdHis").'.pdf', 'D');
            }
        }else{
            return null;
        }
    }


    static function encodebase($string, $passencode = '') {
        // Concatena a senha com a string
        $combinedString = $string . $passencode;
        
        // Codifica a string em Base64
        $encodedString = base64_encode($combinedString);
        
        // Verifica se a string já termina com '='
        if (substr($encodedString, -1) === '=') {
            // Se já tiver '=', retorna a string como está
            return $encodedString;
        } else {
            // Se não tiver '=', adiciona um '=' ao final
            return $encodedString . '=';
        }
    }
    
    

    static function decodebase($encodedString, $passencode = '') {
        // Decodificar a string base64
        $decodedString = base64_decode($encodedString);
        
        // Remover a senha da string decodificada
        //return str_replace($passencode, '', $decodedString);


        // 1. Decodificar a string Base64. Se falhar, retorna false.
        $decodedString = base64_decode($encodedString);
        if ($decodedString === false) {
            return '-'; // Retorna false se a decodificação falhar
        }

        // 2. Verificar se a string decodificada termina com a senha
        $passLength = strlen($passencode);
        if ($passLength > 0 && substr($decodedString, -$passLength) === $passencode) {
            // 3. Remover a senha do final da string decodificada
            return substr($decodedString, 0, -$passLength);
        }

    }



    public static function checkCliConversionTool(): string
    {
        // Os nomes dos executáveis a serem testados (unoconv é o preferido)
        // 'soffice' é o executável principal do LibreOffice em muitos sistemas, incluindo Windows
        $executables = ['unoconv', 'libreoffice', 'soffice'];
        $found = false;
        $executableFound = null;

        foreach ($executables as $exe) {
            // Tenta executar o comando de versão (deve ser rápido e não interativo)
            // Usamos o comando "which" ou "where" para verificar se o executável existe no PATH
            // No Windows, "where" é o equivalente a "which"
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Para Windows: "where unoconv"
                $command = ['where', $exe];
            } else {
                // Para Linux/Mac: "which unoconv"
                $command = ['which', $exe];
            }
            
            $process = new Process($command);
            $process->setTimeout(5); // Timeout de 5 segundos
            $process->run();

            if ($process->isSuccessful() && !empty($process->getOutput())) {
                $found = true;
                $executableFound = $exe;
                break;
            }
        }

        return ($found ? "Ferramenta CLI de conversão ('{$executableFound}') detectada e pronta." : "Nenhuma ferramenta CLI de conversão (unoconv/libreoffice) encontrada ou acessível no PATH.");
        /*
        return [
            'is_available' => $found,
            'executable_name' => $executableFound,
            'message' => $found 
                ? "Ferramenta CLI de conversão ('{$executableFound}') detectada e pronta." 
                : "Nenhuma ferramenta CLI de conversão (unoconv/libreoffice) encontrada ou acessível no PATH.",
        ];*/
    }     

    /*
    public static function createPDFTramiteAssinado(Certificado $certificate, ProcessoTramite $processotramite, ProcessoTramiteDestinatario $procdestino)
    {

        $configQrCOde = self::getConfig(18, 'status');
        $configAssinaturaVisivel = self::getConfig(19, 'status');
        $configLinkVerificador = self::getConfig(20, 'status');


        //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(Constants::NOME_SISTEMA);
        $pdf->SetTitle('Trâmite-'.$procdestino->fkidprocessotramite.'-'.$procdestino->id.'-'.Carbon::now()->toDateTimeString());
        //$pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('Processo, Trâmite, Assinatura, Digital, Destinatario');

        //resource_path('views') storage_path('app/public/signer/').'certificado_noback.png';
        //$PDF_HEADER_LOGO = storage_path('app/public/images/logo_header_relatorio.png');
        //$PDF_HEADER_LOGO_WIDTH = "30";
        //$PDF_HEADER_TITLE = 'Assinatura de Documento Oficial Trâmite-'.$procdestino->fkidprocessotramite.'-'.$procdestino->id.'-'.Carbon::now()->format('d/m/Y H:i:s');   //Carbon::parse(date_format($item['created_at'],'d/m/Y H:i:s');
        //$PDF_HEADER_STRING = env('CLIENT_DATA_NAME').' '. env('CLIENT_DATA_CITY').'-'. env('CLIENT_DATA_STATE')."\n". env('CLIENT_DATA_TELEPHONE')."\n". env('CLIENT_DATA_EMAIL')."\n". env('CLIENT_DATA_SITE');
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, $PDF_HEADER_LOGO_WIDTH, $PDF_HEADER_TITLE, $PDF_HEADER_STRING);
        //$pdf->SetTitle($PDF_HEADER_TITLE);

        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        // set default header data
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.$procdestino->fkidprocessotramite, PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetMargins(PDF_MARGIN_LEFT, 40, 20);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(13);
        // set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        if(!file_exists(storage_path('app/').$certificate->cpathpublic)) {
            return null;
        }

        if(!file_exists(storage_path('app/').$certificate->cpathprivate)) {
            return null;
        }

        $publickey = File::get(storage_path('app/').$certificate->cpathpublic);
        $privatekey = File::get(storage_path('app/').$certificate->cpathprivate);
        $passcert = Crypt::decrypt($certificate->cpassword);

        //$pdf_path = public_path('storage\signer\Doc_'. md5(uniqid("")) . '.pdf') ;
        //$path_assinado = public_path('storage\signer\Assinado_'. md5(uniqid("")) . '.pdf') ;
        //$pdfDocument = File::get(public_path('storage\signer\exemplo.pdf'));
        //file_put_contents($pdf_path, $pdfDocument);
        $info = array(
        'Name'     => 'Assinado por '.$certificate->cidentificacao,
        'Location' => env('CLIENT_DATA_CITY').'-'. env('CLIENT_DATA_STATE'),
        'Date'     => date("d/m/Y H:i:s"),
        'Reason'   => 'Assinatura de Documento Oficial Trâmite-'.$procdestino->fkidprocessotramite.'-'.$procdestino->id.'-'.Carbon::now()->format('d/m/Y H:i:s'),
        'ContactInfo' => env('CLIENT_DATA_NAME').' '. env('CLIENT_DATA_CITY').'-'. env('CLIENT_DATA_STATE').' '.env('CLIENT_DATA_TELEPHONE').' '.env('CLIENT_DATA_EMAIL').' '. env('CLIENT_DATA_SITE'),
        );

        $pdf->setSignature($publickey, $privatekey, $passcert, '', 2, $info, "A");

        // set font
        $pdf->SetFont('helvetica', '', 12);

        // add a page
        $pdf->AddPage();
        $pdf->lastPage();
        //$pdf->Image(storage_path('app/public/images/logo_header_relatorio.png'), 20, 5, 25, 25, 'PNG');
        //$pdf->Image(storage_path('app/public/images/logo_header_relatorio.png'), env('IMGREL_X'), env('IMGREL_Y'), env('IMGREL_WIDTH'), env('IMGREL_HEIGHT'), 'PNG');

        /*$header = 'Confirmação de Tramitação '.env('CLIENT_DATA_NAME').' '. env('CLIENT_DATA_CITY').'-'. env('CLIENT_DATA_STATE')."\n"
        . env('CLIENT_DATA_TELEPHONE')."\n". env('CLIENT_DATA_EMAIL')."\n". env('CLIENT_DATA_SITE');

        $pdf->Write(0, $header, '', 0, 'C', true, 0, false, false, 0);*
        $html1 = '<br/><h1 style="text-align:center; padding-top:20px; padding-bottom:20px; color:black;">Dados da Tramitação</h1>';
        $pdf->writeHTML($html1, true, 0, true, 0);
        $html2 = '<h2 style="text-align:center; padding-top:20px; padding-bottom:20px; color:black;">'.$procdestino->fkidprocessotramite.'-'.$procdestino->id.'</h2><br/>';
        $pdf->writeHTML($html2, true, 0, true, 0);
        /*
        $conteudo1 = 'Origem.: '.$procdestino->fkiddepartamentoorigem.'-'.$procdestino->fkidusuarioorigem.'';
        $conteudo2 = 'Assunto.: '.$processotramite->ptassunto;
        $conteudo3 = 'Conteúdo.: '.$processotramite->pttexto.'';
        $conteudo4 = 'Destino.: '.$procdestino->fkiddepartamento.'-'.$procdestino->fkidusuario.'';*
        //date_format( strtotime($processotramite->ptdataenvio), 'd/m/Y H:i:s')
        $dataenvio = strtotime($processotramite->ptdataenvio);
        $dataenvioptbr = date('d/m/Y - H:i:s', $dataenvio);
        $conteudo1 = '<span style="text-align:left; padding-top:40px; padding-bottom:20px;"><b>Data de Envio.:</b> '.$dataenvioptbr.'</span><br/>';
        $conteudo2 = '<span style="text-align:left; padding-top:40px; padding-bottom:20px;"><b>Origem.:</b> '.$procdestino->fkiddepartamentoorigem.'-'.$procdestino->fkidusuarioorigem.'</span><br/>';
        $conteudo3 = '<span style="text-align:left; padding-top:20px; padding-bottom:20px;"><b>Destino.:</b> '.$procdestino->fkiddepartamento.'-'.$procdestino->fkidusuario.'</span><br/>';
        $conteudo4 = '<span style="text-align:left; padding-top:20px; padding-bottom:20px;"><b>Assunto.:</b> '.$processotramite->ptassunto.'</span><br/>';
        $conteudo5 = '<span style="text-align:left; padding-top:20px; padding-bottom:20px;"><b>Conteúdo.:</b> '.$processotramite->pttexto.'</span><br/>';


        $pdf->writeHTML($conteudo1, true, 0, true, 0);
        $pdf->writeHTML($conteudo2, true, 0, true, 0);
        $pdf->writeHTML($conteudo3, true, 0, true, 0);
        $pdf->writeHTML($conteudo4, true, 0, true, 0);
        $pdf->writeHTML($conteudo5, true, 0, true, 0);
        //$pdf->Write(0, $conteudo1, '', 0, 'C', true, 0, false, false, 0);
        //$pdf->Write(0, $conteudo2, '', 0, 'C', true, 0, false, false, 0);
        //$pdf->Write(0, $conteudo2, '', 0, 'C', true, 0, false, false, 0);
        //$pdf->Write(0, $conteudo4, '', 0, 'C', true, 0, false, false, 0);

        if ($configQrCOde != null && $configQrCOde == 1) {
            $styleqrcode = array(
                'border' => 2,
                'vpadding' => 'auto',
                'hpadding' => 'auto',
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
            );
            // QRCODE,Q : QR-CODE Better error correction
            $pdf->write2DBarcode(env('CLIENT_DATA_SITE').'/'.$procdestino->fkidprocessotramite.'-'.$procdestino->id, 'QRCODE,Q', env('IMGQRCODEREL_X'), env('IMGQRCODEREL_Y'), env('IMGQRCODEREL_WIDTH'), env('IMGQRCODEREL_HEIGHT'), $styleqrcode, 'N');
            //$pdf->Text(100, 190, 'QRCODE Q1');
        }
        if ($configLinkVerificador != null && $configLinkVerificador == 1) {
            $informativo = self::getConfig(20, 'exemplo');
            /*$informativo = '<br/><span style="font-size: x-small; padding-bottom:40px; padding-top:40px; text-align:right;">Para validar a Assinatura do Documento acesse o <b color="#006600">'.
            '<a href="'.env('URL_CERT_VERIFICADOR').'">Verificador</a></b> que detem a <i>Lista de Certificados Confiáveis</i> para validação. </span><br/>';*
            $pdf->SetY(264);
            $pdf->writeHTML($informativo, true, false, true, false, 'R');
        }

        //$pdf->MultiCell(120, 15, $informativo, 0, 'C', false, 2, '32', '255', true, 0, false, false, 0, 'T');
        // print a line of text
        //$text = 'Foi <b color="#FF0000">Digitalmente Assinado</b> por <b>'.$certificate->cidentificacao.'</b> <br />Para validar a Assinatura do Documento acesse <b color="#006600"><a href="https://assina.ufsc.br/verificador">https://assina.ufsc.br/verificador</a></b> <i>Lista de Certificados Confiáveis</i>.';
        //$pdf->writeHTML($text, false, 0, true, 0);

        //$pdf->lastPage();
        if ($configAssinaturaVisivel != null && $configAssinaturaVisivel == 1) {
            $dataassinatura = date("d/m/Y H:i:s");
            $quemassina = 'Assinado Digitalmente por '.$certificate->cidentificacao.' em '.$dataassinatura;
            $pdf->MultiCell(160, 15, $quemassina, 0, 'C', false, 2, '32', '250', true, 0, false, false, 0, 'T');
            $pdf->Image(public_path(env('PATH_FILES_DEFAULT').'\signer\stock_certificate.png'), env('IMGCERTREL_X'), env('IMGCERTREL_Y'), env('IMGCERTREL_WIDTH'), env('IMGCERTREL_HEIGHT'), 'PNG');
        }


        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // *** set signature appearance ***
        /*
                // create content for signature (image and/or text)
                $pdf->Image(public_path('storage\certificados\stock_certificate.png'), 360, 60, 15, 15, 'PNG');

                // define active area for signature appearance
                $pdf->setSignatureAppearance(360, 60, 15, 15);
                // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
                // *** set an empty signature appearance ***
                $pdf->addEmptySignatureAppearance(180, 80, 15, 15);*
        // create content for signature (image and/or text)
        //$pdf->Image(public_path('storage\signer\stock_certificate3.png'), 180, 60, 15, 15, 'PNG');
        // define active area for signature appearance
        $pdf->setSignatureAppearance(180, 60, 15, 15);
        // *** set an empty signature appearance ***
        $pdf->addEmptySignatureAppearance(180, 60, 15, 15);
        // reset text rendering mode
        //$pdf->setTextRenderingMode($stroke=0, $fill=true, $clip=false);
        //$pdf->Output($path_assinado, "F");
        //$filesave = 'Assinado_ProcessoTramite_'.$procdestino->fkidprocessotramite.'_'.rand().'.pdf';
        $filesave = 'Assinado_'.$procdestino->fkidprocessotramite.'_'.date("YmdHis").'.pdf';
        //file_put_contents($pdf_path, $pdfDocument);
        $pp = public_path(env('PATH_FILES_DEFAULT').'/arquivos/processostramites/').$filesave;
        /*
                I : send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.
                D : send to the browser and force a file download with the name given by name.
                F : save to a local server file with the name given by name.
                S : return the document as a string (name is ignored).
                FI : equivalent to F + I option
                FD : equivalent to F + D option
                E : return the document as base64 mime multi-part email attachment (RFC 2045)*
        //$n = $pdf->Output($filesave, 'D');
        $n = $pdf->Output($pp, 'F');
        if($n == '') {
            return $filesave;
        } else {
            return null;
        }

        //return file_get_contents($path_assinado);
    }*/




    
    /*
    public static function assinarPDFTramite($pathPDF, $filename, $certificado, ProcessoTramite $processotramite, ProcessoTramiteDestinatario $procdestino)
    {
        $configQrCOde = self::getConfig(18, 'status');
        $configAssinaturaVisivel = self::getConfig(19, 'status');
        $configLinkVerificador = self::getConfig(20, 'status');


        //$pdfDocument = public_path('storage\signer\exemplo.pdf');
        if(file_exists($pathPDF.$filename)) {
            //$pdfDocument = storage_path($pathPDF);
            $pdfDocument = null;
            $cert_password = Crypt::decrypt($certificado->cpassword);
            $versaoPDF = self::getPDFVersion($pathPDF, $filename);
            if($versaoPDF == true) {
                $pdfDocument =  self::convertPDFVersion($pathPDF, $filename);
                if($pdfDocument != null) {
                    File::Delete($pathPDF.$filename);
                    File::Move($pathPDF.$pdfDocument, $pathPDF.$filename);
                    //$filename = $pdfDocument;
                }
                $pdfDocument = $pathPDF.$filename;
            } else {
                $pdfDocument = $pathPDF.$filename;
            }

            /*
            if($certificado != null && strlen($cert_password) > 2){
                try{
                    $rawpfx = $certificado;
                    $certs = array();
                    openssl_pkcs12_read($rawpfx, $certs, $cert_password);
                    //var_dump($certs);
                    return $certs;
                } catch (Exception $e) {
                    return null;
                }
            }*

            if($pdfDocument != null) {
                $pdf = new PDF();
                //file_put_contents($pdf_path, $pdfDocument);
                $pageCount = $pdf->setSourceFile($pdfDocument);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $pdf->AddPage();
                    $page = $pdf->importPage($i);
                    $pdf->useTemplate($page, 0, 0);
                }

                $dataassinatura = date("d/m/Y H:i:s");
                $quemassina = 'Assinado Digitalmente por '.$certificado->cidentificacao.' em '.$dataassinatura;
                $info = [
                    'Name'        => 'Assinado por '.$certificado->cidentificacao,
                    'Date'        => $dataassinatura,
                    'Reason'      => 'Assinatura de Documento Oficial',
                    'ContactInfo' => 'Entre em contato',
                ];

                if ($configAssinaturaVisivel != null && $configAssinaturaVisivel == 1) {
                    $html = '';
                    $pdf->setPrintHeader(false);
                    $pdf->setPrintFooter(false);
                    $pdf->writeHTML($html, false);
                    $pdf->MultiCell(160, 15, $quemassina, 0, 'C', false, 2, '32', '250', true, 0, false, false, 0, 'T');
                    $pdf->Image(public_path(env('PATH_FILES_DEFAULT').'\signer\stock_certificate.png'), env('IMGCERTREL_X'), env('IMGCERTREL_Y'), env('IMGCERTREL_WIDTH'), env('IMGCERTREL_HEIGHT'), 'PNG');
                }

                if ($configQrCOde != null && $configQrCOde == 1) {
                    $styleqrcode = array(
                        'border' => 2,
                        'vpadding' => 'auto',
                        'hpadding' => 'auto',
                        'fgcolor' => array(0,0,0),
                        'bgcolor' => false, //array(255,255,255)
                        'module_width' => 1, // width of a single module in points
                        'module_height' => 1 // height of a single module in points
                    );
                    // QRCODE,Q : QR-CODE Better error correction
                    $pdf->write2DBarcode(env('CLIENT_DATA_SITE').'/'.$procdestino->fkidprocessotramite.'-'.$procdestino->id, 'QRCODE,Q', env('IMGQRCODEREL_X'), env('IMGQRCODEREL_Y'), env('IMGQRCODEREL_WIDTH'), env('IMGQRCODEREL_HEIGHT'), $styleqrcode, 'N');
                    //$pdf->Text(100, 190, 'QRCODE Q1');
                }

                if ($configLinkVerificador != null && $configLinkVerificador == 1) {
                    $informativo = self::getConfig(20, 'exemplo');
                    /*$informativo = '<br/><span style="font-size: x-small; padding-bottom:40px; padding-top:40px; text-align:right;">Para validar a Assinatura do Documento acesse o <b color="#006600">'.
                    '<a href="'.env('URL_CERT_VERIFICADOR').'">Verificador</a></b> que detem a <i>Lista de Certificados Confiáveis</i> para validação. </span><br/>';*
                    $pdf->SetY(264);
                    $pdf->writeHTML($informativo, true, false, true, false, 'R');
                }


                //$pdf->Image(public_path('storage\signer\stock_certificate.png'), 20, 252, 15, 18, 'PNG');
                //$publickey = File::get(public_path('storage\certificados\antoniopubliccert.pem'));
                //$privatekey = File::get(public_path('storage\certificados\antonioprivatekey.pem'));
                $publickey = $certificado->cconteudopublic;
                $privatekey = $certificado->cconteudoprivate;
                $pdf->setSignature($publickey, $privatekey, $cert_password, '', 2, $info, "A");
                $pdf->addEmptySignatureAppearance(180, 60, 15, 15);
                $filesave = 'Assinado_'.$filename;
                $pp = $pathPDF.$filesave;
                /*
                I : send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.
                D : send to the browser and force a file download with the name given by name.
                F : save to a local server file with the name given by name.
                S : return the document as a string (name is ignored).
                FI : equivalent to F + I option
                FD : equivalent to F + D option
                E : return the document as base64 mime multi-part email attachment (RFC 2045)*
                //$n = $pdf->Output($filesave, 'D');
                $n = $pdf->Output($pp, 'F');
                if($n == '') {
                    return $filesave;
                } else {
                    return null;
                }
                //$pdf->Output('Assinado_'.date("YmdHis").'.pdf', 'D');
            }
        } else {
            return null;
        }

    }*/

    public static function htmlToDecodeText($html) {
        // Substitui tags de bloco por quebras de linha
        $html = preg_replace('/<\s*\/?(h[1-6]|p|div|br|li|ul|ol|table|tr|td|th)\s*>/i', "\n", $html);

        // Remove o restante das tags
        $text = strip_tags($html);

        // Decodifica entidades
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove múltiplas quebras de linha duplicadas
        $text = preg_replace('/\n\s*\n/', "\n\n", $text);

        // Opcional: Trim final
        return trim($text);
    }




    /**
     * 1. Função para gerar o QR Code em formato Base64
     * @param string $data O texto, URL ou dado a ser codificado no QR Code.
     * @return string A string Base64 do QR Code para uso em tags <img>.
     */
    public static function gerarQrCodeBase64(string $data): string
    {
        // Opções de configuração do QR Code
        $options = new QROptions([
            'outputType' => 'png', // Saída como imagem PNG  QROutputInterface::MARKUP_SVG  QROutputInterface::IMG_PNG,
            'imageBase64' => true,                       // Codificar a saída em Base64
            'scale'       => 4,                          // Tamanho da escala (pixel por módulo)
            'version'     => 7,                          // Versão do QR Code
            'eccLevel'    => EccLevel::L,              // Nível de correção de erro (Low)
        ]);

        // Cria a instância do gerador e retorna a string Base64 da imagem
        // O retorno já inclui o prefixo de dados 'data:image/png;base64,...'
        return (new QRCode($options))->render($data);
    }


    /**
    * Responsável por gerar a imagem do QR Code e armazenar no diretório:
    * ./public/img
    *
    * @param  string $content
    * @param  int $dimension
    * @param  string $imgName
    * @return boolean
    */
    /*
    public static function generate(string $content, int $dimension): string
    {
        
        try {
            $renderer = new Png();
            $renderer->setHeight($dimension);
            $renderer->setWidth($dimension);
            /*$renderer = new ImageRenderer(
                new RendererStyle(200),
                new ImagickImageBackEnd()
            );*/
            /*if (!file_exists(public_path('arquivos/qrcode'))) {
                mkdir(public_path('arquivos/qrcode'), 0700);
            }
            //$imgName = 'protocolo_'.rand() . '.png';
            $imgName = $content.'.png';
            if (file_exists(public_path('arquivos/qrcode').'/'.$imgName)) {
                $path = '/arquivos/qrcode/'. $imgName;
                return $path;
            } else {
                $fullPath = public_path('arquivos/qrcode').'/';
                $writer = new Writer($renderer);
                $writer->writeFile($content, $fullPath.$imgName);
                $path = '/arquivos/qrcode/'. $imgName;

                if(!file_exists(public_path('arquivos/qrcode').'/'.$imgName)){
                    throw new Exception();
                }
                return $path;
            }*

            if (!file_exists(storage_path('app/public/arquivos/qrcode'))) {
                mkdir(storage_path('app/public/arquivos/qrcode'), 0700);
            }
            //$imgName = 'protocolo_'.rand() . '.png';
            $imgName = $content.'.png';
            if (file_exists(public_path(env('PATH_FILES_DEFAULT').'/arquivos/qrcode/').$imgName)) {
                $path = env('PATH_FILES_DEFAULT').'/arquivos/qrcode/'. $imgName;
                return $path;
            } else {
                $fullPath = public_path(env('PATH_FILES_DEFAULT').'/arquivos/qrcode/');
                $writer = new Writer($renderer);
                $writer->writeFile($content, $fullPath.$imgName);
                $path = env('PATH_FILES_DEFAULT').'/arquivos/qrcode/'. $imgName;

                if(!file_exists(public_path(env('PATH_FILES_DEFAULT').'/arquivos/qrcode/').$imgName)) {
                    throw new Exception();
                }
                return $path;
            }

        } catch(Exception $exc) {

            return null;
        }
    }*/

    public static function generate1(string $content, int $dimension, string $imgName): bool
    {

        return true;

    }


    
    public static function thumb(string $uri, int $width, int $height = null)
    {
        $cropper = new \CoffeeCode\Cropper\Cropper('../public/'.env('PATH_FILES_DEFAULT').'/cache');
        $pathThumb = $cropper->make(config('filesystems.disks.public.root') . '/' . $uri, $width, $height);

        $file = 'cache/' . collect(explode('/', $pathThumb))->last();
        return $file;
    }

    public static function flush(?string $path)
    {
        $cropper = new \CoffeeCode\Cropper\Cropper('../public/'.env('PATH_FILES_DEFAULT').'/cache');

        if(!empty($path)) {
            $cropper->flush($path);
        } else {
            $cropper->flush();
        }

    }

    public static function imageProporcao($file)
    {
        $imgs = array("png", "jpg", "jpeg", "tiff", "bmp");
        $upload_file = $file;
        if (in_array(strtolower($upload_file->getClientOriginalExtension()), $imgs)) {
            $height = Image::make($upload_file)->height();
            $width = Image::make($upload_file)->width();
            if($height > $width) {
                return 'vertical';
            } else {
                return 'horizontal';
            }
        } else {
            return 'erro';
        }
    }




}