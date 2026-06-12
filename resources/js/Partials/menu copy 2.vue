<script setup>
import { Link, router } from '@inertiajs/vue3';
import { jssistema } from '@/sistema';


import { computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
const page2 = usePage()
const listaMenus = computed(() => page2.props.menus || [])


const { sidebarAberta, submenusAbertos, alternarSubmenu, irParaLink } = jssistema();




onMounted(() => {
    console.log('Iniciando Menu');
    console.log('Montando menus...'+page2.props.menus);
  
});



</script>

<template>
  <aside 
    class="bg-layout-painel/50 border-r border-comum p-4 flex flex-col gap-1 transition-all duration-300 ease-in-out z-10 overflow-hidden select-none"
    :class="sidebarAberta ? 'w-45' : 'w-16'"
  >
    
    <div 
      class="text-[10px] font-bold text-texto-claro/40 uppercase tracking-widest px-1 mb-2 whitespace-nowrap transition-opacity duration-200"
      :class="sidebarAberta ? 'opacity-100' : 'opacity-0'"
    >
      Navegação
    </div>

    <div v-for="menu in listaMenus" :key="menu.id" class="w-full flex flex-col gap-1">
      
      <button 
        @click="alternarSubmenu(menu.id)" 
        class="w-full flex items-center justify-between px-1 py-2 rounded-lg hover:bg-texto-claro/10 text-texto-claro transition-all group text-sm cursor-pointer"
      >
        <div class="flex items-center gap-1">
          <span class="w-6 text-center text-texto-claro/40 group-hover:text-primary text-base transition-colors">
            <i :class="menu.icone"></i>
          </span>
          
          <span 
            class="font-medium whitespace-nowrap transition-all duration-300"
            :class="sidebarAberta ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-4 pointer-events-none'"
          >
            {{ menu.nome }}
          </span>
        </div>

        <i 
          v-if="sidebarAberta && menu.submenus && menu.submenus.length" 
          class="fas fa-chevron-right text-[10px] text-texto-claro/30 transition-transform duration-200" 
          :class="submenusAbertos[menu.id] ? 'rotate-90 text-primary' : ''"
        ></i>
      </button>

      <div 
        v-if="menu.submenus && menu.submenus.length"
        class="overflow-hidden transition-all duration-300 ease-in-out pl-5 flex flex-col gap-1" 
        :class="submenusAbertos[menu.id] && sidebarAberta ? 'max-h-40 mt-1 pb-1' : 'max-h-0'"
      >
        <Link 
          v-for="sub in menu.submenus" 
          :key="sub.url"
          :href="sub.url" 
          class="text-xs text-texto-claro/90 hover:text-primary py-1.5 transition-colors block"
        >
          <i class="fas fa-circle text-[6px] mr-2 opacity-40"></i> 
          {{ sub.nome }}
        </Link>
      </div>

    </div>

  </aside>
</template>