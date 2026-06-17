<script setup>
import Layout from '@/Layouts/LayoutAberto.vue'; 
import { ref, onMounted, inject } from 'vue'; 
import { useForm, Link, usePage } from '@inertiajs/vue3';
import * as sistemajs from '@/sistema.js';


// Injeta o valor provido pelo Layout padrão para resolver as imagens
const appUrl1 = inject('appUrl');
const page = usePage();

// 🌟 Chave Pública injetada pelo Backend via Inertia Shared Props
const siteKey = page.props.NOCAPTCHA_SITEKEY; 
const limiteUpload = page.props.SISTEMA_LIMITE_UPLOAD; 

const campoNome = ref(null);
const campoSenha = ref(null);
const erroRecaptcha = ref(false);
const recaptchaContainer = ref(null);
const verSenha = ref(false); 
let widgetId = null;
const limpandoArrastar = ref(false);

// Capturando logo do .env via Vite
const logoEmpresa = import.meta.env.VITE_COMPANY_LOGO || 'logo_text';

// Mensagens reativas para validação de complexidade em tempo real (Igual ao seu exemplo)
const mensagemErroSenha = ref('');
const classeErroSenha = ref('text-danger');

// --- FORMULÁRIO DE NOVO CADASTRO ---
const registerForm = useForm({
    unidentificacao: '',
    unapelido: '',
    uncpf: '',
    unrg: '',
    fnnumero: '',
    undatanasc: '',
    euemail: '',
    senha: '',             // Vinculado ao tratamento de senha do exemplo
    unsenha_confirmation: '',// Vinculado ao tratamento de senha do exemplo
    aceitatermos: false,
    tipopessoa: 1,
    grecaptcha: '',          // Receberá o token gerado pelo Google
    // --- Dados Complementares / Endereço ---
    unsexo: 'S',               // Sexo (Inicia marcado em 'Prefiro não dizer')
    uncep: '',                 // CEP (Corrigido para evitar conflito com telefone)
    fkidcidade: '',            // Cidade
    unidadefederativa: '0',   // Estado (Inicia selecionado em 'São Paulo-SP')
    unendereco: '',            // Endereço (Textarea)
    unnumero: '',              // Número residencial
    unbairro: '',              // Bairro
    uncomplemento: '',        // Complemento do Endereço  
    files: [],            // Armazenará os arquivos selecionados 
});



// Foco automático e carregamento seguro do reCAPTCHA
onMounted(() => {
    if (campoNome.value) {
        campoNome.value.focus();
    }

    console.log(limiteUpload)
    
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
                    registerForm.grecaptcha = token;
                    erroRecaptcha.value = false;
                },
                'expired-callback': () => { registerForm.grecaptcha = ''; },
                'error-callback': () => { registerForm.grecaptcha = ''; }
            });
        }
    }, 50);
}

// Monitora a força da senha em tempo real (Igual ao seu exemplo)
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

// Gera a senha aleatória (Igual ao seu exemplo, adaptado para registerForm)
const acionarGeradorSenha = () => {
    const senhaGerada = sistemajs.gerarSenha(9);
    registerForm.senha = senhaGerada;
    registerForm.unsenha_confirmation = senhaGerada;
    mensagemErroSenha.value = '';
};

const submeterCadastro = () => {
    // Validações do formulário de Cadastro
    if (!registerForm.unidentificacao.trim() || sistemajs.strlen(registerForm.unidentificacao) < 5) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Por favor, informe seu Nome/Identificação cadastrado Válido.', tipo: 'warning', tempo: 4000 });
        campoNome.value.focus();
        return;
    }
    if (!sistemajs.temMaisDe18Anos(registerForm.undatanasc)) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Informe a Data de Nascimento Válida.', tipo: 'warning', tempo: 4000 });
        return;
    }    
    if (!registerForm.uncpf.trim() || !sistemajs.TestaCPF(registerForm.uncpf)) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Informe o CPF Válido.', tipo: 'warning', tempo: 4000 });
        document.getElementById('uncpf')?.focus();
        return;
    }
    
    if (!registerForm.unrg.trim() || !sistemajs.TestaRG(registerForm.unrg)) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Informe o RG Válido.', tipo: 'warning', tempo: 4000 });
        return;
    }

    if (!registerForm.fnnumero.trim() || sistemajs.strlen(registerForm.fnnumero) < 13) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Por favor, informe seu Telefone.', tipo: 'warning', tempo: 4000 });
        return;
    }    

    if (!registerForm.euemail.trim() || !sistemajs.validacaoEmail(registerForm.euemail)) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Informe o e-mail de cadastro Válido.', tipo: 'warning', tempo: 4000 });
        return;
    }
    
    // Validações da Senha extraídas do comportamento padrão
    if (sistemajs.strlen(registerForm.senha) <= 5) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Informe uma Senha Válida (Mínimo 6 caracteres).', tipo: 'warning', tempo: 4000 });
        campoSenha.value?.focus();
        return;
    }
    if (registerForm.senha !== registerForm.unsenha_confirmation) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'As senhas digitadas não correspondem.', tipo: 'warning', tempo: 4000 });
        return;
    }

    if (!registerForm.aceitatermos) {
        sistemajs.mostrarPopup({ titulo: 'Termos de Uso', conteudo: 'Você precisa aceitar os Termos de Uso e a Política de Privacidade para prosseguir.', tipo: 'warning', tempo: 5000 });
        return;
    }

    if (registerForm.unidadefederativa <= 0) {
        sistemajs.mostrarPopup({ titulo: 'Aviso', conteudo: 'Selecionado o Estado.', tipo: 'warning', tempo: 4000 });
        return;
    }

    if (!registerForm.grecaptcha) {
        erroRecaptcha.value = true;
        sistemajs.mostrarPopup({ titulo: 'Verificação Obrigatória', conteudo: 'Por favor, marque a caixa "Não sou um robô".', tipo: 'warning', tempo: 4000 });
        return;
    }

    // Envio oficial via Inertia para a rota do Laravel de Cadastro
    registerForm.post(route('registrar.novo.usuario'), {
        onError: (errors) => {   
            const rerro = errors.resultado;
            try {
                const djson = JSON.parse(rerro);
                sistemajs.mostrarPopup({ titulo: 'Falha no Cadastro', conteudo: djson.message, tipo: 'danger', tempo: 6000 });
            } catch (e) {
                sistemajs.mostrarPopup({ titulo: 'Falha no Cadastro', conteudo: 'Impossível Processar dados.', tipo: 'danger', tempo: 6000 });
            }
            
            registerForm.reset('senha', 'unsenha_confirmation', 'grecaptcha');
            mensagemErroSenha.value = '';
            
            if (window.grecaptcha && widgetId !== null) {
                window.grecaptcha.reset(widgetId);
                registerForm.grecaptcha = '';
            }
            registerForm.reset();
            registerForm.aceitatermos = false;            
            campoNome.value?.focus();
        },
        onSuccess: () => {
            sistemajs.mostrarPopup({ 
                titulo: 'Cadastro Realizado', 
                conteudo: 'Sua conta foi criada com sucesso! Redirecionando...', 
                tipo: 'success', 
                tempo: 4000 
            });
            registerForm.reset();
            setTimeout(() => {
                window.location.href = route('login');
            }, 4000);            
        }
    });
};



const erroArquivo = ref(''); // Armazena mensagens de validação dos arquivos

const tratarUploadArquivos = (listaArquivos) => {
    erroArquivo.value = ''; // Reseta erros anteriores
    
    if (!listaArquivos || listaArquivos.length === 0) return;

    // 1. Validação de Quantidade Máxima (Máximo 10 arquivos)
    if (listaArquivos.length > 10) {
        erroArquivo.value = 'Você pode enviar no máximo 10 arquivos por vez.';
        registerForm.files = []; // Limpa o formulário por segurança
        return;
    }

    // 2. Validação de Tamanho Máximo (5 MB por arquivo = 5 * 1024 * 1024 bytes)
    const limiteTamanho = page.props.SISTEMA_LIMITE_UPLOAD * 1024 * 1024; 
    const arquivosArray = Array.from(listaArquivos);

    const temArquivoMuitoGrande = arquivosArray.some(arquivo => arquivo.size > limiteTamanho);

    if (temArquivoMuitoGrande) {
        erroArquivo.value = 'Cada arquivo enviado deve ter no máximo 5 MB.';
        registerForm.files = []; // Limpa o formulário por segurança
        return;
    }

    // Se passou em todas as validações, salva os arquivos com sucesso no useForm
    registerForm.files = listaArquivos;
};

</script>

<template>
    <Layout>
        <section class="min-h-screen flex items-center justify-center bg-cover bg-center transition-colors duration-300 py-10">
            
            <div class="w-full max-w-[90%] bg-layout-painel border border-comum shadow-2xl rounded-lg p-8 transition-all duration-300">
                
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

                <h2 class="text-center text-2xl font-bold text-texto-claro mb-2">Criar Nova Conta</h2>
                <h5 class="text-center text-sm font-medium text-texto-claro/70 mb-6">Preencha os dados abaixo para se cadastrar</h5>

                <form @submit.prevent="submeterCadastro" class="flex flex-col gap-4">
                    
                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">Nome Completo <span class="dadorequerido">*</span></label>
                        <input v-model="registerForm.unidentificacao" ref="campoNome" type="text" minlength="5" maxlength="130" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label class="text-sm text-texto-claro/80 font-medium">Nome Social</label>
                            <input v-model="registerForm.unapelido" ref="unapelido" type="text"  maxlength="130" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div> 
                        
                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">Data de Nascimento <span class="dadorequerido">*</span></label>
                            <input v-model="registerForm.undatanasc" type="date" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">CPF <span class="dadorequerido">*</span></label>
                            <input v-model="registerForm.uncpf" id="uncpf" @input="sistemajs.aplicarMascaraCPF($event.target.value, (val) => { registerForm.uncpf = val; $event.target.value = val;})" type="text" maxlength="14" placeholder="000.000.000-00" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div>

                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">RG <span class="dadorequerido">*</span></label>
                            <input v-model="registerForm.unrg" id="unrg" style="text-transform: uppercase;" @input="sistemajs.aplicarMascaraRG($event.target.value, (val) => { registerForm.unrg = val; $event.target.value = val;})" type="text" maxlength="14" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div>                        

                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">Telefone <span class="dadorequerido">*</span></label>
                            <input v-model="registerForm.fnnumero" id="uncpf" @input="sistemajs.aplicarMascaraTelefone($event.target.value, (val) => { registerForm.fnnumero = val; $event.target.value = val;})" type="text" maxlength="15" placeholder="(XX) XXXX-XXXX" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">E-mail (Este será seu Login no Sistema) <span class="dadorequerido">*</span></label>
                        <input
                            v-model="registerForm.euemail"
                            type="email"
                            placeholder="nome@dominio.com"
                            :class="['w-full p-2.5 rounded-lg border bg-layout-fundo text-texto-claro text-sm transition-all focus:outline-none focus:ring-1',
                                (registerForm.euemail && !sistemajs.validacaoEmail(registerForm.euemail))
                                    ? 'dadoinvalido' 
                                    : 'border-comum focus:border-primary focus:ring-primary']"
                        />
                        <p v-if="registerForm.euemail && !sistemajs.validacaoEmail(registerForm.euemail)" class="text-xs dadoinvalido-texto mt-1 font-medium">
                            Por favor, insira um endereço de e-mail válido.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">Senha de Acesso <span class="dadorequerido">*</span></label>
                            <div class="relative w-full flex items-center">
                                <input 
                                    v-model="registerForm.senha" 
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
                                    v-model="registerForm.unsenha_confirmation" 
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
                            <small v-if="registerForm.unsenha_confirmation && registerForm.senha !== registerForm.unsenha_confirmation" class="text-xs text-red-500 mt-1">
                                Senhas Não Correspondem
                            </small>
                        </div>
                    </div>

                    <div class="flex justify-center my-2">
                        <button 
                            type="button" 
                            @click="acionarGeradorSenha" 
                            class="bg-primary hover:bg-primary-hover disabled:opacity-50 text-texto-escuro font-bold py-2 px-6 rounded-full text-sm transition-all cursor-pointer shadow-lg">
                            <i class="fas fa-sync pr-2"></i> Gerar Senha
                        </button>
                    </div> 

                    <div class="text-right text-xs font-medium dadorequerido">* Dados Obrigatórios</div>

                    <div class="text-center my-2">
                        <label class="inline-flex items-center justify-center text-sm text-texto-claro/80 cursor-pointer select-none leading-relaxed align-middle">
                            <input 
                                type="checkbox" 
                                v-model="registerForm.aceitatermos" 
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


                    <h2 class="text-center text-2xl font-bold text-texto-claro mb-2 mt-8 border-b border-comum pb-3">Dados Complementares</h2> 
                    
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1 md:col-span-2 pt-1 items-center">
                            <label class="text-sm text-texto-claro/80 font-medium">Sexo</label>
                            <div class="flex flex-col sm:flex-row gap-4 pt-2">
                                <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                    <div class="relative flex items-center justify-center">
                                        <input 
                                            type="radio" 
                                            name="unsexo" 
                                            value="M" 
                                            v-model="registerForm.unsexo" 
                                            class="sr-only peer" 
                                        />
                                        <div class="w-5 h-5 rounded-full border border-comum bg-layout-fundo peer-focus:ring-2 peer-focus:ring-primary/30 transition-all duration-200 flex items-center justify-center peer-checked:border-primary peer-checked:bg-primary">
                                            <div class="w-2.5 h-2.5 rounded-full bg-white opacity-0 transition-all duration-200 peer-checked:opacity-100" />
                                        </div>
                                    </div>
                                    <span class="text-sm text-texto-claro/90">Masculino</span>
                                </label>

                                <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                    <div class="relative flex items-center justify-center">
                                        <input 
                                            type="radio" 
                                            name="unsexo" 
                                            value="F" 
                                            v-model="registerForm.unsexo" 
                                            class="sr-only peer" 
                                        />
                                        <div class="w-5 h-5 rounded-full border border-comum bg-layout-fundo peer-focus:ring-2 peer-focus:ring-primary/30 transition-all duration-200 flex items-center justify-center peer-checked:border-primary peer-checked:bg-primary">
                                            <div class="w-2.5 h-2.5 rounded-full bg-white opacity-0 transition-all duration-200 peer-checked:opacity-100" />
                                        </div>
                                    </div>
                                    <span class="text-sm text-texto-claro/90">Feminino</span>
                                </label>

                                <label class="inline-flex items-center gap-3 cursor-pointer select-none">
                                    <div class="relative flex items-center justify-center">
                                        <input 
                                            type="radio" 
                                            name="unsexo" 
                                            value="S" 
                                            v-model="registerForm.unsexo" 
                                            checked
                                            class="sr-only peer" 
                                        />
                                        <div class="w-5 h-5 rounded-full border border-comum bg-layout-fundo peer-focus:ring-2 peer-focus:ring-primary/30 transition-all duration-200 flex items-center justify-center peer-checked:border-primary peer-checked:bg-primary">
                                            <div class="w-2.5 h-2.5 rounded-full bg-white opacity-0 transition-all duration-200 peer-checked:opacity-100" />
                                        </div>
                                    </div>
                                    <span class="text-sm text-texto-claro/90">Prefiro não dizer</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">Informe o CEP</label>
                            <input v-model="registerForm.uncep" id="uncep" @input="sistemajs.aplicarMascaraCEP($event.target.value, (val) => { registerForm.uncep = val; $event.target.value = val;})" type="text" maxlength="10" placeholder="XX.XXX-XXX" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label class="text-sm text-texto-claro/80 font-medium">Cidade</label>
                            <input v-model="registerForm.fkidcidade" ref="fkidcidade" type="text" minlength="5" maxlength="130" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div> 
                        
                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">Estado<span class="dadorequerido">*</span></label>
                            <div class="relative w-full">
                                    <select id="unidadefederativa" name="unidadefederativa" v-model="registerForm.unidadefederativa"
                                        class="w-full p-2.5 pr-10 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all appearance-none cursor-pointer">
                                        <option value="0" disabled>Selecione um Estado</option>
                                        <option value="1">Acre-AC</option>
                                        <option value="2">Alagoas</option>
                                        <option value="3">Amazonas-AM</option>
                                        <option value="4">Amapá-AP</option>
                                        <option value="5">Bahia-BA</option>
                                        <option value="6">Ceará-CE</option>
                                        <option value="7">Distrito Federal-DF</option>
                                        <option value="8">Espírito Santo-ES</option>
                                        <option value="9">Goiás-GO</option>
                                        <option value="10">Maranhão-MA</option>
                                        <option value="11">Minas Gerais-MG</option>
                                        <option value="12">Mato Grosso do Sul-MS</option>
                                        <option value="13">Mato Grosso-MT</option>
                                        <option value="14">Pará-PA</option>
                                        <option value="15">Paraíba-PB</option>
                                        <option value="16">Pernambuco-PE</option>
                                        <option value="17">Piauí-PI</option>
                                        <option value="18">Paraná-PR</option>
                                        <option value="19">Rio de Janeiro-RJ</option>
                                        <option value="20">Rio Grande do Norte-RN</option>
                                        <option value="21">Rondônia-RO</option>
                                        <option value="22">Roraima-RR</option>
                                        <option value="23">Rio Grande do Sul-RS</option>
                                        <option value="24">Santa Catarina-SC</option>
                                        <option value="25">Sergipe-SE</option>
                                        <option value="26">São Paulo-SP</option>
                                        <option value="27">Tocantins-TO</option> 
                                    </select>
                                    
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-texto-claro/50">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>                   
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5 my-4">
                        <label for="unendereco" class="text-sm text-texto-claro/80 font-medium">Endereço</label>
                        <textarea 
                            id="unendereco" name="unendereco" rows="2" maxlength="300" placeholder=""
                            v-model="registerForm.unendereco"
                            class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro placeholder-texto-claro/40 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all resize-y min-h-20"
                        ></textarea>
                        <span class="text-xs text-texto-claro/50 text-right">Máximo de 300 caracteres.</span>
                    </div>                    

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm text-texto-claro/80 font-medium">Número</label>
                            <input v-model="registerForm.unnumero" ref="unnumero" type="text" maxlength="90" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div>                        
                        <div class="flex flex-col gap-1 md:col-span-2">
                            <label class="text-sm text-texto-claro/80 font-medium">Bairro</label>
                            <input v-model="registerForm.unbairro" ref="unbairro" type="text" minlength="5" maxlength="200" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                        </div> 
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">Complemento</label>
                        <input v-model="registerForm.uncomplemento" ref="uncomplemento" type="text" minlength="5" maxlength="200" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                    </div>


                    <!--
                    <div class="my-6 border-t border-comum/30 pt-4">
                        <h3 class="text-sm font-semibold text-texto-claro/80 mb-3">Anexos</h3>
                        
                        <div class="bg-layout-painel border border-comum rounded-lg p-4 flex flex-col sm:flex-row items-center gap-4">
                            <button 
                                type="button" 
                                @click="$refs.inputArquivo.click()" 
                                class="flex items-center justify-center w-12 h-12 rounded-full bg-primary hover:bg-primary-hover text-texto-escuro shadow-lg transition-all cursor-pointer text-xl focus:outline-none shrink-0"
                                title="Clique para selecionar arquivos para Upload"
                            >
                                <i class="fas fa-cloud-upload-alt"></i>
                            </button>

                            <div class="flex flex-col text-center sm:text-left overflow-hidden w-full">
                                <span class="text-sm font-medium text-texto-claro">
                                    {{ registerForm.arquivos.length > 0 ? `${registerForm.arquivos.length} arquivo(s) selecionado(s)` : 'Nenhum arquivo selecionado' }}
                                </span>
                                <span class="text-xs text-texto-claro/50 truncate">
                                    {{ registerForm.arquivos.length > 0 ? Array.from(registerForm.arquivos).map(f => f.name).join(', ') : 'Formatos aceitos: PDF, PNG, JPEG, JPG, BMP, TIFF' }}
                                </span>
                            </div>

                            <input 
                                type="file" 
                                id="inputuploadarquivo" 
                                ref="inputArquivo"
                                multiple
                                accept="image/png, image/jpeg, image/jpg, image/bmp, image/tiff, application/pdf" 
                                class="sr-only" 
                                @change="registerForm.arquivos = $event.target.files"
                            />
                        </div>
                    </div>-->

                    <div class="my-6 border-t border-comum/30 pt-4 border-comum">
                        <h3 class="text-sm font-semibold text-texto-claro/80 mb-3">Anexos</h3>
                        
                        <div 
                            @dragover.prevent="limpandoArrastar = true"
                            @dragleave="limpandoArrastar = false"
                            @drop.prevent="tratarUploadArquivos($event.dataTransfer.files); limpandoArrastar = false"
                            @click="$refs.inputArquivo.click()" 
                            :class="[
                                'border-2 border-dashed rounded-lg p-6 flex flex-col items-center justify-center gap-3 transition-all cursor-pointer select-none text-center',
                                erroArquivo 
                                    ? 'border-red-500 bg-red-500/5 hover:border-red-500/80' 
                                    : limpandoArrastar 
                                        ? 'border-primary bg-primary/10 scale-[1.01]' 
                                        : 'border-comum bg-layout-painel hover:border-primary/50'
                            ]">
                            <div 
                                :class="[
                                    'flex items-center justify-center w-12 h-12 rounded-full bg-primary hover:bg-primary-hover text-texto-escuro shadow-lg transition-all cursor-pointer text-xl focus:outline-none shrink-0',   
                                    erroArquivo
                                        ? 'bg-red-500/20 text-red-500'
                                        : limpandoArrastar 
                                            ? 'bg-primary text-texto-escuro animate-bounce' 
                                            : 'bg-comum/20 text-texto-claro/70'
                                ]">
                                <i :class="erroArquivo ? 'fas fa-exclamation-triangle' : 'fas fa-cloud-upload-alt'"></i>
                            </div>

                            <div class="flex flex-col w-full overflow-hidden">
                                <span class="text-sm font-semibold text-texto-claro">
                                    {{ registerForm.files.length > 0 ? `${registerForm.files.length} arquivo(s) selecionado(s)` : 'Arrastar e soltar arquivos aqui' }}
                                </span>
                                <span class="text-xs text-texto-claro/60 mt-0.5">
                                    {{ registerForm.files.length > 0 ? Array.from(registerForm.files).map(f => f.name).join(', ') : 'ou clique para procurar no seu dispositivo' }}
                                </span>
                                <span class="text-[11px] text-texto-claro/40 mt-2">
                                    Limites: Máximo de 10 arquivos de até {{ limiteUpload }} MB cada. Formatos: PDF, Imagens (PNG, JPEG, etc)
                                </span>
                            </div>

                            <input 
                                type="file" 
                                id="inputuploadarquivo" 
                                ref="inputArquivo"
                                multiple
                                accept="image/png, image/jpeg, image/jpg, image/bmp, image/tiff, application/pdf" 
                                class="sr-only" 
                                @change="tratarUploadArquivos($event.target.files)"
                            />
                        </div>

                        <p v-if="erroArquivo" class="text-xs text-red-500 font-medium mt-1.5 flex items-center gap-1.5 animate-pulse">
                            <i class="fas fa-info-circle"></i> {{ erroArquivo }}
                        </p>
                    </div>





                    <div class="flex flex-col items-center justify-center gap-2 py-2 cursor-pointer">
                        <div ref="recaptchaContainer" class="cursor-pointer"></div>
                        <p v-if="erroRecaptcha" class="text-xs text-red-500 font-medium mt-1">
                            Por favor, faça verificação acima.
                        </p>
                    </div>                    

                    <div class="flex items-center justify-between border-t border-comum/30 pt-4 mt-2 ">
                        <Link :href="route('login')" class="flex items-center gap-2 bg-gray-500/20 hover:bg-gray-500/30 text-texto-claro font-semibold px-5 py-2 rounded-lg text-sm transition-all">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </Link>

                        <button 
                            type="submit" 
                            :disabled="registerForm.processing"
                            class="flex items-center gap-2 bg-primary hover:bg-primary-hover disabled:opacity-50 text-texto-escuro font-bold px-6 py-2 rounded-lg text-sm transition-all shadow-lg cursor-pointer"
                        >
                            <i class="fas fa-user-plus"></i> {{ registerForm.processing ? 'Processando...' : 'Cadastrar' }}
                        </button>
                    </div>

                </form>
            </div>
        </section>
    </Layout>
</template>