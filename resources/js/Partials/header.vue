<script setup>
import { Link, Head, router, usePage } from '@inertiajs/vue3';
import { jssistema, mostrarPopupDecisao, mostrarPopup, limitaTexto2 } from '@/sistema';
import { inject } from 'vue';

const { sidebarAberta, userMenuAberto, alternarSidebarPrincipal } = jssistema();
const page1 = usePage();
const appUrl1 = inject('appUrl');
const logoEmpresa = import.meta.env.VITE_COMPANY_LOGO || 'logo_text';
const nameapp = import.meta.env.VITE_APP_NAME || '';

async function prelogout(){
    page1.props.app_debug ? console.log('Clicado para Logout') : '';
    const opcaoClicada = await mostrarPopupDecisao({
        titulo: 'Confirmar Saída do Sistema',
        conteudo: 'Deseja Prosseguir',
        tipo: 'warning',
        bloquearCliqueFora: true, // Força a interação com os botões
        exibirNao: false,
        exibirCancelar: true,
        textoSim: 'SIM',
        textoNao: 'NÃO',
        textoCancelar: 'CANCELAR'
    });

    if (opcaoClicada === 'sim') {
        logout();
    } else if (opcaoClicada === 'nao') {
        //minhaFuncaoParaDescartar();
    } else {
        //console.log("Operação cancelada pelo usuário.");
    }
}

function logout(){
    mostrarPopup({
        titulo: 'Até logo!',
        conteudo: 'Saindo do sistema de forma segura...',
        tipo: 'info',
        tempo: 2000
    });

    // Dispara a requisição de logout para o Laravel após um micro-tempo
    /*setTimeout(() => {
        console.log('deslogando');
        router.post(route('logout'), {}, {
            onSuccess: () => {
                console.log('deslogando sucesso');
                window.location.href = route('login'); 
                console.log('deslogando sucesso redirecionado');
            },
            onError: (errors) => {
                console.error('Erro ao tentar deslogar:', errors);
            }
        });*/

    setTimeout(() => {
        router.get(route('logout'), {}, {
            forceFormData: false,
            preserveState: false, // CRUCIAL: Reseta o estado do Vue ao deslogar
            preserveScroll: false,
            onSuccess: () => {
                window.location.href = route('login'); 
            },
            onError: (errors) => {
                console.error('Falha no Processo de Logout:', errors);
            }
        }); 
    }, 1500);
}





</script>

<template>
    <Head>
        <title>{{ nameapp }}</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link class="bg-primary" rel="icon" type="image/x-icon" :href="`${appUrl1}/build/assets/images/company/${logoEmpresa}_icon.png`" />
        <link rel="shortcut icon" type="image/x-icon" :href="`${appUrl1}/build/assets/images/company/${logoEmpresa}_icon.ico`" />

    </Head>

    <header class="bg-layout-painel border-b border-comum p-4 shadow-md z-40 relative transition-colors duration-300">
        <div class="flex justify-between items-center w-full">
            
            <div class="flex items-center gap-4 transition-all duration-300 ease-in-out" :class="sidebarAberta ? 'w-45' : 'w-16'">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="bg-primary/10 p-2.5 rounded-lg text-primary border border-primary/20 shadow-lg flex items-center justify-center w-10 h-10 flex-shrink-0">
                        <i class="fas fa-bolt text-xl"></i>
                    </div>
                    <h1 class="text-xl font-bold text-primary tracking-wide whitespace-nowrap transition-opacity duration-200"
                        :class="sidebarAberta ? 'opacity-100' : 'opacity-0 w-0 pointer-events-none'">{{ nameapp }}
                    </h1>
                </div>
            </div>

            <div class="flex flex-1 justify-between items-center pl-4">
                
                <button @click="alternarSidebarPrincipal" class="p-2 rounded-lg bg-texto-claro/5 hover:bg-texto-claro/10 text-texto-claro/70 hover:text-texto-claro transition-colors border border-comum flex items-center justify-center w-9 h-9 cursor-pointer">
                    <i :class="sidebarAberta ? 'fas fa-times text-sm' : 'fas fa-bars text-sm'" class="transition-all duration-200"></i>
                </button>

                <div class="relative mr-4">
                    <button @click="userMenuAberto = !userMenuAberto" 
                            class="flex items-center gap-3 hover:bg-texto-claro/5 p-2 rounded-lg transition-all cursor-pointer border border-transparent hover:border-comum z-50 relative">
                        
                        <div class="w-8 h-8 rounded-full bg-texto-claro/5 flex items-center justify-center text-sm font-semibold border border-comum shadow-inner">
                            <i class="fas fa-user text-xs text-texto-claro/70"></i>
                        </div>
                        <span class="text-sm text-texto-claro/80 font-medium hidden md:block">Olá, {{ limitaTexto2((page1.props.auth?.user?.unico?.unapelido?.length <= 0 ? (page1.props.auth?.user?.unico?.unidentificacao ?? 'Usuário') : page1.props.auth?.user?.unico?.unapelido), 2)}}</span>
                        <i class="fas fa-chevron-down text-[10px] text-texto-claro/40 transition-transform" :class="userMenuAberto ? 'rotate-180' : ''"></i>
                    </button>

                    <div v-if="userMenuAberto" 
                         class="absolute right-0 mt-2 w-48 bg-layout-painel border border-comum rounded-xl shadow-2xl z-50 overflow-hidden backdrop-blur-md">
                        
                        <div class="p-3 border-b border-comum bg-texto-claro/[0.02]">
                            <p class="text-[10px] text-texto-claro/40 uppercase font-bold tracking-widest">Minha Conta</p>
                        </div>
                        
                        <Link href="/perfil" class="flex items-center gap-3 px-4 py-3 text-sm text-texto-claro/80 hover:bg-primary hover:text-texto-escuro font-medium transition-colors">
                            <i class="fas fa-id-card w-4"></i> Meu Perfil
                        </Link>
                        
                        <Link @click.prevent="prelogout" href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-500 hover:text-white font-medium transition-colors border-t border-comum">
                            <i class="fas fa-power-off w-4"></i> Sair do Sistema
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </header>
</template>