import './bootstrap';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

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
        // .use(ZiggyVue)     <-- Se der erro neles, certifique-se de que estão importados no topo do arquivo
        // .use(FloatingVue)  <-- Se der erro neles, certifique-se de que estão importados no topo do arquivo
        .mount(el);
  },
})