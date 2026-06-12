<?php

namespace App\Http\Controllers\Core;

use Illuminate\Database\Eloquent\Model;

class Cals extends Model
{
    const CONSTANT = "Olá Constantes.";
    const IDCALUNIDADECONTRATO = 71;
    const IDCALUNIDADEFOMR = 72;
    const IDCALUNIDADEEMAIL = 73;
    const IDCALFUNCIONARIOS = 21;
    const IDCALDEPARTAMENTOS = 19;
    const IDCALUNIDADES = 74;
    const IDCALARQUIVOSFOTOS = 75;
    const IDCALRAMOATIVIDADE = 69;
    const IDCALCIDADES = 17;
    const EXEMPLO = [
        'frutas' => ['pera', 'uva', 'maça',],
        'carros' => ['fusca', 'chevette', 'passat',],
        'mulheres' => ['loira', 'ruiva', 'morena',],
    ];
    //{{Cals::EXEMPLO['frutas'][1]}}
    const CALTIPOSDOCUMENTOS = [
        'cal' => [1, 'Tipos de Documentos', 'Relação de Tipos de Documentos'],
    ];
    const CALMATERIA = [
        'cal' => [2, 'Matérias', 'Relação de Matérias'],
    ];
    const CALANDAMENTOTRAMITE = [
        'cal' => [4, 'Andamentos Trâmite', 'Relação de Andamentos do Tramite'],
    ];
    const CALPARECER = [
        'cal' => [5, 'Parecer', 'Relação de Pareceres'],
    ];
    const CALPROCESSO = [
        'cal' => [6, 'Processos', 'Relação de Processos'],
    ];
    const CALPROTOCOLO = [
        'cal' => [7, 'Protocolos', 'Relação de Protocolos'],
    ]; 
    const CALDIVERSO = [
        'cal' => [8, 'Diversos', 'Relação de Diversos'],
    ];
    const CALADM = [
        'cal' => [9, 'Administrativos', 'Relação de Documentos Administrativos'],
    ];
    const CALCORRESPONDENCIA = [
        'cal' => [10, 'Correspondência', 'Relação de Correspondências'],
    ];
    const CALNORMA = [
        'cal' => [12, 'Normas Legais', 'Relação de Normas Legais'],
    ];
    const CALSESSAO = [
        'cal' => [14, 'Sessões', 'Relação de Sessões'],
    ];
    const CALCLASSESOCIAL = [
        'cal' => [15, 'Classe Social', 'Relação de Classes Sociais'],
    ];
    const CALCARGO = [
        'cal' => [16, 'Cargo', 'Relação de Cargos'],
    ];
    const CALCIDADES = [
        'cal' => [17, 'Cidades', 'Relação de Cidades'],
    ];
    const CALPARTIDO = [
        'cal' => [18, 'Partidos', 'Relação de Partidos'],
    ];
    const CALDIVISAO = [
        'cal' => [19, 'Divisão', 'Relação de Divisões'],
    ];
    const CALESTADOCIVIL = [
        'cal' => [20, 'Estado Civil', 'Estados Civis'],
    ];
    const CALFUNCIONARIO = [
        'cal' => [21, 'Funcionário', 'Relação de Funcionários'],
    ];    
    const CALUSUARIOS = [
        'cal' => [22, 'Usuários', 'Relação de Usuários'],
    ];
    const CALMESA = [
        'cal' => [24, 'Mesas', 'Relação de Mesas'],
    ];
    const CALPARLAMENTARES = [
        'cal' => [25, 'Parlamentares', 'Relação de Parlamentares'],
    ];
    const CALCONTROLE = [
        'cal' => [28, 'Cals', 'Cals do Sistema'],
    ];
    const CALTRATAMENTO = [
        'cal' => [29, 'Tratamento', 'Relação de Tratamentos'],
    ];
    const CALTIPOVOTO = [
        'cal' => [30, 'Tipo Voto', 'Tipos de Votos'],
    ];
    const CALTIPOSITUACAO = [
        'cal' => [31, 'Situação', 'Tipos de Situações'],
    ];
    const CALTIPOQUORUM = [
        'cal' => [32, 'Tipo Quórum', 'Tipos de Quóruns'],
    ];
    const CALTIPOFONE = [
        'cal' => [33, 'Tipo Telefone', 'Tipos de Telefones'],
    ];
    const CALTIPOAPRECIACAO = [
        'cal' => [35, 'Tipo Apreciação', 'Tipos de Apreciações'],
    ];
    const CALEDITAL = [
        'cal' => [38, 'Edital', 'Relação de Editais'],
    ];    
    const CALCONTROLEACESSO = [
        'cal' => [40, 'Controle de Acesso', 'Controle de Acesso'],
    ];
    const CALLEGISLATURA = [
        'cal' => [41, 'Legislatura', 'Relação de Legislaturas'],
    ];
    const CALTIPOCATEGORIA = [
        'cal' => [43, 'Tipo Categoria', 'Tipos de Categorias'],
    ]; 
    const CALTIPODISCUSSAO = [
        'cal' => [44, 'Tipo Discussão', 'Tipos Discussões'],
    ]; 
    const CALTIPOPARECER = [
        'cal' => [46, 'Tipo Parecer', 'Tipos de Pareceres'],
    ]; 
    const CALTIPOSESSAO = [
        'cal' => [47, 'Tipo de Sessão', 'Relação de Tipos de Sessões'],
    ];
    const CALTIPOTRAMITE = [
        'cal' => [48, 'Tipo de Trâmite', 'Tipos de Trâmites'],
    ];  
    const CALETAPATRAMITE = [
        'cal' => [49, 'Etapa de Trâmite', 'Etapas de Trâmites'],
    ];
    const CALTIPOVOTACAO = [
        'cal' => [50, 'Tipo Votação', 'Tipos de Votações'],
    ]; 
    const CALUNICOS = [
        'cal' => [51, 'Unificados', 'Relação de Registros Unificados'],
    ];
    
    const CALENDERECO = [
        'cal' => [52, 'Endereco', 'Logradouros'],
    ];
    const CALTIPOCOMUNICACAO = [
        'cal' => [53, 'Tipo Comunicação', 'Tipos Comunicações'],
    ];
    const CALCONFIGSISTEMA = [
        'cal' => [54, 'Configuração do Sistema', 'Configurações do Sistema'],
    ];
    const CALROTAS = [
        'cal' => [55, 'Rota', 'Relação de Rotas de Processos'],
    ];
    const CALUNIDADES = [
        'cal' => [56, 'Unidade', 'Relação de Unidades'],
    ];
    const CALREUNIOES = [
        'cal' => [57, 'Cadastro de Reunião', 'Relação de Reuniões'],
    ];
    const CALCFGTOUSER = [
        'cal' => [58, 'Configuração para Usuário', 'Relação de Configurações para Usuários'],
    ];
    const CALCFGUSER = [
        'cal' => [59, 'Configurações de Usuários', 'Relação de Configurações dos Usuários'],
    ];
    const CALATOOFICIAL = [
        'cal' => [60, 'Atos Oficiais', 'Atos Oficiais'],
    ];
    const CALPERFILUSUARIO = [
        'cal' => [61, 'Perfil de Usuário', 'Perfis de Usuários'],
    ];  
    const CALPORTAL = [
        'cal' => [62, 'Conteúdo para Portal', 'Conteúdos para Portal'],
    ];
    const CALOUVIDORIA = [
        'cal' => [63, 'Ouvidoria', 'Relação de Demandas de Ouvidorias'],
    ]; 
    const CALLOGSIS = [
        'cal' => [66, 'Log do Sistema', 'Relação de Logs de Atividades'],
    ];
    const CALPROFISSAO = [
        'cal' => [68, 'Profissão', 'Relação de Profissões'],
    ];


    const CALRAMOATIVIDADE = [
        'cal' => [69, 'Ramo de Atividade', 'Relação de Ramos de Atividades'],
    ];
    const CALESCOLARIDADE = [
        'cal' => [70, 'Escolaridade', 'Relação de Escolaridades'],
    ];
    const CALUNIDADESGESTOR = [
        'cal' => [71, 'Unidade', 'Relação de Unidades'],
    ];
    const CALUNICOSEMAIL = [
        'cal' => [76, 'Emails', 'Relação de Emails'],
    ];
    const CALUNICOSFONE = [
        'cal' => [77, 'Fone', 'Relação de Fones'],
    ];
    const CALUNICOSMIDIAS = [
        'cal' => [78, 'Mídia', 'Relação de Mídias'],
    ];
    const CALUNICOSARQUIVOS = [
        'cal' => [79, 'Arquivo', 'Relação de Arquivos'],
    ];
    const CALUNICOSPARENTES = [
        'cal' => [80, 'Parentesco', 'Relação de Parentescos'],
    ];
    const CALUNICOSDADOSPROFISSIONAIS = [
        'cal' => [81, 'Dado Profissional', 'Relação de Dados Profissionais'],
    ];
    const CALUNICOSDECLARACAOBENS = [
        'cal' => [82, 'Declaração de Bens', 'Relação de Declarações de Bens'],
    ];
    const CALEXECUTIVO = [
        'cal' => [83, 'Mandato Executivo', 'Relação de Mandatos de Executivo'],
    ];
    const CALMESAMEMBRO = [
        'cal' => [84, 'Mesa Membro', 'Relação de Membros da Mesa'],
    ];
    const CALTIPOMEMBRO = [
        'cal' => [85, 'Tipo Membro', 'Relação de Tipos de Membros'],
    ];
    const CALCOMISSAO = [
        'cal' => [86, 'Comissão', 'Relação de Comissões'],
    ];
    const CALCOMISSAOTEMP = [
        'cal' => [87, 'Comissão Temporária', 'Relação de Comissões Temporárias'],
    ];
    const CALCOMISSAOMEMBRO = [
        'cal' => [88, 'Membro Comissão', 'Relação de Membros de Comissão'],
    ];
    const CALCOMISSAOTEMPMEMBRO = [
        'cal' => [89, 'Membro de Comissão Temporária', 'Relação de Membros de Comissão Temporária'],
    ];
    const CALBANCADA = [
        'cal' => [90, 'Bancada', 'Relação de Bancadas'],
    ];
    const CALBANCADAMEMBRO = [
        'cal' => [91, 'Bancada Membro', 'Relação de Bancadas Membros'],
    ]; 
    const CALORIGEM = [
        'cal' => [92, 'Origem', 'Relação de Origens'],
    ];
    const CALATOFICIALCONSOLIDACAO = [
        'cal' => [93, 'Consolidação de Atos Oficiais', 'Consolidação de Atos Oficiais'],
    ];
    const CALATOFICIALPUBLICACAO = [
        'cal' => [94, 'Publicação de Atos Oficiais', 'Publicação de Atos Oficiais'],
    ];
    const CALLEGISLACAOCONSOLIDACAO = [
        'cal' => [96, 'Consolidação de Normas', 'Consolidação de Normas'],
    ];
    const CALLEGISLACAOPUBLICACAO = [
        'cal' => [95, 'Publicação de Normas', 'Publicação de Normas'],
    ];
    const CALLEGISLACAOCOMPILACAO = [
        'cal' => [97, 'Compilação de Normas', 'Compilação de Normas'],
    ];
    const CALLEGISLACAOREGULAMENTACAO = [
        'cal' => [98, 'Regulamentação de Normas', 'Regulamentação de Normas'],
    ];
    const CALLEGISLACAODELIBERACAO = [
        'cal' => [99, 'Deliberação de Normas', 'Deliberação de Normas'],
    ];
    const CALTIPOALTERACAO = [
        'cal' => [100, 'Alterações', 'Tipos de Alterações'],
    ];
    const CALNUMREGISTROS = [
        'cal' => [101, 'Alterações de Numeração', 'Manutenção na Numeração de Registros'],
    ];
    const CALPROPOSITURAPUBLICACAO = [
        'cal' => [102, 'Publicação de Documentos', 'Publicação de Documentos'],
    ];
    const CALPROPOSITURAPARECER = [
        'cal' => [103, 'Parecer de Documentos', 'Pareceres de Documentos'],
    ];
    const CALPROPOSITURASESSAO = [
        'cal' => [104, 'Sessões de Documentos', 'Sessões de Documentos'],
    ];
    const CALPROPOSITURAVOTO = [
        'cal' => [105, 'Voto de Documentos', 'Voto de Documentos'],
    ];
    const CALPROPOSITURAACESSORIA = [
        'cal' => [106, 'Matéria Acessória', 'Matérias Acessórias'],
    ];
    const CALPROPOSITURAAUTORIA = [
        'cal' => [108, 'Autoria de Matéria', 'Autoria de Matérias'],
    ];
    const CALPARECERAUTORIA = [
        'cal' => [109, 'Autoria de Parecer', 'Autoria de Pareceres'],
    ];
    const CALPARECERSESSAO = [
        'cal' => [110, 'Sessões de Parecer', 'Sessões de Pareceres'],
    ];
    const CALPARECERVOTO = [
        'cal' => [111, 'Voto de Parecer', 'Voto de Pareceres'],
    ];
    const CALCORRESPONDENCIAAUTORIA = [
        'cal' => [112, 'Autoria de Correspondência', 'Autoria de Correspondências'],
    ];
    const CALCORRESPONDENCIAVOTO = [
        'cal' => [113, 'Voto de Correspondência', 'Voto de Correspondências'],
    ];
    const CALCORRESPONDENCIASESSAO = [
        'cal' => [114, 'Sessões de Correspondência', 'Sessões de Correspondências'],
    ];
    const CALEDITALPUBLICACAO = [
        'cal' => [116, 'Publicação de Edital', 'Publicação de Editais'],
    ];
    const CALPROCESSOPUBLICACAO = [
        'cal' => [119, 'Publicação de Processos', 'Publicação de Processos'],
    ];
    const CALOUVIDORIAPUBLICACAO = [
        'cal' => [121, 'Publicação de Ouvidorias', 'Publicação de Ouvidorias'],
    ];
    const CALPESQUISAPUBLICACAO = [
        'cal' => [122, 'Publicação de Pesquisa', 'Publicação de Pesquisas'],
    ];    
    const CALPESQUISA = [
        'cal' => [123, 'Pesquisas', 'Relação de Pesquisas'],
    ];
    const CALMENUS = [
        'cal' => [124, 'Menus do Sistema', 'Relação de Menus do Sistema'],
    ];
    const CALNOTICIA = [
        'cal' => [125, 'Notícias Portal', 'Notícias para o Portal'],
    ];
    
    const CALPROPOSICAOPARLAMENTAR = [
        'cal' => [127, 'Proposições Parlamentares', 'Proposições Parlamentares'],
    ];
    const CALPROPOSICAOMUNICIPE = [
        'cal' => [128, 'Proposições Munícipe', 'Proposições Munícipe'],
    ];
    
    const CALMUNICIPE = [
        'cal' => [129, 'Cadastro Munícipe', 'Cadastro Munícipe'],
    ];

    const CALTRAMITEDIGITAL = [
        'cal' => [130, 'Trâmite Digital', 'Trâmites Digitais'],
    ];    
    const CALCERTIFICADO = [
        'cal' => [131, 'Certificado', 'Certificados'],
    ];
    const CALASSINATURADIGITAL = [
        'cal' => [132, 'Assinatura Digital', 'Assinaturas Digitais'],
    ];
    const CALARTICULACAO = [
        'cal' => [133, 'Articulação', 'Articulação'],
    ]; 
    const CALCFD = [
        'cal' => [134, 'Carteira Funcional', 'Carteiras Funcionais'],
    ];
    const CALESTOQUE = [
        'cal' => [135, 'Estoque', 'Estoque de Produtos'],
    ];
    const CALFINANCEIRO = [
        'cal' => [136, 'Financeiro', 'Financeiro'],
    ];
    const CALFROTA = [
        'cal' => [138, 'Frota', 'Controle de Frota'],
    ];
    const CALLGPD = [
        'cal' => [139, 'LGPD', 'Lei Geral de Privacidade de Dados'],
    ];
    const CALREPORTARPROBLEMA = [
        'cal' => [143, 'Reportar Problema', 'Reportar Problema'],
    ];
    const CALCONTATO = [
        'cal' => [144, 'Contato', 'Formulário de Contato'],
    ];
    const CALCLIENTES = [
        'cal' => [145, 'Gestor', 'Cadastro de Gestores de Sistemas'],
    ];







    const CALPROFISSIONAL = [
        'cal' => [148, 'Profisional', 'Relação de Registros de Profissionais'],
    ];

    const CALSERVICO = [
        'cal' => [149, 'Serviço', 'Relação de Serviços'],
    ];

    const CALPACOTE = [
        'cal' => [150, 'Pacote', 'Relação de Pacotes'],
    ];

    const CALPACOTEVENDA = [
        'cal' => [151, 'Venda de Pacote', 'Relação de Vendas de Pacotes'],
    ];

    const CALCATEGORIA = [
        'cal' => [152, 'Categorias', 'Relação de Categorias'],
    ];

    const CALFORMASPAGAMENTO = [
        'cal' => [153, 'Forma de Pagamento', 'Relação de Formas de Pagamentos'],
    ];

    const CALMENSAGEMAPP = [
        'cal' => [154, 'Comunicação com Aplicativo', 'Relação de Comunicações com Aplicativos'],
    ];

    const CALPESQUISASATISFACAO = [
        'cal' => [155, 'Pesquisa de Satisfação', 'Pesquisas de Satisfação'],
    ];

    const CALLEMBRETE = [
        'cal' => [156, 'Lembrete', 'Relação de Lembretes'],
    ];

    const CALCONTA = [
        'cal' => [157, 'Conta', 'Relação de Contas'],
    ];

    const CALRECEITA = [
        'cal' => [158, 'Receita', 'Relação de Receitas'],
    ];

    const CALDESPESA = [
        'cal' => [159, 'Despesa', 'Relação de Despesas'],
    ];

    const CALEQUIPAMENTO = [
        'cal' => [160, 'Equipamento', 'Relação de Equipamentos'],
    ];

    const CALFORNECEDOR = [
        'cal' => [161, 'Fornecedor', 'Relação de Fornecedores'],
    ];
    
    const CALTIPOANAMNESE = [
        'cal' => [162, 'Tipo Anamnese', 'Relação de Tipos de Anamnese'],
    ];
    
    const CALBANDEIRA = [
        'cal' => [163, 'Cobrança', 'Relação de Formas de Cobrança'],
    ];

    const CALREMUNERACAO = [
        'cal' => [164, 'Remuneração', 'Relação de Remunerações'],
    ];

    const CALDEDUCAO = [
        'cal' => [165, 'Dedução', 'Relação de Deduções'],
    ];

    const CALLISTAESPERA = [
        'cal' => [166, 'Lista de Espera', 'Lista de Espera'],
    ];

    const CALORDEMCHEGADA = [
        'cal' => [167, 'Ordem de Chegada', 'Ordem de Chegada'],
    ];

    const CALCOMANDA = [
        'cal' => [168, 'Comanda', 'Relação de Comandas'],
    ];

    const CALRODIZIO = [
        'cal' => [169, 'Rodízio', 'Rodízio de Profissionais'],
    ];

    const CALAGENDA = [
        'cal' => [170, 'Agenda', 'Agendamentos'],
    ];

    const CALCAIXA = [
        'cal' => [171, 'Caixa', 'Caixa'],
    ];

    const CALCOMISSAOPROF = [
        'cal' => [172, 'Comissão', 'Comissões'],
    ];
    
    const CALCONTACLIENTE = [
        'cal' => [173, 'Conta de Cliente', 'Contas de Clientes'],
    ];    
    
    const CALCONTAPROFISSIONAL = [
        'cal' => [174, 'Conta de Profissional', 'Contas de Profissionais'],
    ];

    public const CALEVENTO = [
        'cal' => [184, 'Evento', 'Relação de Eventos'],
    ];

    const CALTIPOTRAMITEETAPA = [
        'cal' => [191, 'Tipo Trâmite Etapa', 'Tipos Trâmites Etapas'],
    ];
    const CALORIGEMPARECER = [
        'cal' => [192, 'Origem Parecer', 'Relação de Origens de Pareceres'],
    ];
    const CALTIPOARQ = [
        'cal' => [193, 'Tipo Arquivo', 'Tipos de Arquivos'],
    ];
    const CALTIPOPARENTESCO = [
        'cal' => [194, 'Tipo Parentesco', 'Tipos de Parentescos'],
    ];
    const CALTIPOEDITALACESSORIA = [
        'cal' => [195, 'Tipo Edital Acessoria', 'Tipos Acessorios de Editais'],
    ];
    const CALTIPOETAPA = [
        'cal' => [196, 'Tipo Etapa', 'Tipos de Etapas'],
    ];
    const CALAUDITORIA = [
        'cal' => [197, 'Auditoria', 'Auditorias'],
    ];


    const CALMODELODOCUMENTO = [
        'cal' => [201, 'Modelo Documento', 'Modelos de Documentos'],
    ];

    const CALCOMPRAS = [
        'cal' => [202, 'Compra Direta', 'Compras Diretas'],
    ];

    const CALCONTEUDOARQUIVO = [
        'cal' => [203, 'Conteúdo em Arquivo', 'Conteúdos em Arquivo'],
    ];    
}
