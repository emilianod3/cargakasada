<?php

namespace Database\Seeders;

use App\Models\RamoAtividade;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RamoAtividadeSeeder extends Seeder
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
        RamoAtividade::truncate();
        RamoAtividade::create(['id' => 4, 'rtidentificacao' => 'AGRICULTURA', 'rtcnaeversao' => '2.2', 'rtcnaesecao' => 'A', 'rtcnaedivisao' => '', 'rtcnaegrupo' => '', 'rtcnaeclasse' => '', 'rtcnaesubclasse' => '', 'rtversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

/*
SELECT CONCAT("RamoAtividade::create(['id' => ", a.id,
", 'rtidentificacao' => '", a.rtidentificacao,
"', 'rtcnaeversao' => '", a.rtcnaeversao,
"', 'rtcnaesecao' => '", a.rtcnaesecao,
"', 'rtcnaedivisao' => '", a.rtcnaedivisao,
"', 'rtcnaegrupo' => '", a.rtcnaegrupo,
"', 'rtcnaeclasse' => '", a.rtcnaeclasse,
"', 'rtcnaesubclasse' => '", a.rtcnaesubclasse,
"', 'rtversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM ramoatividade AS a */