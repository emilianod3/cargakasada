<?php

namespace Database\Seeders;

use App\Models\Cargo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CargoSeeder extends Seeder
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
        Cargo::truncate();
        Cargo::create(['cgidentificacao' => 'Presidente', 'cgstatus' => 1,  'fkidgestor' => $fkidgestorparam, 'cgversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        Cargo::create(['cgidentificacao' => 'Vice', 'cgstatus' => 1,  'fkidgestor' => $fkidgestorparam, 'cgversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        Cargo::create(['cgidentificacao' => 'Membro', 'cgstatus' => 1,  'fkidgestor' => $fkidgestorparam, 'cgversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
