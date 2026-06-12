<?php

namespace Database\Seeders;

use App\Models\UnidadeContrato;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UnidadeContratoSeeder extends Seeder
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
        UnidadeContrato::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 
    }
}
