<script setup>
import Layout from '@/Layouts/PainelInterno.vue';
import { onMounted, inject, computed } from 'vue';
import { usePage} from '@inertiajs/vue3';
import { formataDataHora } from '@/sistema.js';

const appUrl1 = inject('appUrl');
const page = usePage();
const logoEmpresa = import.meta.env.VITE_COMPANY_LOGO || 'logo_text';
const usuarioLogado = computed(() => page.props.auth?.user || null);
const grupoLogado = computed(() => page.props.auth?.grupo || null);


const voltar = () => {
    window.history.back();
};

onMounted(() => {
    if (usuarioLogado.value) {
        
    }
});


</script>


<template>
    <Layout>
        <section class="w-full flex justify-center py-4">
            <div class="w-full bg-layout-painel border border-comum shadow-2xl rounded-lg p-6 md:p-10 transition-all duration-300">    
                <div class="flex flex-col items-center text-center border-b border-comum/20 pb-6 mb-6 w-full">
                    <img alt="Logo" 
                        class="w-full max-w-xs md:max-w-sm h-auto mb-4 rounded-lg object-contain" 
                        :src="`${appUrl1}/build/assets/images/company/${logoEmpresa}_005.png`"> 
                    <h2 class="text-2xl font-bold text-texto-claro">Sistema e Recursos Integrados</h2>
                </div>    

                <div class="space-y-6 text-lg text-justify leading-relaxed">
                    <div>
                        <p>Nosso Sistema foi concebido com uma missão clara: transformar a maneira como você e sua equipe gerenciam processos, tomam decisões e alcançam resultados.</p> 
                    </div>

                    <div>
                        <p>Nossa missão é simplificar a complexidade da gestão de processos e eliminar gargalos operacionais, garantindo que sua organização utilize seus recursos de forma mais eficiente, transparente e estratégica. Nós existimos para que você gaste menos tempo com tarefas manuais e mais tempo focado no crescimento e na inovação.</p>
                    </div>

                    <div>
                        <p>Uma plataforma robusta de Controle de Gestão e Processos, desenvolvida para ser a central de inteligência da sua Organização. Ele integra todas as áreas essenciais, desde a entrada de dados até o resultado final, proporcionando uma visão ampla de seus dados.</p>
                    </div>

                    <div>
                        <template v-if="grupoLogado && grupoLogado?.id === 1">
                            <h4 class="text-xl font-bold tracking-wide mb-6">
                                Configurações e Recursos do Servidor da Aplicação
                            </h4>
                            <div class="border-t border-comum/20 pt-4">
                                <h3 class="text-lg font-bold text-primary mb-2">Grupo de Usuário</h3>
                                <p>{{ grupoLogado.id }} - {{ grupoLogado.gidentificacao }}</p>
                                <ul class="list-disc pl-6 flex flex-col gap-1 mb-2"> 
                                    <li> {{ grupoLogado.id }} - {{ grupoLogado.gidentificacao }}</li>
                                </ul>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-primary mb-2">Usuário</h3>
                                <ul class="list-disc pl-6 flex flex-col gap-1 mb-2">
                                    <li><strong>ID:</strong> {{ usuarioLogado.id }}</li>
                                    <li><strong>Login:</strong> {{ usuarioLogado.ulogin }}</li>   
                                    <li><strong>Identificação:</strong> {{ usuarioLogado.unico.id }} - {{ usuarioLogado.unico.unidentificacao }}</li>
                                </ul>    

                                <img :src="usuarioLogado?.unico?.avatar?.uapath
                                ? `${appUrl1}/storage/${usuarioLogado?.unico?.avatar?.uapath}` 
                                : `${appUrl1}/build/assets/images/sistema/defaultuser.png`" 
                                @error="$event.target.src = `${appUrl1}/build/assets/images/sistema/defaultuser.png`"
                                alt="Avatar do Usuário" class="w-48 h-48 rounded-full object-cover shadow-md border-2 border-comum/20 m-5" id="uservatar1">
                            </div>                            

                            <h4 class="text-xl font-bold tracking-wide mb-3 mt-6">
                                Sistema
                            </h4>
                            <hr>
                            <h3 class="text-lg font-bold text-primary mb-2">Dados de Configuração de Sistema</h3>
                            <ul class="list-disc pl-6 flex flex-col gap-1 mb-2">
                                <li><strong>Path Storage:</strong> {{ $page.props.storagepath }}</li>
                                <li><strong>URL Public:</strong> {{ $page.props.app_url }}/public</li>   
                                <li><strong>URL:</strong> {{ $page.props.app_url }}</li>
                                <li><strong>SERVER IP:</strong> {{ $page.props.sistema.serverIp }}</li>
                                <li><strong>CLIENT IP:</strong> {{ $page.props.sistema.clientIp }}</li>
                            </ul>
                            
                            <h4 class="text-xl font-bold tracking-wide mb-3 mt-6 ">
                                Serviços
                            </h4>
                            <hr>
                            <h3 class="text-lg font-bold text-primary mb-2">Serviços de Sistema</h3>
                            <ul class="list-disc pl-6 flex flex-col gap-1 mb-2">
                                <li><strong>Versão do Sistema:</strong> {{ $page.props.sistema.versao }} - {{ formataDataHora($page.props.sistema.atualizado) }}</li>
                                <li><strong>Build:</strong> {{ $page.props.sistema.hash }}</li>   
                                <li><strong>PHP:</strong> {{ $page.props.versions.php_version }}</li>
                                <li><strong>Laravel:</strong> {{ $page.props.versions.laravel_version }}</li>
                                <li><strong>Composer:</strong> {{ $page.props.sistema.composerVersion }}</li>
                                <li><strong>Node:</strong> {{ $page.props.sistema.nodeVersion }}</li>
                                <li><strong>S.O. Servidor:</strong> {{ $page.props.sistema.soServidor }} - {{ $page.props.sistema.soServidorDetalhado }}</li>
                                <li><strong>Libre Office:</strong> {{ $page.props.sistema.libreOfficeVersion }}</li>
                            </ul>                            

                            <button @click="voltar" class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-texto-escuro font-bold px-6 py-2.5 rounded-full text-sm transition-all shadow-lg cursor-pointer opacity-60">
                                    <i class="fas fa-arrow-left"></i> Voltar
                            </button>
                        </template>

                    </div>

                </div>

            </div>
        </section>
    </Layout>    
</template>