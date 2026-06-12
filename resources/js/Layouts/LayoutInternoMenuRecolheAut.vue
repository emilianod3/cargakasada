<script setup>
import { ref } from 'vue';
import { Link, Head } from '@inertiajs/vue3';

// Variáveis reativas de controle
const sidebarAberta = ref(false);
const configAberta = ref(false);
const userMenuAberto = ref(false);

// Controle de submenus abertos (Armazena o nome do menu ativo)
const submenuAtivo = ref('');

// Função para alternar submenus
const alternarSubmenu = (menu) => {
    // Se a sidebar estiver fechada, força a abertura dela primeiro
    if (!sidebarAberta.value) {
        sidebarAberta.value = true;
        submenuAtivo.value = menu;
        return;
    }
    submenuAtivo.value = submenuAtivo.value === menu ? '' : menu;
};
</script>

<template>
    <Head>
        <title>Meu Sistema - Painel Administrativo</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="icon" type="image/x-icon" href="/favicon.ico?v=2" />
    </Head>

    <div class="min-h-screen flex flex-col bg-slate-900 text-white font-sans text-base antialiased relative overflow-x-hidden">
        
        <header class="bg-slate-800 border-b border-slate-700 p-4 shadow-md z-40 relative">
            <div class="flex justify-between items-center w-full">
                
                <div class="flex items-center gap-4 transition-all duration-300 ease-in-out"
                     :class="sidebarAberta ? 'w-45' : 'w-16'">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="bg-emerald-500/10 p-2.5 rounded-lg text-emerald-400 border border-emerald-500/20 shadow-lg flex items-center justify-center w-10 h-10 flex-shrink-0">
                            <i class="fas fa-bolt text-xl"></i>
                        </div>
                        <h1 class="text-xl font-bold text-emerald-400 tracking-wide whitespace-nowrap transition-opacity duration-200"
                            :class="sidebarAberta ? 'opacity-100' : 'opacity-0 w-0 pointer-events-none'">
                            Nome Sistema
                        </h1>
                    </div>
                </div>


                <div class="flex flex-1 justify-between items-center pl-4">
                    <button @click="sidebarAberta = !sidebarAberta; submenuAtivo = ''" 
                            class="p-2 rounded-lg bg-slate-700/50 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors border border-slate-600/50 flex items-center justify-center w-9 h-9 cursor-pointer">
                        <i class="fas fa-bars text-sm"></i>
                    </button>

                    <div class="relative mr-4">
                        <button @click="userMenuAberto = !userMenuAberto" 
                                class="flex items-center gap-3 hover:bg-slate-700/50 p-2 rounded-lg transition-all cursor-pointer border border-transparent hover:border-slate-600/50 z-50 relative">
                            <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-sm font-semibold border border-slate-600 shadow-inner">
                                <i class="fas fa-user text-xs text-slate-300"></i>
                            </div>
                            <span class="text-sm text-slate-300 font-medium hidden md:block">Olá, Usuário</span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-500 transition-transform" :class="userMenuAberto ? 'rotate-180' : ''"></i>
                        </button>

                        <div v-if="userMenuAberto" 
                             class="absolute right-0 mt-2 w-48 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl z-50 overflow-hidden backdrop-blur-md">
                            <div class="p-3 border-b border-slate-700 bg-slate-800/50">
                                <p class="text-xs text-slate-500 uppercase font-bold tracking-widest">Minha Conta</p>
                            </div>
                            <Link href="/perfil" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-300 hover:bg-emerald-500 hover:text-slate-900 transition-colors">
                                <i class="fas fa-id-card w-4"></i> Meu Perfil
                            </Link>
                            <Link href="/sair" class="flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-500 hover:text-white transition-colors border-t border-slate-700">
                                <i class="fas fa-power-off w-4"></i> Sair do Sistema
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex flex-1 relative">
            <aside class="bg-slate-800/50 border-r border-slate-700 p-4 flex flex-col gap-1 transition-all duration-300 ease-in-out z-10 overflow-hidden select-none"
                   :class="sidebarAberta ? 'w-45' : 'w-16'">
                
                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-1 mb-2 whitespace-nowrap transition-opacity duration-200"
                     :class="sidebarAberta ? 'opacity-100' : 'opacity-0'">
                    Navegação
                </div>
                
                <div>
                    <button @click="alternarSubmenu('dashboard')" 
                            class="w-full flex items-center justify-between px-1 py-2 rounded-lg hover:bg-slate-700/60 text-slate-300 hover:text-white transition-all group text-sm cursor-pointer">
                        <div class="flex items-center gap-1">
                            <span class="w-6 text-center text-slate-400 group-hover:text-emerald-400 text-base transition-colors">
                                <i class="fas fa-chart-pie"></i>
                            </span>
                            <span class="font-medium whitespace-nowrap transition-all"
                                  :class="sidebarAberta ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none'">
                                Dashboard
                            </span>
                        </div>
                        <i v-if="sidebarAberta" class="fas fa-chevron-right text-[10px] text-slate-500 transition-transform duration-200"
                           :class="submenuAtivo === 'dashboard' ? 'rotate-90 text-emerald-400' : ''"></i>
                    </button>

                    <div class="overflow-hidden transition-all duration-300 ease-in-out pl-5 flex flex-col gap-1"
                         :class="submenuAtivo === 'dashboard' && sidebarAberta ? 'max-h-40 mt-1 pb-1' : 'max-h-0'">
                        <Link href="/teste-vue" class="text-xs text-slate-400 hover:text-emerald-400 py-1.5 transition-colors block">
                            <i class="fas fa-circle text-[6px] mr-2 text-slate-600"></i> Visão Geral
                        </Link>
                        <Link href="/analytics" class="text-xs text-slate-400 hover:text-emerald-400 py-1.5 transition-colors block">
                            <i class="fas fa-circle text-[6px] mr-2 text-slate-600"></i> Estatísticas
                        </Link>
                    </div>
                </div>
                
                <div>
                    <button @click="alternarSubmenu('usuarios')" 
                            class="w-full flex items-center justify-between px-1 py-2 rounded-lg hover:bg-slate-700/60 text-slate-300 hover:text-white transition-all group text-sm cursor-pointer">
                        <div class="flex items-center gap-3">
                            <span class="w-6 text-center text-slate-400 group-hover:text-emerald-400 text-base transition-colors">
                                <i class="fas fa-users"></i>
                            </span>
                            <span class="font-medium whitespace-nowrap transition-all"
                                  :class="sidebarAberta ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none'">
                                Usuários
                            </span>
                        </div>
                        <i v-if="sidebarAberta" class="fas fa-chevron-right text-[10px] text-slate-500 transition-transform duration-200"
                           :class="submenuAtivo === 'usuarios' ? 'rotate-90 text-emerald-400' : ''"></i>
                    </button>

                    <div class="overflow-hidden transition-all duration-300 ease-in-out pl-5 flex flex-col gap-1"
                         :class="submenuAtivo === 'usuarios' && sidebarAberta ? 'max-h-40 mt-1 pb-1' : 'max-h-0'">
                        <Link href="/usuarios" class="text-xs text-slate-400 hover:text-emerald-400 py-1.5 transition-colors block">
                            <i class="fas fa-circle text-[6px] mr-2 text-slate-600"></i> Listar Todos
                        </Link>
                        <Link href="/usuarios/permissoes" class="text-xs text-slate-400 hover:text-emerald-400 py-1.5 transition-colors block">
                            <i class="fas fa-circle text-[6px] mr-2 text-slate-600"></i> Permissões (ACL)
                        </Link>
                    </div>
                </div>

            </aside>

            <div class="flex-1 flex flex-col justify-between overflow-x-hidden">
                <main class="p-6 w-full max-w-7xl mx-auto">
                    <slot />
                </main>
                <footer class="bg-slate-800/30 border-t border-slate-800 p-4 text-center text-xs text-slate-500">
                    <p>&copy; 2026 Meu Sistema - Todos os direitos reservados.</p>
                </footer>
            </div>

            <button @click="configAberta = !configAberta"
                    class="fixed right-0 top-[20px] bg-emerald-500/60 hover:bg-emerald-500/90 backdrop-blur-sm text-slate-900 p-2 rounded-l-lg shadow-xl transition-all duration-300 z-50 border border-emerald-400/20 group cursor-pointer"
                    :class="configAberta ? 'mr-80' : 'mr-0'">
                <i class="fas fa-gear text-sm group-hover:spin-slow"></i>
            </button>

            <aside class="fixed right-0 top-[72px] h-[calc(100vh-72px)] w-80 bg-slate-800 border-l border-slate-700 shadow-2xl p-6 flex flex-col gap-6 transition-transform duration-300 ease-in-out z-40"
                   :class="configAberta ? 'translate-x-0' : 'translate-x-full'">
                <div class="flex justify-between items-center border-b border-slate-700 pb-3">
                    <h3 class="text-base font-bold text-emerald-400 flex items-center gap-2"><i class="fas fa-sliders"></i> Ajustes</h3>
                    <button @click="configAberta = false" class="text-slate-400 hover:text-white cursor-pointer"><i class="fas fa-xmark"></i></button>
                </div>
                <div class="flex flex-col gap-3">
                    <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Cor do Tema</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button class="h-8 rounded bg-emerald-500 border border-emerald-400"></button>
                        <button class="h-8 rounded bg-blue-500 opacity-60 hover:opacity-100"></button>
                        <button class="h-8 rounded bg-purple-500 opacity-60 hover:opacity-100"></button>
                        <button class="h-8 rounded bg-orange-500 opacity-60 hover:opacity-100"></button>
                    </div>
                </div>
            </aside>

        </div>

        <div v-if="userMenuAberto" @click="userMenuAberto = false" class="fixed inset-0 z-30"></div>
    </div>
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