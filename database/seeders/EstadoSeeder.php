<?php

namespace Database\Seeders;

use App\Models\Estado;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class EstadoSeeder extends Seeder
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
        Estado::truncate();
        Estado::create(['id' => 1, 'etidentificacao' => 'Acre', 'etsigla' => 'AC', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 2, 'etidentificacao' => 'Alagoas', 'etsigla' => 'AL', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1,'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 3, 'etidentificacao' => 'Amazonas', 'etsigla' => 'AM', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 4, 'etidentificacao' => 'Amapá', 'etsigla' => 'AP', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 5, 'etidentificacao' => 'Bahia', 'etsigla' => 'BA', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 6, 'etidentificacao' => 'Ceará', 'etsigla' => 'CE', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 7, 'etidentificacao' => 'Distrito Federal', 'etsigla' => 'DF', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 8, 'etidentificacao' => 'Espírito Santo', 'etsigla' => 'ES', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 9, 'etidentificacao' => 'Goiás', 'etsigla' => 'GO', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 10, 'etidentificacao' => 'Maranhão', 'etsigla' => 'MA', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 11, 'etidentificacao' => 'Minas Gerais', 'etsigla' => 'MG', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 12, 'etidentificacao' => 'Mato Grosso do Sul', 'etsigla' => 'MS', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 13, 'etidentificacao' => 'Mato Grosso', 'etsigla' => 'MT', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 14, 'etidentificacao' => 'Pará', 'etsigla' => 'PA', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 15, 'etidentificacao' => 'Paraíba', 'etsigla' => 'PB', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 16, 'etidentificacao' => 'Pernambuco', 'etsigla' => 'PE', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 17, 'etidentificacao' => 'Piauí', 'etsigla' => 'PI', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 18, 'etidentificacao' => 'Paraná', 'etsigla' => 'PR', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 19, 'etidentificacao' => 'Rio de Janeiro', 'etsigla' => 'RJ', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 20, 'etidentificacao' => 'Rio Grande do Norte', 'etsigla' => 'RN', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 21, 'etidentificacao' => 'Rondônia', 'etsigla' => 'RO', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 22, 'etidentificacao' => 'Roraima', 'etsigla' => 'RR', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 23, 'etidentificacao' => 'Rio Grande do Sul', 'etsigla' => 'RS', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 24, 'etidentificacao' => 'Santa Catarina', 'etsigla' => 'SC', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 25, 'etidentificacao' => 'Sergipe', 'etsigla' => 'SE', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 26, 'etidentificacao' => 'São Paulo', 'etsigla' => 'SP', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Estado::create(['id' => 27, 'etidentificacao' => 'Tocantins', 'etsigla' => 'TO', 'etversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
/*SELECT CONCAT("Estado::create(['id' => ", a.id,
", 'etidentificacao' => '", a.etidentificacao,
"', 'etsigla' => '", if(a.etsigla IS NULL , "", a.etsigla),
", 'etversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM estado AS a */