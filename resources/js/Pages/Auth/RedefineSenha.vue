<script setup>
import Layout from '@/Layouts/LayoutAberto.vue'; 
import { ref, onMounted, inject } from 'vue'; 
import { useForm, Link, usePage } from '@inertiajs/vue3';
import * as sistemajs from '@/sistema.js';

// Injeta o valor provido pelo Layout padrão para resolver as imagens
const appUrl1 = inject('appUrl');
const page = usePage();

// Chave Pública e Token de Recuperação injetados pelo Backend via Inertia Props
const siteKey = page.props.NOCAPTCHA_SITEKEY; 
const recoveryToken = page.props.recoverytoken; // Captura o token enviado pelo controller

const campoSenha = ref(null);
const erroRecaptcha = ref(false);
const recaptchaContainer = ref(null);
const verSenha = ref(false); // Substitui o comportamento do 'btnMostrarSenha' do jQuery
let widgetId = null;

// Capturando logo do .env via Vite
const logoEmpresa = import.meta.env.VITE_COMPANY_LOGO || 'logo_text';

// Mensagens reativas para validação de complexidade em tempo real
const mensagemErroSenha = ref('');
const classeErroSenha = ref('text-danger');

// --- FORMULÁRIO DE NOVA SENHA ---
const newPasswordForm = useForm({
    senha: '',
    senha_confirmation: '',
    token: recoveryToken,
    aceitatermos: false,
    grecaptcha: '', 
});

onMounted(() => {
    // Foco automático no input de senha com leve timeout para estabilização do DOM
    setTimeout(() => {
        if (campoSenha.value) {
            campoSenha.value.focus();
        }
    }, 200);
    
    window.vRecaptchaLoaded = () => {
        inicializarRecaptcha();
    };

    if (window.grecaptcha && typeof window.grecaptcha.render === 'function') {
        inicializarRecaptcha();
    } else if (!document.getElementById('recaptcha-script')) {
        const script = document.createElement('script');
        script.id = 'recaptcha-script';
        script.src = 'https://www.google.com/recaptcha/api.js?render=explicit&onload=vRecaptchaLoaded';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }    
});

function inicializarRecaptcha() {
    setTimeout(() => {
        if (window.grecaptcha && typeof window.grecaptcha.render === 'function' && recaptchaContainer.value) {
            widgetId = window.grecaptcha.render(recaptchaContainer.value, {
                sitekey: siteKey,
                callback: (token) => {
                    newPasswordForm.grecaptcha = token;
                    erroRecaptcha.value = false;
                },
                'expired-callback': () => { newPasswordForm.grecaptcha = ''; },
                'error-callback': () => { newPasswordForm.grecaptcha = ''; }
            });
        }
    }, 50);
}

// Monitora a força da senha em tempo real (Substitui o $('#regsenha').keyup)
const avaliarForcaSenha = (valor) => {
    if (!valor) {
        mensagemErroSenha.value = '';
        return;
    }
    const forca = sistemajs.calcularForcaSenha(valor);
    const textoComplexidade = sistemajs.infoComplexidade(forca);

    if (forca < 30) {
        mensagemErroSenha.value = textoComplexidade;
        classeErroSenha.value = 'text-red-500';
    } else if (forca >= 30 && forca < 60) {
        mensagemErroSenha.value = textoComplexidade;
        classeErroSenha.value = 'text-amber-500';
    } else if (forca >= 60 && forca < 85) {
        mensagemErroSenha.value = textoComplexidade;
        classeErroSenha.value = 'text-blue-500';
    } else {
        mensagemErroSenha.value = textoComplexidade;
        classeErroSenha.value = 'text-green-500';
    }
};

// Gera a senha aleatória (Substitui o $('.btnGerarSenha').on('click'))
const acionarGeradorSenha = () => {
    const senhaGerada = sistemajs.gerarSenha(9);
    newPasswordForm.senha = senhaGerada;
    newPasswordForm.senha_confirmation = senhaGerada;
    mensagemErroSenha.value = '';
};

const submeterNovaSenha = () => {
    // Validações de Regra de Negócio vindas do seu legado
    if (!newPasswordForm.token || sistemajs.strlen(newPasswordForm.token) <= 15) {
        sistemajs.mostrarPopup({ titulo: 'Falha no Processamento', conteudo: 'Dados Inválidos ou Token já Expirado.', tipo: 'danger', tempo: 6000 });
        return;
    }

    if (sistemajs.strlen(newPasswordForm.senha) <= 5) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Informe uma Senha Válida (Mínimo 6 caracteres).', tipo: 'warning', tempo: 4000 });
        campoSenha.value?.focus();
        return;
    }

    if (newPasswordForm.senha !== newPasswordForm.senha_confirmation) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'As senhas digitadas não correspondem.', tipo: 'warning', tempo: 4000 });
        return;
    }

    if (!newPasswordForm.aceitatermos) {
        sistemajs.mostrarPopup({ titulo: 'Termos de Uso', conteudo: 'É Necessário Aceitar os Termos para Prosseguir.', tipo: 'warning', tempo: 5000 });
        return;
    }
    
    if (!newPasswordForm.grecaptcha) {
        erroRecaptcha.value = true;
        sistemajs.mostrarPopup({ titulo: 'Verificação Obrigatória', conteudo: 'Necessário Validar o reCAPTCHA.', tipo: 'warning', tempo: 4000 });
        return;
    }

    // Envio via Inertia para a rota do Laravel
    newPasswordForm.post(route('envio.nova.senha'), {
        onError: (errors) => { 
            const rerro = errors.resultado;
            try {
                const djson = JSON.parse(rerro);
                sistemajs.mostrarPopup({ titulo: 'Falha no Processamento', conteudo: djson.message, tipo: 'danger', tempo: 6000 });
            } catch (e) {
                sistemajs.mostrarPopup({ titulo: 'Falha no Processamento', conteudo: 'Verifique os Requisitos de Senha.', tipo: 'danger', tempo: 6000 });
            }
            
            newPasswordForm.reset('senha', 'senha_confirmation', 'grecaptcha');
            if (window.grecaptcha && widgetId !== null) {
                window.grecaptcha.reset(widgetId);
                newPasswordForm.grecaptcha = '';
            }
            campoSenha.value?.focus();
        },
        onSuccess: () => {
            const rsucesso = usePage().props.flash?.resultado;
            try {
                const djson = JSON.parse(rsucesso);
                sistemajs.mostrarPopup({ titulo: 'Recuperação de Acesso', conteudo: djson.message || 'Registro de Nova Senha Realizado com Sucesso.', tipo: 'success', tempo: 10000 });
            } catch (e) {
                sistemajs.mostrarPopup({ titulo: 'Recuperação de Acesso', conteudo: 'Registro de Nova Senha Realizado com Sucesso. Redirecionando...', tipo: 'success', tempo: 10000 });
            }

            newPasswordForm.reset();
            newPasswordForm.aceitatermos = false;   
            
            setTimeout(() => {
                window.location.href = route('login');
            }, 5000);
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

                <h2 class="text-center text-2xl font-bold text-texto-claro mb-2">Recuperação de Senha</h2>
                <h5 class="text-center text-sm font-medium text-texto-claro/70 mb-6">Informe a Nova Senha</h5>

                <form @submit.prevent="submeterNovaSenha" class="flex flex-col gap-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">Senha de Acesso <span class="dadorequerido">*</span></label>
                            <div class="relative w-full flex items-center">
                                <input 
                                    v-model="newPasswordForm.senha" 
                                    @keyup="avaliarForcaSenha($event.target.value)"
                                    ref="campoSenha"
                                    :type="verSenha ? 'text' : 'password'" 
                                    class="w-full p-2.5 pr-10 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" 
                                />
                                <button 
                                    type="button" 
                                    @click="verSenha = !verSenha"
                                    class="absolute right-3 text-texto-claro/60 hover:text-primary transition-colors text-base cursor-pointer focus:outline-none"
                                >
                                    <i :class="verSenha ? 'fas fa-unlock' : 'fas fa-lock'"></i>
                                </button>
                            </div>
                            <small :class="['text-xs mt-1 transition-all', classeErroSenha]">
                                {{ mensagemErroSenha }}
                            </small> 
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">Confirme a Senha <span class="dadorequerido">*</span></label>
                            <div class="relative w-full flex items-center">
                                <input 
                                    v-model="newPasswordForm.senha_confirmation" 
                                    :type="verSenha ? 'text' : 'password'" 
                                    class="w-full p-2.5 pr-10 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" 
                                />
                                <button 
                                    type="button" 
                                    @click="verSenha = !verSenha"
                                    class="absolute right-3 text-texto-claro/60 hover:text-primary transition-colors text-base cursor-pointer focus:outline-none"
                                >
                                    <i :class="verSenha ? 'fas fa-unlock' : 'fas fa-lock'"></i>
                                </button>
                            </div>
                            <small v-if="newPasswordForm.senha_confirmation && newPasswordForm.senha !== newPasswordForm.senha_confirmation" class="text-xs text-red-500 mt-1">
                                Senhas Não Correspondem
                            </small>
                        </div>
                    </div>

                    <div class="flex justify-center my-2">
                        <button 
                            type="button" 
                            @click="acionarGeradorSenha" 
                            class="w-full bg-primary hover:bg-primary-hover disabled:opacity-50 text-texto-escuro font-bold py-2.5 rounded-full text-sm transition-all cursor-pointer shadow-lg">
                            <i class="fas fa-sync"></i> Gerar Senha                          
                        </button>
                    </div> 

                    <div class="text-right text-xs font-medium dadorequerido">* Dados Obrigatórios</div>

                    <div class="text-center my-2">
                        <label class="text-sm text-texto-claro/80 cursor-pointer select-none leading-relaxed">
                            <input 
                                v-model="newPasswordForm.aceitatermos" 
                                type="checkbox" 
                                class="inline-block align-middle mr-2 w-4 h-4 rounded border-comum bg-layout-fundo text-primary focus:ring-0 focus:ring-offset-0 focus:border-primary checked:bg-primary checked:border-primary cursor-pointer transition-all duration-200"
                            />
                            <span class="align-middle">Estou ciente e concordo com os <a :href="route('termos')" target="_blank" class="text-primary hover:underline mx-1">Termos de Uso e a Política de Privacidade </a>deste serviço.</span>
                        </label>
                    </div>                     

                    <div class="flex justify-center my-1">
                        <Link :href="route('login')" class="text-primary hover:text-primary-hover font-medium flex items-center gap-2 text-sm transition-colors">
                            <i class="fas fa-info-circle text-base"></i> Já Tenho Cadastro
                        </Link>
                    </div>

                    <div class="flex flex-col items-center justify-center gap-2 py-2 cursor-pointer">
                        <div ref="recaptchaContainer" class="cursor-pointer"></div>
                        <p v-if="erroRecaptcha" class="text-xs text-red-500 font-medium mt-1">
                            Por favor, faça verificação acima.
                        </p>
                    </div>                    

                    <div class="flex items-center justify-between border-t border-comum/30 pt-4 mt-2">
                        <Link :href="route('login')" class="flex items-center gap-2 bg-gray-500/20 hover:bg-gray-500/30 text-texto-claro font-semibold px-5 py-2 rounded-lg text-sm transition-all">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </Link>

                        <button 
                            type="submit" 
                            :disabled="newPasswordForm.processing"
                            class="flex items-center gap-2 bg-primary hover:bg-primary-hover disabled:opacity-50 text-texto-escuro font-bold px-6 py-2 rounded-lg text-sm transition-all shadow-lg cursor-pointer"
                        >
                            <i class="fas fa-check"></i> {{ newPasswordForm.processing ? 'Processando...' : 'Enviar' }}
                        </button>
                    </div>

                </form>
            </div>
        </section>
    </Layout>
</template>