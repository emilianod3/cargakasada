<?php

namespace Database\Seeders;

use App\Models\Gestor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GestorSeeder extends Seeder
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
        Gestor::truncate();
        Gestor::create(['id' => 1, 'identificacao' => 'Cliente Inicial Sistema', 'nomefantasia' => 'Sistema', 'lema' => '', 'datacadastro' => Carbon::now()->toDateTimeString(), 'cnpj' => '', 'fkidcidade' => 1, 'endereco' => '', 'cep' => '', 'fkidramoatividade' => 1, 'bairro' => '', 'numero' => '', 'complemento' => '', 'horariofuncionamentomanha' => '', 'horariofuncionamentotarde' => '', 'obs' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagexibe' => 1, 'flagdelete' => 0, 'flagatualiza' => 0, 'flagcontrole' => 1, 'flaguser' => 0]);
        Gestor::create(['id' => 45007720000117, 'identificacao' => 'Cliente Inicial Sistema', 'nomefantasia' => 'Sistema', 'lema' => '', 'datacadastro' => Carbon::now()->toDateTimeString(), 'cnpj' => '', 'fkidcidade' => 1, 'endereco' => '', 'cep' => '', 'fkidramoatividade' => 1, 'bairro' => '', 'numero' => '', 'complemento' => '', 'horariofuncionamentomanha' => '', 'horariofuncionamentotarde' => '', 'obs' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagexibe' => 1, 'flagdelete' => 0, 'flagatualiza' => 0, 'flagcontrole' => 1, 'flaguser' => 0]);
        Gestor::create(['id' => 12345670000123, 'identificacao' => 'Gestor Inicial Sistema', 'nomefantasia' => 'Gestor 2', 'lema' => '', 'datacadastro' => Carbon::now()->toDateTimeString(), 'cnpj' => '', 'fkidcidade' => 1, 'endereco' => '', 'cep' => '', 'fkidramoatividade' => 1, 'bairro' => '', 'numero' => '', 'complemento' => '', 'horariofuncionamentomanha' => '', 'horariofuncionamentotarde' => '', 'obs' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagexibe' => 1, 'flagdelete' => 0, 'flagatualiza' => 0, 'flagcontrole' => 1, 'flaguser' => 0]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 
    }
}
