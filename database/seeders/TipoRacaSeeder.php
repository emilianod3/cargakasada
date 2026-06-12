<?php

namespace Database\Seeders;

use App\Models\TipoRaca;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TipoRacaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fkidgestorparam = env('GESTOR', '1');
        TipoRaca::truncate();
        TipoRaca::create(['id' => 1, 'fkidgestor' => $fkidgestorparam, 'tridentificacao' => 'Branca', 'trversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoRaca::create(['id' => 2, 'fkidgestor' => $fkidgestorparam, 'tridentificacao' => 'Amarela', 'trversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoRaca::create(['id' => 3, 'fkidgestor' => $fkidgestorparam, 'tridentificacao' => 'Parda', 'trversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoRaca::create(['id' => 4, 'fkidgestor' => $fkidgestorparam, 'tridentificacao' => 'Negra', 'trversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);

    }

}
/*
SELECT CONCAT("TipoRaca::create(['id' => ", a.id,
", 'tridentificacao' => '", a.tridentificacao,
"', 'trversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM tiporaca AS a

*/