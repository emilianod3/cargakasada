<?php

namespace Database\Seeders;

use App\Models\TipoAcao;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TipoAcaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoAcao::truncate();
        TipoAcao::create(['id' => 1, 'tpidentificacao' => 'Incluir', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoAcao::create(['id' => 2, 'tpidentificacao' => 'Alterar', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoAcao::create(['id' => 3, 'tpidentificacao' => 'Excluir', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoAcao::create(['id' => 4, 'tpidentificacao' => 'Logar', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoAcao::create(['id' => 5, 'tpidentificacao' => 'Deslogar', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoAcao::create(['id' => 6, 'tpidentificacao' => 'Validação Usuário Logado', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoAcao::create(['id' => 7, 'tpidentificacao' => 'Indefinida', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoAcao::create(['id' => 8, 'tpidentificacao' => 'Iteração de Sistema', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoAcao::create(['id' => 9, 'tpidentificacao' => 'Falha', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);

    }
}
/*
SELECT CONCAT("TipoAcao::create(['id' => ", a.id,
", 'tpidentificacao' => '", a.tpidentificacao,
"', 'tpversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM tipoacao AS a
*/