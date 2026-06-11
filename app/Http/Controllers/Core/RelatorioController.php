<?php

namespace App\Http\Controllers\Core;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RelatorioController extends Controller
{
    public function relatorio($arquivo, $extensao)
    {
        $relatorioFile = public_path('storage/cache/relatorios/'.$arquivo.'.'.$extensao);
        if(file_exists($relatorioFile)) {
            $file = file_get_contents($relatorioFile);
            unlink($relatorioFile);
            return response($file, 200)
                ->header('Cache-Control', 'no-cache private')
                ->header('Content-Type', 'application/'.$extensao)
                ->header('Content-Disposition', 'inline; filename="'.rand().date("YmdHis").'.'.$extensao.'"')
                ->header('Content-Transfer-Encoding', 'binary');
        }else{
            //return redirect()->route('aviso');         
            return redirect()->route('errorautenticado')->with(['coderro' => '', 'titulo' => 'Arquivo não Encontrado', 'mensagem1' => '', 'mensagem2' => '']);
            //return redirect()->route('errorautenticado')->with(array('pages' => 'asdasdasdasd')); 
        }

    }

}
