<script setup>
import Layout from '@/Layouts/PainelInterno.vue';
import { ref, onMounted, computed, nextTick } from 'vue';
import { route } from 'ziggy-js';
import { useForm, usePage, Head, router } from '@inertiajs/vue3';
import * as sistemajs from '@/sistema.js';


const propriet1 = defineProps({
    cal: Object,
    colunasCal: Array,
    permissao: Object
});

const usepage1 = usePage();

// --- ESTADOS REATIVOS (TABS E FILTROS) ---
const abaAtiva1 = ref('inicio'); // inicio, cadastro, colunas
const exibirFiltrosAvancados1 = ref(false);
const calid1 = propriet1.cal?.cal?.[0];
const animarelatorios1 = ref(false);

let permissao1 = null;
let pageatual1 = 1;
let qtdporpg1 = 10;
let tempomessage1 = sistemajs.getCfgSist(13, 'valor1') ?? 5000;
let exibirmessage1 = sistemajs.getCfgSist(29, 'valor1') ?? 'nao';

const clidentificacaoref = ref(null);

// --- FORMULÁRIO DE FILTROS (INERTIA) ---
const formFiltro1 = useForm({
    campoPesquisa: '',
    filtroStatus: '0', // 0=Ambos, 1=Ativo, 2=Inativo
    filtroDataInicio: '',
    filtroDataFim: '',
    filtroCampoOrdem: 'clidentificacao',
    filtroOrdemDirecao: 'asc',
    tipoFiltro: 'amplo',
});

const listagem1 = ref({
    current_page: 1,
    data: [],
    links: [],
    total: 0
});

// --- FORMULÁRIO PRINCIPAL (CRUD) ---
const formcad1 = useForm({
    id: 0,
    clidentificacao: '',
    clbase: '',
    clrota: '',
    cltipo: 1,
    clstatus: 0,
    clobserve: '',
});

// --- CARREGAMENTO INICIAL ---
onMounted(() => {
    // Inicializações se necessário
    permissaoPrincipal1();
    qtdporpg1 = sistemajs.setoptionregporpagina(sistemajs.getCfgUserCal(usepage1.props.auth?.user?.id, calid1));
    filtrar1(); // Carrega a listagem inicial
    
});

function permissaoPrincipal1(){
    permissao1 = sistemajs.getPermissaoCal(calid1);
    if (usepage1.props.app_debug) {
        console.log('Permissões do Cal:', permissao1);
    }
    if(permissao1.consultar != true){

    }
    if(permissao1.inserir != true){

    }else{

    }

    if(permissao1.inserir != true && permissao1.alterar != true){
        //$(".btnSalvarPrincipal").hide();
        //$(".btnsalvarestatistica4").hide();
    }
    else{
        //$(".btnSalvarPrincipal").show();
        //$(".btnsalvarestatistica4").show();
    } 

    if(permissao1.apagar != true){
        //$(".veiculolisttblcolunatd").addClass('hide');
        //$(".btnremoverfoto").hide();
    }
    else{
        //$(".veiculolisttblcolunatd").removeClass('hide');
        //$(".btnremoverfoto").show();
    }
}

// --- MÉTODOS DE NAVEGAÇÃO E AÇÃO ---
const alternarAba1 = (aba = 'inicio') => {
    if (aba !== 'inicio' && formcad1.id === 0 && aba !== 'cadastro') {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Selecione ou salve um registro para continuar.', tipo: 'warning', tempo: tempomessage1 });
        return;
    }

    if(aba === 'inicio'){
        /*
        if (usepage1.props.app_debug) {
            console.log('Limpar Campos do cadastro e listar registros');
        } */       
    }

    if(aba === 'cadastro'){
        if(permissao1?.alterar || permissao1?.inserir || permissao1?.consultar){
            
        }else{
            sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Você não tem Permissão de Acesso.', tipo: 'warning', tempo: tempomessage1 });
            aba = 'inicio';
        }
        /*
        if (usepage1.props.app_debug) {
            console.log('Limpar Campos e Iniciar cadastro');
        } */       
    }

    if(aba === 'colunas'){
        /*
        if (usepage1.props.app_debug) {
            console.log('Campos de colunas');
        } */       
    }

    abaAtiva1.value = aba;
};

// 2. Função inteligente para mudar de página sem perder os filtros existentes na URL
const navegarParaPagina = (page = 1) => { 
    filtrar1(page);
};

const limparFiltro1 = () => {  
    formFiltro1.reset(); // Voltas variáveis ao padrão
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

const paginacaoInteracao1 = computed(() => {
    if (!listagem1?.value?.links || listagem1?.value?.links.length === 0) return [];

    const totalLinks = listagem1?.value?.links.length;
    const paginaAtual = listagem1?.value?.current_page;
    const maxVisiveis = 2; // Quantidade de números ao redor da página atual

    return listagem1?.value?.links.filter((link, index) => {
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

/**
 * Reseta o form ao padrões default
 */
function resetFormCad1(){
    formcad1.reset();
    formcad1.id = 0;
    formcad1.clstatus = 1;    
}

const novoRegistro1 = () => {
    resetFormCad1();
    alternarAba1('cadastro');
    nextTick(() => {
        if (clidentificacaoref.value) {
            clidentificacaoref.value.focus();
        }
    });
    /*
    setTimeout(() => {
        if (clidentificacaoref.value) {
            clidentificacaoref.value.focus();
        }
    }, 8000);*/    

};

// --- SUBMIT DO FORMULÁRIO (SALVAR) ---
const salvarRegistro1 = () => {
    if(permissao1.alterar || permissao1.inserir){
        formcad1.post(route('controle.cals.salvar'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: (response) => {
                if(exibirmessage1 === 'sim'){
                    sistemajs.mostrarPopup({ titulo: 'Sucesso', conteudo: JSON.parse(usepage1.props.flash?.resultado).message ?? 'Sucesso no Processamento', tipo: 'success', tempo: tempomessage1 });
                }
                if(sistemajs.getConfigUser(usepage1.props.auth?.user?.id, 17, 'valor1') == 'sim'){ // volta para listagem sim
                    resetFormCad1();
                    abaAtiva1.value = 'inicio';
                    filtrar1(pageatual1);
                }else{ // continua no cadastro e limpa as variáveis
                    novoRegistro1();
                }
            },
            onError: (errors) => {
                const mensagemErro = typeof errors === 'string' 
                    ? errors 
                    : (Object.values(errors)[0] || 'Impossível Prosseguir com o Processamento');
                //JSON.parse(errors.resultado).message
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
    if(permissao1.alterar){
        formcad1.id = registro.id;
        formcad1.clidentificacao = registro.clidentificacao;
        formcad1.clbase = registro.clbase;
        formcad1.clrota = registro.clrota;
        formcad1.cltipo = registro.cltipo;
        //formcad1.clstatus = registro.clstatus === 1;
        formcad1.clstatus = Number(registro.clstatus) === 1 ? 1 : 0;
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
            axios.get(route('controle.cals.remover', { id: idreg }))
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

const updateRegistro1 = (idreg, campo = 'clstatus') => {
    if(permissao1.alterar){
        let data = {
            idregistro: idreg,
            campo: campo,
        };

        router.post(route('controle.cals.update'), data, {
            preserveState: true,
            replace: true,
            onError: (errors) => { 
                sistemajs.mostrarPopup({ 
                    titulo: 'Falha no Processamento', 
                    conteudo: JSON.parse(errors.resultado).message ?? 'Indeterminado', 
                    tipo: 'danger', 
                    tempo: tempomessage1 
                });
            },
            onSuccess: () => {
                if(exibirmessage1 === 'sim'){
                    sistemajs.mostrarPopup({ 
                        titulo: 'Sucesso no Processamento', 
                        conteudo: JSON.parse(usepage1.props.flash?.resultado).message ?? 'Indeterminado', 
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

// --- SUBMISSÃO DO FILTRO / PESQUISA ---
const filtrar1 = (pg = 1) => {
    if(permissao1.consultar){
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

        router.post(route('controle.cals.lista'), data, {
            preserveState: true,
            replace: true,
            onError: (errors) => { 
                sistemajs.mostrarPopup({ 
                    titulo: 'Erro Listagem', 
                    conteudo: JSON.parse(errors.resultado).message ?? 'Indeterminado', 
                    tipo: 'danger', 
                    tempo: 4000 
                });
            },
            onSuccess: () => {
                /*sistemajs.mostrarPopup({ 
                    titulo: 'OK Listagem', 
                    conteudo: JSON.parse(usepage1.props.flash?.resultado).message ?? 'Outro2', 
                    tipo: 'info', 
                    tempo: 4000 
                });*/
                if(usepage1.props.flash?.resultado != null){
                    listagem1.value = JSON.parse(usepage1.props.flash?.resultado).data;
                }else{
                
                }
                //listagem.value = djson.data;
                /*console.log(djson.message);
                console.log(djson.status);
                console.log(djson.data);*/
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




async function geraRelatorio(extensao = 'pdf', tipo = 0){

    try {
        const urlEndpoint = route('controle.cals.calrelatorio');
        
        // Constrói o payload extraindo os dados do formulário reativo
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
            titulorelatorio: 'Relação de Cals do Sistema',
            modulo: 'Cals',
        };

        // 1. EXECUÇÃO ASSÍNCRONA COM AWAIT (Resolve a Promise da resposta)
        const response = await axios.post(urlEndpoint, payload, {
            responseType: 'arraybuffer',
                headers: {
                    'Accept': 'application/octet-stream, application/pdf, application/msword, text/csv'
                }
        });

        // 2. Extração segura do Content-Type dos headers resolvidos
        const contentTypeHeader = response.headers?.get ? response.headers.get('content-type') : response.headers?.['content-type'];
        const contentType = contentTypeHeader || (
            extensao === 'doc' ? 'application/msword' :
            extensao === 'csv' ? 'text/csv' : 'application/pdf'
        );

        // 4. Se o servidor retornou JSON ou HTML no buffer (Falha tratada pelo Laravel)
        if (contentType.includes('application/json') || contentType.includes('text/html')) {
            const decoder = new TextDecoder('utf-8');
            const jsonText = decoder.decode(response.data);
            
            let mensagemErro = 'Impossível Processar Relatório.';
            if (contentType.includes('application/json')) {
                try {
                    const parsed = JSON.parse(jsonText);
                    mensagemErro = parsed.message || mensagemErro;
                } catch (e) {
                    // Fallback se falhar o parse
                }
            }
            throw new Error(mensagemErro);
        }

        const blob = new Blob([response.data], { type: contentType });

        if (extensao === 'pdf') {
            const pdfUrl = URL.createObjectURL(blob);
            const newWin = window.open(pdfUrl, '_blank');

            if (!newWin || newWin.closed || typeof newWin.closed === 'undefined') {
                sistemajs.mostrarPopup({
                    titulo: 'Aviso de Pop-up',
                    conteudo: 'Pop-up bloqueado pelo navegador. Permita pop-ups para visualizar o relatório.',
                    tipo: 'warning',
                    tempo: tempomessage1
                });
            } else {
                sistemajs.mostrarPopup({
                    titulo: 'Processamento Finalizado',
                    conteudo: 'Geração de Relatório Concluída com Sucesso',
                    tipo: 'info',
                    tempo: tempomessage1
                });
            }

            setTimeout(() => URL.revokeObjectURL(pdfUrl), 10000);

        } else { // 4. DOWNLOAD DIRETO PARA DOC E CSV
            let filename = `relatorio_cals_${new Date().getTime()}.${extensao}`;

            // Tenta extrair o nome do arquivo enviado pelo Laravel via Content-Disposition
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
            }, 200);

            sistemajs.mostrarPopup({
                titulo: 'Processamento Finalizado',
                conteudo: 'Download do Relatório Iniciado.',
                tipo: 'info',
                tempo: tempomessage1
            });
        }
    } catch (error) {
        //console.error('[geraRelatorio] Erro na geração do relatório:', error);
        let mensagemErro = 'Impossível Processar Relatório.';
        // Deserialização segura de ArrayBuffer em caso de erro HTTP (422/500)
        if (error.response?.data) {
            try {
                const decoder = new TextDecoder('utf-8');
                const jsonText = decoder.decode(error.response.data);
                const parsed = JSON.parse(jsonText);
                mensagemErro = parsed.message || mensagemErro;
            } catch (e) {
                mensagemErro = `Falha no processamento (Erro HTTP ${error.response.status || 500}).`;
            }
        } else if (error.message) {
            mensagemErro = error.message;
        }

        sistemajs.mostrarPopup({
            titulo: 'Falha no Processamento',
            conteudo: mensagemErro,
            tipo: 'danger',
            tempo: tempomessage1
        });
    } finally {
        //if (loadingState) loadingState.value = false;
    }
}











</script>

<template>
    <Head title="Controle de Cals" />

    <Layout>
        <div class="pb-2 max-full flex flex-col gap-4">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-comum pb-2 gap-2">
                <div>
                    <h6 class="text font-bold text-texto-claro flex items-center gap-2">
                        {{ propriet1.cal?.cal?.[2] }}
                    </h6>
                </div>
            </div>

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

            <div v-if="abaAtiva1 === 'inicio'" class="flex flex-col gap-4">
                <div class="bg-layout-painel border border-comum rounded-lg p-4 shadow-sm flex flex-col gap-4">
                    <div class="flex items-center w-full box-border">
                        <!-- INICIO - Botoes de Impressao -->
                        <div class="relative inline-block h-10 shrink-0">
                            <button 
                                type="button" @click="animarelatorios1 = !animarelatorios1" title="Relatórios"
                                class="bg-primary hover:bg-primary-hover text-texto-escuro h-10 w-15 rounded-l-lg transition-all cursor-pointer focus:outline-none flex items-center justify-center box-border select-none pr-3 pl-3">
                                <i class="fas fa-print text-sm transition-transform duration-200 cursor-pointer pl-2 pr-5" :class="{ 'rotate-90 cursor-pointer': animarelatorios1 }"></i>
                                <i class="fas fa-chevron-down text-[10px] pr-3 cursor-pointer"></i>
                            </button>

                            <div v-if="animarelatorios1" class="absolute left-0 mt-1 w-48 bg-layout-painel border border-comum rounded-lg shadow-xl z-50 overflow-hidden py-1">
                                <button 
                                    type="button" title="Gerar Relatório - PDF"
                                    @click="geraRelatorio('pdf')"
                                    class="w-full text-left px-4 py-2.5 text-sm text-texto-claro/90 hover:bg-texto-claro/10 transition-colors flex items-center gap-2.5 cursor-pointer">
                                    <i class="fas fa-file-pdf text-xs text-red"></i> Relatório - PDF
                                </button>
                                
                                <button 
                                    type="button" title="Gerar Relatório - DOC"
                                    @click="geraRelatorio('doc')"
                                    class="w-full text-left px-4 py-2.5 text-sm text-texto-claro/90 hover:bg-texto-claro/10 transition-colors flex items-center gap-2.5 cursor-pointer">
                                    <i class="fas fa-file-word text-blue text-xs"></i> Relatório - DOC
                                </button>
                                <hr class="border-comum">
                                
                                <button 
                                    type="button" title="Gerar Relatório - CSV"
                                    @click="geraRelatorio('csv')"
                                    class="w-full text-left px-4 py-2.5 text-sm text-texto-claro/90 hover:bg-texto-claro/10 transition-colors flex items-center gap-2.5 cursor-pointer">
                                    <i class="fas fa-file-csv text-green text-xs opacity-60"></i> Relatório - CSV
                                </button>
                            </div>

                            <div v-if="animarelatorios1" @click="animarelatorios1 = false" class="fixed inset-0 z-40"></div>
                        </div>
                        <!-- FIM - Botoes de Impressao -->
                        

                        <!-- INICIO - OPCOES DE FILTROS -->
                        <button @click="exibirFiltrosAvancados1 = !exibirFiltrosAvancados1" type="button" class="bg-primary hover:bg-primary-hover text-texto-escuro h-10 px-3.5 border-l border-primary/30 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none" title="Mais Opções de Filtros">
                            <i :class="['fas', exibirFiltrosAvancados1 ? 'fa-angle-double-up' : 'fa-filter']"></i>
                        </button>
                        <div class="relative flex-1">
                            <input v-model="formFiltro1.campoPesquisa" @keyup.enter="filtrar1" type="text" placeholder="Dados para pesquisa..." class="w-full p-2.5 pl-10 border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all filtro1" />
                            <i class="fas fa-search absolute left-3.5 top-3.5 text-texto-claro/40 text-sm"></i>
                        </div>
                        
                        <button type="button" @click="filtrar1" class="bg-primary hover:bg-primary-hover text-texto-escuro h-10 px-3.5 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none" title="Pesquisar">
                            <i class="fas fa-search text-sm"></i> <label class="pl-3 cursor-pointer">Filtrar</label>
                        </button>
                        <button type="button" @click="limparFiltro1" class="btn-black h-10 px-3.5 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none rounded-r-lg" title="Limpar Filtro">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                        <!-- FIM - OPCOES DE FILTROS -->
                    </div>
                    
                    <!-- INICIO - OPCOES DE FILTROS AVANCADOS-->
                    <div v-if="exibirFiltrosAvancados1">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t border-comum pt-4 transition-all">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-medium text-texto-claro/70">Status do Registro</label>
                                <select v-model="formFiltro1.filtroStatus" class="select-customizado w-full p-2.5 pr-10 rounded-lg border border-comum bg-layout-fundo text-texto-claro text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all filtro1" id="campoordem" data-default="0">
                                    <option value="0" selected >Ambos</option>
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

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex flex-col gap-1">
                                <label class="text-xs font-medium text-texto-claro/70">Ordenar Por</label>
                                <select v-model="formFiltro1.filtroCampoOrdem" class="select-customizado w-full p-2.5 pr-10 rounded-lg border border-comum bg-layout-fundo text-texto-claro text-sm focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all filtro1">
                                    <option value="clidentificacao">Identificação</option>
                                    <option value="clobserve">Obs</option>
                                    <option value="id">Código</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1 md:col-span-2">
                                <label class="text-xs font-medium text-texto-claro/70">Ordem</label>
                                <div class="btn-group">
                                    <button @click="ordemDirecaoFiltro1" class="bg-primary hover:bg-primary-hover text-texto-escuro cursor-pointer rounded-l-lg pr-3 pl-3"><i :class="['mr-2 fas', formFiltro1.filtroOrdemDirecao === 'asc' ? 'fa-sort-alpha-down' : 'fa-sort-alpha-up']"></i>{{ formFiltro1.filtroOrdemDirecao === 'asc' ? 'Crescente' : 'Decrescente' }}</button>
                                <button 
                                        type="button"
                                        @click="formFiltro1.tipoFiltro = 'exato'"
                                        class="btn flex-1 transition-all"
                                        :class="formFiltro1.tipoFiltro === 'exato' 
                                            ? 'bg-primary hover:bg-primary-hover text-texto-escuro' 
                                            : 'opacity-60 hover:bg-layout-fundo/10 bg-layout-fundo/99 text-texto-claro/99 border-comum'">
                                        <i class="fa fa-crosshairs mr-2"></i>Filtro Exato
                                    </button>

                                    <button 
                                        type="button"
                                        @click="formFiltro1.tipoFiltro = 'amplo'"
                                        class="btn flex-1 transition-all"
                                        :class="formFiltro1.tipoFiltro === 'amplo' 
                                            ? 'bg-primary hover:bg-primary-hover text-texto-escuro font-semibold' 
                                            : 'opacity-60 hover:bg-layout-fundo/10 bg-layout-fundo/99 text-texto-claro/99 border-comum'">
                                        <i class="fa fa-arrows-alt mr-2"></i>Filtro Amplo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- FIM - OPCOES DE FILTROS AVANCADOS-->
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-xs text-texto-claro/50">Resultados encontrados: {{listagem1?.data?.length || 0 }}</span>
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
                    <button @click="novoRegistro1" v-show="permissao1?.inserir" class="btn-pill bg-primary hover:bg-primary-hover text-texto-escuro">
                        <i class="fas fa-plus"></i> Novo Registro
                    </button>
                </div>













                <!-- INICIO - TABELA DE LISTAGEM-->
                <div class="bg-layout-painel border border-comum rounded-lg overflow-x-auto shadow-sm transition-all">

                    <table v-if="(permissao1?.consultar)" class="w-full text-left border-collapse min-w-160">
                        <thead>
                            <tr class="bg-layout-fundo border-b border-comum text-texto-claro/70 text-xs font-semibold uppercase tracking-wider">
                            <th class="p-3 clicavel" @click="sistemajs.setordenarpor(formFiltro1, 'id', () => filtrar1(pageatual1))" title="Clique para ordenar por este campo">Código <i :class="[sistemajs.setordenarporicone(formFiltro1, 'id'), 'text-xs transition-colors']"></i></th>
                            <th class="p-3 clicavel" @click="sistemajs.setordenarpor(formFiltro1, 'clidentificacao', () => filtrar1(pageatual1))" title="Clique para ordenar por este campo">Identificação <i :class="[sistemajs.setordenarporicone(formFiltro1, 'clidentificacao'), 'text-xs transition-colors']"></i></th>
                            <th class="p-3 clicavel" @click="sistemajs.setordenarpor(formFiltro1, 'clrota', () => filtrar1(pageatual1))" title="Clique para ordenar por este campo">Destino <i :class="[sistemajs.setordenarporicone(formFiltro1, 'clrota'), 'text-xs transition-colors']"></i></th>
                            <th class="p-3 text-center" @click="sistemajs.setordenarpor(formFiltro1, 'cltipo', () => filtrar1(pageatual1))" title="Clique para ordenar por este campo">Tipo <i :class="[sistemajs.setordenarporicone(formFiltro1, 'cltipo'), 'text-xs transition-colors']"></i></th>
                            <th class="p-3 text-right" @click="sistemajs.setordenarpor(formFiltro1, 'clversao', () => filtrar1(pageatual1))" title="Clique para ordenar por este campo"> <i :class="[sistemajs.setordenarporicone(formFiltro1, 'clversao'), 'text-xs transition-colors']"></i></th>
                            </tr>
                        </thead>
                        <tbody class="text-xs text-texto-claro/90">
                        <template v-if="listagem1?.data && listagem1.data.length > 0">
                            <tr 
                            v-for="item in listagem1.data" 
                            :key="item.id" 
                            class="border-b border-comum last:border-b-0 hover:bg-layout-fundo/40 transition-colors"
                            >
                            <td class="p-2.5 font-mono font-bold text-xs align-top clicavel" @click="permissao1?.alterar ? editarRegistro1(item) : null">
                                <div class="line-clamp-6 wrap-break-word">
                                    {{ item.id }}
                                </div>
                            </td>
                            <td class="p-2.5 font-medium text-texto-claro text-xs min-w-50 align-top clicavel" :title="item.clidentificacao" @click="permissao1?.alterar ? editarRegistro1(item) : null">
                                <div class="line-clamp-6 wrap-break-word leading-relaxed">
                                    {{ item.clidentificacao }} 
                                </div>
                            </td>
                            <td class="p-2.5 font-medium text-texto-claro text-xs min-w-50 align-top clicavel" :title="item.clrota" @click="permissao1?.alterar ? editarRegistro1(item) : null">
                                <div class="line-clamp-6 wrap-break-word leading-relaxed">
                                    {{ item.clrota }} 
                                </div>
                            </td>
                            <td class="p-2.5 font-medium text-texto-claro text-xs min-w-50 clicavel" @click="permissao1?.alterar ? editarRegistro1(item) : null">
                                <div class="flex flex-wrap gap-1 items-center line-clamp-6 wrap-break-word">
                                    <span v-if="item.cltipo === 1" class="bg-comum px-2 py-0.5 rounded border border-comum">
                                        Módulo Nível 1
                                    </span>
                                    <span v-else-if="item.cltipo === 2" class="bg-primary/20 text-primary px-2 py-0.5 rounded border border-primary/30">
                                        Perfil
                                    </span>
                                    <span v-else-if="item.cltipo === 3" class="bg-primary/20 text-primary px-2 py-0.5 rounded border border-primary/30">
                                        Subcadastro
                                    </span>
                                    <span v-else class="bg-layout-fundo px-2 py-0.5 rounded border border-comum">
                                        Indefinido
                                    </span>
                                </div>
                            </td>

          
                            <td class="p-2.5 text-right align-top">
                                <div class="flex flex-wrap gap-1 justify-end items-center line-clamp-6 wrap-break-word">
                                    <!-- Indicador de Status -->
                                    <button  type="button" v-if="item.clstatus === 1"  @click="permissao1?.alterar && updateRegistro1(item.id, 'status')" class="w-6 h-6 flex items-center justify-center rounded-full btn-green select-none cursor-pointer" title="Registro Ativo">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </button>
                                    <button type="button" v-else @click="permissao1?.alterar && updateRegistro1(item.id, 'status')" class="w-6 h-6 flex items-center justify-center rounded-full btn-red select-none cursor-pointer" title="Registro Inativo">
                                        <i class="fas fa-exclamation-triangle text-[10px]"></i>
                                    </button>

                                    <!-- Ação Editar (Passando o Objeto Reativo Completo) -->
                                    <button type="button" v-if="permissao1?.alterar || permissao1?.alterar" @click="editarRegistro1(item)" class="w-6 h-6 flex items-center justify-center rounded-full btn-blue select-none cursor-pointer" title="Editar Registro">
                                        <i class="fas fa-edit text-[10px]"></i>
                                    </button>

                                    <!-- Ação Apagar -->
                                    <button type="button" v-if="permissao1?.apagar || permissao1?.apagar" @click="apagarRegistro1(item.id)" class="w-6 h-6 flex items-center justify-center rounded-full btn-red select-none cursor-pointer" title="Apagar Registro">
                                        <i class="fas fa-trash-alt text-[10px]"></i>
                                    </button>
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
                <!-- FIM - TABELA DE LISTAGEM-->






                <!-- INICIO - PAGINACAO -->
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
                                <select ref="regporpagina1" @change="alterarqtdporpagina1"
                                    v-model="qtdporpg1"
                                    :class="['bg-layout-fundo border border-comum pl-2 pr-8 py-1 text-texto-claro text-xs focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none cursor-pointer w-auto min-w-18 h-full', permissao1?.alterar || permissao1?.inserir ? 'rounded-l' : 'rounded']">
                                    <option 
                                        v-for="opcao in sistemajs.opcoesQtdPagina1" 
                                        :key="opcao" 
                                        :value="opcao">
                                        {{ opcao }}
                                    </option>
                                </select>
                            </div>
                            <button v-if="permissao1?.alterar || permissao1?.inserir" @click="sistemajs.setregporpagina(calid1, qtdporpg1, usepage1.props.auth?.user?.id, null)" type="button" title="Aplicar quantidade como Padrão" class="bg-primary hover:bg-primary-hover text-texto-escuro px-2 py-1.5 border border-primary transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none rounded-r text-xs"><i class="fa fa-check"></i></button>
                        </div>
                        <div class="flex items-center gap-2 border-l border-comum focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none pl-4" title="Ir para uma página específica">
                            <span class="text-xs">Ir para:</span>
                            <input 
                                    type="number"
                                    @keyup.enter="navegarParaPagina($event.target.value)"
                                    @input="$event.target.value = $event.target.value.replace(/\D/g, ''); 
                                    sistemajs.soNumeros($event.target);"
                                    min="1"
                                    :max="listagem1?.last_page"
                                    placeholder="Pág."
                                    class="w-14 bg-layout-fundo border border-comum rounded px-2 py-1 text-center text-texto-claro text-xs focus:outline-none focus:border-primary"/>
                        </div>
                    </div>

                    <nav class="inline-flex flex-wrap -space-x-px rounded-md shadow-sm" aria-label="Paginação">

                        <button
                            :disabled="listagem1?.current_page === 1"
                            @click="navegarParaPagina(listagem1?.first_page)"
                            class="inline-flex items-center px-3 py-2 text-sm transition-all border border-comum text-texto-comum hover:bg-layout-fundo-subtle rounded-l-md"
                            :class="listagem1?.current_page === 1 ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'"
                            title="Primeira Página"><i class="fa fa-step-backward"></i>
                        </button>

                        <button
                            v-for="(link, index) in paginacaoInteracao1"
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
                            ]"/>

                        <button
                            :disabled="listagem1?.current_page === listagem1?.last_page"
                            @click="navegarParaPagina(listagem1?.last_page)"
                            class="inline-flex items-center px-3 py-2 text-sm transition-all border border-comum text-texto-comum hover:bg-layout-fundo-subtle rounded-r-md"
                            :class="listagem1?.current_page === listagem1?.last_page ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'"
                            title="Última Página"><i class="fa fa-step-forward"></i>
                        </button>
                    </nav>
                </div>
                <!-- FIM - PAGINACAO -->
            </div>
























            <!-- INICIO - CADASTRO-->
            <div v-if="abaAtiva1 === 'cadastro'" class="bg-layout-painel border border-comum rounded-lg p-6 shadow-sm">
                <form @submit.prevent="salvarRegistro1" class="flex flex-col gap-5">
                    <input type="hidden" v-model="formcad1.id"/>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="flex flex-col gap-1 md:col-span-3">
                            <label class="text-sm font-medium text-texto-claro/80">Identificação <span class="dadorequerido">*</span></label>
                            <input v-model="formcad1.clidentificacao" ref="clidentificacaoref" type="text" maxlength="150" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" required />
                        </div>
                        <!-- Campo 2: Switcher Status (Alinhado à Direita e Centralizado na Vertical) -->
                        <div class="flex items-center justify-start md:justify-end h-full pt-0 md:pt-5 md:col-span-1">
                            <label class="inline-flex items-center cursor-pointer select-none">
                            <!-- Bind reativo mapeando true -> 1 e false -> 0 -->
                            <input 
                                type="checkbox" 
                                v-model="formcad1.clstatus" 
                                :true-value="1" 
                                :false-value="0" 
                                class="switcher-input switcher-primary" 
                            />
                            <span class="switcher-track switcher-size-md">
                                <span class="switcher-thumb"></span>
                            </span>
                            
                            <!-- Texto Dinâmico que altera conforme o estado do v-model -->
                            <span 
                                :class="[
                                'ml-2.5 text-xs font-semibold transition-colors',
                                formcad1.clstatus === 1 ? 'text-texto-claro' : 'text-texto-claro/50'
                                ]"
                            >
                                {{ formcad1.clstatus === 1 ? 'Registro Ativo' : 'Registro Inativo' }}
                            </span>
                            </label>   
                        </div>

                        <div class="flex items-center justify-start md:justify-end h-full pt-0 md:pt-5 ">
                            <button 
                            v-if="(permissao1?.alterar || permissao1?.inserir)"
                            type="submit" 
                            :disabled="formcad1.processing" class="btn inline-flex items-center justify-center gap-1.5 py-1.5 text-sm font-bold bg-primary hover:bg-primary-hover text-texto-escuro disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-save text-sm"></i> 
                            <span class="font-bold!">{{ formcad1.processing ? 'Salvando...' : 'Salvar Registro' }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-texto-claro/80">Rota <small>Exemplo: /ajuda/contato</small><span class="dadorequerido">*</span></label>
                            <input v-model="formcad1.clrota" type="text" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all"/>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-texto-claro/80">Tipo<span class="dadorequerido">*</span></label>
                            <select v-model="formcad1.cltipo" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" required>
                                <option :value="1">Módulo Nível 1</option>
                                <option :value="2">Perfil</option>
                                <option :value="3">SubCadastro</option>
                            </select>
                        </div>                    

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="flex flex-col gap-1 md:col-span-3">
                            <label class="text-sm font-medium text-texto-claro/80">Base </label>
                            <input v-model="formcad1.clbase" ref="clbaseref" type="text" maxlength="150" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all"/>
                        </div>
                    </div>


                    <div class="flex flex-col gap-1.5">
                        <label for="clobserve" class="text-sm font-medium text-texto-claro/80">Observação</label>
                        <textarea v-model="formcad1.clobserve" id="clobserve" name="clobserve" rows="5" maxlength="1500" placeholder="Observações..." class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro placeholder-texto-claro/40 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all resize-y min-h-32"
                        ></textarea>
                        <span class="text-xs text-texto-claro/50 text-right">Informe detalhes.</span>
                    </div>

                    <div class="flex flex-col">
                        <label class="text-xs text-red">* Dados Obrigatórios</label>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-comum pt-4 mt-4">
                        <button type="button" @click="alternarAba1('inicio')" class="opacity-60 hover:bg-layout-fundo/10 bg-layout-fundo/99 text-texto-claro/99 border border-comum font-bold py-2.5 px-6 rounded-lg text-sm transition-all cursor-pointer">
                            <i class="fas fa-arrow-left pr-5"></i>Voltar
                        </button>
                        <button v-if="(permissao1?.alterar || permissao1?.inserir)"
                         type="submit" :disabled="formcad1.processing" class="bg-primary hover:bg-primary-hover disabled:opacity-50 text-texto-escuro font-bold py-2.5 px-6 rounded-lg text-sm transition-all shadow-md flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-save"></i> 
                            {{ formcad1.processing ? 'Salvando...' : 'Salvar Registro' }}
                        </button>
                    </div>
                </form>
            </div>
            <!-- FIM - CADASTRO-->








            <div v-if="abaAtiva1 === 'colunas'" class="bg-layout-painel border border-comum rounded-lg p-6 shadow-sm">
                <div class="flex flex-col gap-2">
                    <h3 class="text-md font-bold text-texto-claro">Configurações das Colunas Dinâmicas</h3>
                    <p class="text-xs text-texto-claro/60">Configurações herdadas do módulo estrutural antigo para o mapeamento dinâmico.</p>
                </div>
            </div>

        </div>
        
    </Layout>
</template>