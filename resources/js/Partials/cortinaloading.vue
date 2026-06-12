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
    <Teleport to="body">
        <div class="fixed inset-0 z-[2147483647] flex flex-col items-center justify-center bg-black/60 backdrop-blur-sm transition-all duration-300"
             :class="varloading ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'">
            
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
    </Teleport>
</template>