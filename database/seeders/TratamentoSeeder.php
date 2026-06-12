<?php

namespace Database\Seeders;

use App\Models\Tratamento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TratamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tratamento::truncate();
        Tratamento::create(['id' => 1, 'tidentificacao' => 'Sr.', 'tversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        Tratamento::create(['id' => 2, 'tidentificacao' => 'Sra.', 'tversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        Tratamento::create(['id' => 3, 'tidentificacao' => 'Exmo.', 'tversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        Tratamento::create(['id' => 4, 'tidentificacao' => 'Ilmo.', 'tversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);

    }

}
/*
SELECT CONCAT("Tratamento::create(['id' => ", a.id,
", 'tidentificacao' => '", a.tidentificacao,
"', 'tversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM tratamento AS a
*/