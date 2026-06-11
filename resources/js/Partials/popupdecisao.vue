<script setup>
import { computed } from 'vue';
import { popupDecisaoAtivo, popupDecisaoConfig, fecharPopupDecisao, coresBases, corAtual } from '@/sistema.js';

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
            botao: 'bg-red-500 hover:bg-red-600 focus:ring-red-500',
            icone: '✕'
        },
        information: {
            borda: 'border-blue-500',
            iconeBg: 'bg-blue-500/10 text-blue-500',
            botao: 'bg-blue-500 hover:bg-blue-600 focus:ring-blue-500',
            icone: 'ℹ'
        }
    };
    return mapa[popupDecisaoConfig.value.tipo] || mapa.information;
});
</script>

<template>
    <div class="fixed inset-0 z-[9997] flex items-center justify-center bg-black/50 backdrop-blur-xs p-4 transition-all duration-300 opacity-0 pointer-events-none"
         :class="{ 'opacity-100 pointer-events-auto': popupDecisaoAtivo }"
         @click="() => { if (!popupDecisaoConfig.bloquearCliqueFora) fecharPopupDecisao('fechar') }">
        
        <div class="w-full max-w-md rounded-xl p-6 shadow-2xl border-t-4 transform transition-all duration-300 bg-[rgb(var(--cor-painel-rgb))] text-[rgb(var(--cor-texto-claro))]"
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
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-slate-900 shadow-sm"
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
</template>