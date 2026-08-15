<script setup>
import Layout from '@/Layouts/PainelInterno.vue';
import { ref, onMounted, computed, nextTick } from 'vue';
import { route } from 'ziggy-js';
import { useForm, usePage, Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import * as sistemajs from '@/sistema.js';

// --- PROPS COMPARTILHADAS ---
const propriet1 = defineProps({
    cal: Object,
    colunasCal: Array,
    permissao: Object,       // Lista de Módulos (Cals) para vincular ao Menu
    listaMenusPai: Array     // Lista de Menus para seleção de nível acima (menuacima)
});

const usepage1 = usePage();

// --- ESTADOS REATIVOS ---
const abaAtiva1 = ref('inicio'); // inicio, cadastro, colunas
const exibirFiltrosAvancados1 = ref(false);
const calid1 = propriet1.cal?.cal?.[0]; // ID da Cal de Menus
const animarelatorios1 = ref(false);

let permissao1 = null;
let pageatual1 = 1;
let qtdporpg1 = 10;
let tempomessage1 = sistemajs.getCfgSist(13, 'valor1') ?? 5000;
let exibirmessage1 = sistemajs.getCfgSist(29, 'valor1') ?? 'nao';
let allCals = ref([]);
let allMenus = ref([]);

const mnidentificacaoref = ref(null);

// -- Funcoes Assincronas
const asyncfuncs = async () => {
  allCals.value = await sistemajs.getAllCals();
  allMenus.value = await sistemajs.getAllMenus();
};

// --- FORMULÁRIO DE FILTROS ---
const formFiltro1 = useForm({
    campoPesquisa: '',
    filtroStatus: '0', // 0=Ambos, 1=Ativo, 2=Inativo
    filtroDataInicio: '',
    filtroDataFim: '',
    filtroCampoOrdem: 'mnidentificacao',
    filtroOrdemDirecao: 'asc',
    tipoFiltro: 'amplo',
});

// --- LISTAGEM DE DADOS ---
const listagem1 = ref({
    current_page: 1,
    data: [],
    links: [],
    total: 0
});

// --- FORMULÁRIO PRINCIPAL (CRUD MENU) ---
const formcad1 = useForm({
    id: 0,
    mnidentificacao: '',
    mnicone: '',
    mnnumeracao: '',
    fkidmenunivelacima: 0,
    fkidcal: 0,
    mnsequencia: 0,
    mnstatus: 1
});

const listaIcones = [
  { value: 'fas fa-address-book', label: 'Contatos' },
  { value: 'fas fa-calculator', label: 'Calculadora' },
  { value: 'fas fa-heart', label: 'Coração' },
  { value: 'fas fa-calendar-alt', label: 'Calendário' },
  { value: 'fas fa-circle', label: 'Círculo' },
  { value: 'fas fa-cogs', label: 'Engrenagens' },
  { value: 'fas fa-angle-right', label: 'Seta' },
  { value: 'fas fa-bookmark', label: 'Marcador' },
  { value: 'fas fa-tag', label: 'Tag' },
  { value: 'fas fa-user', label: 'Usuário' },
  { value: 'fas fa-chart-line', label: 'Chart' },
  { value: 'fab fa-youtube', label: 'Youtube' },
  { value: 'fas fa-dot-circle', label: 'Dot Circle' },
  { value: 'fas fa-clone', label: 'Clone' },
  { value: 'fas fa-code-branch', label: 'Branch' },
  { value: 'fas fa-certificate', label: 'Certificate' },
  { value: 'fas fa-circle-notch', label: 'Circle Notch' },
  { value: 'fas fa-bullseye', label: 'Bullseye' },
  { value: 'fas fa-adjust', label: 'Adjust' },
  { value: 'fas fa-info-circle', label: 'Circle Info' },
  { value: 'fas fa-spinner', label: 'Spinner' }
];

// --- CARREGAMENTO INICIAL ---
onMounted(() => {
    permissaoPrincipal1();
    qtdporpg1 = sistemajs.setoptionregporpagina(sistemajs.getCfgUserCal(usepage1.props.auth?.user?.id, calid1));
    filtrar1(); // Carrega listagem inicial
    asyncfuncs();
});


function permissaoPrincipal1() {
    permissao1 = sistemajs.getPermissaoCal(calid1);
    if (usepage1.props.app_debug) {
        console.log('Permissões do Menu:', permissao1);
    }
}

// --- NAVEGAÇÃO E AÇÕES ---
const alternarAba1 = (aba = 'inicio') => {
    if (aba !== 'inicio' && formcad1.id === 0 && aba !== 'cadastro') {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Selecione ou salve um registro para continuar.', tipo: 'warning', tempo: tempomessage1 });
        return;
    }

    if (aba === 'cadastro') {
        if (!permissao1?.alterar && !permissao1?.inserir && !permissao1?.consultar) {
            sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Você não tem Permissão de Acesso.', tipo: 'warning', tempo: tempomessage1 });
            aba = 'inicio';
        }
    }

    abaAtiva1.value = aba;
};

const navegarParaPagina = (page = 1) => { 
    filtrar1(page);
};

const limparFiltro1 = () => {  
    formFiltro1.reset();
    formFiltro1.clearErrors();
    filtrar1();
};

const ordemDirecaoFiltro1 = () => {
    formFiltro1.filtroOrdemDirecao = formFiltro1.filtroOrdemDirecao === 'asc' ? 'desc' : 'asc';
    filtrar1();
};

const alterarqtdporpagina1 = () => {
    filtrar1();
};

// --- PAGINAÇÃO CUSTOMIZADA ---
const paginacaoInteracao1 = computed(() => {
    if (!listagem1?.value?.links || listagem1?.value?.links.length === 0) return [];

    const totalLinks = listagem1?.value?.links.length;
    const paginaAtual = listagem1?.value?.current_page;
    const maxVisiveis = 2;

    return listagem1?.value?.links.filter((link, index) => {
        if (index === 0 || index === totalLinks - 1) return true;
        const numPagina = parseInt(link.label);
        if (isNaN(numPagina)) return true;

        const noMiolo = numPagina >= paginaAtual - maxVisiveis && numPagina <= paginaAtual + maxVisiveis;
        const ehPaginaFinal = index === totalLinks - 2 || index === totalLinks - 3;

        return noMiolo || ehPaginaFinal;
    });
});

function resetFormCad1() {
    formcad1.reset();
    formcad1.id = 0;
    formcad1.mnstatus = 1;  
    formcad1.mnicone = 0;    
}

const novoRegistro1 = () => {
    resetFormCad1();
    alternarAba1('cadastro');
    nextTick(() => {
        if (mnidentificacaoref.value) {
            mnidentificacaoref.value.focus();
        }
    });
};

// --- SUBMIT DO FORMULÁRIO (SALVAR) ---
const salvarRegistro1 = () => {
    if (permissao1?.alterar || permissao1?.inserir) {
        formcad1.post(route('controle.menus.salvar'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                if (exibirmessage1 === 'sim') {
                    const msg = usepage1.props.flash?.sucesso || 'Processamento Realizado com Sucesso';
                    sistemajs.mostrarPopup({ titulo: 'Sucesso', conteudo: msg, tipo: 'success', tempo: tempomessage1 });
                }
                if (sistemajs.getConfigUser(usepage1.props.auth?.user?.id, 17, 'valor1') === 'sim') {
                    resetFormCad1();
                    abaAtiva1.value = 'inicio';
                    filtrar1(pageatual1);
                } else {
                    novoRegistro1();
                }
            },
            onError: (errors) => {
                const mensagemErro = typeof errors === 'string' 
                    ? errors 
                    : (Object.values(errors)[0] || 'Impossível Prosseguir com o Processamento');
                sistemajs.mostrarPopup({ 
                    titulo: 'Impossível Prosseguir', 
                    conteudo: mensagemErro, 
                    tipo: 'danger', 
                    tempo: tempomessage1 
                });
            }
        });
    }else{
        sistemajs.mostrarPopup({
            titulo: 'Restrições',
            conteudo: 'Não há Credenciais para Executar esta Ação',
            tipo: 'warning'
        });
    }
};

const editarRegistro1 = (registro) => {
    if (permissao1?.alterar) {
        formcad1.id = registro.id;
        formcad1.mnidentificacao = registro.mnidentificacao ?? '';
        formcad1.mnicone = registro.mnicone ?? '';
        formcad1.mnnumeracao = registro.mnnumeracao ?? '';
        formcad1.fkidmenunivelacima = (registro.fkidmenunivelacima > 0 ? registro.fkidmenunivelacima : 0);
        formcad1.fkidcal = registro.fkidcal ?? 0;
        formcad1.mnsequencia = registro.mnsequencia ?? 0;
        formcad1.mnstatus = Number(registro.mnstatus) === 1 ? 1 : 0;
        abaAtiva1.value = 'cadastro';
    }else{
        sistemajs.mostrarPopup({
            titulo: 'Restrições',
            conteudo: 'Não há Credenciais para Executar esta Ação',
            tipo: 'warning'
        });
    }
};



async function apagarRegistro1(idreg)
{
    if(permissao1.apagar){
        let decisaoapagar1 = await sistemajs.mostrarPopupDecisao({
            titulo: 'Apagar Registro',
            conteudo: 'Deseja Prosseguir',
            tipo: 'warning',
            bloquearCliqueFora: true, // Força a interação com os botões
            exibirNao: false,
            exibirCancelar: true,
            textoSim: 'SIM',
            textoNao: 'NÃO',
            textoCancelar: 'CANCELAR'
        });

        if (decisaoapagar1 === 'sim') {
            axios.get(route('controle.menus.remover', { id: idreg }))
            .then((response) => {
                const result = JSON.parse(response.data.resultado);
                if (result.status === 'fail') {
                    sistemajs.mostrarPopup({
                        titulo: 'Erro no Processamento',
                        conteudo: result.message,
                        tipo: 'danger'
                    });
                } else {
                    if(exibirmessage1 === 'sim'){
                        sistemajs.mostrarPopup({
                            titulo: 'Sucesso!',
                            conteudo: result.message,
                            tipo: 'success'
                        });
                    }
                    filtrar1(pageatual1);
                }
            })
            .catch((error) => {
                sistemajs.mostrarPopup({
                    titulo: 'Falha',
                    conteudo: 'Impossível Realizar Processamento',
                    tipo: 'danger'
                });
            });
        } else if (decisaoapagar1 === 'nao') {
            //minhaFuncaoParaDescartar();
        } else {
            //console.log("Operação cancelada pelo usuário.");
        }
    }else{
        sistemajs.mostrarPopup({
            titulo: 'Restrições',
            conteudo: 'Não há Credenciais para Executar esta Ação',
            tipo: 'warning'
        });
    }
};

const updateRegistro1 = (idreg, campo = 'status') => {
    if (permissao1?.alterar) {
        let data = {
            idregistro: idreg,
            campo: campo,
        };

        router.post(route('controle.menus.update'), data, {
            preserveState: true,
            preserveScroll: true,
            onError: (errors) => { 
                const msg = typeof errors === 'string' ? errors : (Object.values(errors)[0] || 'Falha no Processamento');
                sistemajs.mostrarPopup({ 
                    titulo: 'Falha no Processamento', 
                    conteudo: msg, 
                    tipo: 'danger', 
                    tempo: tempomessage1 
                });
            },
            onSuccess: () => {
                if (exibirmessage1 === 'sim') {
                    const msgSucesso = usepage1.props.flash?.sucesso || 'Processamento Realizado com Sucesso';
                    sistemajs.mostrarPopup({ 
                        titulo: 'Sucesso no Processamento', 
                        conteudo: msgSucesso, 
                        tipo: 'info', 
                        tempo: tempomessage1 
                    });
                }
                filtrar1(pageatual1);
            }
        });
    }else{
        sistemajs.mostrarPopup({
            titulo: 'Restrições',
            conteudo: 'Não há Credenciais para Executar esta Ação',
            tipo: 'warning'
        });
    }
};

// --- SUBMISSÃO DE FILTRO ---
const filtrar1 = (pg = 1) => {
    if (permissao1?.consultar) {
        pageatual1 = pg;
        let data = {
            campoPesquisa: formFiltro1.campoPesquisa,
            statusfiltro: formFiltro1.filtroStatus,
            datainiciofiltro: formFiltro1.filtroDataInicio,
            datafinalfiltro: formFiltro1.filtroDataFim,
            campoordem: formFiltro1.filtroCampoOrdem,
            ordem: formFiltro1.filtroOrdemDirecao,
            tipofiltro: formFiltro1.tipoFiltro,
            regPg: qtdporpg1,
            page: pg,
        };

        router.post(route('controle.menus.lista'), data, {
            preserveState: true,
            preserveScroll: true,
            onError: (errors) => { 
                const msg = typeof errors === 'string' ? errors : (Object.values(errors)[0] || 'Erro na Listagem');
                sistemajs.mostrarPopup({ 
                    titulo: 'Erro Listagem', 
                    conteudo: msg, 
                    tipo: 'danger', 
                    tempo: 4000 
                });
            },
            onSuccess: () => {
                if(usepage1.props.flash?.resultado != null){
                    listagem1.value = JSON.parse(usepage1.props.flash?.resultado).data;
                }else{
                
                }
            }
        });
    }else{
        sistemajs.mostrarPopup({
            titulo: 'Restrições',
            conteudo: 'Não há Credenciais para Executar esta Ação',
            tipo: 'warning'
        });
    }
};



// --- RELATÓRIOS ---
async function geraRelatorio(extensao = 'pdf', tipo = 0) {
    let janelaPdf = null;
    if (extensao === 'pdf') {
        janelaPdf = window.open('about:blank', '_blank');
    }

    try {
        const urlEndpoint = route('controle.menus.relatorio');
        const payload = {
            extensao: extensao || 'pdf',
            tipo: tipo || 0,
            campoPesquisa: formFiltro1.campoPesquisa ? String(formFiltro1.campoPesquisa).trim() : '',
            statusfiltro: formFiltro1.filtroStatus ?? '0',
            datainiciofiltro: formFiltro1.filtroDataInicio ?? '',
            datafinalfiltro: formFiltro1.filtroDataFim ?? '',
            campoordem: (formFiltro1.filtroCampoOrdem && formFiltro1.filtroCampoOrdem !== 'undefined') ? formFiltro1.filtroCampoOrdem : 'id',
            ordem: (formFiltro1.filtroOrdemDirecao && formFiltro1.filtroOrdemDirecao !== 'undefined') ? formFiltro1.filtroOrdemDirecao : 'asc',
            tipofiltro: formFiltro1.tipoFiltro ?? 'amplo',
            titulorelatorio: 'Relação de Menus do Sistema',
            modulo: 'Menus',
        };

        const response = await axios.post(urlEndpoint, payload, {
            responseType: 'arraybuffer',
            headers: { 'Accept': 'application/pdf, application/msword, text/csv, application/json' }
        });

        const contentTypeHeader = response.headers?.get ? response.headers.get('content-type') : response.headers?.['content-type'];
        const contentType = contentTypeHeader || (
            extensao === 'doc' ? 'application/msword' :
            extensao === 'csv' ? 'text/csv' : 'application/pdf'
        );

        if (contentType.includes('application/json') || contentType.includes('text/html')) {
            const decoder = new TextDecoder('utf-8');
            const jsonText = decoder.decode(response.data);
            let mensagemErro = 'Impossível Processar Relatório.';
            if (contentType.includes('application/json')) {
                try {
                    const parsed = JSON.parse(jsonText);
                    mensagemErro = parsed.message || mensagemErro;
                } catch (e) { }
            }
            throw new Error(mensagemErro);
        }

        const blob = new Blob([response.data], { type: contentType });

        if (extensao === 'pdf') {
            const pdfUrl = URL.createObjectURL(blob);
            if (janelaPdf && !janelaPdf.closed) {
                janelaPdf.location.href = pdfUrl;
            } else {
                window.open(pdfUrl, '_blank');
            }
            sistemajs.mostrarPopup({ titulo: 'Processamento Finalizado', conteudo: 'Geração de Relatório Concluída com Sucesso', tipo: 'info', tempo: tempomessage1 });
            setTimeout(() => URL.revokeObjectURL(pdfUrl), 60000);
        } else {
            if (janelaPdf) janelaPdf.close();
            let filename = `relatorio_menus_${new Date().getTime()}.${extensao}`;
            const disposition = response.headers?.get ? response.headers.get('content-disposition') : response.headers?.['content-disposition'];
            if (disposition && disposition.includes('attachment')) {
                const matches = /filename\*?=['"]?([^'"]+)?['"]?(;|$)/i.exec(disposition);
                if (matches && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '').trim();
                }
            }

            const downloadUrl = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.style.display = 'none';
            link.href = downloadUrl;
            link.download = filename;
            document.body.appendChild(link);
            link.click();

            setTimeout(() => {
                document.body.removeChild(link);
                URL.revokeObjectURL(downloadUrl);
            }, 1000);

            sistemajs.mostrarPopup({ titulo: 'Processamento Finalizado', conteudo: 'Download do Relatório Iniciado.', tipo: 'info', tempo: tempomessage1 });
        }
    } catch (error) {
        if (janelaPdf) janelaPdf.close();
        let mensagemErro = error.message || 'Impossível Processar Relatório.';
        sistemajs.mostrarPopup({ titulo: 'Falha no Processamento', conteudo: mensagemErro, tipo: 'danger', tempo: tempomessage1 });
    }
}
</script>

<template>
    <Head title="Controle de Menus" />

    <Layout>
        <div class="pb-2 max-full flex flex-col gap-4">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-comum pb-2 gap-2">
                <div>
                    <h6 class="text font-bold text-texto-claro flex items-center gap-2">
                        {{ propriet1.cal?.cal?.[2] || 'Gerenciamento de Menus' }}
                    </h6>
                </div>
            </div>

            <!-- TABS DA TELA -->
            <div class="border-b border-comum -mt-3">
                <nav class="flex space-x-4" aria-label="Tabs">
                    <button @click="alternarAba1('inicio')" :class="[abaAtiva1 === 'inicio' ? 'border-primary text-primary' : 'border-transparent text-texto-claro/60 hover:text-texto-claro hover:border-comum', 'py-2 px-1 border-b-2 font-medium text-sm transition-all flex items-center gap-2 cursor-pointer']">
                        <i class="fas fa-home"></i> Início
                    </button>
                    <button @click="alternarAba1('cadastro')" :class="[abaAtiva1 === 'cadastro' ? 'border-primary text-primary' : 'border-transparent text-texto-claro/60 hover:text-texto-claro hover:border-comum', 'py-2 px-1 border-b-2 font-medium text-sm transition-all flex items-center gap-2 cursor-pointer']">
                        <i class="fas fa-edit"></i> {{ formcad1.id > 0 ? 'Editar Cadastro' : 'Cadastro' }}
                    </button>
                    <button @click="alternarAba1('colunas')" :class="[abaAtiva1 === 'colunas' ? 'border-primary text-primary' : 'border-transparent text-texto-claro/60 hover:text-texto-claro hover:border-comum', 'py-2 px-1 border-b-2 font-medium text-sm transition-all flex items-center gap-2 cursor-pointer']">
                        <i class="fas fa-clipboard-list"></i> Colunas
                    </button>
                </nav>
            </div>

            <!-- ABA 1: INÍCIO / LISTAGEM -->
            <div v-if="abaAtiva1 === 'inicio'" class="flex flex-col gap-4">
                <div class="bg-layout-painel border border-comum rounded-lg p-4 shadow-sm flex flex-col gap-4">
                    <div class="flex items-center w-full box-border">
                        
                        <!-- BOTOES DE IMPRESSAO -->
                        <div class="relative inline-block h-10 shrink-0">
                            <button 
                                type="button" @click="animarelatorios1 = !animarelatorios1" title="Relatórios"
                                class="bg-primary hover:bg-primary-hover text-texto-escuro h-10 w-15 rounded-l-lg transition-all cursor-pointer focus:outline-none flex items-center justify-center box-border select-none pr-3 pl-3">
                                <i class="fas fa-print text-sm transition-transform duration-200 cursor-pointer pl-2 pr-5" :class="{ 'rotate-90 cursor-pointer': animarelatorios1 }"></i>
                                <i class="fas fa-chevron-down text-[10px] pr-3 cursor-pointer"></i>
                            </button>

                            <div v-if="animarelatorios1" class="absolute left-0 mt-1 w-48 bg-layout-painel border border-comum rounded-lg shadow-xl z-50 overflow-hidden py-1">
                                <button type="button" title="Gerar Relatório - PDF" @click="geraRelatorio('pdf')" class="w-full text-left px-4 py-2.5 text-sm text-texto-claro/90 hover:bg-texto-claro/10 transition-colors flex items-center gap-2.5 cursor-pointer">
                                    <i class="fas fa-file-pdf text-xs text-red"></i> Relatório - PDF
                                </button>
                                <button type="button" title="Gerar Relatório - DOC" @click="geraRelatorio('doc')" class="w-full text-left px-4 py-2.5 text-sm text-texto-claro/90 hover:bg-texto-claro/10 transition-colors flex items-center gap-2.5 cursor-pointer">
                                    <i class="fas fa-file-word text-blue text-xs"></i> Relatório - DOC
                                </button>
                                <hr class="border-comum">
                                <button type="button" title="Gerar Relatório - CSV" @click="geraRelatorio('csv')" class="w-full text-left px-4 py-2.5 text-sm text-texto-claro/90 hover:bg-texto-claro/10 transition-colors flex items-center gap-2.5 cursor-pointer">
                                    <i class="fas fa-file-csv text-green text-xs opacity-60"></i> Relatório - CSV
                                </button>
                            </div>
                            <div v-if="animarelatorios1" @click="animarelatorios1 = false" class="fixed inset-0 z-40"></div>
                        </div>

                        <!-- OPCOES DE FILTROS -->
                        <button @click="exibirFiltrosAvancados1 = !exibirFiltrosAvancados1" type="button" class="bg-primary hover:bg-primary-hover text-texto-escuro h-10 px-3.5 border-l border-primary/30 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none" title="Mais Opções de Filtros">
                            <i :class="['fas', exibirFiltrosAvancados1 ? 'fa-angle-double-up' : 'fa-filter']"></i>
                        </button>
                        <div class="relative flex-1">
                            <input v-model="formFiltro1.campoPesquisa" @keyup.enter="filtrar1(1)" type="text" placeholder="Pesquisar por identificação, numeração ou ícone..." class="w-full p-2.5 pl-10 border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all filtro1" />
                            <i class="fas fa-search absolute left-3.5 top-3.5 text-texto-claro/40 text-sm"></i>
                        </div>
                        
                        <button type="button" @click="filtrar1(1)" class="bg-primary hover:bg-primary-hover text-texto-escuro h-10 px-3.5 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none" title="Pesquisar">
                            <i class="fas fa-search text-sm"></i> <label class="pl-3 cursor-pointer">Filtrar</label>
                        </button>
                        <button type="button" @click="limparFiltro1" class="btn-black h-10 px-3.5 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none rounded-r-lg" title="Limpar Filtro">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                    
                    <!-- OPÇÕES DE FILTROS AVANÇADOS -->
                    <div v-if="exibirFiltrosAvancados1">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-comum pt-4 transition-all">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-medium text-texto-claro/70">Status do Registro</label>
                                <select v-model="formFiltro1.filtroStatus" class="select-customizado w-full p-2.5 pr-10 rounded-lg border border-comum bg-layout-fundo text-texto-claro text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all filtro1">
                                    <option value="0">Ambos</option>
                                    <option value="1">Ativo</option>
                                    <option value="2">Inativo</option>
                                </select>
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-medium text-texto-claro/70">Data Inicial</label>
                                <input v-model="formFiltro1.filtroDataInicio" type="date" class="p-2 rounded-lg border border-comum bg-layout-fundo text-texto-claro text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary filtro1" />
                            </div>

                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-medium text-texto-claro/70">Data Final</label>
                                <input v-model="formFiltro1.filtroDataFim" type="date" class="p-2 rounded-lg border border-comum bg-layout-fundo text-texto-claro text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary filtro1" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-medium text-texto-claro/70">Ordenar Por</label>
                                <select v-model="formFiltro1.filtroCampoOrdem" class="select-customizado w-full p-2.5 pr-10 rounded-lg border border-comum bg-layout-fundo text-texto-claro text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all filtro1">
                                    <option value="mnidentificacao">Identificação</option>
                                    <option value="mnnumeracao">Numeração</option>
                                    <option value="mnsequencia">Sequência</option>
                                    <option value="id">Código</option>
                                    <option value="fkidcal">Cal/Módulo</option>
                                    <option value="fkidmenunivelacima">Nó Pai</option>
                                    <option value="mnversao">Data</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1 md:col-span-2">
                                <label class="text-xs font-medium text-texto-claro/70">Ordem</label>
                                <div class="btn-group">
                                    <button @click="ordemDirecaoFiltro1" class="bg-primary hover:bg-primary-hover text-texto-escuro cursor-pointer rounded-l-lg pr-3 pl-3">
                                        <i :class="['mr-2 fas', formFiltro1.filtroOrdemDirecao === 'asc' ? 'fa-sort-alpha-down' : 'fa-sort-alpha-up']"></i>{{ formFiltro1.filtroOrdemDirecao === 'asc' ? 'Crescente' : 'Decrescente' }}
                                    </button>
                                    <button type="button" @click="formFiltro1.tipoFiltro = 'exato'" class="btn flex-1 transition-all" :class="formFiltro1.tipoFiltro === 'exato' ? 'bg-primary hover:bg-primary-hover text-texto-escuro' : 'opacity-60 hover:bg-layout-fundo/10 bg-layout-fundo/99 text-texto-claro/99 border-comum'">
                                        <i class="fa fa-crosshairs mr-2"></i>Filtro Exato
                                    </button>
                                    <button type="button" @click="formFiltro1.tipoFiltro = 'amplo'" class="btn flex-1 transition-all" :class="formFiltro1.tipoFiltro === 'amplo' ? 'bg-primary hover:bg-primary-hover text-texto-escuro font-semibold' : 'opacity-60 hover:bg-layout-fundo/10 bg-layout-fundo/99 text-texto-claro/99 border-comum'">
                                        <i class="fa fa-arrows-alt mr-2"></i>Filtro Amplo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-texto-claro/50">Resultados encontrados: {{ listagem1?.data?.length || 0 }}</span>
                    <!-- PAGINAÇÃO -->
                    <nav class="inline-flex flex-wrap -space-x-px rounded-md shadow-sm" aria-label="Paginação" v-if="listagem1?.data && listagem1.data.length > 0">
                        <button :disabled="listagem1?.current_page === 1" @click="navegarParaPagina(1)" class="inline-flex items-center px-3 py-2 text-sm transition-all border border-comum text-texto-comum hover:bg-layout-fundo-subtle rounded-l-md" :class="listagem1?.current_page === 1 ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'" title="Primeira Página">
                            <i class="fa fa-step-backward"></i>
                        </button>
                        <button v-for="(link, index) in paginacaoInteracao1" :key="index" :disabled="!link.url" @click="navegarParaPagina(sistemajs.extrairNumeroPaginaPaginacao(link.url))" v-html="sistemajs.traduzirLabelpaginacao(link.label)" class="inline-flex items-center px-3 py-2 text-sm transition-all focus:z-20 border" :class="[link.active ? 'z-10 bg-primary border-primary text-texto-escuro font-semibold' : 'border-comum text-texto-comum hover:bg-layout-fundo-subtle', !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer']"/>
                        <button :disabled="listagem1?.current_page === listagem1?.last_page" @click="navegarParaPagina(listagem1?.last_page)" class="inline-flex items-center px-3 py-2 text-sm transition-all border border-comum text-texto-comum hover:bg-layout-fundo-subtle rounded-r-md" :class="listagem1?.current_page === listagem1?.last_page ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'" title="Última Página">
                            <i class="fa fa-step-forward"></i>
                        </button>
                    </nav>
                    <!-- PAGINAÇÃO -->
                    <button @click="novoRegistro1" v-show="permissao1?.inserir" class="btn-pill bg-primary hover:bg-primary-hover text-texto-escuro cursor-pointer">
                        <i class="fas fa-plus"></i> Novo Registro
                    </button>
                </div>

                <!-- TABELA DE LISTAGEM DE MENUS -->
                <div class="bg-layout-painel border border-comum rounded-lg overflow-x-auto shadow-sm transition-all">
                    <table v-if="permissao1?.consultar" class="w-full text-left border-collapse min-w-160">
                        <thead>
                            <tr class="bg-layout-fundo border-b border-comum text-texto-claro/70 text-xs font-semibold uppercase tracking-wider">
                                <th class="p-3 clicavel" @click="sistemajs.setordenarpor(formFiltro1, 'id', () => filtrar1(pageatual1))" title="Clique para ordenar por este campo">Código <i :class="[sistemajs.setordenarporicone(formFiltro1, 'id'), 'text-xs transition-colors']"></i></th>
                                <th class="p-3 clicavel" @click="sistemajs.setordenarpor(formFiltro1, 'mnidentificacao', () => filtrar1(pageatual1))" title="Clique para ordenar por este campo">Identificação <i :class="[sistemajs.setordenarporicone(formFiltro1, 'mnidentificacao'), 'text-xs transition-colors']"></i></th>
                                <th class="p-3 text-center clicavel" @click="sistemajs.setordenarpor(formFiltro1, 'mnnumeracao', () => filtrar1(pageatual1))" title="Clique para ordenar por este campo">Numeração <i :class="[sistemajs.setordenarporicone(formFiltro1, 'mnnumeracao'), 'text-xs transition-colors']"></i></th>
                                <th class="p-3 clicavel" @click="sistemajs.setordenarpor(formFiltro1, 'fkidmenunivelacima', () => filtrar1(pageatual1))" title="Clique para ordenar por este campo">Nó Pai<i :class="[sistemajs.setordenarporicone(formFiltro1, 'fkidmenunivelacima'), 'text-xs transition-colors']"></i></th>
                                <th class="p-3 text-center clicavel" @click="sistemajs.setordenarpor(formFiltro1, 'mnsequencia', () => filtrar1(pageatual1))" title="Clique para ordenar por este campo">Seq. <i :class="[sistemajs.setordenarporicone(formFiltro1, 'mnsequencia'), 'text-xs transition-colors']"></i></th>
                                <th class="p-3 text-right" @click="sistemajs.setordenarpor(formFiltro1, 'mnversao', () => filtrar1(pageatual1))"> <i :class="[sistemajs.setordenarporicone(formFiltro1, 'mnversao'), 'text-xs transition-colors']"></i> Ações</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs text-texto-claro/90">
                            <template v-if="listagem1?.data && listagem1.data.length > 0">
                                <tr v-for="item in listagem1.data" :key="item.id" class="border-b border-comum last:border-b-0 hover:bg-layout-fundo/40 transition-colors">
                                    <!-- Código -->
                                    <td class="p-2.5 font-mono font-bold text-xs align-top clicavel" @click="permissao1?.alterar ? editarRegistro1(item) : null">
                                        <div class="line-clamp-6 wrap-break-word">
                                            {{ item.id }}
                                        </div>
                                    </td>

                                    <td class="p-2.5 font-medium text-texto-claro text-xs min-w-50 align-top clicavel" :title="item.mnidentificacao" @click="permissao1?.alterar ? editarRegistro1(item) : null">
                                        <div class="line-clamp-6 wrap-break-word leading-relaxed"><i v-if="item.mnicone" :class="[item.mnicone, 'mr-1.5 text-primary']"></i>
                                            {{ item.mnidentificacao }} 
                                        </div>
                                    </td>

                                    <!-- Numeração -->
                                    <td class="p-2.5 text-center font-mono text-primary text-xs align-top clicavel" @click="permissao1?.alterar ? editarRegistro1(item) : null">
                                        <div class="line-clamp-6 wrap-break-word">{{ item.mnnumeracao || '-' }}</div>
                                    </td>
                                    
                                    <!-- Menu Superior (Limitado a 1 ou 2 linhas) -->
                                    <td class="p-2.5 text-xs max-w-40 align-top clicavel" :title="item.menuacima?.mnidentificacao" @click="permissao1?.alterar ? editarRegistro1(item) : null">
                                        <div class="line-clamp-6 wrap-break-word">
                                            {{ item.menuacima?.mnidentificacao || 'Nenhum (Raiz)' }}
                                        </div>
                                    </td>

                                    <!-- Sequência -->
                                    <td class="p-2.5 text-center font-mono font-semibold text-xs align-top" @click="permissao1?.alterar ? editarRegistro1(item) : null">
                                        <div class="line-clamp-6 wrap-break-word">
                                            {{ item.mnsequencia ?? 0 }}
                                        </div>
                                    </td>

                                    <!-- Ações -->
                                    <td class="p-2.5 text-right align-top">
                                        <div class="flex flex-wrap gap-1 justify-end items-center line-clamp-6 wrap-break-word">
                                            <button type="button" v-if="item.mnstatus === 1" @click="permissao1?.alterar && updateRegistro1(item.id, 'status')" class="w-6 h-6 flex items-center justify-center rounded-full btn-green select-none cursor-pointer"  title="Registro Ativo">
                                                <i class="fas fa-check text-[10px]"></i>
                                            </button>
                                            <button type="button" v-else @click="permissao1?.alterar && updateRegistro1(item.id, 'status')" class="w-6 h-6 flex items-center justify-center rounded-full btn-red select-none cursor-pointer"  title="Registro Inativo">
                                                <i class="fas fa-exclamation-triangle text-[10px]"></i>
                                            </button>

                                            <button type="button" v-if="permissao1?.alterar" @click="editarRegistro1(item)" class="w-6 h-6 flex items-center justify-center rounded-full btn-blue select-none cursor-pointer" title="Editar Registro">
                                                <i class="fas fa-edit text-[10px]"></i>
                                            </button>

                                            <button type="button" v-if="permissao1?.apagar" @click="apagarRegistro1(item.id)" class="w-6 h-6 flex items-center justify-center rounded-full btn-red select-none cursor-pointer" title="Apagar Registro">
                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                            </button>
                                        </div>
                                    </td> 
                                </tr>
                            </template>
                            <tr v-else>
                                <td colspan="6" class="p-8 text-center text-texto-claro/40 font-medium">
                                    Não foram encontrados registros para exibição.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINAÇÃO -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 border border-comum bg-layout-fundo-card rounded-lg px-6 py-4 shadow-sm" v-if="listagem1?.data && listagem1.data.length > 0">
                    <div class="flex flex-wrap items-center gap-4 text-sm text-texto-comum">
                        <div>
                            Exibindo de <span class="font-semibold text-texto-claro">{{ listagem1?.from }}</span> até 
                            <span class="font-semibold text-texto-claro">{{ listagem1?.to }}</span> de um total de 
                            <span class="font-semibold text-texto-claro">{{ listagem1?.total }}</span> registros.
                        </div>
                        <div class="flex items-center border-l border-comum pl-4">
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="text-xs">Exibir:</span>
                                <select @change="alterarqtdporpagina1" v-model="qtdporpg1" :class="['bg-layout-fundo border border-comum pl-2 pr-8 py-1 text-texto-claro text-xs focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none cursor-pointer w-auto min-w-18 h-full', permissao1?.alterar || permissao1?.inserir ? 'rounded-l' : 'rounded']">
                                    <option v-for="opcao in sistemajs.opcoesQtdPagina1" :key="opcao" :value="opcao">{{ opcao }}</option>
                                </select>
                            </div>
                            <button v-if="permissao1?.alterar || permissao1?.inserir" @click="sistemajs.setregporpagina(calid1, qtdporpg1, usepage1.props.auth?.user?.id, null)" type="button" title="Aplicar quantidade como Padrão" class="bg-primary hover:bg-primary-hover text-texto-escuro px-2 py-1.5 border border-primary transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none rounded-r text-xs"><i class="fa fa-check"></i></button>
                        </div>
                        <div class="flex items-center gap-2 border-l border-comum focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none pl-4" title="Ir para uma página específica">
                            <span class="text-xs">Ir para:</span>
                            <input 
                                type="number"
                                @keyup.enter="navegarParaPagina($event.target.value)"
                                @input="$event.target.value = $event.target.value.replace(/\D/g, ''); sistemajs.soNumeros($event.target);"
                                min="1"
                                :max="listagem1?.last_page"
                                placeholder="Pág."
                                class="w-14 bg-layout-fundo border border-comum rounded px-2 py-1 text-center text-texto-claro text-xs focus:outline-none focus:border-primary"/>
                        </div>
                    </div>

                    <nav class="inline-flex flex-wrap -space-x-px rounded-md shadow-sm" aria-label="Paginação">
                        <button :disabled="listagem1?.current_page === 1" @click="navegarParaPagina(1)" class="inline-flex items-center px-3 py-2 text-sm transition-all border border-comum text-texto-comum hover:bg-layout-fundo-subtle rounded-l-md" :class="listagem1?.current_page === 1 ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'" title="Primeira Página">
                            <i class="fa fa-step-backward"></i>
                        </button>

                        <button v-for="(link, index) in paginacaoInteracao1" :key="index" :disabled="!link.url" @click="navegarParaPagina(sistemajs.extrairNumeroPaginaPaginacao(link.url))" v-html="sistemajs.traduzirLabelpaginacao(link.label)" class="inline-flex items-center px-3 py-2 text-sm transition-all focus:z-20 border" :class="[link.active ? 'z-10 bg-primary border-primary text-texto-escuro font-semibold' : 'border-comum text-texto-comum hover:bg-layout-fundo-subtle', !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer']"/>

                        <button :disabled="listagem1?.current_page === listagem1?.last_page" @click="navegarParaPagina(listagem1?.last_page)" class="inline-flex items-center px-3 py-2 text-sm transition-all border border-comum text-texto-comum hover:bg-layout-fundo-subtle rounded-r-md" :class="listagem1?.current_page === listagem1?.last_page ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'" title="Última Página">
                            <i class="fa fa-step-forward"></i>
                        </button>
                    </nav>
                </div>
            </div>

            <!-- ABA 2: CADASTRO / EDIÇÃO DE MENU -->
            <div v-if="abaAtiva1 === 'cadastro'" class="bg-layout-painel border border-comum rounded-lg p-6 shadow-sm">
                <form @submit.prevent="salvarRegistro1" class="flex flex-col gap-5">
                    
                    <input type="hidden" v-model="formcad1.id"/>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="flex flex-col gap-1 md:col-span-3">
                            <label class="text-sm font-medium text-texto-claro/80">Identificação <span class="text-red-500">*</span></label>
                            <input v-model="formcad1.mnidentificacao" ref="mnidentificacaoref" type="text" maxlength="150" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" required />
                        </div>

                        <!-- SWITCHER STATUS -->
                        <div class="flex items-center justify-start md:justify-end h-full pt-0 md:pt-5 md:col-span-1">
                            <label class="inline-flex items-center cursor-pointer select-none">
                                <input 
                                    type="checkbox" 
                                    v-model="formcad1.mnstatus" 
                                    :true-value="1" 
                                    :false-value="0" 
                                    class="switcher-input switcher-primary" 
                                />
                                <span class="switcher-track switcher-size-md">
                                    <span class="switcher-thumb"></span>
                                </span>
                                <span :class="['ml-2.5 text-xs font-semibold transition-colors', formcad1.mnstatus === 1 ? 'text-texto-claro' : 'text-texto-claro/50']">
                                    {{ formcad1.mnstatus === 1 ? 'Registro Ativo' : 'Registro Inativo' }}
                                </span>
                            </label>   
                        </div>

                        <div class="flex items-center justify-start md:justify-end h-full pt-0 md:pt-5">
                            <button v-if="permissao1?.alterar || permissao1?.inserir" type="submit" :disabled="formcad1.processing" class="btn inline-flex items-center justify-center gap-1.5 py-2 px-4 text-sm font-bold bg-primary hover:bg-primary-hover text-texto-escuro disabled:opacity-50 cursor-pointer">
                                <i class="fas fa-save text-sm"></i> 
                                <span>{{ formcad1.processing ? 'Salvando...' : 'Salvar Registro' }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-texto-claro/80">Menu Superior (Nó Pai)</label>
                            <select v-model="formcad1.fkidmenunivelacima" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all">
                                <option :key="0" :value="0">Nenhum (Menu Raiz)</option>
                                <option v-for="mPai in allMenus" :key="mPai.id" :value="mPai.id">
                                    {{ mPai.mnnumeracao }} - {{ mPai.mnidentificacao }}
                                </option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-texto-claro/80">Módulo / Cal Vinculado</label>
                            <select v-model="formcad1.fkidcal" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all">
                                <option :value="0">Nenhum Módulo Vinculado</option>
                                <option v-for="cItem in allCals" :key="cItem.id" :value="cItem.id">
                                   {{ cItem.id }} - {{ cItem.clidentificacao }} 
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-texto-claro/80">Numeração <small>(Ex: 1.1, 2.0)</small></label>
                            <input v-model="formcad1.mnnumeracao" type="text" maxlength="20" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all"/>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-texto-claro/80">Sequência de Exibição</label>
                            <div class="flex items-center w-full box-border">
                                <div class="relative inline-block h-10 shrink-0">
                                    <button type="button" @click="(formcad1.mnsequencia > 0 ? formcad1.mnsequencia-- : null)" title="Diminui Valor" class="bg-primary hover:bg-primary-hover text-texto-escuro h-10 w-15 rounded-l-lg transition-all cursor-pointer focus:outline-none flex items-center justify-center box-border select-none pr-3 pl-3">
                                        <i class="fas fa-minus text-sm cursor-pointer pl-2 pr-5"></i>
                                    </button>
                                </div>
                                <div class="relative flex-1">
                                    <input v-model="formcad1.mnsequencia" type="text" @input="sistemajs.soNumeros($event.target); formcad1.mnsequencia = $event.target.value" placeholder="1" class="w-full p-2.5 pl-10 border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all filtro1" />
                                </div>
                                <button type="button" @click="formcad1.mnsequencia++" title="Aumenta Valor" class="bg-primary hover:bg-primary-hover text-texto-escuro h-10 px-3.5 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none rounded-r-lg" >
                                    <i class="fas fa-plus text-sm cursor-pointer pl-2 pr-5"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-texto-claro/80">Ícone <small>(Ex: fas fa-home)</small></label>
                            <select v-model="formcad1.mnicone" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all">
                                <option :value="0">Nenhum Ícone</option>
                                <option v-for="cItem in listaIcones" :key="cItem.value" :value="cItem.value">
                                   {{ cItem.value }} - {{ cItem.label }} 
                                </option>
                            </select>
                        </div>
                    </div>



                    <div class="flex flex-col">
                        <label class="text-xs text-red-500">* Dados Obrigatórios</label>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-comum pt-4 mt-4">
                        <button type="button" @click="alternarAba1('inicio')" class="opacity-60 hover:bg-layout-fundo/10 bg-layout-fundo/99 text-texto-claro/99 border border-comum font-bold py-2.5 px-6 rounded-lg text-sm transition-all cursor-pointer">
                            <i class="fas fa-arrow-left pr-2"></i>Voltar
                        </button>
                        <button v-if="permissao1?.alterar || permissao1?.inserir" type="submit" :disabled="formcad1.processing" class="bg-primary hover:bg-primary-hover disabled:opacity-50 text-texto-escuro font-bold py-2.5 px-6 rounded-lg text-sm transition-all shadow-md flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-save"></i> 
                            {{ formcad1.processing ? 'Salvando...' : 'Salvar Registro' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- ABA 3: COLUNAS DINÂMICAS -->
            <div v-if="abaAtiva1 === 'colunas'" class="bg-layout-painel border border-comum rounded-lg p-6 shadow-sm">
                <div class="flex flex-col gap-2">
                    <h3 class="text-md font-bold text-texto-claro">Configurações das Colunas Dinâmicas de Menus</h3>
                    <p class="text-xs text-texto-claro/60">Configurações herdadas para mapeamento dinâmico da estrutura do sistema.</p>
                </div>
            </div>

        </div>
    </Layout>
</template>