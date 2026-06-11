<?php

namespace Database\Seeders;

use App\Models\ConfigForUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConfigForUserSeeder extends Seeder
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
        ConfigForUser::truncate();
        ConfigForUser::create(['id' => 1, 'identificacao' => 'Exibir tooltip ao passar o mouse', 'exemplo' => 'Ao passar o mouse sobre algum componente, lista ou campo que tenha uma descrição exibir o tooltip.', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 2, 'identificacao' => 'Uso avançado do Sistema', 'exemplo' => 'Uso avançado do Sistema para usuários que não necessitam de ajuda', 'tipodado' => 1, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 3, 'identificacao' => 'Alteração do Número de Protocolo na Inserção', 'exemplo' => 'Alteração do Número de Protocolo na inserção  de novo protocolo', 'tipodado' => 1, 'status' => 1, 'classificacao' => 3, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 4, 'identificacao' => 'Administrador do Protocolo', 'exemplo' => 'Pode criar o protocolo completo, aceitar ou recusar pedidos de protocolos e protocolos pendentes.', 'tipodado' => 2, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => 'sim', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 5, 'identificacao' => 'Alterar Solicitante/Interessado no Protocolo', 'exemplo' => 'Permite ao usuário para alterar o solicitante/Interessado ao protocolo.', 'tipodado' => 2, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 6, 'identificacao' => 'Interagir ao Protocolo de Segundo Nível', 'exemplo' => 'Permite ao usuário interagir em todos os níveis de Protocolo, podendo acessar e salvar protocolo até ao nível completo, o segundo nível dá acesso a criação de tipo de documento gerado pelo protocolo e acesso a campos do cadastro do documento gerado além do protocolo.', 'tipodado' => 2, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 7, 'identificacao' => 'Inserir Protocolo Sem Legislatura', 'exemplo' => 'Permite ao usuário cadastrar o protocolo mesmo que não exista legislatura no período.', 'tipodado' => 2, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => 'sim', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 8, 'identificacao' => 'Informar data do Protocolo Manualmente', 'exemplo' => 'Permite ao usuário informar a data do protocolo de forma manual.', 'tipodado' => 2, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => 'sim', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 9, 'identificacao' => 'Permitir data e hora manual ao Tramitar', 'exemplo' => 'Permite que o usuário possa informar a data e a hora do trâmite.', 'tipodado' => 2, 'status' => 1, 'classificacao' => 3, 'valor1' => 'sim', 'valor2' => 'sim', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 10, 'identificacao' => 'Tramitar Registros', 'exemplo' => 'Permissão para o usuário tramitar documentos pelo sistema', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'sim', 'valor2' => 'sim', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 11, 'identificacao' => 'Pode alterar a Origem da Tramitação', 'exemplo' => 'Permite que o Usuário possa Alterar a Origem da Tramitação a qualquer tempo da Tramitação', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'nao', 'valor2' => 'sim', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 12, 'identificacao' => 'Pode remover Trâmite', 'exemplo' => 'Concede aos membros a possibilidade de remover apenas o último trâmite a cada vez existente na tramitação.', 'tipodado' => 1, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => 'sim', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 13, 'identificacao' => 'Permite Alterar Situação do Registro sendo Tramitado', 'exemplo' => 'Permite Alterar Situação do Registro sendo Tramitado', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'sim', 'valor2' => 'sim', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 14, 'identificacao' => 'Permite Definir Prioridade do Trâmite', 'exemplo' => 'Permite aos membros Definir Prioridade do Trâmite', 'tipodado' => 1, 'status' => 1, 'classificacao' => 3, 'valor1' => 'sim', 'valor2' => 'sim', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 15, 'identificacao' => 'Permite Definir Publicidade do Trâmite', 'exemplo' => 'Permite aos membros Definir Publicidade do Trâmite', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'sim', 'valor2' => 'sim', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 16, 'identificacao' => 'Permite Definir Prazo no Trâmite', 'exemplo' => 'Permite aos membros Definir Prazo no Trâmite', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'sim', 'valor2' => 'sim', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 17, 'identificacao' => 'Ao Salvar Voltar para a Listagem', 'exemplo' => 'Ao salvar o Registro volta para a listagem automaticamente', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'sim', 'valor2' => 'sim', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 18, 'identificacao' => 'Carregamento da Tela Inicial', 'exemplo' => 'Qual a interface que será exibida inicialmente após o Login', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'viewtelaclean', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 19, 'identificacao' => 'Direcionamento Inicial', 'exemplo' => 'Direcionamento que é carregado ao Iniciar', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'digital/processodigital', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        ConfigForUser::create(['id' => 20, 'identificacao' => 'Visual Tramitação do Processo Padrão', 'exemplo' => 'Alternar entre tabela e Timeline a visualização de processos', 'tipodado' => 0, 'status' => 1, 'classificacao' => 1, 'valor1' => 'timeline', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
/*
SELECT CONCAT("ConfigForUser::create(['id' => ", a.id,
", 'identificacao' => '", a.identificacao,
"', 'exemplo' => '", a.exemplo,
"', 'tipodado' => ", a.tipodado,
", 'status' => ", a.status,
", 'classificacao' => ", a.classificacao,
", 'valor1' => ", a.valor1,
", 'valor2' => ", a.valor2,
", 'versao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM configforuser AS a
*/
/**
 * tipodado = 1=Afeta Usuário 2=Afeta Permissão Usuário 0=Não Padronizado
 * classificacao = 1=Comportamento do Sistema/Usuário 2=Permissão Especial 3=Configuração do Usuário 0=Não Classificado
 */
