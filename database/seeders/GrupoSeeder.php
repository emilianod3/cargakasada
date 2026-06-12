<?php

namespace Database\Seeders;

use App\Models\Grupo;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GrupoSeeder extends Seeder
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
        Grupo::truncate();
        DB::table('grupo')->insert([
            'id' => 1,
            'gidentificacao' => 'Gestor Geral do Sistema',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Criado por Carga Inicial do Sistema',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);
        DB::table('grupo')->insert([
            'id' => 2,
            'gidentificacao' => 'Gestor',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Criado por Carga Inicial do Sistema',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);
        DB::table('grupo')->insert([
            'id' => 3,
            'gidentificacao' => 'Visitantes',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Criado por Carga Inicial do Sistema',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);
        /*
        DB::table('grupo')->insert([
            'id' => 4,
            'gidentificacao' => 'Parlamentar',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Criado por Carga Inicial do Sistema',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);*/
        DB::table('grupo')->insert([
            'id' => 5,
            'gidentificacao' => 'Cliente-APP',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Criado por Carga Inicial do Sistema',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);
        DB::table('grupo')->insert([
            'id' => 6,
            'gidentificacao' => 'Profissional',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Criado por Carga Inicial do Sistema',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);
        /*
        DB::table('grupo')->insert([
            'id' => 7,
            'gidentificacao' => 'Executivo Municipal',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Criado por Carga Inicial do Sistema',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);*/
        DB::table('grupo')->insert([
            'id' => 8,
            'gidentificacao' => 'Externo',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Cadastros Externos ao Sistema como Portal e Afins',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);
        /*
        DB::table('grupo')->insert([
            'id' => 9,
            'gidentificacao' => 'Licitação',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Criado por Carga Inicial do Sistema',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);*/
        DB::table('grupo')->insert([
            'id' => 10,
            'gidentificacao' => 'Compras',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Criado por Carga Inicial do Sistema',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);
        DB::table('grupo')->insert([
            'id' => 11,
            'gidentificacao' => 'Notícias',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Criado por Carga Inicial do Sistema',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);
        DB::table('grupo')->insert([
            'id' => 12,
            'gidentificacao' => 'Ouvidoria',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Criado por Carga Inicial do Sistema',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);
        /*
        DB::table('grupo')->insert([
            'id' => 13,
            'gidentificacao' => 'Processo Digital',
            'gstatus' => 1,
            'gversao' => Carbon::now()->toDateTimeString(),
            'ganotation' => 'Criado por Carga Inicial do Sistema',
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);*/        
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
