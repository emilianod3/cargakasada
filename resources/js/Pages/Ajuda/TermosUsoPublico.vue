<script setup>
import Layout from '@/Layouts/LayoutAberto.vue';
import { ref, onMounted, inject, computed } from 'vue';
import { usePage, router, Link } from '@inertiajs/vue3';
import { mostrarPopup, formataDataHora } from '@/sistema.js';
import TermosUsoConteudo from '@/Pages/Ajuda/TermosUsoConteudo.vue';

const appUrl1 = inject('appUrl');
const page = usePage();
const logoEmpresa = import.meta.env.VITE_COMPANY_LOGO || 'logo_text';
const textoStatusAceite = ref('Aceite dos Termos de Uso');
const usuarioLogado = computed(() => page.props.auth?.user || null);

const voltar = () => {
    window.history.back();
};

onMounted(() => {
    if (usuarioLogado.value) {
        mensagemAceite();
    }
});

function mensagemAceite(){
    if (usuarioLogado.value.uaceitetermosuso > 0) {
        textoStatusAceite.value = `Aceitou os Termos de Uso em ${formataDataHora(usuarioLogado.value.udataaceitetermosuso)}`;
    } else {
        textoStatusAceite.value = 'Aceite os Termos de Uso';
    }
}

const setTermosdeuso = (tipo) => {
    if (!usuarioLogado) {
        mostrarPopup({ titulo: 'Aviso', conteudo: 'Você precisa estar logado para realizar esta ação.', tipo: 'warning' });
        return;
    }

    // Dispara a requisição GET via Axios
    axios.get(route('cadastro.updatetermouso', { iduser: usuarioLogado.value.id, tipo: tipo }))
    .then((response) => {
        // No Axios, a resposta do servidor fica sempre dentro de "response.data"
        const result = response.data.result;
        if (result === true) {
            const dadosUsuario = response.data.usuario;
            usuarioLogado.value.udataaceitetermosuso = dadosUsuario.udataaceitetermosuso;
            usuarioLogado.value.uaceitetermosuso = dadosUsuario.uaceitetermosuso;
            mensagemAceite();
            mostrarPopup({
                titulo: 'Sucesso!',
                conteudo: 'Os termos de uso foram atualizados.',
                tipo: 'success'
            });
        } else {
            mostrarPopup({
                titulo: 'Erro no Processamento',
                conteudo: 'O usuário não foi localizado ou os dados são inválidos.',
                tipo: 'danger'
            });
        }
    })
    .catch((error) => {
        console.error('Erro na requisição:', error);
        mostrarPopup({
            titulo: 'Falha',
            conteudo: 'Impossível processar requisição, tente novamente.',
            tipo: 'danger'
        });
    });
};

</script>



<template>
    <Layout>
        <section class="min-h-screen flex items-center justify-center bg-layout-fundo py-10 px-4">
            
            <div class="w-full max-w-7xl bg-layout-painel border border-comum shadow-2xl rounded-lg p-6 md:p-10 transition-all duration-300">
                
                <div class="text-texto-claro/90 text-sm leading-relaxed text-justify flex flex-col gap-6">
                    <TermosUsoConteudo 
                        :app-url="appUrl1" 
                        :logo-empresa="logoEmpresa" 
                    />

                    <div class="border-t border-comum/20 pt-6 mt-6 flex flex-col items-center justify-center gap-4">
                        <template v-if="usuarioLogado">
                            <h4 class="text-sm font-bold text-texto-claro" id="infoaceite">{{ textoStatusAceite }}</h4> 
                            <div class="flex items-center gap-4">
                                <button 
                                    v-if="usuarioLogado.uaceitetermosuso == 0"
                                    @click="setTermosdeuso(1)"
                                    class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-green-600 text-green-600 font-medium rounded-full hover:bg-green-600 hover:text-white transition-all duration-300 ease-in-out focus:outline-none"
                                    title="Clique para aceitar os termos e liberar o uso sem Restrições">
                                    <i class="fas fa-thumbs-up"></i> Aceitar Termos
                                </button>
                                
                                <button 
                                    v-else
                                    @click="setTermosdeuso(0)"
                                    class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-red-600 text-red-600 font-medium rounded-full hover:bg-red-600 hover:text-white transition-all duration-300 ease-in-out focus:outline-none"
                                    title="Ao clicar você declina dos termos e seu acesso poderá ser Restringido">
                                    <i class="fas fa-thumbs-down"></i> Revogar Aceite
                                </button>
                            </div>
                            <button @click="voltar" class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-texto-escuro font-bold px-6 py-2.5 rounded-full text-sm transition-all shadow-lg cursor-pointer opacity-60">
                                    <i class="fas fa-arrow-left"></i> Voltar
                            </button>
                        </template>

                        <template v-else>
                            <div class="w-full flex flex-col sm:flex-row items-center justify-between gap-4">
                                <button @click="voltar" class="flex items-center gap-2 bg-primary hover:bg-primary-hover text-texto-escuro font-bold px-6 py-2.5 rounded-full text-sm transition-all shadow-lg cursor-pointer opacity-60">
                                        <i class="fas fa-arrow-left"></i> Voltar
                                </button>
                                <span class="text-sm font-semibold text-primary bg-primary/10 border border-primary/50 px-4 py-2 rounded-lg flex items-center gap-2 text-center sm:text-left">
                                    <i class="fas fa-exclamation-circle"></i> Só será possível aceitar os termos criando uma conta.
                                </span>
                                
                                <div class="flex items-center gap-3">
                                    <button disabled
                                        class="bg-success/50 text-texto-claro/20 font-bold px-5 py-2.5 rounded-full text-sm flex items-center gap-2 cursor-not-allowed border border-text-texto-claro/10"
                                        title="Só será possível aceitar os termos criando uma conta">
                                        <i class="fas fa-thumbs-up"></i> Aceitar Termos
                                    </button>
                                </div>
                            </div>
                        </template>

                    </div>

                </div>

            </div>
        </section>
    </Layout>
</template>