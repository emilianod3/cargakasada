



<script setup>
import { ref, onMounted, onUnmounted, computed, provide, inject } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Importação das Partials
import Topo from '../Partials/headerpublico.vue';
import Rodape from '../Partials/footerpublico.vue';
import PopupInformativo from '../Partials/popupinformativo.vue';
import PopupDecisao from '../Partials/popupdecisao.vue';
import CortinaLoading from '../Partials/cortinaloading.vue';

import { modoAtual, modos } from '@/sistema.js';

const page = usePage();

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
    
    if (contextoDesabilitado == 0) {
        e.preventDefault();
        return false;
    }
};


// Seus computeds de estilo existentes (estiloFundo, estiloLuzEsquerda...)
const estiloFundo = computed(() => {
    const rgbFundo = modos[modoAtual.value]?.['--cor-fundo-rgb'] || '15 23 42';
    const rgbTexto = modos[modoAtual.value]?.['--cor-texto-claro'] || '248 250 252';
    return {
        color: `rgb(${rgbTexto})`,
        background: `linear-gradient(to bottom, rgb(${rgbFundo}), rgb(${rgbFundo})) fixed`
    };
});

// 2. ESTILO DA LUZ ESQUERDA (NOROESTE)
/*const estiloLuzEsquerda = computed(() => {
    const rgbMarca = coresBases[corAtual.value]?.['--cor-marca-rgb'] || '16 185 129';
    return {
        background: `radial-gradient(circle at top left, rgba(${rgbMarca}, 0.14), transparent 45%)`
    };
});

// 3. ESTILO DA LUZ DIREITA (NORDESTE) - O QUE ESTAVA FALTANDO!
const estiloLuzDireita = computed(() => {
    const rgbMarca = coresBases[corAtual.value]?.['--cor-marca-rgb'] || '16 185 129';
    return {
        background: `radial-gradient(circle at top right, rgba(${rgbMarca}, 0.06), transparent 50%)`
    };
});*/

// Captura a cor da marca ativa para pintar o ícone animado dinamicamente
/*
const corIconeAnimado = computed(() => {
    const rgbMarca = coresBases[corAtual.value]?.['--cor-marca-rgb'] || '16 185 129';
    return { color: `rgb(${rgbMarca})` };
});*/




// --- CICLO DE VIDA DO VUE ---
onMounted(() => {
    // Inicializa os escutadores globais de eventos do navegador
    window.addEventListener('scroll', loadButtonToTop);
    document.addEventListener('contextmenu', bloquearContextMenu);
});

onUnmounted(() => {
    // Importante: Remove os eventos ao destruir o componente para não vazar memória!
    window.removeEventListener('scroll', loadButtonToTop);
    document.removeEventListener('contextmenu', bloquearContextMenu);
});

</script>




<template>
    <div class="min-h-screen flex flex-col font-sans text-base antialiased relative overflow-x-hidden transition-all duration-500"
         :style="estiloFundo">

        <CortinaLoading />
        <PopupInformativo />
        <PopupDecisao />

        <!--
        <div class="absolute inset-0 pointer-events-none" :style="estiloLuzEsquerda"></div>
        <div class="absolute inset-0 pointer-events-none" :style="estiloLuzDireita"></div>-->

        <Topo />
        
        <div class="flex flex-1 relative overflow-visible z-10">
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
        </div>
    </div>
</template>

<!--
<template>

    <div class="min-h-screen flex flex-col bg-layout-fundo text-texto-claro font-sans text-base antialiased relative overflow-x-hidden">

        <Topo />
        <div class="flex flex-1 relative overflow-visible">
            

            <div class="flex-1 flex flex-col justify-between overflow-x-hidden">
                <main class="p-6 w-full max-w-7xl mx-auto">
                    <slot />
                </main>
                
                <Rodape />
            </div>


        </div>

    </div>
</template>-->