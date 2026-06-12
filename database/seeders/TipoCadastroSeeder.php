<?php

namespace Database\Seeders;

use App\Models\TipoCadastro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TipoCadastroSeeder extends Seeder
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
        TipoCadastro::truncate();
        TipoCadastro::create(['id' => 1, 'tpcidentificacao' => 'Pessoa Física', 'tpcversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        TipoCadastro::create(['id' => 2, 'tpcidentificacao' => 'Pessoa Jurídica', 'tpcversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
}

/*
SELECT CONCAT("TipoCadastro::create(['id' => ", a.id,
", 'tpcidentificacao' => '", a.tpcidentificacao,
"', 'tpcversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM tipocadastro AS a
*/