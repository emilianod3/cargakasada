<?php

namespace Database\Seeders;

use App\Models\Endereco;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EnderecoSeeder extends Seeder
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
        Endereco::truncate();
        Endereco::create(['id' => 1, 'endcep' => '69900-050', 'fkidcidade' => '94', 'endtipologradouro' => 'Praça', 'endlogradouro' => 'Bandeira', 'endbairro' => 'Centro', 'endstatus' => '1', 'endversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'fkidgestor' => $fkidgestorparam]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
/*
SELECT CONCAT("Endereco::create(['id' => ", a.id,
", 'endcep' => '", a.endcep,
"', 'fkidcidade' => '", a.fkidcidade,
"', 'endtipologradouro' => '", a.endtipologradouro,
"', 'endlogradouro' => '", a.endlogradouro,
"', 'endbairro' => '", a.endbairro,
"', 'endstatus' => '", a.endstatus,
"', 'endversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM endereco AS a 

*/