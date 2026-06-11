<?php

namespace Database\Seeders;

use App\Models\Email;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmailSeeder extends Seeder
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
        Email::truncate();
        Email::create(['id' => 1, 'euemail' => 'controlnextdigital@gmail.com', 'fkidunico' => 1, 'emusarcomoprincipal' => 1, 'euanotacao' => 'Criação Automatizada', 'emversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');


    }
}
