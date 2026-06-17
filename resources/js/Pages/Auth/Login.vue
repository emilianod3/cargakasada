<script setup>

import Layout from '@/Layouts/LayoutAberto.vue'; 
import { ref, onMounted, inject } from 'vue';
import { useForm, Link, usePage, router } from '@inertiajs/vue3';
import { mostrarPopup } from '@/sistema.js';

// Injeta o valor provido pelo Layout padrão
const appUrl1 = inject('appUrl');


// Props recebidas do Controller do Laravel
const props = defineProps({
    rememberuserlogincheck: {
        type: String,
        default: ''
    }
});


const verSenha = ref(false);
const loginAnimacao = ref('');
const page = usePage();
const qtderros = ref(0);
const botaoSubmit = ref(null);
const username1 = ref(null);
const userpassword = ref(null);

// Capturando logo do .env via Vite (com fallback idêntico ao seu PHP antigo)
const logoEmpresa = import.meta.env.VITE_COMPANY_LOGO || 'logo_text';

// --- FORMULÁRIO DE LOGIN DIRETO ---
const loginForm = useForm({
    username: '', 
    password: '',
    remember: (props.rememberuserlogincheck.trim().length > 0) ? true : false,
});

// --- FORMULÁRIO DE RECUPERAÇÃO DE SENHA ---
const recoverForm = useForm({
    recoveremail: '',
    // Se for utilizar o reCAPTCHA nativo com vue, armazene o token aqui
    grecaptcha: '', 
});


onMounted(() => {

    if (page.props.app_debug) {
        loginForm.username = import.meta.env.VITE_DEV_USERNAME || '';
        loginForm.password = import.meta.env.VITE_DEV_PASSWORD || '';
        if (botaoSubmit.value) {
            botaoSubmit.value.focus();
        }
    }else{
        if(props.rememberuserlogincheck.trim().length > 0){
            loginForm.username = props.rememberuserlogincheck;
            if (userpassword.value) {
                userpassword.value.focus();
            }            
        }else{
            loginForm.username = '';
            if (username1.value) {
                username1.value.focus();
            }           
        }
        loginForm.password = '';
    }

    page.props.app_debug ? console.log('Iniciando...'+props.rememberuserlogincheck) : '';
  
});

const submeterLogin = () => {
    
    let error1 = false;
    if (loginForm.username.length <= 4) {
        page.props.app_debug ? console.log('Informe um Usuário Válido') : '';
        mostrarPopup({ titulo: '',  conteudo: 'Informe um Usuário Válido', tipo: 'warning', tempo: 5000, bloquearCliqueFora: false });
        error1 = true;  
        shakeform(error1);
        return;
    } 
    
    if (loginForm.password.length <= 5) {        
        page.props.app_debug ? console.log('Informe a Senha Válida') : '';
        mostrarPopup({ titulo: '',  conteudo: 'Informe a Senha Válida', tipo: 'warning', tempo: 5000, bloquearCliqueFora: false });
        error1 = true;
        shakeform(error1);
        return;
    }    


    // 3. Envio oficial via Inertia (Equivalente ao seu $.ajax)
    // O Inertia já injeta o CSRF-TOKEN e gerencia o progresso de tela automaticamente!
    loginForm.post(route('autenticar'), {
        
        // Executado se o Laravel retornar erros de validação (ex: credenciais incorretas)
        onError: (errors) => {
            shakeform(true);
            qtderros.value++;
            
            // Se o backend enviar uma mensagem específica, nós exibimos. Senão, exibe a padrão.
            const mensagemErro = errors.message || 'Falha no Processamento, Tente Novamente';
            
            mostrarPopup({ 
                titulo: 'Informativo', 
                conteudo: mensagemErro, 
                tipo: 'danger', 
                tempo: 8000 
            });

            loginForm.reset(); 
            
            // Lógica antiga de recarregar a página se acumular muitos erros
            if (qtderros.value >= 3) {
                page.props.app_debug ? console.log('Errou 3 vezes carrega novamente') : '';
                window.location.reload();
            }
        },
        
        // Executado quando a requisição completa com sucesso
        onSuccess: () => {
            // Nota: O redirecionamento para o /dashboard deve vir lá do return do seu Controller!
            // Quando o Laravel der o redirect, o Inertia troca de página mantendo o SPA fluído.
            mostrarPopup({ 
                titulo: 'Informativo', 
                conteudo: 'Conectado com sucesso! Aguarde.', 
                tipo: 'success', 
                tempo: 2000
            });

            // 🚀 FORÇA O INERTIA A MUDAR DE TELA VIA JAVASCRIPT:
            setTimeout(() => {
                router.visit(route('dashboard'));
            }, 1500);
        }
    });
};

function shakeform(erro2) {
    if (erro2) {
        loginForm.reset('username');
        loginForm.reset('password');
        loginAnimacao.value = 'animate-shake';
        setTimeout(() => { loginAnimacao.value = ''; }, 1000);
    }
}

function esquecisenha() {
    //page.props.app_debug ? console.log(props.rememberuserlogincheck.trim().length +' testando variavel...'+props.rememberuserlogincheck) : '';
    router.visit(route('esquecisenha'));
}



</script>



<template>
    <Layout>

    <section class="min-h-screen flex items-center justify-center bg-cover bg-center transition-colors duration-300">
        
        <div :class="['w-full max-w-md bg-layout-painel border border-comum shadow-2xl rounded-lg p-6 transition-all duration-300', loginAnimacao]">
            
            <div class="transition-all duration-300">
                <form @submit.prevent="submeterLogin" class="flex flex-col gap-4">
                    
                    <div class="flex flex-col items-center gap-2 mb-4 w-full">
                        <img :src="`${appUrl1}/build/assets/images/company/logo_text_003.png`" 
                            alt="Logo Fixa" 
                            class="w-full max-w-62.5 h-auto cursor-pointer rounded-xl">

                        <img :src="`${appUrl1}/build/assets/images/company/${logoEmpresa}_005.png`" 
                            alt="Logo" 
                            class="w-full max-w-62.5 h-auto cursor-pointer rounded-xl">
                    </div>                    

                    <div class="flex flex-col gap-1">
                        <input v-model="loginForm.username" ref="username1"
                               type="text"
                               placeholder="Usuário/E-mail"
                               tabindex="1"
                               autocomplete="username"
                               class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro placeholder-texto-claro/40 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                    </div>
                    <!-- :autofocus="!props.rememberuserlogincheck"-->

                    <div class="flex flex-col gap-1">
                        <div class="relative w-full">
                            <input v-model="loginForm.password" ref="userpassword"
                                   :type="verSenha ? 'text' : 'password'"                                 
                                   placeholder="Senha"
                                   tabindex="2"
                                   autocomplete="current-password"
                                   class="w-full p-2.5 pr-10 rounded-lg border border-comum bg-layout-fundo text-texto-claro placeholder-texto-claro/40 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                            
                            <span @click="verSenha = !verSenha" 
                                  class="absolute right-3 top-1/2 -translate-y-1/2 text-texto-claro/40 hover:text-primary cursor-pointer text-base transition-colors"
                                  :title="verSenha ? 'Esconder Senha' : 'Mostrar Senha'">
                                <i :class="verSenha ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs font-medium my-1">
                        <label class="flex items-center gap-2 text-texto-claro/70 cursor-pointer select-none" title="Lembrar Usuário no Próximo Login">
                            <input v-model="loginForm.remember" type="checkbox" tabindex="3" class="rounded border-comum bg-layout-fundo text-primary focus:ring-0 focus:ring-offset-0 cursor-pointer" />
                            Lembrar
                        </label>
                        <button type="button" @click="esquecisenha()" class="text-texto-claro/50 hover:text-primary transition-colors flex items-center gap-1 cursor-pointer" title="Recuperação de Senha">
                            <i class="fas fa-lock text-[10px]"></i> Esqueci a Senha
                        </button>
                    </div>

                    <button type="submit" ref="botaoSubmit"
                            :disabled="loginForm.processing"
                            class="w-full bg-primary hover:bg-primary-hover disabled:opacity-50 text-texto-escuro font-bold py-2.5 rounded-full text-sm transition-all cursor-pointer shadow-lg">
                        {{ loginForm.processing ? 'Aguarde...' : 'Autenticar' }} <i class="fas fa-lock text-xl"></i>
                    </button>

                    <div class="text-center text-xs text-texto-claro/60 mt-2">
                        Não tem Conta? 
                        <Link :href="route('registrar.usuario')" class="text-primary font-bold ml-1 hover:underline">Crie uma</Link>
                    </div>
                </form>
            </div>
        </div>
    </section>

    </Layout>
</template>


<style scoped>
/* Efeito shake sutil herdado do seu JQuery para indicar falhas */
.animate-shake {
    animation: shake 1.5s ease-in-out;
}

@keyframes shake {
    /* Criando micro-tremores durante o ciclo de 5 segundos */
    0%, 10%, 20%, 30%, 40%, 100% { transform: translateX(0); }
    2.5%, 12.5%, 22.5%, 32.5% { transform: translateX(-6px); }
    7.5%, 17.5%, 27.5%, 37.5% { transform: translateX(6px); }
}
</style>