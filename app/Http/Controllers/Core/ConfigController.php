<?php

namespace App\Http\Controllers\Core;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Config;
use Illuminate\Support\Facades\DB;

class ConfigController extends Controller
{
    public function getAllSession(){
        /*
        $query = Config::where('id', '>', 0);
        $registros = $query->get();     
        return json_encode($registros);*/

        $query = Config::select([DB::raw('config.id, config.identificacao, config.status,
        config.classificacao, config.valor1, config.valor2, config.flagexibe, config.exemplo')]);
        $registros = $query->groupBy('config.id')->get();
        return json_encode($registros);
    }


}
