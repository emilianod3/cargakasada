<?php

namespace App\Http\Controllers\Core;

use App\Models\Estado;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EstadoController extends Controller
{
    public function getAllSession()
    {
        $registros = Estado::orderBy('etidentificacao', 'asc')->get();
        return json_encode($registros);
    }

}
