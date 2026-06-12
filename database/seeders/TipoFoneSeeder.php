<?php
namespace Database\Seeders;

use App\Models\TipoFone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TipoFoneSeeder extends Seeder
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
        TipoFone::truncate();
        TipoFone::create(['id' => 1, 'tpfnidentificacao' => 'Casa', 'tpfnversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'fkidgestor' => $fkidgestorparam]);
        TipoFone::create(['id' => 2, 'tpfnidentificacao' => 'Particular', 'tpfnversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'fkidgestor' => $fkidgestorparam]);
        TipoFone::create(['id' => 3, 'tpfnidentificacao' => 'Celular', 'tpfnversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'fkidgestor' => $fkidgestorparam]);
        TipoFone::create(['id' => 4, 'tpfnidentificacao' => 'Comercial', 'tpfnversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'fkidgestor' => $fkidgestorparam]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

/*
SELECT CONCAT("TipoFone::create(['id' => ", a.id,
", 'tpfnidentificacao' => '", a.tpfnidentificacao,
"', 'tpfnversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM tipofone AS a

*/