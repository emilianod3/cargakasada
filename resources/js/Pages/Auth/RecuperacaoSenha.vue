<script setup>
import Layout from '@/Layouts/LayoutAberto.vue'; 
import { ref, onMounted, inject, onUnmounted } from 'vue'; // Removido 'reactive' sem uso
import { useForm, Link, usePage, router} from '@inertiajs/vue3';
import * as sistemajs from '@/sistema.js';
import { route } from 'ziggy-js';

// Injeta o valor provido pelo Layout padrão para resolver as imagens
const appUrl1 = inject('appUrl');
const page = usePage();

// 🌟 Pega a Chave Pública injetada pelo Backend via Inertia Shared Props
const siteKey = page.props.NOCAPTCHA_SITEKEY; 

//const aceitatermos = ref(false);
const campoNome = ref(null);
const erroRecaptcha = ref(false);
const recaptchaContainer = ref(null);
let widgetId = null;
let tokenTimer = null;
// Tempo limite de inatividade (Ex: 5 minutos = 5 * 60 * 1000 ms)
const TEMPO_LIMITE_INATIVIDADE = 5 * 60 * 1000;

// Capturando logo do .env via Vite
const logoEmpresa = import.meta.env.VITE_COMPANY_LOGO || 'logo_text';

// --- FORMULÁRIO DE RECUPERAÇÃO DE SENHA AMPLIADO ---
const recoverForm = useForm({
    unidentificacao: '',
    uncpf: '',
    undatanasc: '',
    euemail: '',
    aceitatermos: false,
    grecaptcha: '', // Receberá o token gerado pelo Google
});


// Foco automático e carregamento seguro do reCAPTCHA
onMounted(() => {
    if (campoNome.value) {
        campoNome.value.focus();
    }
    
    // Inicializa o controle de inatividade
    resetarTimerInatividade();
    window.addEventListener('mousemove', resetarTimerInatividade);
    window.addEventListener('keydown', resetarTimerInatividade);
    window.addEventListener('click', resetarTimerInatividade);
    window.addEventListener('scroll', resetarTimerInatividade);
    window.addEventListener('touchstart', resetarTimerInatividade);    

    // 1. Criamos uma função na janela global (window) para o Google achar
    window.vRecaptchaLoaded = () => {
        inicializarRecaptcha();
    };

    // 2. Se o script já existir e o render estiver pronto, inicializa direto
    if (window.grecaptcha && typeof window.grecaptcha.render === 'function') {
        inicializarRecaptcha();
    } else if (!document.getElementById('recaptcha-script')) {
        // 3. Caso contrário, injeta o script passando o callback na URL (?onload=vRecaptchaLoaded)
        const script = document.createElement('script');
        script.id = 'recaptcha-script';
        // Passamos 'onload=vRecaptchaLoaded' para o Google disparar a função na hora certa
        script.src = 'https://www.google.com/recaptcha/api.js?render=explicit&onload=vRecaptchaLoaded';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }    
});

// Limpeza estrita de memória ao destruir a tela
onUnmounted(() => {
    if (tokenTimer) clearTimeout(tokenTimer);
    window.removeEventListener('mousemove', resetarTimerInatividade);
    window.removeEventListener('keydown', resetarTimerInatividade);
    window.removeEventListener('click', resetarTimerInatividade);
    window.removeEventListener('scroll', resetarTimerInatividade);
    window.removeEventListener('touchstart', resetarTimerInatividade);
});

// --- SISTEMA DE CONTROLE DE INATIVIDADE (REDIRECIONAMENTO) ---
const redirecionarParaLogin = () => {
    router.visit(route('login'), {
        replace: true // Substitui o histórico para evitar que volte avançando a página
    });
};

const resetarTimerInatividade = () => {
    if (tokenTimer) clearTimeout(tokenTimer);
    tokenTimer = setTimeout(redirecionarParaLogin, TEMPO_LIMITE_INATIVIDADE);
};

function inicializarRecaptcha() {
    // Adicionada uma proteção extra de segurança (timeout) caso o DOM ainda esteja renderizando a ref
    setTimeout(() => {
        if (window.grecaptcha && typeof window.grecaptcha.render === 'function' && recaptchaContainer.value) {
            widgetId = window.grecaptcha.render(recaptchaContainer.value, {
                sitekey: siteKey,
                callback: (token) => {
                    recoverForm.grecaptcha = token;
                    erroRecaptcha.value = false;
                },
                'expired-callback': () => {
                    recoverForm.grecaptcha = '';
                },
                'error-callback': () => {
                    recoverForm.grecaptcha = '';
                }
            });
        }
    }, 50); // 50 milissegundos são suficientes para o Vue montar a div na tela
}

const submeterRecuperacao = () => {
    if (!recoverForm.unidentificacao.trim() || sistemajs.strlen(recoverForm.unidentificacao) < 5 ) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Por favor, informe seu o Nome/Identificação cadastrado Válido.', tipo: 'warning', tempo: 4000 });
        return;
    }
    if (!recoverForm.uncpf.trim() || !sistemajs.TestaCPF(recoverForm.uncpf)) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Informe o CPF Válido.', tipo: 'warning', tempo: 4000 });
        return;
    }

    if (!sistemajs.temMaisDe18Anos(recoverForm.undatanasc)) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Informe a Data de Nascimento Válida.', tipo: 'warning', tempo: 4000 });
        return;
    }

    if (!recoverForm.euemail.trim() || !sistemajs.validacaoEmail(recoverForm.euemail)) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Informe o e-mail de cadastro Válido.', tipo: 'warning', tempo: 4000 });
        return;
    }
    if (!recoverForm.aceitatermos) {
        sistemajs.mostrarPopup({ titulo: 'Termos de Uso', conteudo: 'Você precisa aceitar os Termos de Uso e a Política de Privacidade para prosseguir.', tipo: 'warning', tempo: 5000 });
        return;
    }
    
    // 🌟 Validação obrigatória do reCAPTCHA no clique do botão
    if (!recoverForm.grecaptcha) {
        erroRecaptcha.value = true;
        sistemajs.mostrarPopup({ titulo: 'Verificação Obrigatória', conteudo: 'Por favor, marque a caixa "Não sou um robô".', tipo: 'warning', tempo: 4000 });
        return;
    }

    // Envio oficial via Inertia para a rota do Laravel
    recoverForm.post(route('envio.recuperar.senha'), {
        onError: (errors) => {   
            const rerro = errors.resultado;
            try {
                const djson = JSON.parse(rerro);
                /*
                console.log('Texto:', djson.message);
                console.log('Código:', djson.status);
                console.log('Dados Lidos:', djson.data);*/
                sistemajs.mostrarPopup({ 
                    titulo: 'Falha no Processamento',
                    conteudo: djson.message, 
                    tipo: 'danger', 
                    tempo: 6000 
                });

            } catch (e) {
                sistemajs.mostrarPopup({ 
                    titulo: 'Falha no Processamento',
                    conteudo: 'Impossível Processar dados.', 
                    tipo: 'danger', 
                    tempo: 6000 
                });
            }
            recoverForm.reset();
            recoverForm.aceitatermos = false;            
            // 🌟 IMPORTANTE: Se o backend retornar erro, reseta o widget do google 
            // para que o cliente consiga clicar novamente sem dar token duplicado
            if (window.grecaptcha && widgetId !== null) {
                window.grecaptcha.reset(widgetId);
                recoverForm.grecaptcha = '';
            }
            campoNome.value.focus();
        },
        onSuccess: () => {
            const rsucesso = usePage().props.flash?.resultado;
            //console.log('Saida:', rsucesso);
            const djson = JSON.parse(rsucesso);
            /*
            console.log('Texto:', djson.message);
            console.log('Código:', djson.status);
            console.log('Dados Lidos:', djson.data);*/
            sistemajs.mostrarPopup({ 
                titulo: 'Recuperação de Acesso', 
                conteudo: 'Acesse seu e-mail para Finalizar o Procedimento', 
                tipo: 'success', 
                tempo: 10000
            });
            recoverForm.reset();
            recoverForm.aceitatermos = false;            
            setTimeout(() => {
                window.location.href = route('login');
            }, 6000);            

        }
    });
};
</script>

<template>
    <Layout>
        <section class="min-h-screen flex items-center justify-center bg-cover bg-center transition-colors duration-300 py-10">
            
            <div class="w-full max-w-2xl bg-layout-painel border border-comum shadow-2xl rounded-lg p-8 transition-all duration-300">
                
                <div class="flex flex-col md:flex-row items-center justify-center gap-6 md:gap-8 mb-6">
                    <img 
                        :src="`${appUrl1}/build/assets/images/company/logo_text_003.png`" 
                        alt="Logo" 
                        class="w-48 sm:w-56 h-auto rounded-md"
                    />
                    <img 
                        :src="`${appUrl1}/build/assets/images/company/${logoEmpresa}_005.png`" 
                        alt="Logo" 
                        class="w-36 sm:w-40 h-auto rounded-md"
                    />
                </div>

                <h2 class="text-center text-2xl font-bold text-texto-claro mb-6">Recuperação de Senha</h2>

                <form @submit.prevent="submeterRecuperacao" class="flex flex-col gap-4">
                    
                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">Identificação/Nome Cadastrado<span class="dadorequerido">*</span></label>
                        <input v-model="recoverForm.unidentificacao" ref="campoNome" type="text" minlength="5" maxlength="130" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">CPF <span class="dadorequerido">*</span></label>
                            <input v-model="recoverForm.uncpf" @input="sistemajs.aplicarMascaraCPF($event.target.value, (val) => { recoverForm.uncpf = val; $event.target.value = val;})" type="text" maxlength="14" placeholder="000.000.000-00" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">Data de Nascimento <span class="dadorequerido">*</span></label>
                            <input v-model="recoverForm.undatanasc" type="date" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">E-mail (Este será seu Login no Sistema) <span class="dadorequerido">*</span></label>
                        <input
                            v-model="recoverForm.euemail"
                            type="email"
                            placeholder="nome@dominio.com"
                            :class="['w-full p-2.5 rounded-lg border bg-layout-fundo text-texto-claro text-sm transition-all focus:outline-none focus:ring-1',
                                (recoverForm.euemail && !sistemajs.validacaoEmail(recoverForm.euemail))
                                    ? 'dadoinvalido' 
                                    : 'border-comum focus:border-primary focus:ring-primary']"
                        />
                        <p v-if="recoverForm.euemail && !sistemajs.validacaoEmail(recoverForm.euemail)" class="text-xs dadoinvalido-texto mt-1 font-medium">
                            Por favor, insira um endereço de e-mail válido.
                        </p>
                    </div>

                    <div class="text-right text-xs font-medium dadorequerido">* Dados Obrigatórios</div>

                    <!--
                    <div class="text-center my-3">
                        <label class="text-sm text-texto-claro/80 cursor-pointer select-none leading-relaxed">
                            <input 
                                v-model="recoverForm.aceitatermos" 
                                type="checkbox" 
                                class="inline-block align-middle mr-2 w-4 h-4 rounded border-comum bg-layout-fundo text-primary focus:ring-0 focus:ring-offset-0 focus:border-primary checked:bg-primary checked:border-primary cursor-pointer transition-all duration-200"
                            />
                            <span class="align-middle">
                                Estou ciente e concordo com os 
                                <a :href="route('termos')" target="_self" class="text-primary hover:underline mx-1">
                                    Termos de Uso e a Política de Privacidade
                                </a> 
                                deste serviço.
                            </span>
                        </label>
                    </div>-->
                    
                    <div class="text-center my-2">
                        <label class="inline-flex items-center justify-center text-sm text-texto-claro/80 cursor-pointer select-none leading-relaxed align-middle">
                            <input 
                                type="checkbox" 
                                v-model="recoverForm.aceitatermos" 
                                class="sr-only peer"
                            />
                            
                            <div class="w-11 h-6 bg-comum/40 border border-comum peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-px after:inset-px after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary peer-checked:border-primary relative shrink-0"></div>
                            
                            <span class="ms-3 text-left">
                                Estou ciente e concordo com os <a :href="route('termos')" target="_blank" class="text-primary hover:underline mx-1">Termos de Uso e a Política de Privacidade</a> deste serviço.
                            </span>
                        </label>
                    </div>                    


                    <div class="flex justify-center my-1">
                        <Link :href="route('login')" class="text-primary hover:text-primary-hover font-medium flex items-center gap-2 text-sm transition-colors">
                            <i class="fas fa-info-circle text-base"></i> Já Tenho Cadastro
                        </Link>
                    </div>

                    <div class="flex flex-col items-center justify-center gap-2 py-2">
                        <div ref="recaptchaContainer"></div>
                        
                        <p v-if="erroRecaptcha" class="text-xs text-red-500 font-medium mt-1">
                            Por favor, faça verificação acima.
                        </p>
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