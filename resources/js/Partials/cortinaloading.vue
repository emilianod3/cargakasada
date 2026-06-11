<script setup>
import { computed } from 'vue';
import { varloading, coresBases, corAtual } from '@/sistema.js';

// Captura a cor da marca ativa para pintar o ícone animado dinamicamente
const corIconeAnimado = computed(() => {
    const rgbMarca = coresBases[corAtual.value]?.['--cor-marca-rgb'] || '16 185 129';
    return { color: `rgb(${rgbMarca})` };
});
</script>

<template>
    <div class="fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-300 pointer-events-none opacity-0"
         :class="{ 'opacity-100 pointer-events-auto': varloading }">
        
        <div class="w-14 h-14 border-4 border-t-transparent animate-spin rounded-full mb-4"
             :style="{ 
                 borderLeftColor: corIconeAnimado.color, 
                 borderRightColor: corIconeAnimado.color, 
                 borderBottomColor: corIconeAnimado.color 
             }">
        </div>
        
        <span class="text-sm font-medium tracking-wide animate-pulse" :style="corIconeAnimado">
            Processando...
        </span>
    </div>
</template>