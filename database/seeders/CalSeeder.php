<?php

namespace Database\Seeders;

use App\Models\Cal;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;



class CalSeeder extends Seeder
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
        Cal::truncate();
        
        
        Cal::create(["id" => 8, "clidentificacao" => "Diversos", "clobserve" => "", "clbase" => "", "clrota" => "/diversos", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 9, "clidentificacao" => "Administrativos", "clobserve" => "", "clbase" => "", "clrota" => "/administrativos", "cltipo" => 1, "clstatus" => 0, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 1, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 15, "clidentificacao" => "Classes Socias", "clobserve" => "", "clbase" => "", "clrota" => "/auxiliar/classes_sociais", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 16, "clidentificacao" => "Cargos", "clobserve" => "", "clbase" => "", "clrota" => "/auxiliar/cargos", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 17, "clidentificacao" => "Cidades", "clobserve" => "", "clbase" => "", "clrota" => "/auxiliar/cidades", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 1, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 19, "clidentificacao" => "Divisões", "clobserve" => "", "clbase" => "", "clrota" => "/cadastro/divisao", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 20, "clidentificacao" => "Estados Civis", "clobserve" => "", "clbase" => "", "clrota" => "/auxiliar/estados_civis", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 21, "clidentificacao" => "Funcionários", "clobserve" => "", "clbase" => "", "clrota" => "/cadastro/funcionarios", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 22, "clidentificacao" => "Usuários", "clobserve" => "", "clbase" => "", "clrota" => "/usuarios", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 28, "clidentificacao" => "Controle de Cals", "clobserve" => "", "clbase" => "", "clrota" => "/controle/cals", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 1, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 29, "clidentificacao" => "Tratamentos", "clobserve" => "", "clbase" => "", "clrota" => "/auxiliar/tratamentos", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 33, "clidentificacao" => "Tipos de Telefones", "clobserve" => "", "clbase" => "", "clrota" => "/auxiliar/tipos_telefones", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 40, "clidentificacao" => "Controle Acesso", "clobserve" => "", "clbase" => "", "clrota" => "/controleacesso", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 43, "clidentificacao" => "Tipos Categorias", "clobserve" => "", "clbase" => "", "clrota" => "/auxiliar/tipos_categorias", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 51, "clidentificacao" => "Cadastro Unificado", "clobserve" => "", "clbase" => "", "clrota" => "/cadastro/unificado", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 52, "clidentificacao" => "Relação de Logradouros", "clobserve" => "", "clbase" => "", "clrota" => "/auxiliar/logradouros", "cltipo" => 1, "clstatus" => 0, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 1, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 54, "clidentificacao" => "Controle de Configurações", "clobserve" => "", "clbase" => "", "clrota" => "/configuracoes", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 55, "clidentificacao" => "Rotas de Processos", "clobserve" => "", "clbase" => "", "clrota" => "/rotas", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 56, "clidentificacao" => "Cadastro de Unidades", "clobserve" => "", "clbase" => "", "clrota" => "/cadastro/unidades", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 57, "clidentificacao" => "Cadastro de Reuniões", "clobserve" => "", "clbase" => "", "clrota" => "/reunioes", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 58, "clidentificacao" => "Controle de Configurações do Sistema para Ser Usado por Usuários", "clobserve" => "", "clbase" => "", "clrota" => "/configuracao_usuarios", "cltipo" => 1, "clstatus" => 0, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 1, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 59, "clidentificacao" => "Controle de Perfil de Usuário", "clobserve" => "", "clbase" => "", "clrota" => "/cfguser", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 62, "clidentificacao" => "Conteúdos Para Site", "clobserve" => "", "clbase" => "", "clrota" => "/portal", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 64, "clidentificacao" => "Menu Inicial", "clobserve" => "", "clbase" => "", "clrota" => "/dashboard", "cltipo" => 1, "clstatus" => 0, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 1, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(['id' => 65, 'clidentificacao' => 'Informativo Sistema', 'clobserve' => '', 'clbase' => '', 'clrota' => '/sobre', 'cltipo' => 1, 'clstatus' => 1, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);
        Cal::create(["id" => 66, "clidentificacao" => "Logs", "clobserve" => "", "clbase" => "", "clrota" => "/controle/logsistema", "cltipo" => 1, "clstatus" => 0, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 1, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 68, "clidentificacao" => "Profissões", "clobserve" => "", "clbase" => "", "clrota" => "/auxiliar/profissoes", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 69, "clidentificacao" => "Ramos de Atividade", "clobserve" => "", "clbase" => "", "clrota" => "/auxiliar/ramoatividades", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 70, "clidentificacao" => "Escolaridades", "clobserve" => "", "clbase" => "", "clrota" => "/auxiliar/escolaridades", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 100, "clidentificacao" => "Tipos de Alterações", "clobserve" => "", "clbase" => "", "clrota" => "/auxiliar/tipos_alteracoes", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 124, "clidentificacao" => "Menus", "clobserve" => "", "clbase" => "", "clrota" => "/controle/menus", "cltipo" => 1, "clstatus" => 0, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 1, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 131, "clidentificacao" => "Certificados", "clobserve" => "", "clbase" => "", "clrota" => "/certificados", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 132, "clidentificacao" => "Assinatura Digital", "clobserve" => "", "clbase" => "", "clrota" => "/assinaturadigital", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(["id" => 135, "clidentificacao" => "Estoque", "clobserve" => "", "clbase" => "", "clrota" => "/estoque", "cltipo" => 1, "clstatus" => 1, "clversao" => Carbon::now()->toDateTimeString(), "flagdelete" => 0, "flagatualiza" => 0, 'flagcontrole' => 1, 'flaguser' => 1, 'flagcontrole' => 1]);
        Cal::create(['id' => 141, 'clidentificacao' => 'Termos de Uso', 'clobserve' => '', 'clbase' => '', 'clrota' => '/termos/uso', 'cltipo' => 1, 'clstatus' => 0, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);
        Cal::create(['id' => 142, 'clidentificacao' => 'Versões', 'clobserve' => '', 'clbase' => '', 'clrota' => '/versoes', 'cltipo' => 1, 'clstatus' => 0, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);
        Cal::create(['id' => 143, 'clidentificacao' => 'Reportar Problema', 'clobserve' => '', 'clbase' => '', 'clrota' => '/reportar-problema', 'cltipo' => 1, 'clstatus' => 0, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);
        Cal::create(['id' => 144, 'clidentificacao' => 'Contato', 'clobserve' => '', 'clbase' => '', 'clrota' => '/contato', 'cltipo' => 1, 'clstatus' => 0, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);
        Cal::create(['id' => 145, 'clidentificacao' => 'Cadastro Gestor', 'clobserve' => '', 'clbase' => '', 'clrota' => '/sistema/gestor', 'cltipo' => 1, 'clstatus' => 1, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);
        Cal::create(['id' => 152, 'clidentificacao' => 'Categorias', 'clobserve' => '', 'clbase' => '', 'clrota' => '/auxiliar/categorias', 'cltipo' => 1, 'clstatus' => 1, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);
        Cal::create(['id' => 193, 'clidentificacao' => 'Tipo Arquivo', 'clobserve' => '', 'clbase' => '', 'clrota' => '/auxiliar/tipos_arquivos', 'cltipo' => 1, 'clstatus' => 1, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);
        Cal::create(['id' => 194, 'clidentificacao' => 'Tipo Parentesco', 'clobserve' => '', 'clbase' => '', 'clrota' => '/auxiliar/tipos_parentescos', 'cltipo' => 1, 'clstatus' => 1, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);
        Cal::create(['id' => 196, 'clidentificacao' => 'Tipo Etapa', 'clobserve' => '', 'clbase' => '', 'clrota' => '/auxiliar/tipos_etapas', 'cltipo' => 1, 'clstatus' => 1, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);
        Cal::create(['id' => 197, 'clidentificacao' => 'Auditoria', 'clobserve' => '', 'clbase' => '', 'clrota' => '/controle/auditoria', 'cltipo' => 1, 'clstatus' => 1, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);
        Cal::create(['id' => 201, 'clidentificacao' => 'Modelos de Documentos', 'clobserve' => '', 'clbase' => '', 'clrota' => '/modelosdocumentos', 'cltipo' => 1, 'clstatus' => 1, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);
		Cal::create(['id' => 204, 'clidentificacao' => 'Meus Dados', 'clobserve' => '', 'clbase' => '', 'clrota' => '/meusdados', 'cltipo' => 1, 'clstatus' => 1, 'clversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

/*SELECT CONCAT("Cal::create(['id' => ", a.id,
", 'clidentificacao' => '", if(a.clidentificacao IS NULL , "", a.clidentificacao),
"', 'clobserve' => '", if(a.clobserve IS NULL , "", a.clobserve),
"', 'clbase' => '", a.clbase,
"', 'clrota' => '", a.clrota,
"', 'cltipo' => ", a.cltipo,
", 'clstatus' => ", a.clstatus,
", 'clversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0, 'flagcontrole' => 1]);") 
FROM cal AS a */