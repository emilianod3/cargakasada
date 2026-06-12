import { ref } from 'vue';
import { nextTick } from 'vue';

// Estados Globais
const modoAtual = ref(localStorage.getItem('sistema-modo') || 'claro'); // 'escuro' ou 'claro'
const corAtual = ref(localStorage.getItem('sistema-cor') || 'azul');   // cinza, azul, verde, etc.

// 1. Definição dos Modos (Fundo e Texto)
const modos = {
    escuro: {
        '--cor-fundo-rgb': '15 23 42',       /* Slate 900 */
        '--cor-painel-rgb': '30 41 59',      /* Slate 800 */
        '--cor-texto-claro': '248 250 252',  /* Slate 50 */
        '--cor-texto-escuro': '15 23 42',    /* Slate 900 (Para tooltips) */
    },
    claro: {
        '--cor-fundo-rgb': '241 245 249',    /* Slate 100 */
        '--cor-painel-rgb': '255 255 255',   /* Branco Puro */
        '--cor-texto-claro': '15 23 42',     /* Slate 900 */
        '--cor-texto-escuro': '255 255 255', /* Branco (Para tooltips) */
    }
};

// 2. Definição das 8 Cores Bases (Valores RGB)
const coresBases = {
    cinza:    { '--cor-marca-rgb': '100 116 139', '--cor-marca-hover': '71 85 105' },   /* Slate 500 / 600 */
    azul:     { '--cor-marca-rgb': '59 130 246',  '--cor-marca-hover': '29 78 216' },   /* Blue 500 / 700 */
    verde:    { '--cor-marca-rgb': '16 185 129',  '--cor-marca-hover': '5 150 105' },   /* Emerald 500 / 600 */
    vermelho: { '--cor-marca-rgb': '239 68 68',   '--cor-marca-hover': '185 28 28' },   /* Red 500 / 700 */
    roxo:     { '--cor-marca-rgb': '139 92 246',  '--cor-marca-hover': '109 40 217' },  /* Violet 500 / 700 */
    laranja:  { '--cor-marca-rgb': '249 115 22',  '--cor-marca-hover': '234 88 12' },   /* Orange 500 / 600 */
    ciano:    { '--cor-marca-rgb': '6 182 212',   '--cor-marca-hover': '8 145 178' },   /* Cyan 500 / 600 */
    amarelo:  { '--cor-marca-rgb': '234 179 8',   '--cor-marca-hover': '202 138 4' },   /* Amber 500 / 600 */
};

const varloading = ref(false);

// --- ESTADOS DO POPUP GLOBAL ---
const popupAtivo = ref(false);
const popupConfig = ref({
    titulo: '',
    conteudo: '',
    tipo: 'information', // success, warning, danger, information
    bloquearCliqueFora: false
});

// Timeout ID para podermos cancelar se o usuário fechar antes
let popupTimeoutId = null;

// --- NOVOS ESTADOS EXCLUSIVOS PARA O POPUP DE DECISÃO ---
export const popupDecisaoAtivo = ref(false);
export const popupDecisaoConfig = ref({
    titulo: '',
    conteudo: '',
    tipo: 'information',
    bloquearCliqueFora: false,
    botoes: []
});

let popupDecisaoResolvePromise = null;
let popupDecisaoTimeoutId = null;

// Função unificada para aplicar as variáveis no HTML
export function atualizarEstilosGlobais() {
    const variaveisModo = modos[modoAtual.value];
    const variaveisCor = coresBases[corAtual.value];

    // Aplica variáveis do modo (claro/escuro)
    Object.keys(variaveisModo).forEach(chave => {
        document.documentElement.style.setProperty(chave, variaveisModo[chave]);
    });

    // Aplica variáveis da cor base selecionada
    Object.keys(variaveisCor).forEach(chave => {
        document.documentElement.style.setProperty(chave, variaveisCor[chave]);
    });
}

// Funções de disparo que serão chamadas nos cliques
export function alterarModo(novoModo) {
    modoAtual.value = novoModo;
    localStorage.setItem('sistema-modo', novoModo);
    atualizarEstilosGlobais();
}

export function alterarCorBase(novaCor) {
    corAtual.value = novaCor;
    localStorage.setItem('sistema-cor', novaCor);
    atualizarEstilosGlobais();
}

// Injeção inicial ao carregar a página
atualizarEstilosGlobais();

// Definimos os estados fora da função para que funcionem como um "Single Source of Truth" (Estado Global Compartilhado)
const sidebarAberta = ref(false);
const configAberta = ref(false);
const userMenuAberto = ref(false);
const submenusAbertos = ref({
    dashboard: false,
    usuarios: false
});

// FUNÇÃO CENTRALIZADA QUE VOCÊ VAI CHAMAR NO SISTEMA INTEIRO
/*
export function mostrarPopup({ titulo, conteudo, tipo = 'information', tempo = 0, bloquearCliqueFora = false }) {
    // Limpa qualquer timeout pendente de um popup anterior
    if (popupTimeoutId) clearTimeout(popupTimeoutId);

    // Define as configurações
    popupConfig.value = { titulo, conteudo, tipo, bloquearCliqueFora };
    popupAtivo.value = true;

    // Se foi passado um tempo (ex: 5000 para 5 segundos), fecha automaticamente
    if (tempo > 0) {
        popupTimeoutId = setTimeout(() => {
            fecharPopup();
        }, tempo);
    }
}*/

export function mostrarPopup({ titulo, conteudo, tipo = 'information', tempo = 0, bloquearCliqueFora = false }) {
    // Limpa qualquer timeout pendente de um popup anterior
    if (popupTimeoutId) clearTimeout(popupTimeoutId);

    // Define as configurações
    popupConfig.value = { titulo, conteudo, tipo, bloquearCliqueFora };
    popupAtivo.value = true;

    // ==========================================================================
    // CAPTURA DE FOCO ASSÍNCRONA
    // ==========================================================================
    nextTick(() => {
        // Encontra o botão de OK do popup e crava o foco nele
        const botaoOk = document.querySelector('.popup-simples-card button');
        if (botaoOk) {
            botaoOk.focus();
        }
    });

    // Se foi passado um tempo (ex: 5000 para 5 segundos), fecha automaticamente
    if (tempo > 0) {
        popupTimeoutId = setTimeout(() => {
            fecharPopup();
        }, tempo);
    }
}

export function fecharPopup() {
    popupAtivo.value = false;
    if (popupTimeoutId) clearTimeout(popupTimeoutId);
}


export function mostrarPopupDecisao({ 
    titulo, 
    conteudo, 
    tipo = 'information', 
    tempo = 0, 
    bloquearCliqueFora = false,
    exibirSim = true,
    exibirNao = false,
    exibirCancelar = false,
    textoSim = 'Sim',
    textoNao = 'Não',
    textoCancelar = 'Cancelar'
}) {
    // Limpa timeouts anteriores específicos deste popup
    if (popupDecisaoTimeoutId) window.clearTimeout(popupDecisaoTimeoutId);

    const botoesProcessados = [];

    // 1. Botão Não (Esquerda)
    if (exibirNao) {
        botoesProcessados.push({ texto: textoNao, valor: 'nao', classe: 'danger' });
    }

    // 2. Botão Cancelar (Meio)
    if (exibirCancelar) {
        botoesProcessados.push({ texto: textoCancelar, valor: 'cancelar', classe: 'neutral' });
    }

    // 3. Botão Sim (Direita - Ação Principal)
    if (exibirSim) {
        botoesProcessados.push({ texto: textoSim, valor: 'sim', classe: 'default' });
    }

    // Configura o estado reativo
    popupDecisaoConfig.value = { 
        titulo, 
        conteudo, 
        tipo, 
        bloquearCliqueFora, 
        botoes: botoesProcessados 
    };
    
    popupDecisaoAtivo.value = true;
    nextTick(() => {
        // Busca o primeiro botão ativo de dentro do container do seu popup customizado
        // (Ajuste a classe '.v-popper__inner' se o seu container usar outra classe)
        const botaoPrincipal = document.querySelector('.v-popper__inner button, .popup-decisao button');
        
        if (botaoPrincipal) {
            botaoPrincipal.focus();
        }
    });
    // Retorna a Promise que tornará o fechamento síncrono na sua função externa
    return new Promise((resolve) => {
        popupDecisaoResolvePromise = resolve;

        if (tempo > 0) {
            popupDecisaoTimeoutId = window.setTimeout(() => {
                fecharPopupDecisao('timeout');
            }, tempo);
        }
    });
}

// FUNÇÃO DE FECHAMENTO EXCLUSIVA
export function fecharPopupDecisao(valorDoBotao = 'fechar') {
    popupDecisaoAtivo.value = false;
    if (popupDecisaoTimeoutId) window.clearTimeout(popupDecisaoTimeoutId);
    
    // Devolve o valor clicado para quem invocou o 'await mostrarPopupDecisao'
    if (popupDecisaoResolvePromise) {
        popupDecisaoResolvePromise(valorDoBotao);
        popupDecisaoResolvePromise = null; // Libera a memória
    }
}

export function jssistema() {
    
    const alternarSubmenu = (menu) => {
        if (!sidebarAberta.value) {
            sidebarAberta.value = true;
            submenusAbertos.value[menu] = true;
            return;
        }
        submenusAbertos.value[menu] = !submenusAbertos.value[menu];
    };

    const alternarSidebarPrincipal = () => {
        sidebarAberta.value = !sidebarAberta.value;
        if (!sidebarAberta.value) {
            Object.keys(submenusAbertos.value).forEach(key => {
                submenusAbertos.value[key] = false;
            });
        }
    };

    const irParaLink = (url, target = '_blank') => {
        window.open(url, target);
    };

    return {
        sidebarAberta,
        userMenuAberto,
        submenusAbertos,
        alternarSubmenu,
        alternarSidebarPrincipal,
        irParaLink,
        configAberta: ref(false),
        modoAtual,
        corAtual,
        varloading
    };
}


// Exporta o estado isolado também para usarmos nos listeners do Inertia
export { 
    modos, 
    coresBases, 
    modoAtual, 
    corAtual, 
    varloading,
    popupAtivo, 
    popupConfig
};

/*
export function urlredirect(url = '/'){
    const urlCompleta = window.location.origin+url;
    window.location.href = urlCompleta;
}*/


export function formataDataHora(dt){
        var d = new Date(dt);
        var dia = d.getDate();
        if (dia.toString().length == 1)
        if (dia.toString().length == 1)
            dia = "0"+dia;
        var mes = d.getMonth()+1;
        if (mes.toString().length == 1)
            mes = "0"+mes;
        var ano = d.getFullYear();
        var h = d.getHours();
        if (h.toString().length == 1)
            h = "0"+h;
        var m = d.getMinutes();
        if (m.toString().length == 1)
            m = "0"+m;
        var s = d.getSeconds();
        if (s.toString().length == 1)
            s = "0"+s;
        return dia+"/"+mes+"/"+ano+" " + h + ":" + m + ":"+s;
    }

/*
const formataDataHora = (dataString) => {
    if (!dataString) return '';
    const data = new Date(dataString);
    return data.toLocaleString('pt-BR');
};*/





export function formataMilhar(numero){
    //const format = num => 
    return String(numero).replace(/(?<!\..*)(\d)(?=(?:\d{3})+(?:\.|$))/g, '$1.')
}

export function strlen(texto) {
    if(temTexto(texto)){
        return texto.length;
    }else{
        return 0;
    }
};

export function copiarTexto(texto, tamanho = 2){
    var len = texto.toString().length;
    if (len > tamanho) {
        return texto.toString().substring(0, tamanho);
    }else{
        return texto;
    }
}

export function limitaTexto(texto) {
    // Segurança: Evita travar a tela se o texto vier nulo, indefinido ou não for texto
    if (texto === null || texto === undefined || texto === '') {
        return '';
    }

    // Garante que o valor tratado seja uma String pura
    const stringTexto = String(texto).trim();
    var len = stringTexto.length;

    // Se o tamanho dos caracteres passar de 40
    if (len > 40) {
        // Quebra o texto por espaços e pega as primeiras 10 palavras
        var query = stringTexto.split(" ", 10);
        
        query.push('...');
        
        // CORRIGIDO: Declarando a variável de resultado localmente com const
        const res = query.join(' ');
        return res;
    } else {
        return stringTexto;
    }
}

export function limitaTexto2(texto, qtd = 6){
    // Segurança: se o texto vier nulo ou indefinido, evita que o sistema quebre
    if (!texto) return 'Usuário';

    var len = texto.length;
    let res = '';

    // Convertemos para string e limpamos espaços extras nas pontas
    const stringTexto = String(texto).trim();

    // Aqui usamos o split para transformar o texto em um array de palavras
    var palavras = stringTexto.split(" ");

    // Se a quantidade de palavras no texto for maior do que a 'qtd' que você pediu
    if (palavras.length > qtd) {
        // Corta o array de palavras exatamente na quantidade (qtd) desejada
        var query = palavras.slice(0, qtd);
        
        query.push('...');
        res = query.join(' ');
        return res;
    } else {
        return stringTexto;
    }
}


export function getWords(text, qtd){    
    var wordsToCut = qtd;
    var wordsArray = text.split(" ");
    if(wordsArray.length>wordsToCut){
        var strShort = "";
        for(i = 0; i < wordsToCut; i++){
            strShort += wordsArray[i] + " ";
        }   
        return strShort+"...";
    }else{
        return text;
    }
};

export function limitaTextoHtml(texto){
    var len = texto.length;
    let res = '';
    if (len > 250) {
        var query = texto.split(" ", 21);
        query.push('...');
        res = query.join(' ');
        return res;
    }else{
        return texto;
    }
}

export function limitaHtmlTamanho(texto = '', partes = 50){
    let wordsArray = texto.split(" ");
    let chunks = Array();
    const wordsInChunkCount = partes;
    let temp = wordsInChunkCount;
    let str = '';
    wordsArray.forEach(item => {
        if (temp > 0) {
            str += ' ' + item
            temp--
        } else {
            chunks.push(str)
            str = ''
            temp = wordsInChunkCount
        }
    });
    if(texto.length > partes){
        str += '...';
    }    
    
    return str.replace(/<[^>]*>/g, '');
}