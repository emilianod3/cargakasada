<script setup>
import { ref } from 'vue';
import Layout from '@/Layouts/PainelInterno.vue'; 

const cliques = ref(0);
</script>

<template>
    <Layout>
        
        <div class="bg-layout-painel p-8 rounded-xl border border-comum text-center shadow-xl transition-all duration-300">
            
            <h2 class="text-2xl font-bold text-primary mb-2">Página Interna do com Menu Lateral2</h2>
            
            <p class="text-texto-claro/60 mb-6">Repare que o topo e o rodapé apareceram sozinhos!</p>
            
            <button @click="cliques++" class="bg-primary hover:bg-primary-hover text-texto-escuro font-bold px-4 py-2 rounded-lg transition-colors cursor-pointer">
                Cliques: {{ cliques }}
            </button>

        </div>
        <div class="flex flex-wrap gap-4 p-6 justify-center">
    
            <button class="btn btn-purple">Roxo</button>
            <button class="btn btn-red">Vermelho</button>
            <button class="btn btn-green">Verde</button>
            <button class="btn btn-yellow">Amarelo</button>
            <button class="btn btn-blue">Azul</button>
            <button class="btn btn-cyan">Ciano</button>
            <button class="btn btn-black">Preto</button>
            <button class="btn btn-gray">Cinza</button>
            <button class="btn btn-white">Branco</button>

        </div>

        <div class="flex flex-wrap gap-4 p-6 justify-center bg-layout-painel">
            
            <button class="btn btn-purple">
            <i class="fas fa-check  mr-2"></i>
            <span>Confirmar</span>
            </button>

            <button class="btn btn-red">
            <i class="fas fa-trash-alt mr-2"></i>
            <span>Excluir</span>
            </button>

            <button class="btn btn-green">
            <i class="fas fa-plus mr-2"></i>
            <span>Adicionar</span>
            </button>

            <button class="btn btn-yellow">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <span>Atenção</span>
            </button>

            <button class="btn btn-blue">
            <i class="fas fa-edit mr-2"></i>
            <span>Editar</span>
            </button>

            <button class="btn btn-cyan">
            <i class="fas fa-info-circle mr-2"></i>
            <span>Info</span>
            </button>

            <button class="btn btn-black">
            <i class="fas fa-cog mr-2"></i>
            <span>Ajustes</span>
            </button>

            <button class="btn btn-gray">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Voltar</span>
            </button>

            <button class="btn btn-white">
            <i class="fas fa-search mr-2"></i>
            <span>Buscar</span>
            </button>

        </div>        

        <div class="flex justify-center p-6 bg-layout-painel">
            
            <div class="btn-group">
            
            <button class="btn btn-blue">
                <i class="fas fa-edit"></i>
                <span>Editar</span>
            </button>

            <button class="btn btn-purple">
                <i class="fas fa-cog"></i>
                <span>Ajustes</span>
            </button>

            <button class="btn btn-red">
                <i class="fas fa-trash-alt"></i>
                <span>Excluir</span>
            </button>
            
            </div>

        </div>
        

        <div class="flex justify-center p-6 bg-layout-painel">
            
            <div class="btn-group">
            
            <button class="btn btn-blue">
                <i class="fas fa-edit"></i>
                <span>Editar</span>
            </button>

            <button class="btn btn-blue">
                <i class="fas fa-cog"></i>
                <span>Ajustes</span>
            </button>

            <button class="btn btn-blue">
                <i class="fas fa-trash-alt"></i>
                <span>Excluir</span>
            </button>
            
            </div>

        </div>
        
        <div class="flex flex-wrap gap-4 p-6 justify-center bg-layout-painel">
    
            <button class="btn-circle btn-purple" title="Confirmar">
            <i class="fas fa-check"></i>
            </button>

            <button class="btn-circle btn-red" title="Excluir">
            <i class="fas fa-trash-alt"></i>
            </button>

            <button class="btn-circle btn-green" title="Adicionar">
            <i class="fas fa-plus"></i>
            </button>

            <button class="btn-circle btn-blue" title="Editar">
            <i class="fas fa-edit"></i>
            </button>

            <button class="btn-circle btn-gray" title="Voltar">
            <i class="fas fa-arrow-left"></i>
            </button>

            <button class="btn-circle btn-white" title="Buscar">
            <i class="fas fa-search"></i>
            </button>

            <button class="btn-circle btn-black" title="Voltar">
            <i class="fas fa-arrow-left"></i>
            </button>
            
            <button class="btn-circle btn-yellow" title="Voltar">
            <i class="fas fa-arrow-left"></i>
            </button>

        </div>
        <div class="flex flex-wrap gap-4 p-6 justify-center bg-layout-painel">
            <button class="relative group overflow-hidden rounded-lg p-[2px] focus:outline-none">
                <span class="absolute inset-0 bg-[conic-gradient(from_90deg_at_50%_50%,#e2e8f0_0%,#3b82f6_50%,#e2e8f0_100%)] animate-[spin_3s_linear_infinite] group-hover:animate-[spin_1.5s_linear_infinite]"></span>
                
                <div class="relative bg-gray-950 text-white rounded-[6px] px-6 py-2.5 text-sm font-medium transition-all duration-200 group-hover:bg-gray-900">
                Borda Rotativa
                </div>
            </button>
        </div>
        <div class="flex flex-wrap gap-4 p-6 justify-center bg-layout-painel">                   
            <button class="relative px-6 py-2.5 text-white bg-gray-900 rounded-md font-medium overflow-hidden group">
                <span class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-primary to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-out"></span>
                <span class="absolute bottom-0 right-0 w-full h-[2px] bg-gradient-to-r from-transparent via-primary to-transparent translate-x-full group-hover:-translate-x-full transition-transform duration-1000 ease-out"></span>
                
                <span class="absolute top-0 right-0 w-[2px] h-full bg-gradient-to-b from-transparent via-primary to-transparent -translate-y-full group-hover:translate-y-full transition-transform duration-1000 ease-out delay-150"></span>
                <span class="absolute bottom-0 left-0 w-[2px] h-full bg-gradient-to-b from-transparent via-primary to-transparent translate-y-full group-hover:-translate-y-full transition-transform duration-1000 ease-out delay-150"></span>

                <span class="relative z-10 flex items-center gap-2">
                <i class="fas fa-magic text-primary"></i>
                Efeito Feixe de Luz
                </span>
            </button>

            <div class="relative inline-flex group">
                <div class="absolute transition-all duration-1000 opacity-70 -inset-px bg-gradient-to-r from-purple-600 to-primary rounded-xl blur-lg group-hover:opacity-100 group-hover:-inset-1 group-hover:duration-200 animate-pulse"></div>
            
                <button class="relative inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-gray-950 font-pj rounded-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                Botão Pulsante
                </button>
            </div>  
                <button class="relative group overflow-hidden w-12 h-12 rounded-full p-[2px] focus:outline-none flex items-center justify-center">
                <span class="absolute inset-0 bg-[conic-gradient(from_90deg_at_50%_50%,#e2e8f0_0%,#3b82f6_50%,#e2e8f0_100%)] animate-[spin_3s_linear_infinite] group-hover:animate-[spin_1.5s_linear_infinite] rounded-full"></span>
                
                <div class="relative w-full h-full bg-gray-950 text-white rounded-full flex items-center justify-center transition-all duration-200 group-hover:bg-gray-900">
                <i class="fas fa-plus text-sm"></i>
                </div>
            </button> 
            
            
            <button class="relative w-12 h-12 text-white bg-gray-900 rounded-full flex items-center justify-center overflow-hidden group border border-gray-800">
                <span class="absolute top-0 left-0 w-full h-full bg-gradient-to-tr from-transparent via-primary to-transparent -translate-x-full -translate-y-full group-hover:translate-x-full group-hover:translate-y-full transition-transform duration-700 ease-out"></span>

                <span class="relative z-10 flex items-center justify-center">
                <i class="fas fa-bell text-gray-300 group-hover:text-primary transition-colors duration-200"></i>
                </span>
            </button>

            <div class="relative inline-flex group">
                <div class="absolute transition-all duration-1000 opacity-70 inset-0 bg-gradient-to-r from-purple-600 to-primary rounded-full blur-md group-hover:opacity-100 group-hover:scale-110 group-hover:duration-200 animate-pulse"></div>
                
                <button class="relative w-14 h-14 flex items-center justify-center text-white bg-gray-950 rounded-full focus:outline-none">
                <i class="fas fa-comment-dots text-lg text-primary"></i>
                </button>
            </div>


        </div>
        <div class="flex flex-wrap gap-4 p-6 justify-center bg-layout-painel">                      
            <button class="relative group overflow-hidden rounded-full p-[2px] focus:outline-none">
            <span class="absolute inset-0 bg-[conic-gradient(from_90deg_at_50%_50%,#e2e8f0_0%,#3b82f6_50%,#e2e8f0_100%)] animate-[spin_4s_linear_infinite] rounded-full"></span>
            <div class="relative bg-gray-950 text-white rounded-full px-6 py-2.5 text-sm font-medium flex items-center gap-2">
                <i class="fas fa-check"></i>
                <span>Salvar Registro</span>
            </div>
            </button>  
        </div>

        <div class="flex flex-wrap gap-4 p-6 justify-center bg-layout-painel">                      
            <button class="flex items-center gap-2 px-5 py-2.5 bg-transparent border-2 border-primary text-primary font-medium rounded-lg hover:bg-primary hover:text-white transition-all duration-300 ease-in-out focus:outline-none">
                <i class="fas fa-save"></i>
                <span>Salvar Alterações</span>
            </button>
            <button class="relative flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-primary text-primary font-bold rounded-lg overflow-hidden group transition-colors duration-300 ease-in-out hover:text-white focus:outline-none">
                
                <span class="absolute inset-0 w-full h-full bg-primary -translate-x-full group-hover:translate-x-0 transition-transform duration-300 ease-out z-0"></span>
                
                <i class="fas fa-arrow-right relative z-10 group-hover:translate-x-1 transition-transform duration-200"></i>
                <span class="relative z-10">Avançar Sistema</span>
            </button>     
            <button class="flex items-center gap-2 px-5 py-2.5 bg-transparent border border-gray-700 text-gray-300 font-medium rounded-lg hover:border-primary hover:bg-primary hover:text-white hover:shadow-[0_0_15px_rgba(59,130,246,0.4)] transition-all duration-200 focus:outline-none group">
                
                <i class="fas fa-cog text-gray-500 group-hover:text-white transition-colors duration-200"></i>
                <span>Configurações</span>
            </button>         
        </div>

        <div class="flex flex-wrap gap-4 p-6 justify-center bg-layout-painel">     
            <button class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-blue-600 text-blue-600 font-medium rounded-full hover:bg-blue-600 hover:text-white transition-all duration-300 ease-in-out focus:outline-none">
            <i class="fas fa-plus"></i>
            <span>Novo Cadastro</span>
            </button>

            <button class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-green-600 text-green-600 font-medium rounded-full hover:bg-green-600 hover:text-white transition-all duration-300 ease-in-out focus:outline-none">
            <i class="fas fa-check"></i>
            <span>Aprovar Item</span>
            </button>   

            <button class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-red-600 text-red-600 font-medium rounded-full hover:bg-red-600 hover:text-white transition-all duration-300 ease-in-out focus:outline-none">
            <i class="fas fa-trash-alt"></i>
            <span>Excluir Registro</span>
            </button>

            <button class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-amber-500 text-amber-500 font-medium rounded-full hover:bg-amber-500 hover:text-white transition-all duration-300 ease-in-out focus:outline-none">
            <i class="fas fa-edit"></i>
            <span>Editar Dados</span>
            </button>

            <button class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-slate-600 text-slate-600 font-medium rounded-full hover:bg-slate-600 hover:text-white transition-all duration-300 ease-in-out focus:outline-none">
            <i class="fas fa-arrow-left"></i>
            <span>Voltar Tela</span>
            </button>

            <div class="neon-btn__wrapper">
                <!-- O @click emite o evento para o componente pai -->
                <button class="neon-btn" @click="$emit('click')">
                <!-- O slot permite mudar o texto dinamicamente -->
                <slot>Clique Aqui</slot>
                </button>
            </div>
                        

            <BotaoRedondo cor="red-600" icone="fas fa-trash"> Deletar </BotaoRedondo>

        </div>

    </Layout>
</template>
