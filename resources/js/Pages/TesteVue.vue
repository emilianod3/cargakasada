<script setup>
import { ref } from 'vue';
// 1. Importe o layout que você acabou de criar
import Layout from '@/Layouts/LayoutAberto.vue'; 

const cliques = ref(0);
</script>

<template>
    <Layout>
        
        <div class="bg-slate-800 p-8 rounded-xl border border-slate-700 text-center shadow-xl">
            <h2 class="text-2xl font-bold text-emerald-400 mb-2">Página Inicial do Sistema</h2>
            <p class="text-slate-400 mb-6">Repare que o topo e o rodapé apareceram sozinhos!</p>
            
            <button @click="cliques++" class="bg-emerald-500 hover:bg-emerald-600 text-slate-900 font-bold px-4 py-2 rounded-lg">
                Cliques: {{ cliques }}
            </button>
        </div>

    </Layout>
</template>