<?php

namespace App\Http\Controllers\Core;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route; 
use Illuminate\Support\Facades\File;
use Illuminate\Routing\Controller;

class TelemetriaController extends Controller
{
    public function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Desconhecido';
        $platform = 'Desconhecido';
        $version= "";
        $ub = "";
        // First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'Mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'Windows';
        }
        // Next get the name of the useragent yes seperately and for good reason
        if(strpos($u_agent, 'MSIE') || strpos($u_agent, 'Trident/7')) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif(strpos($u_agent, 'Firefox')) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif(strpos($u_agent, 'Chrome')) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif(strpos($u_agent, 'Safari')) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif(strpos($u_agent, 'Opera') || strpos($u_agent, 'OPR/')) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif(strpos($u_agent, 'Edge')) {
            $bname = 'Edge';
            $ub = "Edge";
        }
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        // see how many we have
        $i = count($matches['browser']);

        if($bname != 'Internet Explorer'){
            if ($i != 1 ) {
                //we will have two since we are not using 'other' argument yet
                //see if version is before or after the name

                if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                    $version= $matches['version'][0];
                } else {
                    $version= $matches['version'][1];
                }
            } else {
                $version= $matches['version'][0];
            }
        }
        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    }

    function getBrowserName()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
        elseif (strpos($user_agent, 'Edge')) return 'Edge';
        elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
        elseif (strpos($user_agent, 'Safari')) return 'Safari';
        elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
        elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
        return 'Desconhecido';
    }


    public function recursosdesistema(Request $request)
    {
        // --- INFORMAÇÕES DO SISTEMA ---
        $phpVersion     = PHP_VERSION;
        $laravelVersion = \Illuminate\Foundation\Application::VERSION;
        $SOsistema = PHP_OS;
        $libreoffice = '';

        // --- INFORMAÇÕES DE DEPENDÊNCIA (Lidas de arquivos estáticos) ---
        
        // 1. Versão do Node.js (Requisito lido do package.json)
        $nodeVersion = 'N/A';
        if (File::exists(base_path('package.json'))) {
            $packageJson = json_decode(File::get(base_path('package.json')), true);
            $nodeVersion = $packageJson['engines']['node'] ?? 'N/A (Requisito)';
        }

        // 2. Versão do Composer (Lido do composer.lock)
        $composerVersion = 'N/A';
        if (function_exists('shell_exec')) {
            // Tenta executar o comando no servidor
            $versionOutput = shell_exec('composer --version');
            
            // Processa a saída (ex: 'Composer version 2.7.5 2024-05-18...')
            if ($versionOutput && preg_match('/Composer version (\S+)/', $versionOutput, $matches)) {
                $composerVersion = $matches[1];
            } else {
                $composerVersion = 'Acesso shell falhou/bloqueado';
            }
        } else {
            $composerVersion = 'Função shell_exec desativada';
        }    

        $clientIp = $request->ip();

        $libreoffice = Tools::checkCliConversionTool();
        
        if(Session::has('user')) {
            return view('ajuda.sobreautenticado', [
                'phpVersion'     => $phpVersion,
                'laravelVersion' => $laravelVersion,
                'nodeVersion'    => $nodeVersion,
                'composerVersion'=> $composerVersion,
                'soservidor' => $SOsistema,
                'clientIp'=> $clientIp,
                'libreoffice'=> $libreoffice,
            ]);
        } else {
            return '';
        }
    }



   

}
