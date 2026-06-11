<script setup>

import Layout from '@/Layouts/LayoutAberto.vue'; 
import { ref, onMounted, inject } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

// Injeta o valor provido pelo Layout padrão
const appUrl1 = inject('appUrl');


// Props recebidas do Controller do Laravel
const props = defineProps({
    rememberuserlogincheck: {
        type: String,
        default: ''
    }
});

const mostrarFormLogin = ref(true);
const verSenha = ref(false);
const loginAnimacao = ref('');

// Capturando logo do .env via Vite (com fallback idêntico ao seu PHP antigo)
const logoEmpresa = import.meta.env.VITE_COMPANY_LOGO || 'logo_text';


// --- FORMULÁRIO DE LOGIN (Corrigido o erro de sintaxe das props) ---
const loginForm = useForm({
    username: props.rememberuserlogincheck || '',
    password: '',
    remember: props.rememberuserlogincheck ? true : false,
});

// --- FORMULÁRIO DE RECUPERAÇÃO DE SENHA ---
const recoverForm = useForm({
    recoveremail: '',
    // Se for utilizar o reCAPTCHA nativo com vue, armazene o token aqui
    grecaptcha: '', 
});

// --- EXECUÇÕES AO MONTAR O COMPONENTE ---
onMounted(() => {
    // Replica a lógica do {!! env('APP_DEBUG') !!} do seu script antigo
    if (import.meta.env.DEV) {
        loginForm.username = 'sistema';
        loginForm.password = 'Sistem@';
    }
});

// --- MÉTODOS / LÓGICAS ---
const alternarFormularios = (exibirLogin) => {
    loginAnimacao.value = 'animate-shake'; // Exemplo de gatilho para efeito visual
    mostrarFormLogin.value = exibirLogin;
    if (!exibirLogin) recoverForm.reset();
};

const submeterLogin = () => {
    // Validações básicas idênticas às que você fazia por ifs no JQuery
    if (loginForm.username.length <= 3) {
        alert('Informe um Usuário Válido');
        return;
    }
    if (loginForm.password.length <= 5) {
        alert('Informe uma Senha Válida');
        return;
    }
    console.log('Tentando login')

    // Dispara a requisição POST oficial do Inertia
   /* loginForm.post(route('autenticar'), {
        onError: () => {
            loginAnimacao.value = 'animate-shake';
            loginForm.reset('password');
            setTimeout(() => loginAnimacao.value = '', 500);
        }
    });*/
};

const submeterRecuperacao = () => {
    if (!recoverForm.recoveremail.includes('@')) {
        alert('Informe um e-mail Válido');
        return;
    }

    recoverForm.post(route('enviasenharecuperacaoporemail'), {
        onSuccess: () => {
            alert('Você receberá um e-mail com as informações de recuperação.');
            alternarFormularios(true);
        }
    });
};
</script>



<template>
    <Layout>

    <section class="min-h-screen flex items-center justify-center bg-cover bg-center transition-colors duration-300">
        
        <div :class="['w-full max-w-md bg-layout-painel border border-comum shadow-2xl rounded-lg p-6 transition-all duration-300', loginAnimacao]">
            
            <div v-if="mostrarFormLogin" class="transition-all duration-300">
                <form @submit.prevent="submeterLogin" class="flex flex-col gap-4">
                    
                    <div class="flex flex-col items-center gap-2 mb-4">
                        <img :src="`${appUrl1}/build/assets/images/company/logo_text_002.png`" alt="Logo Fixa" width="250" style="cursor: pointer;">
                        <img alt="Logo" width="250" :src="`${appUrl1}/build/assets/images/company/${logoEmpresa}_001.png`" style="cursor: pointer;">
                    </div>

                    <div class="flex flex-col gap-1">
                        <input v-model="loginForm.username"
                               type="text" 
                               required
                               placeholder="Usuário/E-mail"
                               tabindex="1"
                               autocomplete="username"
                               :autofocus="!props.rememberuserlogincheck"
                               class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro placeholder-texto-claro/40 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                    </div>

                    <div class="flex flex-col gap-1">
                        <div class="relative w-full">
                            <input v-model="loginForm.password"
                                   :type="verSenha ? 'text' : 'password'" 
                                   required
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
                        <span v-if="loginForm.errors.password" class="text-xs text-red-500 font-medium">{{ loginForm.errors.password }}</span>
                    </div>

                    <div class="flex items-center justify-between text-xs font-medium my-1">
                        <label class="flex items-center gap-2 text-texto-claro/70 cursor-pointer select-none">
                            <input v-model="loginForm.remember" type="checkbox" tabindex="3" class="rounded border-comum bg-layout-fundo text-primary focus:ring-0 focus:ring-offset-0 cursor-pointer" />
                            Lembrar
                        </label>
                        <button type="button" @click="alternarFormularios(false)" class="text-texto-claro/50 hover:text-primary transition-colors flex items-center gap-1 cursor-pointer">
                            <i class="fas fa-lock text-[10px]"></i> Esqueci a Senha
                        </button>
                    </div>

                    <button type="submit"
                            :disabled="loginForm.processing"
                            class="w-full bg-primary hover:bg-primary-hover disabled:opacity-50 text-texto-escuro font-bold py-2.5 rounded-lg text-sm transition-all cursor-pointer shadow-lg">
                        {{ loginForm.processing ? 'Aguarde...' : 'Autenticar' }}
                    </button>

                    <div class="text-center text-xs text-texto-claro/60 mt-2">
                        Não tem Conta? 
                        <Link :href="route('dashboard')" class="text-primary font-bold ml-1 hover:underline">Crie uma</Link>
                    </div>
                </form>
            </div>

            <div v-else class="transition-all duration-300">
                <form @submit.prevent="submeterRecuperacao" class="flex flex-col gap-4">
                    <div>
                        <h3 class="text-base font-bold text-primary mb-1">Recuperação de Senha</h3>
                        <p class="text-xs text-texto-claro/60">Informe seu e-mail para que as instruções sejam enviadas a você!</p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <input v-model="recoverForm.recoveremail"
                               type="email" 
                               required
                               placeholder="Email"
                               class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:outline-none text-sm transition-all" />
                        <span v-if="recoverForm.errors.recoveremail" class="text-xs text-red-500 font-medium">{{ recoverForm.errors.recoveremail }}</span>
                    </div>

                    <button type="submit"
                            :disabled="recoverForm.processing"
                            class="w-full bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-semibold py-2.5 rounded-lg text-sm transition-all cursor-pointer shadow-md">
                        {{ recoverForm.processing ? 'Processando...' : 'Quero resetar' }}
                    </button>

                    <button type="button" 
                            @click="alternarFormularios(true)"
                            class="w-full bg-layout-fundo border border-comum text-texto-claro hover:bg-texto-claro/10 font-medium py-2 rounded-lg text-xs transition-all cursor-pointer flex items-center justify-center gap-1">
                        <i class="fas fa-undo"></i> Cancela
                    </button>


                </form>
            </div>

        </div>
    </section>

    </Layout>
</template>


<style scoped>
/* Efeito shake sutil herdado do seu JQuery para indicar falhas */
.animate-shake {
    animation: shake 0.4s ease-in-out;
}
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-6px); }
    75% { transform: translateX(6px); }
}
</style>