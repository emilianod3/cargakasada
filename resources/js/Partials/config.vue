<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import { jssistema, alterarModo, alterarCorBase } from '@/sistema';

const { configAberta, modoAtual, corAtual } = jssistema();

// Referência para mapear a tag <aside> no DOM
const painelConfigRef = ref(null);

// Função para fechar a aba ao clicar fora
const verificarCliqueFora = (event) => {
    // Se o menu já estiver fechado, não faz nada
    if (!configAberta.value) return;

    // Se o clique foi no botão da engrenagem (ou elementos internos dele), ignora
    if (event.target.closest('.btn-engrenagem-trigger')) return;

    // Se o clique NÃO foi dentro da tag <aside>, fecha o painel
    if (painelConfigRef.value && !painelConfigRef.value.contains(event.target)) {
        configAberta.value = false;
    }
};

// Escuta os cliques na janela ao montar o componente
onMounted(() => {
    window.addEventListener('click', verificarCliqueFora);
});

// Remove o listener para evitar vazamento de memória ao destruir o componente
onUnmounted(() => {
    window.removeEventListener('click', verificarCliqueFora);
});

// Mapeamento para renderizar o background correto dos botões na listagem
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
           class="fixed right-0 top-[72px] h-[calc(100vh-72px)] w-80 bg-layout-painel border-l border-comum shadow-2xl p-6 flex flex-col gap-6 transition-transform duration-300 ease-in-out z-40"
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
            <div class="grid grid-cols-2 gap-2">
                <button @click="alterarModo('escuro')" 
                        class="p-2 rounded-lg border text-sm font-medium flex items-center justify-center gap-2 cursor-pointer transition-all"
                        :class="modoAtual === 'escuro' ? 'border-primary bg-primary/10 text-primary' : 'border-comum text-texto-claro/65 hover:text-texto-claro'">
                    <i class="fas fa-moon"></i> Escuro
                </button>
                <button @click="alterarModo('claro')" 
                        class="p-2 rounded-lg border text-sm font-medium flex items-center justify-center gap-2 cursor-pointer transition-all"
                        :class="modoAtual === 'claro' ? 'border-primary bg-primary/10 text-primary' : 'border-comum text-texto-claro/65 hover:text-texto-claro'">
                    <i class="fas fa-sun"></i> Claro
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