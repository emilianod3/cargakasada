<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
// 🌟 IMPORTANTE: Certifique-se de exportar 'modosDisponiveis' no seu @/sistema
import { jssistema, alterarModo, alterarCorBase, modosDisponiveis } from '@/sistema';

const { configAberta, modoAtual, corAtual } = jssistema();

// Referência para mapear a tag <aside> no DOM
const painelConfigRef = ref(null);

// Função para fechar a aba ao clicar fora
const verificarCliqueFora = (event) => {
    if (!configAberta.value) return;
    if (event.target.closest('.btn-engrenagem-trigger')) return;

    if (painelConfigRef.value && !painelConfigRef.value.contains(event.target)) {
        configAberta.value = false;
    }
};

onMounted(() => {
    window.addEventListener('click', verificarCliqueFora);
});

onUnmounted(() => {
    window.removeEventListener('click', verificarCliqueFora);
});

// Mapeamento para renderizar o background correto dos botões na listagem de destaque
const previewCores = {
    cinza: 'bg-[#64748b]',
    azul: 'bg-[#3b82f6]',
    verde: 'bg-[#10b981]',
    vermelho: 'bg-[#ef4444]',
    roxo: 'bg-[#8b5cf6]',
    laranja: 'bg-[#f97316]',
    ciano: 'bg-[#06b6d4]',
    amarelo: 'bg-[#eab308]'
};
</script>

<template>
    <button @click="configAberta = !configAberta"
            class="btn-engrenagem-trigger fixed right-0 top-[20px] bg-primary/60 hover:bg-primary-hover/90 text-texto-escuro backdrop-blur-sm p-2 rounded-l-lg shadow-xl transition-all duration-300 z-50 border border-comum group cursor-pointer"
            :class="configAberta ? 'mr-80' : 'mr-0'">
        <i class="fas fa-gear text-sm group-hover:spin-slow pointer-events-none"></i>
    </button>

    <aside ref="painelConfigRef"
           class="fixed right-0 top-[72px] h-[calc(100vh-72px)] w-80 bg-layout-painel border-l border-comum shadow-2xl p-6 flex flex-col gap-6 transition-transform duration-300 ease-in-out z-40 overflow-y-auto"
           :class="configAberta ? 'translate-x-0' : 'translate-x-full'">
        
        <div class="flex justify-between items-center border-b border-comum pb-3">
            <h3 class="text-base font-bold text-primary flex items-center gap-2">
                <i class="fas fa-sliders"></i> Personalização
            </h3>
            <button @click="configAberta = false" class="text-texto-claro/40 hover:text-texto-claro cursor-pointer">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        
        <div class="flex flex-col gap-2">
            <label class="text-xs font-semibold text-texto-claro/40 uppercase tracking-wider">Modo de Visualização</label>
            <div class="flex flex-col gap-1.5">
            
                <div class="w-full border-t border-comum/50 my-1"></div>

                    <button v-for="(config, chave) in modosDisponiveis" 
                            :key="chave"
                            @click="alterarModo(chave)" 
                            class="w-full p-2.5 rounded-lg border text-sm font-medium flex items-center justify-between cursor-pointer transition-all px-4"
                            :class="modoAtual === chave ? 'border-primary bg-primary/10 text-primary' : 'border-comum text-texto-claro/65 hover:bg-layout-fundo hover:text-texto-claro'">
                        
                        <span class="flex items-center gap-2">
                            <i v-if="chave === 'claro'" class="fas fa-sun text-amber-500"></i>
                            <i v-else class="fas fa-moon text-indigo-400"></i> 
                            
                            {{ config.nome }}
                        </span>
                        
                        <i v-if="modoAtual === chave" class="fas fa-circle-check text-xs"></i>
                    </button>

            </div>
        </div>

        <div class="flex flex-col gap-3">
            <label class="text-xs font-semibold text-texto-claro/40 uppercase tracking-wider">Cor de Destaque</label>
            <div class="grid grid-cols-4 gap-2">
                <button v-for="(classeBg, nomeCor) in previewCores" 
                        :key="nomeCor"
                        @click="alterarCorBase(nomeCor)" 
                        class="h-8 rounded transition-all cursor-pointer border-2"
                        :class="[
                            classeBg,
                            corAtual === nomeCor ? 'border-texto-claro scale-105 shadow-md' : 'border-transparent opacity-60 hover:opacity-100'
                        ]"
                        :title="nomeCor">
                </button>
            </div>
        </div>        

    </aside>
</template>

<style scoped>
.group:hover .group-hover\:spin-slow {
    animation: spin 4s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>