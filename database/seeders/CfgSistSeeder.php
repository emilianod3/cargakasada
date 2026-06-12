<?php

namespace Database\Seeders;

use App\Models\CfgSist;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CfgSistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=CfgSistSeeder
     * @return void
     */
	 
	 
	 
    public function run()
    {
		$fkidgestorparam = env('GESTOR', '1'); 
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        CfgSist::truncate();
        CfgSist::create(['fkidconfig' => 1, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 2, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 3, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 4, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 5, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 6, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 7, 'transtatus' => 1, 'valor1' => '8', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 8, 'transtatus' => 1, 'valor1' => '8', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 9, 'transtatus' => 1, 'valor1' => 'http://127.0.0.1:8282/site', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 10, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 11, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 12, 'transtatus' => 1, 'valor1' => '60', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 13, 'transtatus' => 1, 'valor1' => '7000', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 14, 'transtatus' => 1, 'valor1' => '8', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 15, 'transtatus' => 1, 'valor1' => '15', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 16, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 17, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 18, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 19, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 20, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 21, 'transtatus' => 1, 'valor1' => 'sim', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 22, 'transtatus' => 1, 'valor1' => '5', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 23, 'transtatus' => 1, 'valor1' => '2', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 24, 'transtatus' => 1, 'valor1' => 'Segundas-Feiras', 'valor2' => 'às Segundas-Feiras', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 25, 'transtatus' => 1, 'valor1' => 'Segundas-Feiras', 'valor2' => 'às Segundas-Feiras', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 26, 'transtatus' => 1, 'valor1' => 'Segunda à sexta-feira', 'valor2' => 'das 08:00h às 11:00h e das 13:00h às 17:00h', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 27, 'transtatus' => 1, 'valor1' => 'nao', 'valor2' => '', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
		CfgSist::create(['fkidconfig' => 28, 'transtatus' => 1, 'valor1' => '3', 'valor2' => '8', 'tranversao' => Carbon::now()->toDateTimeString(), 'fkidgestor' => $fkidgestorparam, 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);	
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
