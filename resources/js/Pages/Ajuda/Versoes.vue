<script setup>
import Layout from '@/Layouts/PainelInterno.vue';
import { Head } from '@inertiajs/vue3';

// 📝 HISTÓRICO DE VERSÕES MANUAL E ESTÁTICO
// Basta adicionar novas versões no topo deste array sempre que atualizar o sistema!
const historicoVersoes = [
    {
        versao: 'v1.0.21',
        data: '28/05/2026',
        tipo: 'ajuste', // feature, ajuste, correcao
        titulo: 'Melhorias de interface',
        descricao: 'Implementada Novas Opções de Cores escuras para background.',
    },
    {
        versao: 'v1.0.11',
        data: '21/05/2026',
        tipo: 'ajuste', // feature, ajuste, correcao
        titulo: 'Melhorias no Painel e Validações',
        descricao: 'Implementada a validação inteligente de nomes. Ajustada a rota do formulário de contato para o padrão Rest e aplicados novos designs dinâmicos nos botões do menu lateral.',
    },
    {
        versao: 'v1.0.5',
        data: '18/05/2026',
        tipo: 'correcao',
        titulo: 'Resolução de Conflito de Sessões',
        descricao: 'Correções estruturais de cache/sessão',
    },
    {
        versao: 'v1.0.0',
        data: '13/05/2026',
        tipo: 'feature',
        titulo: 'Lançamento da Primeira Versão do Sistema',
        descricao: 'Primeira Versão do Sistema em Beta. Este lançamento engloba o painel interno completo, central de ajuda, formulário de contato com validação inteligente.', 
    }
    
];

// Função auxiliar para definir as cores das badges/etiquetas de alteração
const obterClasseTipo = (tipo) => {
    switch (tipo) {
        case 'feature': return 'bg-green-500/10 text-green-400 border-green-500/20';
        case 'ajuste': return 'bg-blue-500/10 text-blue-400 border-blue-500/20';
        case 'correcao': return 'bg-amber-500/10 text-amber-400 border-amber-500/20';
        default: return 'bg-comum text-texto-claro/80';
    }
};

const obterTextoTipo = (tipo) => {
    switch (tipo) {
        case 'feature': return 'Nova Funcionalidade';
        case 'ajuste': return 'Melhoria';
        case 'correcao': return 'Correção de Erro';
        default: return 'Atualização';
    }
};
</script>

<template>
    <Head title="Histórico de Versões" />

    <Layout>
        <div class="p-6 max-w-4xl mx-auto flex flex-col gap-6">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-comum pb-4 gap-2">
                <div>
                    <h1 class="text-2xl font-bold text-texto-claro flex items-center gap-2">
                        <i class="fas fa-history text-primary"></i> Notas de Lançamento
                    </h1>
                    <p class="text-sm text-texto-claro/60 mt-1">Acompanhe a evolução, correções e novas funcionalidades do sistema</p>
                </div>
                <span class="text-xs font-semibold bg-primary/20 text-primary border border-primary/30 px-3 py-1 rounded-full">
                    Versão Atual: {{ $page.props.sistema.versao }}
                </span>
            </div>

            <div class="relative border-l border-comum/60 ml-4 flex flex-col gap-8 my-2">
                
                <div v-for="(item, index) in historicoVersoes" :key="index" class="relative pl-6">
                    
                    <span :class="[
                        'absolute -left-[11px] top-1.5 w-5 h-5 rounded-full border-4 border-layout-painel transition-all',
                        index === 0 ? 'bg-primary animate-pulse' : 'bg-comum'
                    ]"></span>
                    
                    <div class="bg-layout-painel border border-comum rounded-lg p-5 shadow-sm hover:border-comum/80 transition-all">
                        
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                <span class="text-lg font-black text-texto-claro tracking-wide">{{ item.versao }}</span>
                                <span :class="['text-xs font-bold px-2.5 py-0.5 rounded-full border', obterClasseTipo(item.tipo)]">
                                    {{ obterTextoTipo(item.tipo) }}
                                </span>
                            </div>
                            <span class="text-xs text-texto-claro/40 font-medium flex items-center gap-1.5">
                                <i class="far fa-calendar-alt"></i> {{ item.data }}
                            </span>
                        </div>
                        
                        <h3 class="text-sm font-bold text-texto-claro/90 mb-2">{{ item.titulo }}</h3>
                        <p class="text-sm text-texto-claro/75 leading-relaxed whitespace-pre-line bg-layout-fundo/40 p-3 rounded-md border border-comum/30">
                            {{ item.descricao }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </Layout>
</template>