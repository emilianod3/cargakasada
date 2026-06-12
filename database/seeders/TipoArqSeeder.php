<?php

namespace Database\Seeders;

use App\Models\TipoArq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class TipoArqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        TipoArq::truncate();
        TipoArq::create(['id' => 1, 'tpaidentificacao' => 'Galeria Vídeo', 'tpaplural' => 'Galerias de Vídeos', 'tpaclassificacao' => '125', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 2, 'tpaidentificacao' => 'Galeria Foto', 'tpaplural' => 'Galerias de Fotos', 'tpaclassificacao' => '125', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 3, 'tpaidentificacao' => 'Galeria Áudio', 'tpaplural' => 'Galerias de Áudio', 'tpaclassificacao' => '125', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 4, 'tpaidentificacao' => 'Arquivo Assinado', 'tpaplural' => 'Arquivos Assinados', 'tpaclassificacao' => '0', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 5, 'tpaidentificacao' => 'Certificado', 'tpaplural' => 'Certificados', 'tpaclassificacao' => '0', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 6, 'tpaidentificacao' => 'Token', 'tpaplural' => 'Tokens', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 7, 'tpaidentificacao' => 'Arquivo', 'tpaplural' => 'Arquivos', 'tpaclassificacao' => '0', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 8, 'tpaidentificacao' => 'Thumb Perfil', 'tpaplural' => 'Thumbs de Perfil', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 9, 'tpaidentificacao' => 'Arquivo de Ata', 'tpaplural' => 'Arquivos de Ata', 'tpaclassificacao' => '8', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 10, 'tpaidentificacao' => 'Documento Pessoal', 'tpaplural' => 'Documentos Pessoais', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 11, 'tpaidentificacao' => 'Documento de Nomeação', 'tpaplural' => 'Documentos de Nomeação', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 12, 'tpaidentificacao' => 'Estágio Probatório', 'tpaplural' => 'Estágios Probatórios', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 13, 'tpaidentificacao' => 'Nomeação', 'tpaplural' => 'Nomeações', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 14, 'tpaidentificacao' => 'Férias', 'tpaplural' => 'Férias', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 15, 'tpaidentificacao' => 'Licença Prêmio', 'tpaplural' => 'Licenças Prêmio', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 16, 'tpaidentificacao' => 'Falta Abonada', 'tpaplural' => 'Faltas Abonadas', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 17, 'tpaidentificacao' => 'Falta Justificada', 'tpaplural' => 'Faltas Justificadas', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 18, 'tpaidentificacao' => 'Falta Injustificada', 'tpaplural' => 'Faltas Injustificadas', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 19, 'tpaidentificacao' => 'Licença', 'tpaplural' => 'Licenças', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 20, 'tpaidentificacao' => 'Incorporação', 'tpaplural' => 'Incorporações', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 21, 'tpaidentificacao' => 'Gratificação Universitária', 'tpaplural' => 'Gratificações Universitárias', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 22, 'tpaidentificacao' => 'Adicional de Quinquênio', 'tpaplural' => 'Adicionais de Quinquênio', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 23, 'tpaidentificacao' => 'Sexta Parte', 'tpaplural' => 'Sextas Partes', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 24, 'tpaidentificacao' => 'Vale Transporte', 'tpaplural' => 'Vales Transportes', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 25, 'tpaidentificacao' => 'Promoção Horizontal', 'tpaplural' => 'Promoções Horizontais', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 26, 'tpaidentificacao' => 'Exoneração', 'tpaplural' => 'Exonerações', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 27, 'tpaidentificacao' => 'Imagem de Capa', 'tpaplural' => 'Imagens de Capa', 'tpaclassificacao' => '125', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 28, 'tpaidentificacao' => 'Thumb da Notícia', 'tpaplural' => 'Thumbs da Notícia', 'tpaclassificacao' => '125', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 29, 'tpaidentificacao' => 'Thumb do Conteúdo', 'tpaplural' => 'Thumbs do Conteúdo', 'tpaclassificacao' => '62', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 30, 'tpaidentificacao' => 'Imagem Destaque', 'tpaplural' => 'Imagens de Destaque', 'tpaclassificacao' => '62', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 31, 'tpaidentificacao' => 'Imagem de Fundo 940x400', 'tpaplural' => 'Imagens de Fundo', 'tpaclassificacao' => '125', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 32, 'tpaidentificacao' => 'Imagem de Capa - Vertical 371x667', 'tpaplural' => 'Imagens de Capa', 'tpaclassificacao' => '125', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 33, 'tpaidentificacao' => 'Imagem de Fundo 1630 x 800', 'tpaplural' => 'Imagens de Fundo', 'tpaclassificacao' => '62', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 34, 'tpaidentificacao' => 'Foto de Capa', 'tpaplural' => 'Fotos de Capa', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 35, 'tpaidentificacao' => 'Galeria Pessoal', 'tpaplural' => 'Imagens de Galeria Pessoal', 'tpaclassificacao' => '51', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 36, 'tpaidentificacao' => 'Imagem da Notícia', 'tpaplural' => 'Imagens de Notícia', 'tpaclassificacao' => '125', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 37, 'tpaidentificacao' => 'Ícone Partido', 'tpaplural' => 'Ícones Partido', 'tpaclassificacao' => '18', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 38, 'tpaidentificacao' => 'Estatuto', 'tpaplural' => 'Estatutos', 'tpaclassificacao' => '18', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 39, 'tpaidentificacao' => 'Norma Complementar', 'tpaplural' => 'Normas Complementares', 'tpaclassificacao' => '18', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 40, 'tpaidentificacao' => 'Brasão', 'tpaplural' => 'Brasões', 'tpaclassificacao' => '56', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 41, 'tpaidentificacao' => 'Bandeira', 'tpaplural' => 'Bandeiras', 'tpaclassificacao' => '56', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 42, 'tpaidentificacao' => 'Foto', 'tpaplural' => 'Fotos', 'tpaclassificacao' => '56', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 43, 'tpaidentificacao' => 'Estrutura Organizacional', 'tpaplural' => 'Estrutura Organizacional', 'tpaclassificacao' => '56', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 44, 'tpaidentificacao' => 'Arquivo Assinado do Tramite', 'tpaplural' => 'Arquivos Assinados dos Trâmites', 'tpaclassificacao' => '130', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 45, 'tpaidentificacao' => 'Arquivo da Assinatura Manuscrita', 'tpaplural' => 'Arquivos de Assinaturas Manuscritas', 'tpaclassificacao' => '134', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 46, 'tpaidentificacao' => 'Arquivo da Foto para Carteira Funcional', 'tpaplural' => 'Arquivos das Fotos para Carteiras Funcionais', 'tpaclassificacao' => '134', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 47, 'tpaidentificacao' => 'Arquivo da Assinatura Manuscrita Emissor', 'tpaplural' => 'Arquivos de Assinaturas Manuscritas Emissor', 'tpaclassificacao' => '134', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 48, 'tpaidentificacao' => 'Arquivo Modelo Frontal Carteira', 'tpaplural' => '', 'tpaclassificacao' => '134', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 49, 'tpaidentificacao' => 'Arquivo Modelo Traseira Carteira', 'tpaplural' => '', 'tpaclassificacao' => '134', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 50, 'tpaidentificacao' => 'Compra Direta', 'tpaplural' => 'Arquivos de Compras Diretas', 'tpaclassificacao' => '38', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 51, 'tpaidentificacao' => 'Lei, Resolução e Regulamentação', 'tpaplural' => '1. Leis, Resoluções e Regulamentações', 'tpaclassificacao' => '203', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 52, 'tpaidentificacao' => 'Escola Municipal - Dados e Contatos', 'tpaplural' => '2. Escolas Municipais - Dados e Contatos', 'tpaclassificacao' => '203', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 53, 'tpaidentificacao' => 'Regimento Escolar', 'tpaplural' => '3. Regimento Escolar', 'tpaclassificacao' => '203', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 54, 'tpaidentificacao' => 'Cardápio de Alimentação Escolar ', 'tpaplural' => '4. Cardápios de Alimentação Escolar ', 'tpaclassificacao' => '203', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 55, 'tpaidentificacao' => 'Conselho Municipal de Educação', 'tpaplural' => '5. Conselho Municipal de Educação', 'tpaclassificacao' => '203', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 56, 'tpaidentificacao' => 'Conselho de Alimentação Escolar', 'tpaplural' => '6. Conselho de Alimentação Escolar', 'tpaclassificacao' => '203', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 57, 'tpaidentificacao' => 'Conselho FUNDEB', 'tpaplural' => '7. Conselho FUNDEB', 'tpaclassificacao' => '203', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 58, 'tpaidentificacao' => 'Projeto', 'tpaplural' => '8. Projetos', 'tpaclassificacao' => '203', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 59, 'tpaidentificacao' => 'Curso e Capacitação', 'tpaplural' => '9. Cursos e Capacitação', 'tpaclassificacao' => '203', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        TipoArq::create(['id' => 60, 'tpaidentificacao' => 'Convênio e Parceria', 'tpaplural' => '10. Convênios e Parcerias', 'tpaclassificacao' => '203', 'tpaversao' => Carbon::now()->toDateTimeString(), 'flagdelete' => 0, 'flagatualiza' => 1, 'flagcontrole' => 1,  'flaguser' => 0]);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
/*
php artisan db:seed --class=TipoArqSeeder

SELECT CONCAT("TipoArq::create(['id' => ", a.id,
", 'tpaidentificacao' => '", a.tpaidentificacao,
"', 'tpaplural' => '", a.tpaplural,
"', 'tpaclassificacao' => '", a.tpaclassificacao,
"', 'tpaversao' => Carbon::now()->toDateTimeString()",
", 'flagdelete' => 0, 'flagatualiza' => 1, 'flaguser' => 0]);") 
FROM tipoarq AS a
*/