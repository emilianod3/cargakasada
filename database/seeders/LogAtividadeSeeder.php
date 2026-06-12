<?php

namespace Database\Seeders;

use App\Models\LogAtividade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LogAtividadeSeeder extends Seeder
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
        LogAtividade::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 

        LogAtividade::create([
            'id'      => 1,
			'fkidusuario' => 0,
			'lgonde' => 'Carga do Sistema',
			'lgtexto' => 'Carga Inicial',
			'fktipoacao' => 0,
            'idregistro' => 0,
            'llocal' => 'Login',
            'lip' => '127.0.0.1',
            'lagent' => 'Chrome',
			'lgversao' => Carbon::now()->toDateTimeString(),
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0
        ]);
    }
}
    