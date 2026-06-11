<script setup>
import Layout from '@/Layouts/LayoutAberto.vue'; 
import { ref, onMounted, inject } from 'vue';
import { useForm, Link, usePage } from '@inertiajs/vue3';
import { mostrarPopup } from '@/sistema.js';

// Injeta o valor provido pelo Layout padrão para resolver as imagens
const appUrl1 = inject('appUrl');
const page = usePage();

const termosAceitos = ref(false);
const campoNome = ref(null);

// Capturando logo do .env via Vite
const logoEmpresa = import.meta.env.VITE_COMPANY_LOGO || 'logo_text';

// --- FORMULÁRIO DE RECUPERAÇÃO DE SENHA AMPLIADO ---
const recoverForm = useForm({
    nome: '',
    cpf: '',
    data_nascimento: '',
    recoveremail: '',
    grecaptcha: '', // Token do reCAPTCHA se for integrar posteriormente
});

// Foco automático no primeiro campo ao carregar a página
onMounted(() => {
    if (campoNome.value) {
        campoNome.value.focus();
    }
});

const submeterRecuperacao = () => {
    // Validações básicas antes do envio
    if (!recoverForm.nome.trim()) {
        mostrarPopup({ titulo: 'Aviso', conteudo: 'Por favor, informe seu Nome/Identificação.', tipo: 'warning', tempo: 4000 });
        return;
    }
    if (!recoverForm.cpf.trim()) {
        mostrarPopup({ titulo: 'Aviso', conteudo: 'O campo CPF é obrigatório.', tipo: 'warning', tempo: 4000 });
        return;
    }
    if (!recoverForm.data_nascimento) {
        mostrarPopup({ titulo: 'Aviso', conteudo: 'Informe sua data de nascimento.', tipo: 'warning', tempo: 4000 });
        return;
    }
    if (!recoverForm.recoveremail.trim()) {
        mostrarPopup({ titulo: 'Aviso', conteudo: 'Informe o e-mail de cadastro.', tipo: 'warning', tempo: 4000 });
        return;
    }
    if (!termosAceitos.value) {
        mostrarPopup({ titulo: 'Termos de Uso', conteudo: 'Você precisa aceitar os Termos de Uso e a Política de Privacidade para prosseguir.', tipo: 'warning', tempo: 5000 });
        return;
    }

    // Envio oficial via Inertia para a rota do Laravel
    recoverForm.post(route('password.email'), {
        onError: (errors) => {
            const mensagemErro = errors.message || 'Falha ao processar solicitação. Verifique os dados.';
            mostrarPopup({ 
                titulo: 'Erro', 
                conteudo: mensagemErro, 
                tipo: 'danger', 
                tempo: 6000 
            });
        },
        onSuccess: () => {
            mostrarPopup({ 
                titulo: 'Sucesso', 
                conteudo: 'Se os dados estiverem corretos, um link de recuperação foi enviado para o seu e-mail!', 
                tipo: 'success', 
                tempo: 6000
            });
            recoverForm.reset();
        }
    });
};
</script>

<template>
    <Layout>
        <section class="min-h-screen flex items-center justify-center bg-cover bg-center transition-colors duration-300 py-10">
            
            <div class="w-full max-w-2xl bg-layout-painel border border-comum shadow-2xl rounded-lg p-8 transition-all duration-300">
                
                <div class="flex items-center justify-center gap-8 mb-6">
                    <img :src="`${appUrl1}/build/assets/images/company/logo_text_003.png`" alt="Logo CTRL NEXT" width="180" style="border-radius: 6px;">
                    <img :src="`${appUrl1}/build/assets/images/company/${logoEmpresa}_005.png`" alt="Logo InterDoc" width="180" style="border-radius: 6px;">
                </div>

                <h2 class="text-center text-2xl font-bold text-texto-claro mb-6">Recuperação de Senha</h2>

                <form @submit.prevent="submeterRecuperacao" class="flex flex-col gap-4">
                    
                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">Identificação/Nome Cadastrado<span class="text-red-500">*</span></label>
                        <input v-model="recoverForm.nome" ref="campoNome"
                               type="text"
                               class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">CPF <span class="text-red-500">*</span></label>
                            <input v-model="recoverForm.cpf"
                                   type="text"
                                   placeholder="000.000.000-00"
                                   class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">Data de Nascimento <span class="text-red-500">*</span></label>
                            <input v-model="recoverForm.data_nascimento"
                                   type="date"
                                   class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">E-mail (Este será seu Login no Sistema) <span class="text-red-500">*</span></label>
                        <input v-model="recoverForm.recoveremail"
                               type="email"
                               class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                    </div>

                    <div class="text-right text-xs text-red-500 font-medium">* Dados Obrigatórios</div>

                    <div class="flex justify-center my-2">
                        <label class="flex items-center gap-2 text-sm text-texto-claro/80 cursor-pointer select-none text-center">
                            <input v-model="termosAceitos" type="checkbox" class="rounded border-comum bg-layout-fundo text-primary focus:ring-0 focus:ring-offset-0 cursor-pointer" />
                            Estou ciente e concordo com os <a :href="route('termos')" target="_self" class="text-primary hover:underline">Termos de Uso e a Política de Privacidade</a> deste serviço.
                        </label>
                    </div>

                    <div class="flex justify-center my-1">
                        <Link :href="route('login')" class="text-primary hover:text-primary-hover font-medium flex items-center gap-2 text-sm transition-colors">
                            <i class="fas fa-info-circle text-base"></i> Já Tenho Cadastro
                        </Link>
                    </div>

                    <div class="flex justify-center my-2">
                        <div class="p-3 border border-comum bg-layout-fundo rounded-lg text-xs text-texto-claro/50 flex items-center gap-3">
                            <input type="checkbox" disabled class="rounded border-comum bg-layout-fundo" />
                            <span>Não sou um robô (reCAPTCHA integrado pelo Backend)</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-comum/30 pt-4 mt-2">
                        <Link :href="route('login')" class="flex items-center gap-2 bg-gray-500/20 hover:bg-gray-500/30 text-texto-claro font-semibold px-5 py-2 rounded-lg text-sm transition-all">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </Link>

                        <button type="submit" :disabled="recoverForm.processing"
                                class="flex items-center gap-2 bg-primary hover:bg-primary-hover disabled:opacity-50 text-texto-escuro font-bold px-6 py-2 rounded-lg text-sm transition-all shadow-lg cursor-pointer">
                            <i class="fas fa-check"></i> {{ recoverForm.processing ? 'Processando...' : 'Solicitar' }}
                        </button>
                    </div>

                </form>
            </div>
        </section>
    </Layout>
</template>