<?php

namespace Database\Seeders;

use App\Models\ClasseSocial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClasseSocialSeeder extends Seeder
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
        ClasseSocial::truncate();
        ClasseSocial::create(['id' => 1,'clscidentificacao' => 'Classe A', 'clscversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);//Classe A: mais de 15 salários mínimos
        ClasseSocial::create(['id' => 2,'clscidentificacao' => 'Classe B', 'clscversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);//Classe B: de 5 a 15 salários mínimos
        ClasseSocial::create(['id' => 3,'clscidentificacao' => 'Classe C', 'clscversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);//Classe C: de 3 a 5 salários mínimos
        ClasseSocial::create(['id' => 4,'clscidentificacao' => 'Classe D', 'clscversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);//Classe D: de 1 a 3 salários mínimos
        ClasseSocial::create(['id' => 5,'clscidentificacao' => 'Classe E', 'clscversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);//Classe E: até 1 salário mínimo 
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 
    }
}

