import './bootstrap';
import { createApp, h } from 'vue'
import { createInertiaApp, router } from '@inertiajs/vue3'
//import { ZiggyVue } from 'ziggy-js';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import FloatingVue from 'floating-vue';
import 'floating-vue/dist/style.css';

import '../css/app.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
//import { varloading, mostrarPopup, mostrarPopupDecisao } from '@/sistema.js';
import * as jssistema from '@/sistema.js';

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
    
    // 4. Ativamos os plugins e montamos o app no HTML
    return app
        .use(plugin)
        .use(ZiggyVue)    
        .use(FloatingVue)
        .mount(el);
  },
})