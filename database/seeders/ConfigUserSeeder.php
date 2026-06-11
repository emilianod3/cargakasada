<?php

namespace Database\Seeders;

use App\Models\ConfigUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConfigUserSeeder extends Seeder
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
        ConfigUser::truncate();
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 1, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 2, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 3, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 6, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 5, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 8, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 7, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 10, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 11, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 12, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 17, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 18, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 19, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);
        ConfigUser::create([ 'fkidcal' => null, 'fkidusuario' => 1, 'fkidconfigforuser' => 20, 'status' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);

    }
}

/*
SELECT CONCAT("ConfigUser::create(['id' => ", a.id,
", 'fkidcal' => ", a.fkidcal,
", 'fkidusuario' => ", a.fkidusuario,
", 'fkidconfigforuser' => ", a.fkidconfigforuser,
", 'status' => ", a.status,
", 'versao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM configuser AS a

*/