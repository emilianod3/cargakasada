<script setup>
import { computed, watch, nextTick, ref } from 'vue';
import { popupDecisaoAtivo, popupDecisaoConfig, fecharPopupDecisao, coresBases, corAtual } from '@/sistema.js';

// Referência para o container do popup para podermos injetar o foco
const cardPopup = ref(null);

const classesTipoPopup = computed(() => {
    const mapa = {
        success: {
            borda: 'border-emerald-500',
            iconeBg: 'bg-emerald-500/10 text-emerald-500',
            botao: 'bg-emerald-500 hover:bg-emerald-600 focus:ring-emerald-500',
            icone: '✓'
        },
        warning: {
            borda: 'border-amber-500',
            iconeBg: 'bg-amber-500/10 text-amber-500',
            botao: 'bg-amber-500 hover:bg-amber-600 focus:ring-amber-500',
            icone: '⚠️'
        },
        danger: {
            borda: 'border-red-500',
            iconeBg: 'bg-red-500/10 text-red-500',
            botao: 'bg-red-500 hover:bg-red-700 focus:ring-red-500', // Ajustado hover para consistência
            icone: '✕'
        },
        information: {
            borda: 'border-blue-500',
            iconeBg: 'bg-blue-500/10 text-blue-500',
            botao: 'bg-blue-500 hover:bg-blue-600 focus:ring-blue-500',
            icone: 'ℹ'
        }
    };
    return mapa[popupDecisaoConfig.value?.tipo] || mapa.information;
});

// FISGA O FOCO ASSIM QUE O POPUP ABRE
watch(popupDecisaoAtivo, async (velhoValor) => {
    if (velhoValor) {
        await nextTick();
        // Joga o foco no card do popup
        cardPopup.value?.focus();
    }
});
</script>

<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 transition-all duration-300"
             :class="popupDecisaoAtivo ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
             @click="() => { if (!popupDecisaoConfig.bloquearCliqueFora) fecharPopupDecisao('fechar') }">
            
            <div ref="cardPopup"
                 tabindex="-1"
                 class="w-full max-w-md rounded-xl p-6 shadow-2xl border-t-4 transform transition-all duration-300 bg-[rgb(var(--cor-painel-rgb))] text-[rgb(var(--cor-texto-claro))] focus:outline-none select-none"
                 :class="[classesTipoPopup.borda, popupDecisaoAtivo ? 'scale-100' : 'scale-95']"
                 @click.stop>
                
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 font-bold text-lg"
                         :class="classesTipoPopup.iconeBg">
                        {{ classesTipoPopup.icone }}
                    </div>

                    <div class="flex-1">
                        <h3 class="text-lg font-bold tracking-wide">
                            {{ popupDecisaoConfig.titulo }}
                        </h3>
                        <p class="text-sm mt-2 opacity-80 leading-relaxed whitespace-pre-line">
                            {{ popupDecisaoConfig.conteudo }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <button 
                        v-for="(botao, index) in popupDecisaoConfig.botoes" 
                        :key="index"
                        @click="fecharPopupDecisao(botao.valor)"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 shadow-sm cursor-pointer pointer-events-auto select-none"
                        :class="[
                            botao.classe === 'default' ? classesTipoPopup.botao + ' text-white' : 
                            botao.classe === 'danger' ? 'bg-red-600 hover:bg-red-700 text-white' :
                            'bg-zinc-700 hover:bg-zinc-600 text-zinc-100' 
                        ]"
                    >
                        {{ botao.texto }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>