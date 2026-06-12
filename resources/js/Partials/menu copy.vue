<script setup>
import { Link, router } from '@inertiajs/vue3';
import { jssistema } from '@/sistema';


import { computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
const page2 = usePage()
const menuHtml = computed(() => page2.props.menus || '')


const { sidebarAberta, submenusAbertos, alternarSubmenu, irParaLink } = jssistema();




onMounted(() => {

    console.log('Montando menus...'+page2.props.menus);
  
});



</script>

<template>
    <aside class="bg-layout-painel/50 border-r border-comum p-4 flex flex-col gap-1 transition-all duration-300 ease-in-out z-10 overflow-hidden select-none"
           :class="sidebarAberta ? 'w-45' : 'w-16'">
        
        <div class="text-[10px] font-bold text-texto-claro/40 uppercase tracking-widest px-1 mb-2 whitespace-nowrap transition-opacity duration-200"
             :class="sidebarAberta ? 'opacity-100' : 'opacity-0'">
            Navegação
        </div>

        <div class="sidebar-container">
            <div 
            v-html="menuHtml" 
            @click="lidarComCliqueNoMenu"
            class="w-full"
            ></div>
        </div>
        
        <div>
            <button @click="alternarSubmenu('dashboard')" v-tooltip.right="!sidebarAberta ? 'Dashboard' : null" class="w-full flex items-center justify-between px-1 py-2 rounded-lg hover:bg-texto-claro/10 text-texto-claro transition-all group text-sm cursor-pointer">
                <div class="flex items-center gap-1">
                    <span class="w-6 text-center text-texto-claro/40 group-hover:text-primary text-base transition-colors">
                        <i class="fas fa-chart-pie"></i>
                    </span>
                    <span class="font-medium whitespace-nowrap transition-all" :class="sidebarAberta ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none'">
                        Dashboard
                    </span>
                </div>
                <i v-if="sidebarAberta" class="fas fa-chevron-right text-[10px] text-texto-claro/30 transition-transform duration-200" :class="submenusAbertos.dashboard ? 'rotate-90 text-primary' : ''"></i>
            </button>

            <div class="overflow-hidden transition-all duration-300 ease-in-out pl-5 flex flex-col gap-1" :class="submenusAbertos.dashboard && sidebarAberta ? 'max-h-40 mt-1 pb-1' : 'max-h-0'">
                <Link href="/teste-vue" class="text-xs text-texto-claro/90 hover:text-primary py-1.5 transition-colors block"><i class="fas fa-circle text-[6px] mr-2 opacity-40"></i> Visão Geral</Link>
                <Link href="/analytics" class="text-xs text-texto-claro/90 hover:text-primary py-1.5 transition-colors block"><i class="fas fa-circle text-[6px] mr-2 opacity-40"></i> Estatísticas</Link>
            </div>
        </div>

        <div>
            <button @click="alternarSubmenu('dashboard1')" v-tooltip.right="!sidebarAberta ? 'Dashboard 1' : null" class="w-full flex items-center justify-between px-1 py-2 rounded-lg hover:bg-texto-claro/10 text-texto-claro transition-all group text-sm cursor-pointer">
                <div class="flex items-center gap-1">
                    <span class="w-6 text-center text-texto-claro/40 group-hover:text-primary text-base transition-colors">
                        <i class="fas fa-chart-pie"></i>
                    </span>
                    <span class="font-medium whitespace-nowrap transition-all" :class="sidebarAberta ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none'">
                        Dashboard 1
                    </span>
                </div>
                <i v-if="sidebarAberta" class="fas fa-chevron-right text-[10px] text-texto-claro/30 transition-transform duration-200" :class="submenusAbertos.dashboard1 ? 'rotate-90 text-primary' : ''"></i>
            </button>

            <div class="overflow-hidden transition-all duration-300 ease-in-out pl-5 flex flex-col gap-1" :class="submenusAbertos.dashboard1 && sidebarAberta ? 'max-h-40 mt-1 pb-1' : 'max-h-0'">
                <Link href="/teste-vue" class="text-xs text-texto-claro/90 hover:text-primary py-1.5 transition-colors block"><i class="fas fa-circle text-[6px] mr-2 opacity-40"></i> Visão Geral</Link>
                <Link href="/analytics" class="text-xs text-texto-claro/90 hover:text-primary py-1.5 transition-colors block"><i class="fas fa-circle text-[6px] mr-2 opacity-40"></i> Estatísticas</Link>
            </div>
        </div>
        
        <div>
            <button @click="alternarSubmenu('usuarios')" v-tooltip.right="!sidebarAberta ? 'Usuários' : null" class="w-full flex items-center justify-between px-1 py-2 rounded-lg hover:bg-texto-claro/10 text-texto-claro transition-all group text-sm cursor-pointer">
                <div class="flex items-center gap-3">
                    <span class="w-6 text-center text-texto-claro/40 group-hover:text-primary text-base transition-colors">
                        <i class="fas fa-users"></i>
                    </span>
                    <span class="font-medium whitespace-nowrap transition-all" :class="sidebarAberta ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none'">
                        Usuários
                    </span>
                </div>
                <i v-if="sidebarAberta" class="fas fa-chevron-right text-[10px] text-texto-claro/30 transition-transform duration-200" :class="submenusAbertos.usuarios ? 'rotate-90 text-primary' : ''"></i>
            </button>

            <div class="overflow-hidden transition-all duration-300 ease-in-out pl-5 flex flex-col gap-1" :class="submenusAbertos.usuarios && sidebarAberta ? 'max-h-40 mt-1 pb-1' : 'max-h-0'">
                <Link href="/usuarios" class="text-xs text-texto-claro/90 hover:text-primary py-1.5 transition-colors block"><i class="fas fa-circle text-[6px] mr-2 opacity-40"></i> Listar Todos</Link>
                <Link href="/usuarios/permissoes" class="text-xs text-texto-claro/90 hover:text-primary py-1.5 transition-colors block"><i class="fas fa-circle text-[6px] mr-2 opacity-40"></i> Permissões (ACL)</Link>
            </div>
        </div>

        <div>
            <button @click="router.visit('teste-vue')" v-tooltip.right="!sidebarAberta ? 'Abre Link' : null" class="w-full flex items-center justify-between px-1 py-2 rounded-lg hover:bg-texto-claro/10 text-texto-claro transition-all group text-sm cursor-pointer">
                <div class="flex items-center gap-1">
                    <span class="w-6 text-center text-texto-claro/40 group-hover:text-primary text-base transition-colors">
                        <i class="fa fa-exclamation-circle"></i>
                    </span>
                    <span class="font-medium whitespace-nowrap transition-all" :class="sidebarAberta ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none'">
                        Abre Link
                    </span>
                </div>
            </button>
        </div>  
        
        <div>
            <button @click="irParaLink('https://google.com', '_self')" v-tooltip.right="!sidebarAberta ? 'Abre Link 2' : null" class="w-full flex items-center justify-between px-1 py-2 rounded-lg hover:bg-texto-claro/10 text-texto-claro transition-all group text-sm cursor-pointer">
                <div class="flex items-center gap-1">
                    <span class="w-6 text-center text-texto-claro/40 group-hover:text-primary text-base transition-colors">
                        <i class="fa-solid fa-apple-whole"></i>
                    </span>
                    <span class="font-medium whitespace-nowrap transition-all" :class="sidebarAberta ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none'">
                        Abre Link 2
                    </span>
                </div>
            </button>
        </div>  

        <div>
            <button @click="irParaLink('https://google.com')" v-tooltip.right="!sidebarAberta ? 'Abre Link 3' : null" class="w-full flex items-center justify-between px-1 py-2 rounded-lg hover:bg-texto-claro/10 text-texto-claro transition-all group text-sm cursor-pointer">
                <div class="flex items-center gap-1">
                    <span class="w-6 text-center text-texto-claro/40 group-hover:text-primary text-base transition-colors">
                        <i class="fa-solid fa-atom"></i>
                    </span>
                    <span class="font-medium whitespace-nowrap transition-all" :class="sidebarAberta ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none'">
                        Abre Link 3
                    </span>
                </div>
            </button>
        </div>                     
    </aside>
</template>