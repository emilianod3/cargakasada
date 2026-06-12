<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuarioSeeder extends Seeder
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
        Usuario::truncate();
        DB::table('usuario')->insert([
            'id' => 1,
            'ulogin' => 'sistema',
            'upassword' => bcrypt('Sistem@'),
            'ustatus' => 1,
            'uanotation' => 'Criado por Carga Inicial do Sistema',
            'udatacadastro' => Carbon::now()->toDateTimeString(),
            'udatatermosuso' => Carbon::now()->toDateTimeString(),
            'fkidunico' => 1,
            'fkidgrupo' => 1,
            'uhash' => '',
            'uaceitetermosuso' => 1,
            'udataaceitetermosuso' => Carbon::now()->toDateTimeString(),
            'usolicitalocalizacao' => 1,
            'ugestor' => 1, // 0 = ususario comum 1 = Usuario gestor
            'fkidgestor' => $fkidgestorparam,
            'uversao' => Carbon::now()->toDateTimeString(),
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flagcontrole' => 1,
            'flaguser' => 0
        ]);
        DB::table('usuario')->insert([
            'id' => 2,
            'ulogin' => 'drummond@teste.com',
            'upassword' => bcrypt('Sistem@'),
            'ustatus' => 1,
            'uanotation' => 'Criado por Carga Inicial do Sistema',
            'udatacadastro' => Carbon::now()->toDateTimeString(),
            'udatatermosuso' => Carbon::now()->toDateTimeString(),
            'fkidunico' => 2,
            'fkidgrupo' => 6,
            'uhash' => '',
            'uaceitetermosuso' => 1,
            'udataaceitetermosuso' => Carbon::now()->toDateTimeString(),
            'usolicitalocalizacao' => 1,
            'ugestor' => 0, // 0 = ususario comum 1 = Usuario gestor
            'fkidgestor' => $fkidgestorparam,
            'uversao' => Carbon::now()->toDateTimeString(),
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0
        ]);
        DB::table('usuario')->insert([
            'id' => 3,
            'ulogin' => 'clarice@teste.com',
            'upassword' => bcrypt('Sistem@'),
            'ustatus' => 1,
            'uanotation' => 'Criado por Carga Inicial do Sistema',
            'udatacadastro' => Carbon::now()->toDateTimeString(),
            'udatatermosuso' => Carbon::now()->toDateTimeString(),
            'fkidunico' => 3,
            'fkidgrupo' => 6,
            'uhash' => '',
            'uaceitetermosuso' => 1,
            'udataaceitetermosuso' => Carbon::now()->toDateTimeString(),
            'usolicitalocalizacao' => 1,
            'ugestor' => 0, // 0 = ususario comum 1 = Usuario gestor
            'fkidgestor' => $fkidgestorparam,
            'uversao' => Carbon::now()->toDateTimeString(),
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0
        ]);
        DB::table('usuario')->insert([
            'id' => 4,
            'ulogin' => 'erico@teste.com',
            'upassword' => bcrypt('Sistem@'),
            'ustatus' => 1,
            'uanotation' => 'Criado por Carga Inicial do Sistema',
            'udatacadastro' => Carbon::now()->toDateTimeString(),
            'udatatermosuso' => Carbon::now()->toDateTimeString(),
            'fkidunico' => 4,
            'fkidgrupo' => 6,
            'uhash' => '',
            'uaceitetermosuso' => 1,
            'udataaceitetermosuso' => Carbon::now()->toDateTimeString(),
            'usolicitalocalizacao' => 1,
            'ugestor' => 0, // 0 = ususario comum 1 = Usuario gestor
            'fkidgestor' => $fkidgestorparam,
            'uversao' => Carbon::now()->toDateTimeString(),
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0
        ]);
        DB::table('usuario')->insert([
            'id' => 5,
            'ulogin' => 'cunha@teste.com',
            'upassword' => bcrypt('Sistem@'),
            'ustatus' => 1,
            'uanotation' => 'Criado por Carga Inicial do Sistema',
            'udatacadastro' => Carbon::now()->toDateTimeString(),
            'udatatermosuso' => Carbon::now()->toDateTimeString(),
            'fkidunico' => 5,
            'fkidgrupo' => 6,
            'uhash' => '',
            'uaceitetermosuso' => 1,
            'udataaceitetermosuso' => Carbon::now()->toDateTimeString(),
            'usolicitalocalizacao' => 1,
            'ugestor' => 0, // 0 = ususario comum 1 = Usuario gestor
            'fkidgestor' => $fkidgestorparam,
            'uversao' => Carbon::now()->toDateTimeString(),
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0
        ]);
        DB::table('usuario')->insert([
            'id' => 6,
            'ulogin' => 'cliente6@teste.com',
            'upassword' => bcrypt('Sistem@'),
            'ustatus' => 1,
            'uanotation' => 'Criado por Carga Inicial do Sistema',
            'udatacadastro' => Carbon::now()->toDateTimeString(),
            'udatatermosuso' => Carbon::now()->toDateTimeString(),
            'fkidunico' => 6,
            'fkidgrupo' => 5,
            'uhash' => '',
            'uaceitetermosuso' => 1,
            'udataaceitetermosuso' => Carbon::now()->toDateTimeString(),
            'usolicitalocalizacao' => 1,
            'ugestor' => 0, // 0 = ususario comum 1 = Usuario gestor
            'fkidgestor' => $fkidgestorparam,
            'uversao' => Carbon::now()->toDateTimeString(),
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0
        ]);
        DB::table('usuario')->insert([
            'id' => 7,
            'ulogin' => 'cliente7@teste.com',
            'upassword' => bcrypt('Sistem@'),
            'ustatus' => 1,
            'uanotation' => 'Criado por Carga Inicial do Sistema',
            'udatacadastro' => Carbon::now()->toDateTimeString(),
            'udatatermosuso' => Carbon::now()->toDateTimeString(),
            'fkidunico' => 7,
            'fkidgrupo' => 5,
            'uhash' => '',
            'uaceitetermosuso' => 1,
            'udataaceitetermosuso' => Carbon::now()->toDateTimeString(),
            'usolicitalocalizacao' => 1,
            'ugestor' => 0, // 0 = ususario comum 1 = Usuario gestor
            'fkidgestor' => $fkidgestorparam,
            'uversao' => Carbon::now()->toDateTimeString(),
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0
        ]);
        DB::table('usuario')->insert([
            'id' => 8,
            'ulogin' => 'cliente8@teste.com',
            'upassword' => bcrypt('Sistem@'),
            'ustatus' => 1,
            'uanotation' => 'Criado por Carga Inicial do Sistema',
            'udatacadastro' => Carbon::now()->toDateTimeString(),
            'udatatermosuso' => Carbon::now()->toDateTimeString(),
            'fkidunico' => 8,
            'fkidgrupo' => 5,
            'uhash' => '',
            'uaceitetermosuso' => 1,
            'udataaceitetermosuso' => Carbon::now()->toDateTimeString(),
            'usolicitalocalizacao' => 1,
            'ugestor' => 0, // 0 = ususario comum 1 = Usuario gestor
            'fkidgestor' => $fkidgestorparam,
            'uversao' => Carbon::now()->toDateTimeString(),
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0
        ]);
        DB::table('usuario')->insert([
            'id' => 9,
            'ulogin' => 'cliente9@teste.com',
            'upassword' => bcrypt('Sistem@'),
            'ustatus' => 1,
            'uanotation' => 'Criado por Carga Inicial do Sistema',
            'udatacadastro' => Carbon::now()->toDateTimeString(),
            'udatatermosuso' => Carbon::now()->toDateTimeString(),
            'fkidunico' => 9,
            'fkidgrupo' => 5,
            'uhash' => '',
            'uaceitetermosuso' => 1,
            'udataaceitetermosuso' => Carbon::now()->toDateTimeString(),
            'usolicitalocalizacao' => 1,
            'ugestor' => 0, // 0 = ususario comum 1 = Usuario gestor
            'fkidgestor' => $fkidgestorparam,
            'uversao' => Carbon::now()->toDateTimeString(),
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0
        ]);
        DB::table('usuario')->insert([
            'id' => 10,
            'ulogin' => 'cliente10@teste.com',
            'upassword' => bcrypt('Sistem@'),
            'ustatus' => 1,
            'uanotation' => 'Criado por Carga Inicial do Sistema',
            'udatacadastro' => Carbon::now()->toDateTimeString(),
            'udatatermosuso' => Carbon::now()->toDateTimeString(),
            'fkidunico' => 10,
            'fkidgrupo' => 5,
            'uhash' => '',
            'uaceitetermosuso' => 1,
            'udataaceitetermosuso' => Carbon::now()->toDateTimeString(),
            'usolicitalocalizacao' => 1,
            'ugestor' => 0, // 0 = ususario comum 1 = Usuario gestor
            'fkidgestor' => $fkidgestorparam,
            'uversao' => Carbon::now()->toDateTimeString(),
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0
        ]);
        DB::table('usuario')->insert([
            'id' => 11,
            'ulogin' => 'cliente11@teste.com',
            'upassword' => bcrypt('Sistem@'),
            'ustatus' => 1,
            'uanotation' => 'Criado por Carga Inicial do Sistema',
            'udatacadastro' => Carbon::now()->toDateTimeString(),
            'udatatermosuso' => Carbon::now()->toDateTimeString(),
            'fkidunico' => 11,
            'fkidgrupo' => 5,
            'uhash' => '',
            'uaceitetermosuso' => 1,
            'udataaceitetermosuso' => Carbon::now()->toDateTimeString(),
            'usolicitalocalizacao' => 1,
            'ugestor' => 0, // 0 = ususario comum 1 = Usuario gestor
            'fkidgestor' => $fkidgestorparam,
            'uversao' => Carbon::now()->toDateTimeString(),
            'flagdelete' => 0,
            'flagatualiza' => 1,
            'flaguser' => 0
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        /*
        User::create([
            'name'      => 'Carlos Ferreira',
            'email'     => 'carlos@especializati.com.br',
            'password'  => bcrypt('MinhaSenhaAqui'),
        ]);*/

    }
}
