<?php

namespace Database\Seeders;

use App\Models\TipoParentesco;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TipoParentescoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fkidgestorparam = env('GESTOR', '1');
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        TipoParentesco::truncate();
        TipoParentesco::create(['tpidentificacao' => 'Pai', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Mãe', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Avó', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Avô', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Tia', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Tio', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Sobrinho', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Tia', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Tia Avó', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Tio Avô', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Sogro', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Sogra', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Irmão', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Irmã', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Primo', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Prima', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Sobrinha', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Genro', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Nora', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Entiado', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Bisavó', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Entiada', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Tataraneto', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Bisneto', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Bisavô', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Trisavó', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Trisavô', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Tataraneta', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Cunhada', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoParentesco::create(['tpidentificacao' => 'Cunhado', 'fkidgestor' => $fkidgestorparam, 'tpstatus' => 1, 'tpversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
/*
SELECT CONCAT("TipoParentesco::create(['id' => ", a.id,
", 'tpidentificacao' => '", a.tpidentificacao,
"', 'tpstatus' => '", 1,
"', 'tpvversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM tipoparentesco AS a

*/