<?php

namespace Database\Seeders;

use App\Models\EstadoCivil;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EstadoCivilSeeder extends Seeder
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
        EstadoCivil::truncate();
        EstadoCivil::create(['id' => 1, 'ecidentificacao' => 'Casado', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        EstadoCivil::create(['id' => 2, 'ecidentificacao' => 'Solteiro', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        EstadoCivil::create(['id' => 3, 'ecidentificacao' => 'Divorciado', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        EstadoCivil::create(['id' => 4, 'ecidentificacao' => 'Separado', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        EstadoCivil::create(['id' => 5, 'ecidentificacao' => 'Viúvo', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        EstadoCivil::create(['id' => 6, 'ecidentificacao' => 'Amasiado', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        EstadoCivil::create(['id' => 7, 'ecidentificacao' => 'União Estável', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
/*
SELECT CONCAT("EstadoCivil::create(['id' => ", a.id,
", 'ecidentificacao' => '", a.ecidentificacao,
"', 'ecversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM estadocivil AS a */