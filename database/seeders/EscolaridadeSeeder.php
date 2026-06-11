<?php

namespace Database\Seeders;

use App\Models\Escolaridade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EscolaridadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Escolaridade::truncate();
        Escolaridade::create(['id' => 1, 'ecidentificacao' => 'Ensino fundamental completo', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Escolaridade::create(['id' => 2, 'ecidentificacao' => 'Ensino fundamental incompleto', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Escolaridade::create(['id' => 3, 'ecidentificacao' => 'Ensino médio completo', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Escolaridade::create(['id' => 4, 'ecidentificacao' => 'Ensino médio incompleto', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Escolaridade::create(['id' => 5, 'ecidentificacao' => 'Superior completo', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Escolaridade::create(['id' => 6, 'ecidentificacao' => 'Pós-graduado', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Escolaridade::create(['id' => 7, 'ecidentificacao' => 'Mestrado', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Escolaridade::create(['id' => 8, 'ecidentificacao' => 'Doutorado', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Escolaridade::create(['id' => 9, 'ecidentificacao' => 'Pós-Doutorado', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Escolaridade::create(['id' => 10, 'ecidentificacao' => 'Analfabeto', 'ecversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
    }
}
/*
SELECT CONCAT("Escolaridade::create(['id' => ", a.id,
", 'ecidentificacao' => '", a.ecidentificacao,
"', 'ecversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM escolaridade AS a
*/