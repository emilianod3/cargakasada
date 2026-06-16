<script setup>
import { usePage } from '@inertiajs/vue3';
import { sistemajs, formataDataHora } from '@/sistema';
import { onMounted, onUnmounted, ref, inject } from 'vue';

// Importação das Partials
import Topo from '../Partials/header.vue';
import Menu from '../Partials/menu.vue';
import PopupInformativo from '../Partials/popupinformativo.vue';
import PopupDecisao from '../Partials/popupdecisao.vue';
import CortinaLoading from '../Partials/cortinaloading.vue';
import Configuracao from '../Partials/config.vue';
import Rodape from '../Partials/footer.vue';

const appUrl1 = inject('appUrl');
const { userMenuAberto } = sistemajs();
const page = usePage();

const logoutTime = 30 * 60 * 1000; // 15 minutos em milissegundos (ou o seu tempo padrão)
//const logoutTime = 2 * 60 * 60 * 1000; // Tempo de inatividade em milissegundos (2 horas)
//const logoutTime = 30 * 60 * 1000;  // 30 minutos em milissegundos
let timeout = null;
const tempofalta = ref(''); // Variável reativa caso queira exibir na tela "Expira em: HH:MM:SS"

//Função para fazer o bloqueio/logout por inatividade
const logoutUser = async () => {
    // Verifica se o debug está ativo usando o que compartilhamos no HandleInertiaRequests
    if (page.props.app_debug) {
        console.log('Logout de Inatividade acionado. Redirecionando...');
    }

    try {
        window.location.href = `${appUrl1}/lockscreen`;
        // Redireciona para a tela de bloqueio
    } catch (error) {
        // Fallback caso a rota falhe
        window.location.href = `${appUrl1}/login`;
    }
};

// 🔄 Função para iniciar/resetar o contador de inatividade
const resetTimer = () => {
    clearTimeout(timeout); // Limpa qualquer timer anterior

    // Inicia um novo timer para o bloqueio
    timeout = setTimeout(logoutUser, logoutTime);

    // Obtém a data e hora atual
    let currentTime = new Date();

    // Soma o logoutTime à data atual (Exatamente sua lógica antiga!)
    const expiraEm = new Date(currentTime.getTime() + logoutTime);
    tempofalta.value = formataDataHora(expiraEm);

    // Se o APP_DEBUG compartilhado pelo Laravel for true, solta o log no console
    if (page.props.app_debug) {
        //console.log(`${logoutTime} - Contador de Inatividade ${timeout}`);
        //console.log(`Expira em ${tempofalta.value}`);
    }
};

// --- 2. CONTROLE DO BOTÃO "VOLTAR AO TOPO" ---
const showButtonToTop = ref(false);
const loadButtonToTop = () => {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        showButtonToTop.value = true;
    } else {
        showButtonToTop.value = false;
    }
};

const voltarAoTopo = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth' // Faz o efeito de subida suave nativo do navegador (substitui o .animate do JQuery)
    });
};

// --- 3. BLOQUEIO DE CLIQUE DIREITO (CONTEXT MENU) ---
const bloquearContextMenu = (e) => {
    // Captura a variável de ambiente injetada globalmente pelo Share do Inertia no HandleInertiaRequests.php
    const contextoDesabilitado = page.props.SISTEMA_CONTEXTBTNDIR; 
    console.log('BTN DIR '+page.props.SISTEMA_CONTEXTBTNDIR);
    if (contextoDesabilitado == 0) {
        e.preventDefault();
        return false;
    }
};




onMounted(() => {
    // 1. Inicia o contador assim que o layout monta na tela
    resetTimer();
    window.addEventListener('mousemove', resetTimer);
    window.addEventListener('keydown', resetTimer);
    window.addEventListener('touchstart', resetTimer, { passive: true });
    window.addEventListener('scroll', resetTimer, { passive: true });
    window.addEventListener('scroll', loadButtonToTop);
    document.addEventListener('contextmenu', bloquearContextMenu);    
});


onUnmounted(() => {
    clearTimeout(timeout);
    window.removeEventListener('mousemove', resetTimer);
    window.removeEventListener('keydown', resetTimer);
    window.removeEventListener('touchstart', resetTimer);
    window.removeEventListener('scroll', resetTimer);
    window.removeEventListener('scroll', loadButtonToTop);
    document.removeEventListener('contextmenu', bloquearContextMenu);    
});

</script>




<template>

    <div class="min-h-screen flex flex-col bg-layout-fundo text-texto-claro font-sans text-base antialiased relative overflow-x-hidden">

        <CortinaLoading />
        <PopupInformativo />
        <PopupDecisao />

        <Topo />

        <div class="flex flex-1 relative overflow-visible">
            
            <Menu />

            <div class="flex-1 flex flex-col justify-between overflow-x-hidden">
                <main class="p-6 w-full max-w-7xl mx-auto">
                    <slot />
                    <button 
                        v-show="showButtonToTop" 
                        @click="voltarAoTopo"
                        id="btnBackToTop"
                        class="fixed bottom-5 right-5 z-50 bg-primary text-texto-escuro p-3 rounded-full shadow-lg hover:bg-primary-hover transition-all duration-300 cursor-pointer opacity-30"
                        title="Voltar ao Topo">
                        <i class="fas fa-arrow-up"></i>
                    </button>                    
                </main>
                
                <Rodape />
            </div>

            <Configuracao />

        </div>

        <div v-if="userMenuAberto" @click="userMenuAberto = false" class="fixed inset-0 z-30"></div>
    </div>
</template>