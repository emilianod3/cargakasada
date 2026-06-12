<?php

namespace Database\Seeders;

use App\Models\UnicoArq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UnicoArqSeeder extends Seeder
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
        UnicoArq::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 

    }
}
