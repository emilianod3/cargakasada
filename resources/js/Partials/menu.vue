<script setup>
import { Link, router } from '@inertiajs/vue3';
import { jssistema } from '@/sistema';

import { computed, onMounted, inject } from 'vue'
import { usePage } from '@inertiajs/vue3'
const page2 = usePage()
const appUrl1 = inject('appUrl');
const listaMenus = computed(() => page2.props.menus || [])

const { sidebarAberta, submenusAbertos, alternarSubmenu, irParaLink } = jssistema();

onMounted(() => {
    
    page2.props.app_debug ? console.log('Iniciando Menu') : '';
});
</script>








<template>
  <aside 
    class="bg-layout-painel md:bg-layout-painel/50 border-r border-comum flex flex-col gap-1 transition-all duration-300 ease-in-out select-none flex-shrink-0
           md:sticky md:top-16 md:h-[calc(100vh-4rem)] 
           max-md:fixed max-md:top-16 max-md:bottom-0 max-md:left-0 max-md:z-40
           overflow-y-auto overflow-x-hidden"
    :class="[
      sidebarAberta 
        ? 'md:w-48 max-md:w-48 max-md:translate-x-0 p-4 max-md:p-5' 
        : 'md:w-16 max-md:-translate-x-full py-4 px-2'
    ]"
  >
    
    <div 
      v-show="sidebarAberta"
      class="text-[10px] font-bold text-texto-claro/40 uppercase tracking-widest px-1 mb-2 whitespace-nowrap transition-opacity duration-200"
    >
      Navegação
    </div>

    <div v-for="menu in listaMenus" :key="menu.id" class="w-full flex flex-col gap-1">
      
      <component 
        :is="menu.submenus && menu.submenus.length ? 'button' : Link"
        :href="menu.submenus && menu.submenus.length ? null : (appUrl1+menu.rota || appUrl1+menu.url)"
        @click="menu.submenus && menu.submenus.length ? alternarSubmenu(menu.id) : null"
        class="w-full flex items-center justify-between px-2 py-2 rounded-lg hover:bg-texto-claro/10 text-texto-claro transition-all group text-sm cursor-pointer text-left"
      >
        <div class="flex items-center gap-2 min-w-0 flex-1">
          <span class="w-6 text-center text-texto-claro/40 group-hover:text-primary text-base transition-colors flex-shrink-0">
            <i :class="menu.icone"></i>
          </span>
          
          <span 
            v-show="sidebarAberta"
            class="font-medium whitespace-nowrap transition-all duration-300 dynamic-text"
          >
            {{ menu.nome }}
          </span>
        </div>

        <i v-if="sidebarAberta && menu.submenus && menu.submenus.length" 
          class="fas fa-chevron-right text-[10px] text-texto-claro/30 transition-transform duration-200 flex-shrink-0 ml-1" 
          :class="submenusAbertos[menu.id] ? 'rotate-90 text-primary' : ''"></i>
      </component>

      <div 
        v-if="menu.submenus && menu.submenus.length && sidebarAberta"
        class="overflow-hidden transition-all duration-300 ease-in-out pl-6 flex flex-col gap-1" 
        :class="submenusAbertos[menu.id] ? 'max-h-40 mt-1 pb-1' : 'max-h-0'">
        
        <Link 
          v-for="sub in menu.submenus" 
          :key="sub.id || sub.rota || sub.url"
          :href="appUrl1+sub.rota || appUrl1+sub.url"
          class="text-xs text-texto-claro/90 hover:text-primary py-1.5 transition-colors block whitespace-nowrap overflow-hidden text-ellipsis">
          <i class="fas fa-circle text-[6px] mr-2 opacity-40"></i> 
          {{ sub.nome }}
        </Link>
      </div>

    </div>

  </aside>
</template>