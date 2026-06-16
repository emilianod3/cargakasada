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
/*
export function formataDataHora(dt) {
    const d = new Date(dt);
    if (isNaN(d.getTime())) return "";
    const dia = d.getDate().toString().padStart(2, '0');
    const mes = (d.getMonth() + 1).toString().padStart(2, '0');
    const ano = d.getFullYear();
    const h = d.getHours().toString().padStart(2, '0');
    const m = d.getMinutes().toString().padStart(2, '0');
    const s = d.getSeconds().toString().padStart(2, '0');
    return `${dia}/${mes}/${ano} ${h}:${m}:${s}`;
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


/**
 * Retorna a diferença de tempo amigável entre uma data informada e o momento atual.
 */
export function getDiasFromDate(dt) {
    let today = new Date();
    let data = null;
    if (dt != null && dt !== '0000-00-00') {
        data = new Date(dt);  
    } else {
        return '';
    }

    let difference = Math.abs(data - today);
    
    let Days = Math.floor(difference / (1000 * 60 * 60 * 24));
    let Hours = Math.floor((difference / (1000 * 60 * 60)) % 24);
    let Mins = Math.floor((difference / (1000 * 60)) % 60);
    let Seconds = Math.floor((difference / 1000) % 60);

    let result = '';

    if (Seconds > 0) result = Seconds + ' seg.';
    if (Mins > 0) result = Mins + ' min.';
    if (Hours > 0) result = Hours + (Hours > 1 ? ' horas' : ' hora');
    if (Days > 0) result = Days + (Days > 1 ? ' dias' : ' dia');

    return result;
}

export function getDiaAtual() {
    const today = new Date();
    return today.getDate().toString().padStart(2, '0');
}

export function getMesAtual() {
    const today = new Date();
    const mm = today.getMonth() + 1;
    // Garante que a chamada interna funcione caso a função getMonthExtenso esteja no mesmo arquivo
    return getMonthExtenso(mm);
}

export function getMesAtualNumero() {
    const today = new Date();
    const mm = today.getMonth() + 1;
    return mm.toString().padStart(2, '0');
}

export function getAnoAtual() {
    return new Date().getFullYear();
}

export function getDataHoraAtualBanco() {
    const d = new Date();
    const day = d.getDate().toString().padStart(2, '0');
    const month = (d.getMonth() + 1).toString().padStart(2, '0');
    const year = d.getFullYear();
    const h = d.getHours().toString().padStart(2, '0');
    const m = d.getMinutes().toString().padStart(2, '0');
    const s = d.getSeconds().toString().padStart(2, '0');
    
    return `${year}-${month}-${day} ${h}:${m}:${s}`;
}

export function getDataAtualBanco() {
    const d = new Date();
    const day = d.getDate().toString().padStart(2, '0');
    const month = (d.getMonth() + 1).toString().padStart(2, '0');
    const year = d.getFullYear();
    
    return `${year}-${month}-${day}`;
}

export function addDaysToDate(date, days) {
    const result = new Date(date);
    result.setDate(result.getDate() + days);
    return result;
}

export function isDataValida(dataStr) {
    if (!dataStr) return false;
    if (dataStr.trim() === '0000-00-00 00:00:00' || dataStr.trim() === '0000-00-00') return false;
    
    const data = new Date(dataStr);
    return !isNaN(data.getTime());
}

export function datavalida(dateString) {
    const datePattern = /^\d{4}-\d{2}-\d{2}$/;
    if (!datePattern.test(dateString)) return false;

    const parts = dateString.split('-');
    const year = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10);
    const day = parseInt(parts[2], 10);

    const date = new Date(year, month - 1, day);

    const checkYear = date.getFullYear() === year;
    const checkMonth = date.getMonth() === month - 1;
    const checkDay = date.getDate() === day;
    const isNotNaN = !isNaN(date.getTime());

    return checkYear && checkMonth && checkDay && isNotNaN;
}

export function temMaisDe18Anos(dataNascimento) {
    const dataNasc = new Date(dataNascimento);
    const hoje = new Date();
    let idade = hoje.getFullYear() - dataNasc.getFullYear();

    const mesAtual = hoje.getMonth();
    const diaAtual = hoje.getDate();
    const mesNasc = dataNasc.getMonth();
    const diaNasc = dataNasc.getDate();

    if (mesAtual < mesNasc || (mesAtual === mesNasc && diaAtual < diaNasc)) {
        idade--;
    }
    return idade >= 18;
}

export function getDataAtualBancoAddDays(hours, type = 'soma') {
    const d = new Date();
    if (parseInt(hours) > 0) {
        if (type === 'soma') {
            d.setHours(d.getHours() + parseInt(hours));
        } else {
            d.setHours(d.getHours() - parseInt(hours));
        }
    }
    const day = d.getDate().toString().padStart(2, '0');
    const month = (d.getMonth() + 1).toString().padStart(2, '0');
    const year = d.getFullYear();
    
    return `${year}-${month}-${day}`;
}

export function getDataAtual() {
    const today = new Date();
    const dd = today.getDate().toString().padStart(2, '0');
    const mm = (today.getMonth() + 1).toString().padStart(2, '0');
    const yyyy = today.getFullYear();
    
    return `${dd}/${mm}/${yyyy}`;
}

export function dataPassou(dt) {
    const today = new Date();
    let data = null;
    if (dt != null && dt !== '0000-00-00') {
        data = new Date(dt);  
    }
    
    if (!data) return 'menor';
    
    // Zera as horas para comparar apenas os dias corridos de forma justa
    today.setHours(0,0,0,0);
    data.setHours(0,0,0,0);

    if (data.getTime() === today.getTime()) {
        return 'igual';
    } else if (data > today) {
        return 'maior';
    } else {
        return 'menor';
    }
}

export function getDataHoraAtual() {
    const d = new Date();
    const day = d.getDate().toString().padStart(2, '0');
    const month = (d.getMonth() + 1).toString().padStart(2, '0');
    const year = d.getFullYear();
    const h = d.getHours().toString().padStart(2, '0');
    const m = d.getMinutes().toString().padStart(2, '0');
    const s = d.getSeconds().toString().padStart(2, '0');

    return `${day}/${month}/${year} ${h}:${m}:${s}`;
}

export function getHoraAtual() {
    const d = new Date();
    const h = d.getHours().toString().padStart(2, '0');
    const m = d.getMinutes().toString().padStart(2, '0');
    const s = d.getSeconds().toString().padStart(2, '0');
    return `${h}:${m}:${s}`;
}

export function getHoraMinutoAtual() {
    const d = new Date();
    const h = d.getHours().toString().padStart(2, '0');
    const m = d.getMinutes().toString().padStart(2, '0');
    return `${h}:${m}`;
}

export function getHoraCheiaAtual(minutos = 0) {
    const d = new Date();

    // Define a hora atual zerando os minutos e segundos originais
    d.setMinutes(minutos);
    d.setSeconds(0);

    const h = d.getHours().toString().padStart(2, '0');
    const m = d.getMinutes().toString().padStart(2, '0');

    return `${h}:${m}`;
} 

export function getAno(dt) {
    const d = new Date(dt);
    return d.getFullYear();
}

export function getMonthExtenso(mes) {
    const meses = {
        1: 'Janeiro', 2: 'Fevereiro', 3: 'Março', 4: 'Abril',
        5: 'Maio', 6: 'Junho', 7: 'Julho', 8: 'Agosto',
        9: 'Setembro', 10: 'Outubro', 11: 'Novembro', 12: 'Dezembro'
    };
    return meses[parseInt(mes)] || 'Indefinido';
}

export function formataData(dt) {
    if (dt != null && dt !== '0000-00-00') { 
        const ano = dt.substring(0, 4);
        const mes = dt.substring(5, 7);
        const dia = dt.substring(8, 10);
        return `${dia}/${mes}/${ano}`;
    }
    return "";
}

// Apontado para a mesma lógica para evitar código duplicado desnecessário
export const formataData2 = formataData;

export function formataDataHora2(dt) {
    if (!dt) return "";
    const ano = dt.substring(0, 4);
    const mes = dt.substring(5, 7);
    const dia = dt.substring(8, 10);
    const h = dt.substring(11, 19);
    return `${dia}/${mes}/${ano} ${h}`;
}

export function getHora(dt) {
    if (dt != null && dt.length >= 19) {
        return dt.substring(11, 19);
    }
    return '00:00:00';
}

export function getHoraMinuto(dt) {
    if (dt != null && dt.length >= 16) {
        return dt.substring(11, 16);
    }
    return '00:00';
}

export function formataDataHoraParaBanco2(dt) {
    if (!dt) return "";
    const ano = dt.substring(6, 10);
    const mes = dt.substring(3, 5);
    const dia = dt.substring(0, 2);
    const h = dt.substring(11, 19);
    return `${ano}-${mes}-${dia} ${h}`;
}

export function formataDataHoraView(dt) {
    if (!dt) return "";
    const ano = dt.substring(0, 4);
    const mes = dt.substring(5, 7);
    const dia = dt.substring(8, 10);
    const h = dt.substring(11, 19);
    return `${ano}-${mes}-${dia}T${h}`;
}

export function formataHoraMinuto(str) {
    if (!str) return "";
    return str.substring(0, 5);
}



export function formataDataHoraSemAno(dt) {
    const d = new Date(dt);
    if (isNaN(d.getTime())) return "";
    const dia = d.getDate().toString().padStart(2, '0');
    const mes = (d.getMonth() + 1).toString().padStart(2, '0');
    const h = d.getHours().toString().padStart(2, '0');
    const m = d.getMinutes().toString().padStart(2, '0');
    return `${dia}/${mes} ${h}:${m}`;
}

export function formataDataHoraParaBanco(dt) {
    const d = new Date(dt);
    if (isNaN(d.getTime())) return "";
    
    let h = d.getHours();
    let m = d.getMinutes();
    let s = d.getSeconds();

    if (h === 0 && m === 0 && s === 0) {
        const t2 = new Date();
        h = t2.getHours();
        m = t2.getMinutes();
        s = t2.getSeconds();
    }

    const dia = d.getDate().toString().padStart(2, '0');
    const mes = (d.getMonth() + 1).toString().padStart(2, '0');
    const ano = d.getFullYear();
    const hr = h.toString().padStart(2, '0');
    const min = m.toString().padStart(2, '0');
    const seg = s.toString().padStart(2, '0');

    return `${ano}-${mes}-${dia} ${hr}:${min}:${seg}`;
}

export function formataDataHoraParaPonto(dt) {
    if (!dt) return "";
    const ano = dt.substring(6, 10);
    const mes = dt.substring(3, 5);
    const dia = dt.substring(0, 2);
    const h = dt.substring(11, 13);
    const m = dt.substring(14, 16);
    return `${dia}${mes}${ano}${h}${m}`;
}

export function formataDataParaBanco(dt) {
    if (dt == null) return "";
    const d = new Date(dt);
    if (isNaN(d.getTime())) return "";
    const dia = d.getDate().toString().padStart(2, '0');
    const mes = (d.getMonth() + 1).toString().padStart(2, '0');
    return `${d.getFullYear()}-${mes}-${dia}`;
}

export function formataDataParaBanco2(dt) {
    if (dt == null) return "";
    let ano = dt.substring(6, 10);
    let mes = "";
    let dia = "";
    
    if (ano.includes("-")) {
        ano = dt.substring(0, 4);
        mes = dt.substring(5, 7);
        dia = dt.substring(8, 10);
    } else {
        mes = dt.substring(3, 5);
        dia = dt.substring(0, 2);
    }
    return `${ano}-${mes}-${dia}`;
}

export function contarDiasEntreDatas(data1, data2) {
    const dataInicio = new Date(data1);
    const dataFim = new Date(data2);
    
    if (dataFim > dataInicio) {
        const diferencaEmMilissegundos = Math.abs(dataFim.getTime() - dataInicio.getTime());
        const milissegundosPorDia = 1000 * 60 * 60 * 24;
        return Math.ceil(diferencaEmMilissegundos / milissegundosPorDia);
    }
    return 0;
}

/*
function isEmpty(texto){
    if(texto == null || texto.length <= 0 ){
        return true;
    }else{
        return false;
    }
}
*/



function temTexto(texto) {
    if (texto == '' || texto == null) {
        return false;
    }else{
        return true;
    }
};

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





/**   MASCARAS */

/**
 * Máscara de CPF (000.000.000-00)
 */
export function aplicarMascaraCPF(valor, callback) {
    // Garante que o valor seja uma string e evita quebras por nulo/indefinido
    if (valor === undefined || valor === null) {
        if (callback) callback('');
        return;
    }
    
    // Força virar string e remove tudo o que não for número
    let limpo = String(valor).replace(/\D/g, '');
    
    // Aplica a formatação tradicional de CPF (000.000.000-00)
    limpo = limpo.replace(/^(\d{3})(\d)/, '$1.$2');
    limpo = limpo.replace(/(\d{3})(\d)/, '$1.$2');
    limpo = limpo.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    
    // Retorna limitando ao tamanho máximo do CPF formatado (14 caracteres)
    if (callback) {
        callback(limpo.substring(0, 14));
    }
}


/**
 * Máscara de CEP (00.000-000)
 */
export function aplicarMascaraCEP(valor, callback) {
    // Garante que o valor seja uma string e evita quebras por nulo/indefinido
    if (valor === undefined || valor === null) {
        if (callback) callback('');
        return;
    }
    
    // Força virar string e remove tudo o que não for número
    let limpo = String(valor).replace(/\D/g, '');
    
    // Aplica a formatação do seu padrão: 00.000-000
    limpo = limpo.replace(/^(\d{2})(\d)/, '$1.$2');
    limpo = limpo.replace(/(\d{3})(\d{1,3})$/, '$1-$2');
    
    // Retorna limitando ao tamanho máximo do CEP formatado (10 caracteres)
    if (callback) {
        callback(limpo.substring(0, 10));
    }
}

/**
 * Máscara de RG Padrão (00.000.000-0)
 */
export function aplicarMascaraRG(valor, callback) {
    if (valor === undefined || valor === null) return;
    let limpo = valor.replace(/\D/g, '');
    limpo = limpo.replace(/^(\d{2})(\d)/, '$1.$2');
    limpo = limpo.replace(/(\d{3})(\d)/, '$1.$2');
    limpo = limpo.replace(/(\d{3})(\d{1})$/, '$1-$2');
    callback(limpo.substring(0, 12));
}

/**
 * Máscara de Inscrição Estadual (000.000.000.000)
 */
export function aplicarMascaraIE(valor, callback) {
    if (valor === undefined || valor === null) return;
    let limpo = valor.replace(/\D/g, '').substring(0, 12);
    limpo = limpo.replace(/^(\d{3})(\d)/, '$1.$2'); 
    limpo = limpo.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
    limpo = limpo.replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3.$4');
    callback(limpo);
}

/**
 * Máscara de Inscrição Municipal (000.000.000-0)
 */
export function aplicarMascaraIM(valor, callback) {
    if (valor === undefined || valor === null) return;
    let limpo = valor.replace(/\D/g, '').substring(0, 10);
    limpo = limpo.replace(/^(\d{3})(\d)/, '$1.$2'); 
    limpo = limpo.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
    limpo = limpo.replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d{1})$/, '$1.$2.$3-$4');
    callback(limpo);
}

/**
 * Máscara de CNPJ (00.000.000/0001-00)
 */
export function aplicarMascaraCNPJ(valor, callback) {
    // Garante que o valor seja uma string e evita nulos
    if (valor === undefined || valor === null) {
        if (callback) callback('');
        return;
    }
    
    // Força virar string e remove absolutamente tudo que não for dígito
    let limpo = String(valor).replace(/\D/g, '');
    
    // Aplica a formatação do CNPJ passo a passo
    limpo = limpo.replace(/^(\d{2})(\d)/, '$1.$2');
    limpo = limpo.replace(/(\d{3})(\d)/, '$1.$2');
    limpo = limpo.replace(/(\d{3})(\d)/, '$1/$2');
    limpo = limpo.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    
    // Retorna limitando ao tamanho máximo do CNPJ formatado (18 caracteres)
    if (callback) {
        callback(limpo.substring(0, 18));
    }
}


/**
 * Aplica máscara de telefone dinamicamente (Fixo ou Celular)
 * @param {string} valor - O texto do input
 * @param {Function} callback - Função de retorno para o Vue
 */
export function aplicarMascaraTelefone(valor, callback) {
    if (valor === undefined || valor === null) {
        if (callback) callback('');
        return;
    }

    // Remove tudo o que não for número
    let limpo = String(valor).replace(/\D/g, '');

    // Se passar do limite de um celular (11 dígitos), limita o tamanho
    if (limpo.length > 11) {
        limpo = limpo.substring(0, 11);
    }

    // Aplica a máscara baseada na quantidade de números puros
    if (limpo.length > 10) {
        // Formato Celular: (00) 00000-0000
        limpo = limpo.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
    } else if (limpo.length > 6) {
        // Formato Fixo intermediário ou completo: (00) 0000-0000
        limpo = limpo.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
    } else if (limpo.length > 2) {
        // Formato inicial com DDD: (00) 0000
        limpo = limpo.replace(/^(\d{2})(\d{0,5})$/, '($1) $2');
    } else if (limpo.length > 0) {
        // Apenas o DDD sendo digitado: (00
        limpo = limpo.replace(/^(\d{0,2})$/, '($1');
    }

    if (callback) {
        callback(limpo);
    }
}

/**
 * Aplica máscara de data tradicional (DD/MM/AAAA)
 * @param {string} valor - O texto do input
 * @param {Function} callback - Função de retorno para o Vue
 */
export function aplicarMascaraData(valor, callback) {
    if (valor === undefined || valor === null) {
        if (callback) callback('');
        return;
    }

    // Remove tudo o que não for número e limita a 8 dígitos puros
    let limpo = String(valor).replace(/\D/g, '').substring(0, 8);

    // Aplica a formatação dos blocos: DD/MM/AAAA
    limpo = limpo.replace(/^(\d{2})(\d)/, '$1/$2');
    limpo = limpo.replace(/^(\d{2})\/(\d{2})(\d)/, '$1/$2/$3');

    if (callback) {
        callback(limpo);
    }
}

/**
 * Valida se uma string de data no formato DD/MM/AAAA é uma data real no calendário
 * @param {string} dataStr - Data formatada ou apenas números
 * @returns {boolean} True se a data for válida, False se não for
 */
export function TestaData(dataStr) {
    if (!dataStr) return false;

    // Obtém apenas os números (deve ter 8 dígitos)
    const limpo = String(dataStr).replace(/\D/g, '');
    if (limpo.length !== 8) return false;

    // Divide em Dia, Mês e Ano
    const dia = parseInt(limpo.substring(0, 2), 10);
    const mes = parseInt(limpo.substring(2, 4), 10) - 1; // Meses no JS vão de 0 a 11
    const ano = parseInt(limpo.substring(4, 8), 10);

    // Cria um objeto de data nativo do JS
    const dataObjeto = new Date(ano, mes, dia);

    // O JavaScript corrige datas automaticamente (Ex: 31/11 vira 01/12).
    // Para validar, checamos se o JS não alterou o dia, mês ou ano que informamos.
    return (
        dataObjeto.getFullYear() === ano &&
        dataObjeto.getMonth() === mes &&
        dataObjeto.getDate() === dia &&
        ano >= 1900 // Evita anos inválidos muito antigos ou com erro de digitação
    );
}

/**
 * Aplica máscara de Cartão de Crédito (0000 0000 0000 0000)
 * @param {string} valor - O texto do input
 * @param {string} valor - O texto do input
 * @param {Function} callback - Função de retorno para o Vue
 */
export function aplicarMascaraCartao(valor, callback) {
    if (valor === undefined || valor === null) {
        if (callback) callback('');
        return;
    }

    // Remove tudo o que não for número e limita ao tamanho máximo padrão (16 dígitos puros)
    let limpo = String(valor).replace(/\D/g, '').substring(0, 16);

    // Agrupa de 4 em 4 números separados por espaço
    limpo = limpo.replace(/(\d{4})(\d)/, '$1 $2');
    limpo = limpo.replace(/(\d{4})\s(\d{4})(\d)/, '$1 $2 $3');
    limpo = limpo.replace(/(\d{4})\s(\d{4})\s(\d{4})(\d)/, '$1 $2 $3 $4');

    if (callback) {
        callback(limpo);
    }
}


/**
 * Aplica uma máscara de milhar dinâmica enquanto o usuário digita no input.
 * @param {HTMLInputElement | object} i - O elemento do input.
 */
export function milharMask(i) {
    if (!i || i.value === undefined) return;
    
    // Remove tudo o que não for dígito
    let v = i.value.replace(/\D/g, '');
    
    // Aplica a separação de milhar usando expressão regular
    v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    
    i.value = v;
}

/**
 * Aplica uma máscara de dinheiro (dinâmica enquanto digita) alterando o valor do elemento diretamente.
 * Ideal para inputs nativos onde se passa o elemento.
 * @param {HTMLInputElement | object} i - O elemento do input.
 */
export function moneyMask2(i) {
    if (!i || i.value === undefined) return;

    // Remove tudo o que não for dígito
    let v = i.value.replace(/\D/g, '');
    if (v === '') {
        i.value = '';
        return;
    }

    // Transforma em decimal fixo (dividindo por 100 para pegar os centavos)
    v = (parseInt(v, 10) / 100).toFixed(2);
    
    const splits = v.split(".");
    let p_parte = splits[0].replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    const separador_decimal = ',';

    i.value = p_parte + separador_decimal + splits[1];
}

/**
 * Recebe o objeto do input (ou um objeto com a propriedade value), 
 * lê os dígitos decimais e retorna a string formatada com "R$ ".
 * @param {HTMLInputElement | object} value - O elemento contendo o valor.
 * @returns {string} O valor formatado em reais (ex: "R$ 1.500,25").
 */
export function moneyMask(value) {
    if (!value || !value.value) return 'R$ 0,00';

    // Remove qualquer caractere que não seja dígito
    const apenasDigitos = value.value.replace(/\D/g, '');
    if (!apenasDigitos) return 'R$ 0,00';

    // Formata usando a API nativa internacional do JS
    const options = { minimumFractionDigits: 2, maximumFractionDigits: 2 };
    const resultadoFormatado = new Intl.NumberFormat('pt-BR', options).format(
        parseFloat(apenasDigitos) / 100
    );

    return 'R$ ' + resultadoFormatado;
}

/**
 * Executada diretamente por eventos de input do Vue (ex: @input="aplicarMascaraMonetaria").
 * Formata o input em tempo real adicionando o prefixo "R$ " e os separadores.
 * @param {Event} event - O evento nativo do input.
 */
export function aplicarMascaraMonetaria(event) {
    if (!event || !event.target) return;
    
    let valor = event.target.value;

    // 1. Remove todos os caracteres que não sejam dígitos
    valor = valor.replace(/\D/g, '');

    // 2. Se o valor for vazio, limpa o campo e sai da função
    if (valor.length === 0) {
        event.target.value = '';
        return;
    }

    // 3. Adiciona zeros à esquerda para ter pelo menos 3 dígitos (para R$ 0,0x)
    valor = valor.padStart(3, '0');

    // 4. Separa os centavos (os 2 últimos dígitos)
    const centavos = valor.slice(-2);

    // 5. Separa os reais (o restante dos dígitos)
    let inteiros = valor.slice(0, -2);
    
    // 6. Remove zeros à esquerda na parte inteira
    inteiros = inteiros.replace(/^0+/, '');
    
    // 7. Se a parte inteira ficar vazia, define como "0"
    if (inteiros === '') {
        inteiros = '0';
    }

    // 8. Adiciona os separadores de milhares (pontos)
    inteiros = inteiros.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');

    // 9. Monta o valor final com o prefixo, inteiros, vírgula e centavos
    event.target.value = 'R$ ' + inteiros + ',' + centavos;
}




/*
export function formataMilhar(numero){
    //const format = num => 
    return String(numero).replace(/(?<!\..*)(\d)(?=(?:\d{3})+(?:\.|$))/g, '$1.')
}*/
/**
 * Formata um número ou string numérica para o padrão brasileiro (Milhares com ponto, decimais com vírgula).
 * @param {number|string} numero 
 * @returns {string} Valor formatado (ex: 1.500,00 ou 1.500)
 */
export function formataMilhar(numero) {
    if (numero === undefined || numero === null || numero === '') return '0';

    // Garante que o valor seja um número válido antes de formatar
    const numeroValido = typeof numero === 'string' 
        ? Number(numero.replace(/[^0-9.-]+/g, "")) 
        : numero;

    if (isNaN(numeroValido)) return '0';

    // Formata usando a API nativa internacional do JavaScript para o padrão do Brasil
    return new Intl.NumberFormat('pt-BR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    }).format(numeroValido);
}



/**
 * Formata um valor inteiro (centavos) vindos do banco em formato de moeda Real (R$).
 * Exemplo: 1500 vira "R$ 15,00"
 * @param {number | string} value
 * @returns {string}
 */
export function formatarmoeda(value) {
    if (value === null || value === undefined || value === '') return 'R$ 0,00';
    
    const num = Number(value);
    if (isNaN(num)) return 'R$ 0,00';
    
    return (num / 100).toLocaleString('pt-BR', { 
        style: 'currency', 
        currency: 'BRL' 
    });
}

/**
 * Converte um número flutuante ou string numérica para o padrão de moeda Real.
 * @param {number | string} number - O número a ser convertido.
 * @param {object} options - Opções de configuração { moneySign: true/false }.
 * @returns {string}
 */
export function convertToReal(number, options = {}) {
    const { moneySign = true } = options;

    if (number === null || number === undefined || number === '') {
        return moneySign ? "R$ 0,00" : "0,00";
    }

    const num = Number(number);
    if (isNaN(num)) return moneySign ? "R$ 0,00" : "0,00";

    const config = moneySign 
        ? { style: 'currency', currency: 'BRL' } 
        : { minimumFractionDigits: 2, maximumFractionDigits: 2 };

    // O pt-BR nativo já cuida perfeitamente dos pontos e vírgulas em qualquer valor
    return num.toLocaleString('pt-BR', config);
}

/**
 * Formata o número mantendo as duas casas decimais brasileiras, mas remove o símbolo "R$".
 * @param {number | string} number 
 * @param {object} options 
 * @returns {string}
 */
export function formatardecimal(number, options = {}) {
    // Força o moneySign a ser false para usar a configuração decimal pura, limpando espaços
    const resultado = convertToReal(number, { ...options, moneySign: false });
    return resultado.trim();
}

/**
 * Mascara o CPF para conformidade com a LGPD, deixando apenas os 3 primeiros 
 * e os 2 últimos dígitos visíveis (e.g., 123******45).
 * @param {string} cpfCompleto O CPF (pode incluir pontos e traços).
 * @returns {string} O CPF mascarado ou o original se for inválido.
 */
export function cpfLGPD(cpfCompleto) {
    if (!cpfCompleto) {
        return '';
    }

    // 1. Remove tudo que não for dígito
    const cpfLimpo = cpfCompleto.replace(/[^\d]/g, '');

    // 2. Verifica se tem 11 dígitos
    if (cpfLimpo.length !== 11) {
        return cpfCompleto; 
    }

    // 3. Aplica a máscara nos 6 dígitos centrais
    return cpfLimpo.replace(/^(\d{3})\d{6}(\d{2})$/, '$1******$2');
}





/**
 * Valida e força os limites de valor mínimo e máximo de um input numérico.
 * @param {HTMLInputElement | object} el - O elemento do input ou objeto contendo value, min e max.
 */
export function inputMinMax(el) {
    if (!el || el.value === undefined || el.value === "") return;

    const valorAtual = parseInt(el.value, 10);
    const min = el.min !== undefined && el.min !== "" ? parseInt(el.min, 10) : null;
    const max = el.max !== undefined && el.max !== "" ? parseInt(el.max, 10) : null;

    // Se o valor digitado não for um número válido (ex: letras ou símbolos), limpa ou ignora
    if (isNaN(valorAtual)) return;

    if (min !== null && valorAtual < min) {
        el.value = min.toString();
    } else if (max !== null && valorAtual > max) {
        el.value = max.toString();
    }
}






/**
 * Garante que uma string de link seja válida e possua o protocolo HTTP/HTTPS.
 * * @param {string | null} value - O link informado pelo usuário.
 * @returns {string} O link corrigido ou uma string vazia caso seja inválido.
 */
export function verifiedLink(value) {
    if (value != null && value.trim().length > 7) {
        const link = value.trim();
        // Verifica se já começa com http:// ou https:// de forma direta e segura
        if (/^https?:\/\//i.test(link)) {
            return link;
        }
        return `http://${link}`;
    }
    return '';
}

/**
 * Valida se uma string possui o formato básico de e-mail.
 * @param {string} str O e-mail a ser validado.
 * @returns {boolean} True se for válido, false se for inválido.
 */
export function validacaoEmail(str) {
    if (!str || typeof str !== 'string') return false;
    const arrObjeto = str.trim();
    // 🚀 TRAVA DE DIGITAÇÃO: Se digitou menos de 4 caracteres, considera válido por enquanto
    if (arrObjeto.length < 5) {
        return true;
    }

    const arrobaIndex = arrObjeto.indexOf("@");
    if (arrobaIndex === -1) return false;

    const usuario = arrObjeto.substring(0, arrobaIndex);
    const dominio = arrObjeto.substring(arrobaIndex + 1, arrObjeto.length);

    if ((usuario.length >= 1) &&
        (dominio.length >= 3) && 
        (usuario.search("@") == -1) && 
        (dominio.search("@") == -1) &&
        (usuario.search(" ") == -1) && 
        (dominio.search(" ") == -1) &&
        (dominio.search(/\./) != -1) && // Ajustado para escapar o ponto corretamente
        (dominio.indexOf(".") >= 1) && 
        (dominio.lastIndexOf(".") < dominio.length - 1)) {
        
        return true;
    } else {
        return false;
    }
}



/**
 * Valida o Cadastro de Pessoa Física (CPF)
 * @param {string} cpf 
 * @returns {boolean}
 */
export function TestaCPF(cpf) { 
    if (!cpf) return false;
    const limpo = String(cpf).replace(/[^\d]+/g, ''); 
    
    if (limpo.length !== 11 || /^(\d)\1{10}$/.test(limpo)) return false; 
    
    // Valida 1º dígito 
    let add = 0; 
    for (let i = 0; i < 9; i++) {
        add += parseInt(limpo.charAt(i)) * (10 - i); 
    }
    let rev = 11 - (add % 11); 
    if (rev === 10 || rev === 11) rev = 0; 
    if (rev !== parseInt(limpo.charAt(9))) return false; 
        
    // Valida 2º dígito 
    add = 0; 
    for (let i = 0; i < 10; i++) {
        add += parseInt(limpo.charAt(i)) * (11 - i); 
    }
    rev = 11 - (add % 11); 
    if (rev === 10 || rev === 11) rev = 0; 
    if (rev !== parseInt(limpo.charAt(10))) return false; 
    
    return true; 
}



/**
 * Valida comprimento mínimo genérico para Registro Geral (RG)
 * @param {string} str 
 * @returns {boolean}
 */
export function TestaRG(str) {
    if (!str) return false;
    // Remove pontos, traços e underlines comuns em máscaras de RG
    const x = String(str).replace(/[\._\-]/g, '');
    return x.length >= 9; // Ajustado para 9 que é o padrão de dígitos puro (SP)
}

/**
 * Valida o Cadastro Nacional da Pessoa Jurídica (CNPJ)
 * @param {string} cnpj 
 * @returns {boolean}
 */
export function testaCNPJ(cnpj) {
    if (!cnpj) return false;
    const limpo = String(cnpj).replace(/[^\d]+/g, '');

    if (limpo.length !== 14 || /^(\d)\1{13}$/.test(limpo)) return false;
        
    // Valida DVs
    let tamanho = limpo.length - 2;
    let numeros = limpo.substring(0, tamanho);
    const digitos = limpo.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
        if (pos < 2) pos = 9;
    }
    
    let resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
    if (resultado !== parseInt(digitos.charAt(0))) return false;
        
    tamanho = tamanho + 1;
    numeros = limpo.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;
    
    for (let i = tamanho; i >= 1; i--) {
        soma += parseInt(numeros.charAt(tamanho - i)) * pos--;
        if (pos < 2) pos = 9;
    }
    
    resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
    if (resultado !== parseInt(digitos.charAt(1))) return false;
            
    return true;
}

/**
 * Valida Título de Eleitor
 * @param {string} inscricao 
 * @returns {boolean}
 */
export function TestaTituloEleitor(inscricao) {
    if (!inscricao) return false;
    const limpo = String(inscricao).replace(/[^\d]+/g, '');
    const tam = limpo.length;
    
    // O título precisa ter entre 11 e 12 dígitos
    if (tam < 11 || tam > 12) return false;

    const digitos = limpo.substring(tam - 2);
    const estado = limpo.substring(tam - 4, tam - 2);
    const titulo = limpo.substring(0, tam - 2);
    const exce = (estado === '01') || (estado === '02');
    
    let dig1 = 0;
    for (let i = 0; i < titulo.length - 2; i++) {
        // Multiplicadores dinâmicos baseados no tamanho padrão da string base
        const multiplicador = (titulo.length === 9) ? (9 - i) : (8 - i);
        if (multiplicador >= 2) {
            dig1 += (titulo.charCodeAt(i) - 48) * multiplicador;
        }
    }
    // Tratamento específico dos últimos dois dígitos da base do título para o primeiro DV
    if (titulo.length === 9) {
        dig1 += (titulo.charCodeAt(7) - 48) * 2;
    } else {
        dig1 += (titulo.charCodeAt(6) - 48) * 3 + (titulo.charCodeAt(7) - 48) * 2;
    }

    let resto = (dig1 % 11);
    if (resto === 0) {
        dig1 = exce ? 1 : 0;
    } else if (resto === 1) {
        dig1 = 0;
    } else {
        dig1 = 11 - resto;
    }

    let dig2 = (titulo.charCodeAt(titulo.length - 2) - 48) * 4 + 
               (titulo.charCodeAt(titulo.length - 1) - 48) * 3 + 
               dig1 * 2;
               
    resto = (dig2 % 11);
    if (resto === 0) {
        dig2 = exce ? 1 : 0;
    } else if (resto === 1) {
        dig2 = 0;
    } else {
        dig2 = 11 - resto;
    }

    return (digitos.charCodeAt(0) - 48 === dig1) && (digitos.charCodeAt(1) - 48 === dig2);
}

/**
 * Valida o Programa de Integração Social (PIS)
 * @param {string} pis 
 * @returns {boolean}
 */
export function ValidaPIS(pis) {
    if (!pis) return false;
    const numeroPIS = String(pis).replace(/[^\d]+/g, '');

    if (numeroPIS.length !== 11 || /^(\d)\1{10}$/.test(numeroPIS)) return false;

    const multiplicadorBase = "3298765432";
    let total = 0;
    
    for (let i = 0; i < 10; i++) {
        const multiplicando = parseInt(numeroPIS.substring(i, i + 1));
        const multiplicador = parseInt(multiplicadorBase.substring(i, i + 1));
        total += multiplicando * multiplicador;
    }

    let resto = 11 - (total % 11);
    resto = (resto === 10 || resto === 11) ? 0 : resto;

    const digito = parseInt(numeroPIS.charAt(10));
    return resto === digito;
}

/**
 * Remove zeros à esquerda mantendo a integridade da string
 * @param {string} numeroFormatado 
 * @returns {string}
 */
export function removerZerosAEsquerda(numeroFormatado) {
    if (numeroFormatado === undefined || numeroFormatado === null) return '';
    
    const str = String(numeroFormatado);
    // Se for apenas zeros, retorna '0' para não sumir com o dado do input
    if (/^0+$/.test(str)) return '0';
    
    return str.replace(/^0+/, '');
}

/**
 * Converte valor numérico para o padrão de moeda Real (R$) brasileiro
 * @param {number|string} numero 
 * @returns {string}
 */
export function numberToReal(numero) {
    const valor = parseFloat(numero);
    if (isNaN(valor)) return "R$ 0,00";
    
    return valor.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
}




/**
 * Remove qualquer caractere não numérico diretamente do valor de um elemento HTML (Input)
 * @param {HTMLInputElement} elm - O elemento do input nativo
 */
export function soNumeros(elm) {
    if (!elm) return;
    // Remove tudo o que não for dígito e atualiza o valor do input nativo
    elm.value = String(elm.value).replace(/\D/g, '');
}

/**
 * Extrai apenas os números de uma string, retornando-os como String ou como Inteiro seguro.
 * @param {string|number} string - A string contendo texto e números
 * @param {boolean} [retornarComoNumero=false] - Se true, converte para inteiro seguro
 * @returns {string|number}
 */
export function apenasNumeros(string, retornarComoNumero = false) {
    if (string === undefined || string === null) {
        return retornarComoNumero ? 0 : '';
    }
    
    // Remove absolutamente tudo que não for número de 0 a 9
    const numsStr = String(string).replace(/\D/g, '');
    
    if (retornarComoNumero) {
        // Se a string de números estiver vazia, retorna 0 em vez de NaN
        return numsStr ? parseInt(numsStr, 10) : 0;
    }
    
    // Retorna como string para preservar casos como "000123"
    return numsStr;
}
























/**
 * Gera uma senha aleatória segura com o comprimento desejado
 * @param {number} [comprimento=12] - Tamanho da senha
 * @returns {string}
 */
export function gerarSenha(comprimento = 12) {
    const caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@$*-+_=&";
    let senha = "";
    
    // Garante criptografia um pouco mais robusta usando a API nativa se disponível, ou Math.random
    const array = new Uint32Array(comprimento);
    const cryptoObj = window.crypto || window.msCrypto;
    
    if (cryptoObj) {
        cryptoObj.getRandomValues(array);
        for (let i = 0; i < comprimento; i++) {
            senha += caracteres.charAt(array[i] % caracteres.length);
        }
    } else {
        for (let i = 0; i < comprimento; i++) {
            senha += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
        }
    }
    return senha;
}

/**
 * Calcula a pontuação de força (força) de uma senha
 * @param {string} senha 
 * @returns {number} Pontuação de 0 a 100
 */
export function calcularForcaSenha(senha) {
    if (!senha) return 0;
    let forca = 0;
    forca += senha.length * 4;
    let gruposPreenchidos = 0;
    
    if (/[a-z]/.test(senha)) gruposPreenchidos++;
    if (/[A-Z]/.test(senha)) gruposPreenchidos++;
    if (/\d/.test(senha)) gruposPreenchidos++;
    if (/[^A-Za-z0-9]/.test(senha)) gruposPreenchidos++;

    forca += (gruposPreenchidos - 1) * 10;

    // 3. Penalidades de Segurança (UX e robustez)
    if (senha.length < 6) return 10; // Senha muito curta nunca passa de nível "Muito Fraca"
    if (/^[0-9]+$/.test(senha) || /^[a-zA-Z]+$/.test(senha)) {
        // Se a senha for só número ou só letra, reduz a força pela metade
        forca = Math.floor(forca / 2); 
    }

    // Normaliza o retorno entre 0 e 100
    return Math.max(0, Math.min(forca, 100));
}


/**
 * Retorna o texto descritivo baseado na pontuação de força da senha
 * @param {number} forca - Pontuação gerada por calcularForcaSenha
 * @returns {string}
 */
export function infoComplexidade(forca) {
    if (forca < 30) {
        return 'Senha Fraca, Não atende aos requisitos de Complexidade de Senha';
    } else if (forca >= 30 && forca < 60) {
        return 'Senha de Complexidade Média';
    } else if (forca >= 60 && forca < 85) {
        return 'Senha de Complexidade Forte, Atende aos Requisitos Mínimos';
    } else {
        return 'Senha de Complexidade Excelente, Atende a Todos os Requisitos';
    }
}

/**
 * Compara se duas senhas são estritamente iguais
 * @param {string} pass1 
 * @param {string} pass2 
 * @returns {boolean}
 */
export function comparaSenhas(pass1, pass2) {
    return pass1 === pass2;
}

/**
 * Incrementa um valor numérico de forma pura (Substitui o JQuery antigo)
 * @param {number|string} valorAtual 
 * @returns {number}
 */
export function incrementarValor(valorAtual) {
    const numero = parseInt(valorAtual, 10);
    return isNaN(numero) ? 1 : numero + 1;
}

/**
 * Decrementa um valor numérico impedindo que fique menor que zero (Substitui o JQuery antigo)
 * @param {number|string} valorAtual 
 * @returns {number}
 */
export function decrementarValor(valorAtual) {
    const numero = parseInt(valorAtual, 10);
    if (isNaN(numero) || numero <= 0) return 0;
    return numero - 1;
}

/**
 * Codifica uma string em Base64 adicionando uma assinatura/sal de segurança
 * @param {string} string - Texto para codificar
 * @param {string} [passencode=''] - Chave/Assinatura opcional
 * @returns {string} Base64 Resultante
 */
export function encodebase(string, passencode = '') {
    if (string === undefined || string === null) return '';
    const combinedString = String(string) + passencode;
    const utf8Bytes = new TextEncoder().encode(combinedString);
    return btoa(String.fromCharCode(...utf8Bytes));
}

/**
 * Decodifica uma string Base64 verificando e removendo a assinatura de segurança
 * @param {string} encodedString - String em Base64
 * @param {string} [passencode=''] - Chave/Assinatura opcional usada na codificação
 * @returns {string} Texto puro decodificado ou '-' em caso de falha
 */
export function decodebase(encodedString, passencode = '') {
    if (!encodedString) return '-';
    try {
        const binaryString = atob(encodedString);
        const utf8Bytes = Uint8Array.from(binaryString, (m) => m.codePointAt(0));
        const decodedString = new TextDecoder().decode(utf8Bytes);

        const passLength = passencode.length;
        if (passLength > 0 && decodedString.endsWith(passencode)) {
            return decodedString.slice(0, -passLength);
        }
        return decodedString;
    } catch (e) {
        return '-';
    }
}























// Dicionário de mapeamento para busca instantânea O(1) - Substitui o switch gigante
const ICONES_POR_EXTENSAO = {
    'pdf': 'icone pdf',
    'pdfx': 'icone pdfx',
    'pdfassinado': 'icone pdfassinado',
    
    'doc': 'icone doc',
    'docx': 'icone docx',
    'odt': 'icone documento',
    'txt': 'icone txt',
    'log': 'icone log',
    
    'xls': 'icone xls',
    'xlsx': 'icone xlsx',
    'odf': 'icone table',
    'ods': 'icone table',
    
    'otp': 'icone slide',
    'odp': 'icone slide',
    'ppt': 'icone slidex',
    'pptx': 'icone slidex',
    
    'gif': 'icone gif',
    'png': 'icone png',
    'jpg': 'icone jpg',
    'jpeg': 'icone imagem',
    'tiff': 'icone tiff',
    'psd': 'icone psd',
    
    'audio': 'icone audio',
    'mp3': 'icone mp3',
    'mp4': 'icone mp4',
    'video': 'icone video',
    'avi': 'icone avi',
    
    'zipado': 'icone zipado',
    'rar': 'icone zipado',
    'zip': 'icone zipado',
    '7z': 'icone zipado',
    '7-zip': 'icone zipado', // Corrigido de video para zipado
    'gzip': 'icone zipado',  // Corrigido de txt para zipado
    
    'pfx': 'icone certificado',
    'cer': 'icone certificado',
    'crt': 'icone certificado',
    'pem': 'icone certificado',
    'key': 'icone certificado',
    'p7s': 'icone certificado',
    'certificadoassinado': 'icone certificadoassinado'
};

/**
 * Retorna o nome do ícone correspondente à extensão do arquivo
 * @param {string} extensao 
 * @returns {string}
 */
export function getImageTipoFile(extensao) {
    if (!extensao) return 'icone file';
    const extLimpa = String(extensao).toLowerCase().trim();
    return ICONES_POR_EXTENSAO[extLimpa] || 'icone file';
}

/**
 * Converte bytes para uma string legível humana formatada (KB, MB, GB, etc)
 * @param {number|string} bytes 
 * @returns {string}
 */
export function convSize(bytes) {
    const numBytes = parseInt(bytes, 10);
    if (isNaN(numBytes) || numBytes <= 0) return '0 Bytes';
    
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    // Calcula o índice com base no logaritmo de base 1024
    const i = Math.floor(Math.log(numBytes) / Math.log(1024));
    
    // Correção: Para fixar 2 casas decimais reais em JS, usa-se .toFixed(2)
    const valorFormatado = (numBytes / Math.pow(1024, i)).toFixed(2);
    
    // Remove o ".00" se for um número redondo (ex: transforma "2.00 KB" em "2 KB")
    return `${parseFloat(valorFormatado)} ${sizes[i]}`;
}

/**
 * Sanitiza strings transformando-as em formatos amigáveis para arquivos ou URLs (snake_case)
 * @param {string} text 
 * @returns {string}
 */
export function sanitizeFilename(text) {
    if (!text) return '';

    return String(text)
        .toLowerCase()
        .normalize('NFD')                     // Separa acentos dos caracteres base
        .replace(/[\u0300-\u036f]/g, "")      // Remove todos os diacríticos (acentos)
        .replace(/[^a-z0-9\s-]/g, '')         // Limpa símbolos especiais remanescentes
        .replace(/[\s-]+/g, '_')              // Consolida espaços e hífens em um único "_"
        .replace(/^_+|_+$/g, '');             // Remove underscores órfãos nas extremidades
}