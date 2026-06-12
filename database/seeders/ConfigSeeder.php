<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=ConfigSeeder
     * @return void
     */
    public function run()
    {
        $fkidgestorparam = env('GESTOR', '1');
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Config::truncate();
        Config::create(['id' => 1, 'identificacao' => 'Salvar Log', 'exemplo' => 'Ativo ', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 123, 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 2, 'identificacao' => 'Avisar prazo ao logar', 'exemplo' => 'Avisa de prazos vencendo ao se logar no sistema', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 3, 'identificacao' => 'Disponibilizar Aba Compilação', 'exemplo' => 'Disponibilizar Aba Compilação na lei', 'tipodado' => 1, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 4, 'identificacao' => 'Fazer varredura de texto em arquivo', 'exemplo' => 'Fazer varredura de texto em arquivo', 'tipodado' => 1, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 5, 'identificacao' => 'Duplo clique na grid selecionar o registro no cadastro que o abriu', 'exemplo' => 'Duplo clique na grid selecionar o registro no combo do cadastro que o abriu', 'tipodado' => 1, 'status' => 0, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 6, 'identificacao' => 'Exibir opção de inserção de alteração/revogação da lei na própria lei', 'exemplo' => 'Na aba de Consolidação da lei Exibir opção de inserção de alteração/revogação que a lei sofre a partir de outras Leis', 'tipodado' => 1, 'status' => 0, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 7, 'identificacao' => 'Quantidade de Noticias a serem exibidas no topo', 'exemplo' => 'Quantidade de Noticias a serem exibidas no topo do site', 'tipodado' => 1, 'status' => 1, 'classificacao' => 2, 'valor1' => 8, 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 8, 'identificacao' => 'Quantidade de Últimas Notícias a serem exibidas na página principal', 'exemplo' => 'Quantidade de Últimas Notícias a serem exibidas na página principal', 'tipodado' => 1, 'status' => 1, 'classificacao' => 2, 'valor1' => 8, 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 9, 'identificacao' => 'Site vinculado ao sistema', 'exemplo' => 'Site vinculado ao sistema para geraçãoo de atalhos.', 'tipodado' => 2, 'status' => 1, 'classificacao' => 1, 'valor1' => 'http://127.0.0.1:8282/site', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 10, 'identificacao' => 'Upload de Arquivos em Banco', 'exemplo' => '', 'tipodado' => 1, 'status' => 0, 'classificacao' => 1, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 11, 'identificacao' => 'Upload de Arquivos em pasta', 'exemplo' => 'Upload de Arquivos em pasta', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 12, 'identificacao' => 'Tempo de Inatividade para Bloqueio de Tela', 'exemplo' => 'Tempo de Inatividade para Bloqueio de Tela em Minutos após ficar sem Uso', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 60, 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 13, 'identificacao' => 'Tempo de exibição de mensagem de aviso', 'exemplo' => 'Tempo de exibição de mensagem de aviso em Milisegundos', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 7000, 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 14, 'identificacao' => 'Destino para Proposição Parlamentar', 'exemplo' => 'Informa o destino para proposições parlamentares em seu trâmite comum', 'tipodado' => 1, 'status' => 1, 'classificacao' => 2, 'valor1' => 8, 'valor2' => 1, 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 15, 'identificacao' => 'Dias padrão de prazo para Tramitação', 'exemplo' => 'Informa a quantidade de dias de prazo para a tramitação.', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 15, 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 16, 'identificacao' => 'Gerar Hash de Arquivo', 'exemplo' => 'O hash do Arquivo é utilizado para comparação, garantindo a integridade do arquivo referenciado', 'tipodado' => 1, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 17, 'identificacao' => 'Exibição de Tramitação como Timeline', 'exemplo' => 'Exibe a Tramitação como Timeline no lugar de Exibir Tabelas', 'tipodado' => 1, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 18, 'identificacao' => 'Inserir QRCODE no arquivo Assinado', 'exemplo' => 'Quando ativo insere o QRCODE com o link da página da origem ou até mesmo o link do arquivo propriamente dito.', 'tipodado' => 1, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 19, 'identificacao' => 'Assinatura visível', 'exemplo' => 'Quando ativo insere o campo de texto no documento assinado, informando qual certificado assinou.', 'tipodado' => 1, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 20, 'identificacao' => 'Link do Verificador de Documentos Assinados', 'exemplo' => '<br/><span style="font-size: x-small; padding-bottom:40px; padding-top:40px; text-align:right;">Para validar a Assinatura do Documento acesse o <b color="#006600"><a href="https://assina.ufsc.br/verificador"  target="_blank">Verificador</a></b> que detem a <i>Lista de Certificados Confiáveis</i> para validação. </span><br/>', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 21, 'identificacao' => 'Assinatura Digital', 'exemplo' => 'Permite ou não a assinatura com certificado de documentos no sistema', 'tipodado' => 1, 'status' => 1, 'classificacao' => 2, 'valor1' => 'sim', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 22, 'identificacao' => 'Divisão de Recebimento de Pedido Externos', 'exemplo' => 'Id da tabela ProcessoDivisao de recebimento de Pedidos enviados ao Processo quando não informando em Serviços', 'tipodado' => 3, 'status' => 1, 'classificacao' => 1, 'valor1' => '5', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 23, 'identificacao' => 'Tipo de Correspondência Recebida Padrao', 'exemplo' => 'Id da tabela tipocomunicacao de recebimento de Pedidos do Tipo Correspondência enviados ao Processo', 'tipodado' => 3, 'status' => 1, 'classificacao' => 1, 'valor1' => '2', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 24, 'identificacao' => 'Dias das Sessões Ordinárias', 'exemplo' => 'Texto informando os Dias das Sessões Ordinárias', 'tipodado' => 2, 'status' => 1, 'classificacao' => 1, 'valor1' => 'Segundas-Feiras', 'valor2' => 'às Segundas-Feiras', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 25, 'identificacao' => 'Horários das Sessões Ordinárias', 'exemplo' => 'Texto informando os Horários das Sessões Ordinárias', 'tipodado' => 2, 'status' => 1, 'classificacao' => 1, 'valor1' => 'Segundas-Feiras', 'valor2' => 'às Segundas-Feiras', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 26, 'identificacao' => 'Horários de Atendimento ao Público', 'exemplo' => 'Texto informando os Horários e dias de  Atendimentos', 'tipodado' => 2, 'status' => 1, 'classificacao' => 1, 'valor1' => 'Segunda à sexta-feira', 'valor2' => 'das 08:00h às 11:00h e das 13:00h às 17:00h', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        Config::create(['id' => 27, 'identificacao' => 'Existe Redação Final', 'exemplo' => 'Informa se existe redação final ativa para o sistema', 'tipodado' => 1, 'status' => 1, 'classificacao' => 1, 'valor1' => 'nao', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);		
        Config::create(['id' => 28, 'identificacao' => 'Divisão Padrão de Origem de Pedido Externos', 'exemplo' => 'Id da tabela ProcessoDivisao de Envio de Pedidos de Processo quando não informando em Serviços', 'tipodado' => 3, 'status' => 1, 'classificacao' => 1, 'valor1' => '3', 'valor2' => '', 'versao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flagcontrole' => 1, 'flaguser' => 0]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
		
    }
}


/*
SELECT CONCAT("Config::create(['id' => ", a.id,
", 'identificacao' => '", a.identificacao,
"', 'exemplo' => '", a.exemplo,
"', 'tipodado' => ", a.tipodado,
", 'status' => ", a.status,
", 'classificacao' => ", a.classificacao,
", 'valor1' => ", a.valor1,
", 'valor2' => ", a.valor2,
", 'versao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagexibe' => 1, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM config AS a

tipodado = 1=Opcional 2=Texto 3=Numero 4=URL
classificacao = 1=Parametro  2=Comportamento


*/
