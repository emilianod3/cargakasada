<?php

namespace Database\Seeders;

use App\Models\CalPermissao;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CalPermissaoSeeder extends Seeder
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
        CalPermissao::truncate();


        CalPermissao::create(["fkidcal" => 8, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 9, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 15, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 16, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 17, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 19, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 20, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 21, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 22, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 28, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 29, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 33, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 40, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 43, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 51, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 52, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 54, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 55, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 56, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 57, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 58, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 59, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 62, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 64, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 65, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 66, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 68, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 69, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 70, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 100, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 124, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 131, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 132, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 135, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 141, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 142, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 143, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 144, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 145, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 152, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 184, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 193, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 194, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 196, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 197, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 201, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        CalPermissao::create(["fkidcal" => 204, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 1, "flagcontrole" => 1]);
        





        
        CalPermissao::create(["fkidcal" => 64, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 2]);
        CalPermissao::create(["fkidcal" => 141, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 2]);
        CalPermissao::create(["fkidcal" => 142, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 2]);
        CalPermissao::create(["fkidcal" => 143, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 2]);
        CalPermissao::create(["fkidcal" => 144, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 2]);
        CalPermissao::create(["fkidcal" => 40, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 2]);
        CalPermissao::create(["fkidcal" => 59, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 2]);
        CalPermissao::create(["fkidcal" => 17, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 2]);
        CalPermissao::create(["fkidcal" => 51, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 2]);
        CalPermissao::create(["fkidcal" => 52, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 2]);




        CalPermissao::create(["fkidcal" => 64, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 3]);
        CalPermissao::create(["fkidcal" => 141, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 3]);
        CalPermissao::create(["fkidcal" => 142, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 3]);
        CalPermissao::create(["fkidcal" => 143, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 3]);
        CalPermissao::create(["fkidcal" => 144, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 3]);
        CalPermissao::create(["fkidcal" => 176, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 3]);
        CalPermissao::create(["fkidcal" => 59, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 3]);
        


        CalPermissao::create(["fkidcal" => 8, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 9, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 15, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 16, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 17, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 19, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 20, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 21, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 22, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 28, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 29, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 33, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 40, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 43, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 51, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 52, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 54, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 55, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 56, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 57, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 58, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 59, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 62, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 64, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 65, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 66, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 68, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 69, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 100, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 124, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 131, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 132, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 135, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 141, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 142, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 143, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 144, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 145, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 193, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 194, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 196, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);
        CalPermissao::create(["fkidcal" => 197, "cppermissao" => "11:11:11:11:00", "fkidgrupo" => 6]);

        



        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 
    }
}

/*SELECT CONCAT("CalPermissao::create(['id' => ", a.id,
", 'cppermissao' => '", if(a.cppermissao IS NULL , "11:11:11:00:00", a.cppermissao),
"', 'fkidcal' => ", a.fkidcal,
", 'fkidgrupo' => ", a.fkidgrupo,
", 'cpversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM calpermissao AS a*/