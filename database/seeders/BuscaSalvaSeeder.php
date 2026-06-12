<?php

namespace Database\Seeders;

use App\Models\Buscasalva;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BuscaSalvaSeeder extends Seeder
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
        Buscasalva::truncate();        
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}


/*
SELECT CONCAT("BuscaSalva::create(['id' => ", a.id,
", 'bsdescricao' => '", a.bsdescricao,
"', 'bscampos' => '", a.bscampos,
"', 'subcalaux' => '", a.subcalaux,
"', 'subcalaux1' => '", a.subcalaux1,
", 'bsfixada' => '", a.bsfixada,
", 'fkidcal' => '", a.fkidcal,
", 'fkidusuario' => '", a.fkidusuario,
", 'bspublico' => '", a.bspublico,
", 'bsversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM buscasalva AS a 

*/