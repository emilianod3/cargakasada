<?php

namespace Database\Seeders;

use App\Models\Unidade;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadeSeeder extends Seeder
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
        Unidade::truncate();
        DB::table('unidade')->insert([
            'id' => 1,
            'undidentificacao' => 'Unidade de Implantação do Sistema',
            'undclienteprincipal' => 1,
            'undtexto' => '',
            'unnomefantasia' => 'Unidade de Implantação do Sistema',
            'undcep' => '',
            'undinscrmunicipal' => '',
            'unddatacadastro' => Carbon::now()->toDateTimeString(),
            'unddataasscontrato' => Carbon::now()->toDateTimeString(),
            'unddatainicontrato' => Carbon::now()->toDateTimeString(),
            'unddatafimcontrato' => null,
            'uncnpj' => '',
            'fkidramoatividade' => 0,
            'fkidcidade' => 0,
            'fkidgestor' => $fkidgestorparam,
            'undendereco' => null,
            'undbairro' => '',
            'undnumero' => '',
            'undcomplemento' => '',
            'unddesignacao' => '',
            'undinscrestadual' => '',
            'unhorariofuncionamentomanha' => '',
            'unhorariofuncionamentotarde' => '',
            'undobs' => null,
            'unlema' => null,
            'uncomochegar' => null,
            'ungeorreferencia' => null,
            'unhistoria' => null,
            'undadosgeograficos' => null,
            'unsiteativo' => 0,
            'untiposite' => 0,
            'unmodelosite' => 0,
            'undversao' => Carbon::now()->toDateTimeString(),
            'flagexibe' => 0,
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0,
            'flagcontrole' => 1
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 
    }
}
