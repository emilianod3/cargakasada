<?php

namespace Database\Seeders;

use App\Models\TipoPermissao;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TipoPermissaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TipoPermissao::truncate();
        TipoPermissao::create(['id' => 1, 'tpidentificacao' => 'Consultar', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoPermissao::create(['id' => 2, 'tpidentificacao' => 'Inserir', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoPermissao::create(['id' => 3, 'tpidentificacao' => 'Alterar', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoPermissao::create(['id' => 4, 'tpidentificacao' => 'Apagar', 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);

    }
}
/*
SELECT CONCAT("TipoPermissao::create(['id' => ", a.id,
", 'tpidentificacao' => '", a.tpidentificacao,
"', 'tpversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM tipopermissao AS a
*/