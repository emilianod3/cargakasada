<?php

namespace Database\Seeders;

use App\Models\UnidadeArq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadeArqSeeder extends Seeder
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
        UnidadeArq::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 
    }
}
