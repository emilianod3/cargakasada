<script setup>
// 🌟 Atualizado para utilizar o seu componente de layout correto
import Layout from '@/Layouts/PainelInterno.vue'; 
import { ref, onMounted, inject } from 'vue'; 
import { useForm, usePage } from '@inertiajs/vue3';
import * as sistemajs from '@/sistema.js';

const page = usePage();

// Chave Pública do reCAPTCHA injetada pelo Backend via Inertia Shared Props
const siteKey = page.props.NOCAPTCHA_SITEKEY; 
const limiteUpload = import.meta.env.VITE_SISTEMA_LIMITE_UPLOAD || 5;

const campoNome = ref(null);
const erroRecaptcha = ref(false);
const recaptchaContainer = ref(null);
let widgetId = null;

// --- FORMULÁRIO DE CONTATO (DADOS INTERNOS) ---
const contactForm = useForm({
    contact_name: page.props.auth?.user?.unico?.unidentificacao || '',   // Autocompila se o utilizador já estiver logado
    contact_email: page.props.auth?.user?.unico?.email?.euemail || '', // Autocompila se o utilizador já estiver logado
    contact_assunto: '',
    contact_message: '',
    grecaptcha: '',
});

onMounted(() => {
    if (campoNome.value && !contactForm.contact_name) {
        campoNome.value.focus();
    }
    
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
                    contactForm.grecaptcha = token;
                    erroRecaptcha.value = false;
                },
                'expired-callback': () => { contactForm.grecaptcha = ''; },
                'error-callback': () => { contactForm.grecaptcha = ''; }
            });
        }
    }, 50);
}

const submeterContato = () => {
    if (!contactForm.contact_name.trim() || !sistemajs.validacaoNomeCompleto(contactForm.contact_name)) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Por favor, informe seu Nome Completo.', tipo: 'warning', tempo: 4000 });
        campoNome.value?.focus();
        return;
    }

    if (!contactForm.contact_email.trim() || !sistemajs.validacaoEmail(contactForm.contact_email)) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Informe um endereço de e-mail válido.', tipo: 'warning', tempo: 4000 });
        return;
    }

    if (!contactForm.contact_assunto.trim() || sistemajs.strlen(contactForm.contact_assunto) < 5) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Por favor, preencha o assunto da mensagem.', tipo: 'warning', tempo: 4000 });
        return;
    }

    if (!contactForm.contact_message.trim() || sistemajs.strlen(contactForm.contact_message) < 10) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Sua mensagem deve conter no mínimo 10 caracteres.', tipo: 'warning', tempo: 4000 });
        return;
    }

    if (!contactForm.grecaptcha) {
        erroRecaptcha.value = true;
        sistemajs.mostrarPopup({ titulo: 'Verificação Obrigatória', conteudo: 'Por favor, faça a validação "Não sou um robô".', tipo: 'warning', tempo: 4000 });
        return;
    }

    contactForm.post(route('rest.formulariocontato'), {
        onError: (errors) => {   
            const rerro = errors.resultado;
            try {
                const djson = JSON.parse(rerro);
                sistemajs.mostrarPopup({ titulo: 'Falha no Envio', conteudo: djson.message, tipo: 'danger', tempo: 6000 });
            } catch (e) {
                sistemajs.mostrarPopup({ titulo: 'Falha no Envio', conteudo: 'Impossível processar os dados da mensagem.', tipo: 'danger', tempo: 6000 });
            }
            
            if (window.grecaptcha && widgetId !== null) {
                window.grecaptcha.reset(widgetId);
                contactForm.grecaptcha = '';
            }
        },
        onSuccess: () => {
            sistemajs.mostrarPopup({
                titulo: 'Mensagem Enviada',
                conteudo: 'Seu contato foi enviado com sucesso!', 
                tipo: 'success', 
                tempo: 5000 
            });
            //contactForm.reset('contact_assunto', 'contact_message', 'grecaptcha', 'files');
            /*if (window.grecaptcha && widgetId !== null) {
                window.grecaptcha.reset(widgetId);
            }*/
            contactForm.reset();
            setTimeout(() => {
                window.location.href = route('dashboard');
            }, 4000);             
        }
    });
};


</script>

<template>
    <Layout>
        <div class="p-6 max-w-7xl mx-auto flex flex-col gap-6">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-comum pb-4 gap-2">
                <div>
                    <h1 class="text-2xl font-bold text-texto-claro flex items-center gap-2">
                        <i class="fas fa-envelope text-primary"></i> Central de Ajuda & Suporte
                    </h1>
                    <p class="text-sm text-texto-claro/60 mt-1">Utilize o formulário abaixo para enviar a sua mensagem à equipe</p>
                </div>
            </div>

            <div class="bg-layout-painel border border-comum rounded-lg p-6 shadow-sm">
                
                <form @submit.prevent="submeterContato" class="flex flex-col gap-5">
                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-texto-claro/80">Nome Completo <span class="dadorequerido">*</span></label>
                            <input v-model="contactForm.contact_name" ref="campoNome" type="text" minlength="5" maxlength="130" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-medium text-texto-claro/80">Endereço de E-mail <span class="dadorequerido">*</span></label>
                            <input
                                v-model="contactForm.contact_email"
                                type="email"
                                placeholder=""
                                :class="['w-full p-2.5 rounded-lg border bg-layout-fundo text-texto-claro text-sm transition-all focus:outline-none focus:ring-1',
                                    (contactForm.contact_email && !sistemajs.validacaoEmail(contactForm.contact_email))
                                        ? 'dadoinvalido' 
                                        : 'border-comum focus:border-primary focus:ring-primary']"
                            />
                            <p v-if="contactForm.contact_email && !sistemajs.validacaoEmail(contactForm.contact_email)" class="text-xs dadoinvalido-texto mt-1 font-medium">
                                Por favor, insira um endereço de e-mail válido.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-texto-claro/80">Assunto<span class="dadorequerido">*</span></label>
                        <input v-model="contactForm.contact_assunto" type="text" maxlength="150" placeholder="" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="contact_message" class="text-sm font-medium text-texto-claro/80">Mensagem<span class="dadorequerido">*</span></label>
                        <textarea 
                            id="contact_message" name="contact_message" rows="5" maxlength="1500" placeholder="Escreva a sua mensagem em detalhe..."
                            v-model="contactForm.contact_message"
                            class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro placeholder-texto-claro/40 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all resize-y min-h-32"
                        ></textarea>
                        <span class="text-xs text-texto-claro/50 text-right">Informe detalhes.</span>
                    </div>

                    <div class="text-right text-xs font-medium dadorequerido">* Dados Obrigatórios</div>

                    <div class="flex flex-col items-start gap-1 my-2">
                        <div ref="recaptchaContainer"></div>
                        <p v-if="erroRecaptcha" class="text-xs text-red-500 font-bold mt-1">
                            A validação do reCAPTCHA é obrigatória.
                        </p>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-comum pt-4">
                        <button 
                            type="submit" 
                            :disabled="contactForm.processing"
                            class="bg-primary hover:bg-primary-hover disabled:opacity-50 text-texto-escuro font-bold py-2.5 px-6 rounded-lg text-sm transition-all cursor-pointer shadow-md flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i> 
                            {{ contactForm.processing ? 'A enviar...' : 'Enviar Mensagem' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </Layout>
</template>