<script setup>
import Layout from '@/Layouts/PainelInterno.vue';
import { ref, onMounted, computed } from 'vue';
import { useForm, usePage, Head, router } from '@inertiajs/vue3';
import * as sistemajs from '@/sistema.js';


const props = defineProps({
    cal: Object,
    colunasCal: Array,
    permissao: Object
});

const page = usePage();

// --- ESTADOS REATIVOS (TABS E FILTROS) ---
const abaAtiva = ref('inicio'); // inicio, cadastro, colunas
const campoPesquisa = ref('');
const exibirFiltrosAvancados = ref(false);

const filtroStatus = ref('1'); // 0=Ambos, 1=Ativo, 2=Inativo
const filtroDataInicio = ref('');
const filtroDataFim = ref('');
const filtroCampoOrdem = ref('clidentificacao');
const filtroOrdemDirecao = ref('asc');
const tipoFiltro = ref('amplo'); // amplo ou exato
const calid = props.cal?.cal?.[0];
let permissao = null;
let pageatual = 1;
let qtdporpg = 10;

const btnnovoregistro = ref(false);

const listagem = ref({
    current_page: 1,
    data: [],
    links: [],
    total: 0
});

// --- FORMULÁRIO PRINCIPAL (CRUD) ---
const form = useForm({
    id: 0,
    clidentificacao: '',
    base: '',
    rota: '',
    tipo: 1,
    status: true
});

// --- CARREGAMENTO INICIAL ---
onMounted(() => {
    // Inicializações se necessário
    permissaoPrincipal();
    qtdporpg = sistemajs.getCfgUserCal(page.props.auth?.user?.id, calid);
    filtrar(); // Carrega a listagem inicial
    
});


function permissaoPrincipal(){
    permissao = sistemajs.getPermissaoCal(calid);
    if (page.props.app_debug) {
        console.log('Permissões do Cal:', permissao);
    }
    if(permissao.inserir != true){
        btnnovoregistro.value = permissao.inserir;
        if (page.props.app_debug) {
            console.log('Sem Permissão Inserir:', permissao.inserir);
        }
    }else{
        btnnovoregistro.value = permissao.inserir;
        if (page.props.app_debug) {
            console.log('Permissão Inserir:', permissao.inserir);
        }
        btnnovoregistro.value = permissao.inserir;
    }

    if(permissao.inserir != true && permissao.alterar != true){
        //$(".btnSalvarPrincipal").hide();
        //$(".btnsalvarestatistica4").hide();
    }
    else{
        //$(".btnSalvarPrincipal").show();
        //$(".btnsalvarestatistica4").show();
    } 

    if(permissao.apagar != true){
        //$(".veiculolisttblcolunatd").addClass('hide');
        //$(".btnremoverfoto").hide();
    }
    else{
        //$(".veiculolisttblcolunatd").removeClass('hide');
        //$(".btnremoverfoto").show();
    }
}

// --- MÉTODOS DE NAVEGAÇÃO E AÇÃO ---
const alternarAba = (aba = 'inicio') => {
    if (aba !== 'inicio' && form.id === 0 && aba !== 'cadastro') {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Selecione ou salve um registro para continuar.', tipo: 'warning', tempo: 4000 });
        return;
    }

    if(aba === 'inicio'){
        if (page.props.app_debug) {
            console.log('Limpar Campos do cadastro e listar registros');
        }        
    }


    if(aba === 'cadastro'){
        if (page.props.app_debug) {
            console.log('Limpar Campos e Iniciar cadastro');
        }        
    }

    if(aba === 'colunas'){
        if (page.props.app_debug) {
            console.log('Campos de colunas');
        }        
    }

    abaAtiva.value = aba;
};

const novoRegistro = () => {
    form.reset();
    form.id = 0;
    alternarAba('cadastro');
    if (page.props.app_debug) {
        console.log('Btn Novo Registro - Clicado');
    }    
};

const editarRegistro = (registro) => {
    form.id = registro.id;
    form.clidentificacao = registro.clidentificacao;
    form.base = registro.base;
    form.rota = registro.rota;
    form.tipo = registro.tipo;
    form.status = registro.status === 1;
    abaAtiva.value = 'cadastro';
};

// --- SUBMISSÃO DO FILTRO / PESQUISA ---
const filtrar = (pg = 1) => {
    /*form.get(route('controle.cals.lista'), {
        data: {
            campoPesquisa: campoPesquisa.value,
            statusfiltro: filtroStatus.value,
            datainiciofiltro: filtroDataInicio.value,
            datafinalfiltro: filtroDataFim.value,
            campoordem: filtroCampoOrdem.value,
            ordem: filtroOrdemDirecao.value,
            tipofiltro: tipoFiltro.value,
            regPg: 10
        },
        preserveState: true,
        replace: true
    });*/
    pageatual = pg;
    let data = {
        campoPesquisa: campoPesquisa.value,
        statusfiltro: filtroStatus.value,
        datainiciofiltro: filtroDataInicio.value,
        datafinalfiltro: filtroDataFim.value,
        campoordem: filtroCampoOrdem.value,
        ordem: filtroOrdemDirecao.value,
        tipofiltro: tipoFiltro.value,
        regPg: 2,
        page: pg,
    };

    /*router.post(route('controle.cals.lista'), {
    campoPesquisa: campoPesquisa.value,
    statusfiltro: filtroStatus.value,
    datainiciofiltro: filtroDataInicio.value,
    datafinalfiltro: filtroDataFim.value,
    campoordem: filtroCampoOrdem.value,
    ordem: filtroOrdemDirecao.value,
    tipofiltro: tipoFiltro.value,
    regPg: 10,
    preserveState: true,
    replace: true,
    onError: (errors) => {*/

    router.post(route('controle.cals.lista'), data, {
        preserveState: true,
        replace: true,
        onError: (errors) => {   
            /*const rerro = errors.resultado;
            try {
                const djson = JSON.parse(rerro);
                sistemajs.mostrarPopup({ titulo: 'Falha no Cadastro', conteudo: djson.message, tipo: 'danger', tempo: 6000 });
            } catch (e) {
                sistemajs.mostrarPopup({ titulo: 'Falha no Cadastro', conteudo: 'Impossível Processar dados.', tipo: 'danger', tempo: 6000 });
            }
            
            registerForm.reset('senha', 'unsenha_confirmation', 'grecaptcha');
            mensagemErroSenha.value = '';
            
            if (window.grecaptcha && widgetId !== null) {
                window.grecaptcha.reset(widgetId);
                registerForm.grecaptcha = '';
            }
            registerForm.reset();
            registerForm.aceitatermos = false;            
            campoNome.value?.focus();*/
            sistemajs.mostrarPopup({ 
                titulo: 'Erro Listagem', 
                conteudo: 'Falha na Listagem de CAlls...', 
                tipo: 'danger', 
                tempo: 4000 
            });
        },
        onSuccess: () => {
            /*sistemajs.mostrarPopup({ 
                titulo: 'Listagem Realizada', 
                conteudo: 'Sucesso na Listagem...', 
                tipo: 'success', 
                tempo: 4000 
            });*/
            /*
            registerForm.reset();
            setTimeout(() => {
                window.location.href = route('login');
            }, 4000);  */  
            //const rsucesso = page.props.flash?.resultado;
            //console.log('Saida:', rsucesso);
            //const djson = JSON.parse(rsucesso);
            if(page.props.flash?.resultado != null){
                listagem.value = JSON.parse(page.props.flash?.resultado).data;
            }else{
              
            }
            //listagem.value = djson.data;
            /*console.log(djson.message);
            console.log(djson.status);
            console.log(djson.data);*/
        }
    });


};


// 2. Função inteligente para mudar de página sem perder os filtros existentes na URL
const navegarParaPagina = (page = 1) => {

    
        console.log('filtrar - navegarParaPagina:', page);
    
    filtrar(page);
    /*
    if (!url) return;
    
    router.port(url, {}, {
        preserveState: true, // Mantém o texto digitado nos filtros da tela
        preserveScroll: true // Evita que a página pule para o topo ao mudar de página
    });*/
};

const limparFiltros = () => {
    campoPesquisa.value = '';
    filtroStatus.value = '1';
    filtroDataInicio.value = '';
    filtroDataFim.value = '';
    filtrar();
};

const alternarOrdemDirecao = () => {
    filtroOrdemDirecao.value = filtroOrdemDirecao.value === 'asc' ? 'desc' : 'asc';
    filtrar();
};

// --- SUBMIT DO FORMULÁRIO (SALVAR) ---
const submeterFormulario = () => {
    if (!form.clidentificacao.trim()) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Por favor, informe a Identificação.', tipo: 'warning', tempo: 4000 });
        return;
    }

    form.post(route('controle.cals.salvar'), {
        onSuccess: () => {
            sistemajs.mostrarPopup({ titulo: 'Sucesso', conteudo: 'Registro salvo com sucesso!', tipo: 'success', tempo: 4000 });
            abaAtiva.value = 'inicio';
            form.reset();
        },
        onError: () => {
            sistemajs.mostrarPopup({ titulo: 'Erro', conteudo: 'Não foi possível salvar o registro.', tipo: 'danger', tempo: 5000 });
        }
    });
};



const exibirOpcoesCal = ref(false);

// Função que será chamada ao clicar em cada opção
const executarAcao = (acao) => {
    console.log('Ação disparada:', acao);
    
    // Aqui você coloca a sua lógica (ex: abrir um modal, deletar, etc.)
    if (acao === 'editar') { /* ... */ }
    
    exibirOpcoesCal.value = false; // Fecha o menu automaticamente
};



const linksPaginacaoFiltrados = computed(() => {
    if (!listagem.value.links || listagem.value.links.length === 0) return [];

    const totalLinks = listagem.value.links.length;
    const paginaAtual = listagem.value.current_page;
    const maxVisiveis = 2; // Quantidade de números ao redor da página atual

    return listagem.value.links.filter((link, index) => {
        // 1. Sempre mantém o primeiro botão (Anterior) e o último botão (Próximo)
        if (index === 0 || index === totalLinks - 1) return true;

        const numPagina = parseInt(link.label);
        
        // 2. Se não for um número (ex: reticências "..."), mantém na tela
        if (isNaN(numPagina)) return true;

        // 3. Mantém os números próximos à página atual (miolo)
        const noMiolo = numPagina >= paginaAtual - maxVisiveis && numPagina <= paginaAtual + maxVisiveis;

        // 4. Nova Regra: Sempre mantém as duas últimas páginas numéricas da lista
        // Como o último link (index totalLinks - 1) é o botão "Próximo", as páginas finais estão logo antes dele
        const ehPaginaFinal = index === totalLinks - 2 || index === totalLinks - 3;

        return noMiolo || ehPaginaFinal;
    });
});

</script>

<template>
    <Head title="Controle de Cals" />

    <Layout>
        <div class="pb-2 max-full flex flex-col gap-4">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-comum pb-2 gap-2">
                <div>
                    <h6 class="text font-bold text-texto-claro flex items-center gap-2">
                        {{ props.cal?.cal?.[2] }}
                    </h6>
                </div>
            </div>

            <div class="border-b border-comum -mt-3">
                <nav class="flex space-x-4" aria-label="Tabs">
                    <button @click="alternarAba('inicio')" :class="[abaAtiva === 'inicio' ? 'border-primary text-primary' : 'border-transparent text-texto-claro/60 hover:text-texto-claro hover:border-comum', 'py-2 px-1 border-b-2 font-medium text-sm transition-all flex items-center gap-2 cursor-pointer']">
                        <i class="fas fa-home"></i> Início
                    </button>
                    <button @click="alternarAba('cadastro')" :class="[abaAtiva === 'cadastro' ? 'border-primary text-primary' : 'border-transparent text-texto-claro/60 hover:text-texto-claro hover:border-comum', 'py-2 px-1 border-b-2 font-medium text-sm transition-all flex items-center gap-2 cursor-pointer']">
                        <i class="fas fa-edit"></i> {{ form.id > 0 ? 'Editar Cadastro' : 'Cadastro' }}
                    </button>
                    <button @click="alternarAba('colunas')" :class="[abaAtiva === 'colunas' ? 'border-primary text-primary' : 'border-transparent text-texto-claro/60 hover:text-texto-claro hover:border-comum', 'py-2 px-1 border-b-2 font-medium text-sm transition-all flex items-center gap-2 cursor-pointer']">
                        <i class="fas fa-clipboard-list"></i> Colunas
                    </button>
                </nav>
            </div>

            <div v-if="abaAtiva === 'inicio'" class="flex flex-col gap-4">
                
                <div class="bg-layout-painel border border-comum rounded-lg p-4 shadow-sm flex flex-col gap-4">
                    <div class="flex items-center w-full box-border">
                        <div class="relative inline-block h-10 shrink-0">
                            <button 
                                type="button" @click="exibirOpcoesCal = !exibirOpcoesCal" title="Relatórios"
                                class="bg-primary hover:bg-primary-hover text-texto-escuro h-10 w-15 rounded-l-lg transition-all cursor-pointer focus:outline-none flex items-center justify-center box-border select-none pr-3 pl-3">
                                <i class="fas fa-print text-sm transition-transform duration-200 cursor-pointer pl-2 pr-5" :class="{ 'rotate-90 cursor-pointer': exibirOpcoesCal }"></i>
                                <i class="fas fa-chevron-down text-[10px] pr-3 cursor-pointer"></i>
                            </button>

                            <div v-if="exibirOpcoesCal" class="absolute left-0 mt-1 w-48 bg-layout-painel border border-comum rounded-lg shadow-xl z-50 overflow-hidden py-1">
                                <button 
                                    type="button" title="Gerar Relatório - PDF"
                                    @click="executarAcao('acao1')"
                                    class="w-full text-left px-4 py-2.5 text-sm text-texto-claro/90 hover:bg-texto-claro/10 transition-colors flex items-center gap-2.5 cursor-pointer"
                                >
                                    <i class="fas fa-plus text-xs text-texto-claro/40"></i> Relatório
                                </button>
                                
                                <button 
                                    type="button" title="Gerar Relatório - DOC"
                                    @click="executarAcao('acao2')"
                                    class="w-full text-left px-4 py-2.5 text-sm text-texto-claro/90 hover:bg-texto-claro/10 transition-colors flex items-center gap-2.5 cursor-pointer"
                                >
                                    <i class="fas fa-download text-xs text-texto-claro/40"></i> Relatório
                                </button>
                                <hr class="border-comum">
                                
                                <button 
                                    type="button" title="Gerar Relatório - CSV"
                                    @click="executarAcao('acao3')"
                                    class="w-full text-left px-4 py-2.5 text-sm text-red-400 hover:bg-red-500/10 transition-colors flex items-center gap-2.5 cursor-pointer"
                                >
                                    <i class="fas fa-exclamation-triangle text-xs opacity-60"></i> Relatório
                                </button>
                            </div>

                            <div v-if="exibirOpcoesCal" @click="exibirOpcoesCal = false" class="fixed inset-0 z-40"></div>
                        </div>
                        

                        <button @click="exibirFiltrosAvancados = !exibirFiltrosAvancados" type="button" class="bg-primary hover:bg-primary-hover text-texto-escuro h-10 px-3.5 border-l border-primary/30 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none" title="Mais Opções de Filtros">
                            <i :class="['fas', exibirFiltrosAvancados ? 'fa-angle-double-up' : 'fa-filter']"></i>
                        </button>
                        <div class="relative flex-1">
                            <input v-model="campoPesquisa" @keyup.enter="filtrar" type="text" placeholder="Dados para pesquisa..." class="w-full p-2.5 pl-10 border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                            <i class="fas fa-search absolute left-3.5 top-3.5 text-texto-claro/40 text-sm"></i>
                        </div>
                        
                        <button type="button" @click="filtrar" class="bg-primary hover:bg-primary-hover text-texto-escuro h-10 px-3.5 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none" title="Pesquisar">
                            <i class="fas fa-search text-sm"></i> <label class="pl-3 cursor-pointer">Filtrar</label>
                        </button>
                        <button type="button" class="btn-black h-10 px-3.5 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none rounded-r-lg" title="Limpar Filtro">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                    
                    <div v-if="exibirFiltrosAvancados">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-comum pt-4 transition-all">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-medium text-texto-claro/70">Status do Registro</label>
                                <select v-model="filtroStatus" class="select-customizado w-full p-2.5 pr-10 rounded-lg border border-comum bg-layout-fundo text-texto-claro text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all ">
                                    <option value="0">Ambos</option>
                                    <option value="1">Ativo</option>
                                    <option value="2">Inativo</option>
                                </select>
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-medium text-texto-claro/70">Data Inicial</label>
                                <input v-model="filtroDataInicio" type="date" class="p-2 rounded-lg border border-comum bg-layout-fundo text-texto-claro text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" />
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-medium text-texto-claro/70">Data Final</label>
                                <input v-model="filtroDataFim" type="date" class="p-2 rounded-lg border border-comum bg-layout-fundo text-texto-claro text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-medium text-texto-claro/70">Ordenar Por</label>
                                <select v-model="filtroCampoOrdem" class="select-customizado w-full p-2.5 pr-10 rounded-lg border border-comum bg-layout-fundo text-texto-claro text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all ">
                                    <option value="clidentificacao">Identificação</option>
                                    <option value="clobserve">Obs</option>
                                    <option value="id">Código</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1 md:col-span-2">
                                <label class="text-xs font-medium text-texto-claro/70">Ordem</label>
                                <div class="btn-group">
                                    <button class="bg-primary hover:bg-primary-hover text-texto-escuro cursor-pointer rounded-l-lg pr-3 pl-3"><i :class="['mr-2 fas', filtroOrdemDirecao === 'asc' ? 'fa-sort-alpha-down' : 'fa-sort-alpha-up']"></i>{{ filtroOrdemDirecao === 'asc' ? 'Crescente' : 'Decrescente' }}</button>
                                <button 
                                        type="button"
                                        @click="tipoFiltro = 'exato'"
                                        class="btn flex-1 transition-all"
                                        :class="tipoFiltro === 'exato' 
                                            ? 'bg-primary hover:bg-primary-hover text-texto-escuro' 
                                            : 'opacity-60 hover:bg-layout-fundo/10 bg-layout-fundo/99 text-texto-claro/99 border-comum'"
                                    >
                                        <i class="fa fa-crosshairs mr-2"></i>Filtro Exato
                                    </button>

                                    <button 
                                        type="button"
                                        @click="tipoFiltro = 'amplo'"
                                        class="btn flex-1 transition-all"
                                        :class="tipoFiltro === 'amplo' 
                                            ? 'bg-primary hover:bg-primary-hover text-texto-escuro font-semibold' 
                                            : 'opacity-60 hover:bg-layout-fundo/10 bg-layout-fundo/99 text-texto-claro/99 border-comum'"
                                    >
                                        <i class="fa fa-arrows-alt mr-2"></i>Filtro Amplo
                                    </button>
                                </div>
                            </div>                         
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-texto-claro/50">Resultados encontrados: {{ props.registrosIniciais?.total || 0 }}</span>
                    <button @click="novoRegistro" v-show="btnnovoregistro" class="bg-primary hover:bg-primary-hover text-texto-escuro text-xs font-bold py-2 px-4 rounded-full transition-all flex items-center gap-1.5 shadow-sm">
                        <i class="fas fa-plus"></i> Novo Registro
                    </button>
                </div>

                <div class="bg-layout-painel border border-comum rounded-lg overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-layout-fundo border-b border-comum text-texto-claro/70 text-xs font-semibold uppercase tracking-wider">
                                <th class="p-4">Código</th>
                                <th class="p-4">Identificação</th>
                                <th class="p-4">Destino</th>
                                <th class="p-4 text-center">Tipo</th>
                                <th class="p-4 text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-comum/40 text-sm text-texto-claro/90">
                        <template v-if="listagem.data && listagem.data.length > 0">
                            <tr v-for="item in listagem.data" :key="item.id" class="hover:bg-layout-fundo/40 transition-colors">
                                <td class="px-6 py-4 font-mono font-bold text-xs">{{ item.id }}</td>
                                <td class="px-6 py-4 font-medium text-texto-claro">{{ item.clidentificacao }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-primary">{{ item.clrota }}</td>
                                <td class="p-4">
                                    <span v-if="item.cltipo === 1" class="text-xs bg-comum px-2 py-0.5 rounded border border-comum cursor-pointer">Módulo Nível 1</span>
                                    <span v-else-if="item.cltipo === 2" class="text-xs bg-primary/20 text-primary px-2 py-0.5 rounded border border-primary/30 cursor-pointer">Perfil</span>
                                    <span v-else class="text-xs bg-layout-fundo px-2 py-0.5 rounded border border-comum cursor-pointer">Outro</span>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex flex-wrap gap-1 justify-center">
                                        <button v-if="item.clstatus === 1" class="w-7 h-7 flex items-center justify-center rounded-full text-xs btn-green cursor-pointer" title="Registro Ativo"><i class="fas fa-check"></i></button>
                                        <button v-else class="w-7 h-7 flex items-center justify-center rounded-full text-xs btn-red cursor-pointer" title="Registro Inativo"><i class="fas fa-exclamation-triangle"></i></button>

                                        <button v-if="permissao?.alterar" @click="editarRegistro(item.id)" class="w-7 h-7 flex items-center justify-center rounded-full text-xs btn-blue cursor-pointer" title="Editar Registro"><i class="fas fa-edit"></i></button>
                                        <button v-if="permissao?.apagar" @click="apagarRegistro(item.id)" class="w-7 h-7 flex items-center justify-center rounded-full text-xs btn-red cursor-pointer" title="Apagar Registro"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td colspan="7" class="p-8 text-center text-texto-claro/40 font-medium">
                                Não foram encontrados registros para exibição.
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>

                <!--
                <div class="flex items-center justify-between border border-comum bg-layout-fundo-card rounded-lg px-6 py-4 shadow-sm">
                    <div class="text-sm text-texto-comum">
                        Exibindo de <span class="font-semibold text-texto-claro">{{ listagem.from }}</span> até 
                        <span class="font-semibold text-texto-claro">{{ listagem.to }}</span> de um total de 
                        <span class="font-semibold text-texto-claro">{{ listagem.total }}</span> registros.
                    </div>

                    <nav class="inline-flex -space-x-px rounded-md shadow-sm" aria-label="Paginação">
            
                        <button
                            :disabled="listagem.current_page === 1"
                            @click="navegarParaPagina(listagem.first_page)"
                            class="inline-flex items-center px-3 py-2 text-sm transition-all border border-comum text-texto-comum hover:bg-layout-fundo-subtle rounded-l-md"
                            :class="listagem.current_page === 1 ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'"
                            title="Primeira Página"><i class="fa fa-step-backward"></i>
                        </button>

                        <button
                            v-for="(link, index) in listagem.links"
                            :key="index"
                            :disabled="!link.url"
                            @click="navegarParaPagina(sistemajs.extrairNumeroPaginaPaginacao(link.url))"
                            v-html="sistemajs.traduzirLabelpaginacao(link.label)"
                            class="inline-flex items-center px-3 py-2 text-sm transition-all focus:z-20 border"
                            :class="[
                                // Botão Ativo/Página Atual
                                link.active 
                                    ? 'z-10 bg-primary border-primary text-texto-escuro font-semibold' 
                                    : 'border-comum text-texto-comum hover:bg-layout-fundo-subtle',
                                // Botão Desabilitado (ex: anterior quando se está na página 1)
                                !link.url ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'
                            ]"/>

                        <button
                            :disabled="listagem.current_page === listagem.last_page"
                            @click="navegarParaPagina(listagem.last_page)"
                            class="inline-flex items-center px-3 py-2 text-sm transition-all border border-comum text-texto-comum hover:bg-layout-fundo-subtle rounded-r-md"
                            :class="listagem.current_page === listagem.last_page ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'"
                            title="Última Página"><i class="fa fa-step-forward"></i>
                        </button>
                    </nav>
                </div>
-->





                <div class="flex flex-col md:flex-row items-center justify-between gap-4 border border-comum bg-layout-fundo-card rounded-lg px-6 py-4 shadow-sm">
                    
                    <div class="flex flex-wrap items-center gap-4 text-sm text-texto-comum">
                        <div>
                            Exibindo de <span class="font-semibold text-texto-claro">{{ listagem.from }}</span> até 
                            <span class="font-semibold text-texto-claro">{{ listagem.to }}</span> de um total de 
                            <span class="font-semibold text-texto-claro">{{ listagem.total }}</span> registros.
                        </div>
                        <div class="flex items-center border-l border-comum pl-4">
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-xs">Exibir:</span>
                                <select 
                                    ref="regporpagina" @change="alterarQuantidadeRegistros"
                                    :class="['bg-layout-fundo border border-comum pl-2 pr-8 py-1 text-texto-claro text-xs focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none cursor-pointer w-auto min-w-18 h-full', permissao?.alterar || permissao?.inserir ? 'rounded-l' : 'rounded']">
                                    <option :value="10" selected>10</option>
                                    <option :value="25">25</option>
                                    <option :value="50">50</option>
                                    <option :value="100">100</option>
                                    <option :value="200">200</option>
                                    <option :value="500">500</option>
                                    <option :value="1000">1000</option>
                                </select>
                            </div>
                            <button v-if="permissao?.alterar || permissao?.inserir" @click="salvarregporpagina(regporpagina.value)" type="button" title="Aplicar quantidade como Padrão" class="bg-primary hover:bg-primary-hover text-texto-escuro px-2 py-1.5 border border-primary transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none rounded-r text-xs"><i class="fa fa-check"></i></button>
                        </div>





                        <div class="flex items-center gap-2 border-l border-comum focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none pl-4" title="Ir para uma página específica">
                            <span class="text-xs">Ir para:</span>
                            <input 
                                    type="number"
                                    @keyup.enter="navegarParaPagina($event.target.value)"
                                    @input="$event.target.value = $event.target.value.replace(/\D/g, ''); 
                                    sistemajs.soNumeros($event.target);"
                                    min="1"
                                    :max="listagem.last_page"
                                    placeholder="Pág."
                                    class="w-14 bg-layout-fundo border border-comum rounded px-2 py-1 text-center text-texto-claro text-xs focus:outline-none focus:border-primary"
                                />
                        </div>
                    </div>

                    <nav class="inline-flex flex-wrap -space-x-px rounded-md shadow-sm" aria-label="Paginação">

                        <button
                            :disabled="listagem.current_page === 1"
                            @click="navegarParaPagina(listagem.first_page)"
                            class="inline-flex items-center px-3 py-2 text-sm transition-all border border-comum text-texto-comum hover:bg-layout-fundo-subtle rounded-l-md"
                            :class="listagem.current_page === 1 ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'"
                            title="Primeira Página"><i class="fa fa-step-backward"></i>
                        </button>

                        <button
                            v-for="(link, index) in linksPaginacaoFiltrados"
                            :key="index"
                            :disabled="!link.url"
                            @click="navegarParaPagina(sistemajs.extrairNumeroPaginaPaginacao(link.url))"
                            v-html="sistemajs.traduzirLabelpaginacao(link.label)"
                            class="inline-flex items-center px-3 py-2 text-sm transition-all focus:z-20 border"
                            :class="[
                                link.active 
                                    ? 'z-10 bg-primary border-primary text-texto-escuro font-semibold' 
                                    : 'border-comum text-texto-comum hover:bg-layout-fundo-subtle',
                                !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                            ]"
                        />

                        <button
                            :disabled="listagem.current_page === listagem.last_page"
                            @click="navegarParaPagina(listagem.last_page)"
                            class="inline-flex items-center px-3 py-2 text-sm transition-all border border-comum text-texto-comum hover:bg-layout-fundo-subtle rounded-r-md"
                            :class="listagem.current_page === listagem.last_page ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'"
                            title="Última Página"><i class="fa fa-step-forward"></i>
                        </button>
                    </nav>
                </div>
            </div>

            <div v-if="abaAtiva === 'cadastro'" class="bg-layout-painel border border-comum rounded-lg p-6 shadow-sm">
                <form @submit.prevent="submeterFormulario" class="flex flex-col gap-5">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-texto-claro/80">Identificação <span class="dadorequerido">*</span></label>
                            <input v-model="form.clidentificacao" type="text" maxlength="150" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" required />
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-texto-claro/80">Rota Base</label>
                            <input v-model="form.base" type="text" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-texto-claro/80">Tipo de Módulo</label>
                            <select v-model="form.tipo" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all">
                                <option :value="1">Módulo Nível 1</option>
                                <option :value="2">Perfil</option>
                                <option :value="3">SubCadastro</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1 justify-center pt-5">
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" v-model="form.status" class="sr-only peer" />
                                <div class="w-11 h-6 bg-comum rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-texto-claro after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                <span class="ml-3 text-sm font-medium text-texto-claro/80">Registro Ativo</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-comum pt-4 mt-4">
                        <button type="button" @click="alternarAba('inicio')" class="opacity-60 hover:bg-layout-fundo/10 bg-layout-fundo/99 text-texto-claro/99 border border-comum font-bold py-2.5 px-6 rounded-lg text-sm transition-all cursor-pointer">
                            <i class="fas fa-arrow-left pr-5"></i>Voltar
                        </button>
                        <button type="submit" :disabled="form.processing" class="bg-primary hover:bg-primary-hover disabled:opacity-50 text-texto-escuro font-bold py-2.5 px-6 rounded-lg text-sm transition-all shadow-md flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-save"></i> 
                            {{ form.processing ? 'Salvando...' : 'Salvar Registro' }}
                        </button>
                    </div>

                </form>
            </div>

            <div v-if="abaAtiva === 'colunas'" class="bg-layout-painel border border-comum rounded-lg p-6 shadow-sm">
                <div class="flex flex-col gap-2">
                    <h3 class="text-md font-bold text-texto-claro">Configurações das Colunas Dinâmicas</h3>
                    <p class="text-xs text-texto-claro/60">Configurações herdadas do módulo estrutural antigo para o mapeamento dinâmico.</p>
                </div>
                </div>

        </div>
    </Layout>
</template>