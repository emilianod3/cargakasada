import './bootstrap';
import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js';
//import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import FloatingVue from 'floating-vue';
import 'floating-vue/dist/style.css';

import '../css/app.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
//import { varloading, mostrarPopup, mostrarPopupDecisao } from '@/sistema.js';
import * as sistemajs from '@/sistema.js';
import axios from 'axios';






// Ativa a cortina quando qualquer requisição para o controller iniciar
router.on('start', () => {
    sistemajs.varloading.value = true;
});

// Desativa a cortina quando o controller responder (sucesso ou erro)
router.on('finish', () => {
    sistemajs.varloading.value = false;
});



// 1. Interceptador de INÍCIO (Antes de enviar a requisição)
axios.interceptors.request.use((config) => {
    sistemajs.varloading.value = true;
    return config;
}, (error) => {
    // Se der erro antes mesmo de enviar, desliga a cortina
    sistemajs.varloading.value = false;
    return Promise.reject(error);
});

// 2. Interceptador de FIM (Quando recebe a resposta do servidor)
axios.interceptors.response.use((response) => {
    sistemajs.varloading.value = false;
    return response;
}, (error) => {
    sistemajs.varloading.value = false;
    return Promise.reject(error);
});


axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (!error.response) return Promise.reject(error);

        const status = error.response.status;
        const urlAtual = window.location.href;

        if (status === 419 || status === 401) {
            /*axios.post('/log-frontend-erro', { status: status, url: urlAtual })
                .catch(err => console.error('Falha ao registrar log de sessão', err));*/

            sistemajs.mostrarPopup({
                titulo: 'Sessão Expirada',
                conteudo: 'Seu tempo de sessão acabou. Você será redirecionado para o login...',
                tipo: 'warning',
                tempo: 4000
            });

            setTimeout(() => {
                //window.location.href = typeof router !== 'undefined' ? router('login') : '/login';
                window.location.href = router('login');
            }, 4000);

            return new Promise(() => {}); 
        }

        if (status === 500) {
            /*axios.post('/log-frontend-erro', { status: status, url: urlAtual })
                .catch(err => console.error('Falha ao registrar log de erro 500', err));*/

            sistemajs.mostrarPopup({
                titulo: 'Estamos Enfrentando Instabilidade',
                conteudo: 'Aguarde! O sistema fará a tentativa de recuperação.',
                tipo: 'danger',
                tempo: 6000
            });

            setTimeout(() => {
                //window.location.href = typeof route !== 'undefined' ? route('login') : '/login';
                window.location.href = router('login');
            }, 6000);

            return new Promise(() => {});
        }

        return Promise.reject(error);
    }
);


// Intercepta respostas inválidas do servidor (como expiração de sessão)
/*
router.on('invalid', (event) => {
    const status = event.detail.response.status;

    // 419 = Token/Sessão expirou no Laravel
    // 401 = Usuário perdeu a autenticação
    if (status === 419 || status === 401 ) {
        // 1. Evita que a tela cinza padrão de erro do Laravel apareça
        event.preventDefault(); 

        // 2. Dispara o seu popup reativo na tela
        mostrarPopup({
            titulo: 'Sessão Expirada',
            conteudo: 'Seu tempo de Sessão Acabou. Você será redirecionado o login...',
            tipo: 'warning',
            tempo: 4000
        });

        // 3. Aguarda o tempo do popup para redirecionar o navegador de forma limpa
        setTimeout(() => {
            window.location.href = '/login';
        }, 4000);

    }else if (status === 500) {
        event.preventDefault(); // Impede a tela preta padrão do Laravel
        
        mostrarPopup({
            titulo: 'Estamos Enfrentando Instabilidade',
            conteudo: 'Aguarde! O Sistema Fará a Tentativa de Recuperação.',
            tipo: 'danger',
            tempo: 6000
        });

        setTimeout(() => {
            //window.location.href = '/login';
            //route('login');
            window.location.href = route('login');
        }, 6000);        
    }
});*/


// Intercepta respostas inválidas do servidor (como expiração de sessão)
router.on('invalid', (event) => {
    const status = event.detail.response.status;
    const urlAtual = window.location.href; // Captura a tela onde o usuário estava

    // 419 = Token/Sessão expirou no Laravel
    // 401 = Usuário perdeu a autenticação
    if (status === 419 || status === 401 ) {
        event.preventDefault(); 

        // 🚀 ENVIAR LOG PARA O BACKEND VIA AXIOS
        /*axios.post('/log-frontend-erro', { status: status, url: urlAtual })
            .catch(err => console.error('Falha ao registrar log de sessão', err));*/

        sistemajs.mostrarPopup({
            titulo: 'Sessão Expirada',
            conteudo: 'Seu tempo de Sessão Acabou. Você será redirecionado para o login...',
            tipo: 'warning',
            tempo: 4000
        });

        setTimeout(() => {
            window.location.href = router('login');
        }, 4000);

    } else if (status === 500) {
        event.preventDefault(); 
        
        // 🚀 ENVIAR LOG PARA O BACKEND VIA AXIOS
        /*axios.post('/log-frontend-erro', { status: status, url: urlAtual })
            .catch(err => console.error('Falha ao registrar log de erro 500', err));*/

        sistemajs.mostrarPopup({
            titulo: 'Estamos Enfrentando Instabilidade',
            conteudo: 'Aguarde! O Sistema Fará a Tentativa de Recuperação.',
            tipo: 'danger',
            tempo: 6000
        });

        setTimeout(() => {
            window.location.href = router('login');
        }, 6000);        
    }
});



createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    // 1. Criamos a instância do App e guardamos na variável 'app'
    const app = createApp({ render: () => h(App, props) });

    // 2. Capturamos a URL global com segurança
    const globalAppUrl = props.initialPage?.props?.app_url || window.location.origin;

    // 3. Injetamos as propriedades globais na variável 'app' que agora existe
    app.config.globalProperties.$appUrl = globalAppUrl;
    app.provide('appUrl', globalAppUrl);  
    
    const ziggyConfig = props.initialPage?.props?.ziggy;
    // 4. Ativamos os plugins e montamos o app no HTML
    return app
        .use(plugin)
        .use(ZiggyVue, ziggyConfig)    
        .use(FloatingVue)
        .mount(el);
  },
});