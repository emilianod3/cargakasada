<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CargaInicialSeeder extends Seeder
{
    public function run()
    {
        $fkidgestorparam = env('GESTOR', '1');
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        $this->call([
            EstadoSeeder::class,
            //CidadeSeeder::class,
            EnderecoSeeder::class,
        ]);

        $this->call([
            ClasseSocialSeeder::class,
            GestorSeeder::class,
        ]);      

        $this->call([
            RamoAtividadeSeeder::class,
            EscolaridadeSeeder::class,
            EstadoCivilSeeder::class,
            TratamentoSeeder::class,
            TipoAcaoSeeder::class,
            TipoCadastroSeeder::class,
            TipoFoneSeeder::class,
            TipoParentescoSeeder::class,
            TipoPermissaoSeeder::class,
            TipoRacaSeeder::class,
            TipoArqSeeder::class,
        ]);

        $this->call([
            GrupoSeeder::class,
            UsuarioSeeder::class,
            // Permissões e Menus
            CalSeeder::class,
            CalPermissaoSeeder::class,
            ColumnCalSeeder::class,
            MenuSeeder::class,
            // Logs e Dados Únicos
            //LogAtividadeSeeder::class,
            UnicoSeeder::class,
            UnicoArqSeeder::class,
            EmailSeeder::class,
            TipoFoneSeeder::class,
            FoneSeeder::class,
            PassRecoverySeeder::class,
        ]);

        // 2. CONFIGURAÇÕES GERAIS E DE USUÁRIO
        $this->call([
            ConfigSeeder::class,
            ConfigForUserSeeder::class,
            CfgSistSeeder::class,
            ConfigUserSeeder::class,
            CfgUserCalSeeder::class,
            BuscaSalvaSeeder::class,
        ]);

        // 3. UNIDADES E CONTRATOS
        $this->call([
            UnidadeSeeder::class,
            EmailUnidadeSeeder::class,
            FoneUnidadeSeeder::class,
            UnidadeArqSeeder::class,
            UnidadeContratoSeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
