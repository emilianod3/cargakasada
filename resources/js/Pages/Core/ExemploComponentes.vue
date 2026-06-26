<script setup>
import { ref, reactive } from 'vue';
import { Link } from '@inertiajs/vue3'; // 🌟 CORREÇÃO: Importação necessária para o <Link> funcionar
import Layout from '@/Layouts/PainelInterno.vue'; 
import * as sistemajs from '@/sistema.js';

// Controle da aba ativa
const abaAtiva = ref('botoes');

// Contador de cliques do teste
const cliques = ref(0);

// Estado reativo para os termos
const termosAceitos = ref(false);

// Objeto reativo simulando o formulário para os testes de inputs e máscaras
const formTeste = reactive({
    nome: '',
    cpf: '',
    data_nascimento: '',
    recoveremail: '',
    cep: '',
    rg: '',
    ie: '',
    im: '',
    cnpj: '',
    apenasNumeros: '',
    telefone: '',
    dataNascimento: '',
    cartao: '',
    valor_pago: ''
});
</script>

<template>
    <Layout>
        <div class="w-full max-w-5xl mx-auto p-4 space-y-6">
            
            <div class="bg-layout-painel p-6 rounded-xl border border-comum shadow-xl transition-all duration-300">
                <h2 class="text-2xl font-bold text-primary mb-2">Laboratório de Componentes & Máscaras</h2>
                <p class="text-texto-claro/60">Utilize as abas abaixo para visualizar os elements visuais e validar as regras do arquivo global de scripts.</p>
            </div>

            <div class="flex border-b border-comum gap-2 overflow-x-auto pb-1">
                <button 
                    @click="abaAtiva = 'botoes'" 
                    :class="['px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors cursor-pointer whitespace-nowrap', abaAtiva === 'botoes' ? 'bg-primary text-texto-escuro' : 'bg-layout-painel text-texto-claro/60 hover:text-texto-claro']">
                    <i class="fas fa-toggle-on mr-2"></i> Botões & Ações
                </button>
                <button 
                    @click="abaAtiva = 'inputs'" 
                    :class="['px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors cursor-pointer whitespace-nowrap', abaAtiva === 'inputs' ? 'bg-primary text-texto-escuro' : 'bg-layout-painel text-texto-claro/60 hover:text-texto-claro']">
                    <i class="fas fa-keyboard mr-2"></i> Inputs & Máscaras
                </button>
                <button 
                    @click="abaAtiva = 'formatacoes'" 
                    :class="['px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors cursor-pointer whitespace-nowrap', abaAtiva === 'formatacoes' ? 'bg-primary text-texto-escuro' : 'bg-layout-painel text-texto-claro/60 hover:text-texto-claro']">
                    <i class="fas fa-text-width mr-2"></i> Formatações & LGPD
                </button>
                <button 
                    @click="abaAtiva = 'checkboxes'" 
                    :class="['px-4 py-2 text-sm font-semibold rounded-t-lg transition-colors cursor-pointer whitespace-nowrap', abaAtiva === 'checkboxes' ? 'bg-primary text-texto-escuro' : 'bg-layout-painel text-texto-claro/60 hover:text-texto-claro']">
                    <i class="fas fa-check-square mr-2"></i> Checkboxes & Seleções
                </button>
            </div>

            <div v-if="abaAtiva === 'botoes'" class="space-y-6 animate-fadeIn">
                <div class="bg-layout-painel p-6 rounded-xl border border-comum text-center shadow-md">
                    <p class="text-texto-claro/60 mb-4">Teste básico de reatividade de clique:</p>
                    <button @click="cliques++" class="bg-primary hover:bg-primary-hover text-texto-escuro font-bold px-5 py-2.5 rounded-lg transition-colors cursor-pointer shadow-md">
                        Contador de Cliques: {{ cliques }}
                    </button>
                </div>

                <div class="bg-layout-painel p-6 rounded-xl border border-comum shadow-md">
                    <h3 class="text-sm font-bold text-texto-claro/40 uppercase tracking-wider mb-4">Paleta de Cores Básicas (Sólidas)</h3>
                    <div class="flex flex-wrap gap-3 justify-center">
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
                </div>

                <div class="bg-layout-painel p-6 rounded-xl border border-comum shadow-md">
                    <h3 class="text-sm font-bold text-texto-claro/40 uppercase tracking-wider mb-4">Ações Contextuais com Ícones</h3>
                    <div class="flex flex-wrap gap-3 justify-center">
                        <button class="btn btn-purple"><i class="fas fa-check mr-2"></i>Confirmar</button>
                        <button class="btn btn-red"><i class="fas fa-trash-alt mr-2"></i>Excluir</button>
                        <button class="btn btn-green"><i class="fas fa-plus mr-2"></i>Adicionar</button>
                        <button class="btn btn-yellow"><i class="fas fa-exclamation-triangle mr-2"></i>Atenção</button>
                        <button class="btn btn-blue"><i class="fas fa-edit mr-2"></i>Editar</button>
                        <button class="btn btn-cyan"><i class="fas fa-info-circle mr-2"></i>Info</button>
                        <button class="btn btn-black"><i class="fas fa-cog mr-2"></i>Ajustes</button>
                        <button class="btn btn-gray"><i class="fas fa-arrow-left mr-2"></i>Voltar</button>
                        <button class="btn btn-white"><i class="fas fa-search mr-2"></i>Buscar</button>
                    </div>
                </div>

                <div class="bg-layout-painel p-6 rounded-xl border border-comum shadow-md">
                    <h3 class="text-sm font-bold text-texto-claro/40 uppercase tracking-wider mb-4">Grupos Combinados (Btn-Group)</h3>
                    <div class="flex flex-col gap-4 items-center">
                        <div class="btn-group">
                            <button class="btn btn-blue"><i class="fas fa-edit mr-2"></i>Editar</button>
                            <button class="btn btn-purple"><i class="fas fa-cog mr-2"></i>Ajustes</button>
                            <button class="btn btn-red"><i class="fas fa-trash-alt mr-2"></i>Excluir</button>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-blue"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-blue"><i class="fas fa-cog"></i></button>
                            <button class="btn btn-blue"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </div>
                </div>

                <div class="bg-layout-painel p-6 rounded-xl border border-comum shadow-md">
                    <h3 class="text-sm font-bold text-texto-claro/40 uppercase tracking-wider mb-4">Botões Circulares (Icon Only)</h3>
                    <div class="flex flex-wrap gap-3 justify-center">
                        <button class="btn-circle btn-purple" title="Confirmar"><i class="fas fa-check"></i></button>
                        <button class="btn-circle btn-red" title="Excluir"><i class="fas fa-trash-alt"></i></button>
                        <button class="btn-circle btn-green" title="Adicionar"><i class="fas fa-plus"></i></button>
                        <button class="btn-circle btn-blue" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn-circle btn-gray" title="Voltar"><i class="fas fa-arrow-left"></i></button>
                        <button class="btn-circle btn-white" title="Buscar"><i class="fas fa-search"></i></button>
                        <button class="btn-circle btn-black" title="Configurar"><i class="fas fa-cog"></i></button>
                        <button class="btn-circle btn-yellow" title="Alerta"><i class="fas fa-exclamation"></i></button>
                    </div>
                </div>

                <div class="bg-layout-painel p-6 rounded-xl border border-comum shadow-md">
                    <h3 class="text-sm font-bold text-texto-claro/40 uppercase tracking-wider mb-4">Efeitos Avançados e Dinâmicos</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 justify-items-center items-center">
                        
                        <button class="relative group overflow-hidden rounded-lg p-0.5 focus:outline-none cursor-pointer">
                            <span class="absolute inset-0 bg-[conic-gradient(from_90deg_at_50%_50%,#e2e8f0_0%,#3b82f6_50%,#e2e8f0_100%)] animate-[spin_3s_linear_infinite] group-hover:animate-[spin_1.5s_linear_infinite]"></span>
                            <div class="relative bg-gray-950 text-white rounded-[6px] px-6 py-2.5 text-sm font-medium transition-all duration-200 group-hover:bg-gray-900">
                                Borda Rotativa
                            </div>
                        </button>

                        <button class="relative px-6 py-2.5 text-white bg-gray-900 rounded-md font-medium overflow-hidden group cursor-pointer">
                            <span class="absolute top-0 left-0 w-full h-[2px] bg-gradient-to-r from-transparent via-primary to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-out"></span>
                            <span class="absolute bottom-0 right-0 w-full h-[2px] bg-gradient-to-r from-transparent via-primary to-transparent translate-x-full group-hover:-translate-x-full transition-transform duration-1000 ease-out"></span>
                            <span class="absolute top-0 right-0 w-[2px] h-full bg-gradient-to-b from-transparent via-primary to-transparent -translate-y-full group-hover:translate-y-full transition-transform duration-1000 ease-out delay-150"></span>
                            <span class="absolute bottom-0 left-0 w-[2px] h-full bg-gradient-to-b from-transparent via-primary to-transparent translate-y-full group-hover:-translate-y-full transition-transform duration-1000 ease-out delay-150"></span>
                            <span class="relative z-10 flex items-center gap-2">
                                <i class="fas fa-magic text-primary"></i> Efeito Feixe de Luz
                            </span>
                        </button>

                        <div class="relative inline-flex group">
                            <div class="absolute transition-all duration-1000 opacity-70 -inset-px bg-gradient-to-r from-purple-600 to-primary rounded-xl blur-lg group-hover:opacity-100 group-hover:-inset-1 group-hover:duration-200 animate-pulse"></div>
                            <button class="relative inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-gray-950 rounded-xl focus:outline-none cursor-pointer">
                                Botão Pulsante
                            </button>
                        </div>  

                        <button class="relative group overflow-hidden w-12 h-12 rounded-full p-0.5 focus:outline-none flex items-center justify-center cursor-pointer">
                            <span class="absolute inset-0 bg-[conic-gradient(from_90deg_at_50%_50%,#e2e8f0_0%,#3b82f6_50%,#e2e8f0_100%)] animate-[spin_3s_linear_infinite] group-hover:animate-[spin_1.5s_linear_infinite] rounded-full"></span>
                            <div class="relative w-full h-full bg-gray-950 text-white rounded-full flex items-center justify-center transition-all duration-200 group-hover:bg-gray-900">
                                <i class="fas fa-plus text-sm"></i>
                            </div>
                        </button> 

                        <button class="relative w-12 h-12 text-white bg-gray-900 rounded-full flex items-center justify-center overflow-hidden group border border-gray-800 cursor-pointer">
                            <span class="absolute top-0 left-0 w-full h-full bg-gradient-to-tr from-transparent via-primary to-transparent -translate-x-full -translate-y-full group-hover:translate-x-full group-hover:translate-y-full transition-transform duration-700 ease-out"></span>
                            <span class="relative z-10 flex items-center justify-center">
                                <i class="fas fa-bell text-gray-300 group-hover:text-primary transition-colors duration-200"></i>
                            </span>
                        </button>

                        <div class="relative inline-flex group">
                            <div class="absolute transition-all duration-1000 opacity-70 inset-0 bg-gradient-to-r from-purple-600 to-primary rounded-full blur-md group-hover:opacity-100 group-hover:scale-110 group-hover:duration-200 animate-pulse "></div>
                            <button class="relative w-14 h-14 flex items-center justify-center text-white bg-gray-950 rounded-full focus:outline-none cursor-pointer">
                                <i class="fas fa-comment-dots text-lg text-primary"></i>
                            </button>
                        </div>

                        <button class="relative group overflow-hidden rounded-full p-0.5 focus:outline-none cursor-pointer">
                            <span class="absolute inset-0 bg-[conic-gradient(from_90deg_at_50%_50%,#e2e8f0_0%,#3b82f6_50%,#e2e8f0_100%)] animate-[spin_4s_linear_infinite] rounded-full"></span>
                            <div class="relative bg-gray-950 text-white rounded-full px-6 py-2.5 text-sm font-medium flex items-center gap-2">
                                <i class="fas fa-check"></i> <span>Salvar Registro</span>
                            </div>
                        </button> 

                        <button class="relative flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-primary text-primary font-bold rounded-lg overflow-hidden group transition-colors duration-300 ease-in-out hover:text-white focus:outline-none cursor-pointer">
                            <span class="absolute inset-0 w-full h-full bg-primary -translate-x-full group-hover:translate-x-0 transition-transform duration-300 ease-out z-0"></span>
                            <i class="fas fa-arrow-right relative z-10 group-hover:translate-x-1 transition-transform duration-200"></i>
                            <span class="relative z-10">Avançar Sistema</span>
                        </button> 

                        <button class="flex items-center gap-2 px-5 py-2.5 bg-transparent border border-gray-700 text-gray-300 font-medium rounded-lg hover:border-primary hover:bg-primary hover:text-white hover:shadow-[0_0_15px_rgba(59,130,246,0.4)] transition-all duration-200 focus:outline-none group cursor-pointer">
                            <i class="fas fa-cog text-gray-500 group-hover:text-white transition-colors duration-200"></i>
                            <span>Configurações</span>
                        </button> 
                    </div>
                </div>

                <div class="bg-layout-painel p-6 rounded-xl border border-comum shadow-md">
                    <h3 class="text-sm font-bold text-texto-claro/40 uppercase tracking-wider mb-4">Botões Estilo Pill</h3>
                    <div class="flex flex-wrap gap-3 justify-center">
                        <button class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-blue-600 text-blue-600 font-medium rounded-full hover:bg-blue-600 hover:text-white transition-all duration-300 ease-in-out cursor-pointer">
                            <i class="fas fa-plus"></i> <span>Novo Cadastro</span>
                        </button>
                        <button class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-green-600 text-green-600 font-medium rounded-full hover:bg-green-600 hover:text-white transition-all duration-300 ease-in-out cursor-pointer">
                            <i class="fas fa-check"></i> <span>Aprovar Item</span>
                        </button>     
                        <button class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-red-600 text-red-600 font-medium rounded-full hover:bg-red-600 hover:text-white transition-all duration-300 ease-in-out cursor-pointer">
                            <i class="fas fa-trash-alt"></i> <span>Excluir Registro</span>
                        </button>
                        <button class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-amber-500 text-amber-500 font-medium rounded-full hover:bg-amber-500 hover:text-white transition-all duration-300 ease-in-out cursor-pointer">
                            <i class="fas fa-edit"></i> <span>Editar Dados</span>
                        </button>
                        <button class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-slate-600 text-slate-600 font-medium rounded-full hover:bg-slate-600 hover:text-white transition-all duration-300 ease-in-out cursor-pointer">
                            <i class="fas fa-arrow-left"></i> <span>Voltar Tela</span>
                        </button>

                        <button class="flex items-center gap-2 px-6 py-2.5 bg-transparent border-2 border-slate-600 text-slate-600 font-medium rounded-full hover:bg-slate-600 hover:text-white transition-all duration-300 ease-in-out cursor-pointer">
                            <i class="fa fa-cog"></i> <span>ExemploIcone</span>
                        </button>
                    </div>
                </div>


                <div class="bg-layout-painel p-6 rounded-xl border border-comum shadow-md">
                    <h3 class="text-sm font-bold text-texto-claro/40 uppercase tracking-wider mb-4">Group Componentes</h3>
                    <div class="flex items-center w-full box-border">
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white h-10 px-3.5 rounded-l-lg border border-blue-600 border-r-blue-500/30 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white h-10 px-3.5 border border-blue-600 border-r-blue-500/30 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none">
                            <i class="fas fa-cog text-sm"></i>
                        </button>

                        <input 
                            type="text" 
                            placeholder="Pesquisar..." 
                            class="flex-1 w-full h-10 px-3 border border-comum bg-layout-fundo text-texto-claro text-sm placeholder-texto-claro/40 focus:outline-none focus:border-primary transition-all rounded-none box-border"
                        />

                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white h-10 px-3.5 border border-blue-600 border-r-blue-500/30 transition-all cursor-pointer flex items-center justify-center shrink-0 focus:outline-none">
                            <i class="fas fa-trash-alt text-sm"></i> <label>Excluir - </label> Teste
                        </button>

                        <div class="relative inline-block h-10 shrink-0">
                            <select class="appearance-none bg-blue-600 hover:bg-blue-700 text-white h-full pl-4 pr-9 rounded-r-lg font-medium text-sm border border-blue-600 transition-all cursor-pointer focus:outline-none focus:border-primary box-border">
                                <option value="1" class="bg-layout-painel text-texto-claro">Opção 1</option>
                                <option value="2" class="bg-layout-painel text-texto-claro">Opção 2</option>
                                <option value="3" class="bg-layout-painel text-texto-claro">Opção 3</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-white/70">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div v-if="abaAtiva === 'inputs'" class="bg-layout-painel p-6 rounded-xl border border-comum shadow-xl space-y-6 animate-fadeIn">
                <div class="flex items-center justify-between border-b border-comum pb-3">
                    <h3 class="text-lg font-bold text-primary">Formulário Informativo de Validações</h3>
                    <div class="text-right text-xs font-semibold dadorequerido">* Campos Obrigatórios</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">Nome Cadastrado <span class="dadorequerido">*</span></label>
                        <input v-model="formTeste.nome" type="text" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">E-mail (Validação Dinâmica após 4 chars) <span class="dadorequerido">*</span></label>
                        <input 
                            v-model="formTeste.recoveremail" 
                            type="email" 
                            placeholder="nome@dominio.com" 
                            :class="[
                                'w-full p-2.5 rounded-lg border bg-layout-fundo text-texto-claro text-sm transition-all focus:outline-none focus:ring-1',
                                (!sistemajs.validacaoEmail(formTeste.recoveremail)) ? 'dadoinvalido' : 'border-comum focus:border-primary focus:ring-primary'
                            ]"
                        />
                        <p v-if="!sistemajs.validacaoEmail(formTeste.recoveremail)" class="text-xs dadoinvalido-texto mt-1 font-medium">
                            Por favor, insira um endereço de e-mail válido.
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">CPF <span class="dadorequerido">*</span></label>
                        <input 
                            :value="formTeste.cpf" 
                            @input="sistemajs.aplicarMascaraCPF($event.target.value, (val) => { formTeste.cpf = val; $event.target.value = val; })" 
                            type="text" maxlength="14" placeholder="000.000.000-00" 
                            :class="['w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all',
                            (formTeste.cpf && !sistemajs.TestaCPF(formTeste.cpf))
                                ? 'dadoinvalido' 
                                : 'border-comum focus:border-primary focus:ring-primary']"/>
                        <p v-if="formTeste.cpf && !sistemajs.TestaCPF(formTeste.cpf)" class="text-xs dadoinvalido-texto mt-1 font-medium">
                            Por favor, informe um CPF válido.
                        </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">CNPJ</label>
                        <input 
                            :value="formTeste.cnpj" 
                            @input="sistemajs.aplicarMascaraCNPJ($event.target.value, (val) => { formTeste.cnpj = val; $event.target.value = val; })" 
                            type="text" maxlength="18" placeholder="00.000.000/0001-00" 
                            class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" 
                        />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">RG Padrão</label>
                        <input 
                            :value="formTeste.rg" 
                            @input="sistemajs.aplicarMascaraRG($event.target.value, (val) => formTeste.rg = val)" 
                            type="text" maxlength="12" placeholder="00.000.000-0" 
                            class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" 
                        />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">CEP</label>
                        <input 
                            :value="formTeste.cep" 
                            @input="sistemajs.aplicarMascaraCEP($event.target.value, (val) => { formTeste.cep = val;  $event.target.value = val; })" 
                            type="text" maxlength="10" placeholder="00.000-000" 
                            class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" 
                        />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">Inscrição Estadual (IE)</label>
                        <input 
                            :value="formTeste.ie" 
                            @input="sistemajs.aplicarMascaraIE($event.target.value, (val) => formTeste.ie = val)" 
                            type="text" maxlength="15" placeholder="000.000.000.000" 
                            class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" 
                        />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">Inscrição Municipal (IM)</label>
                        <input 
                            :value="formTeste.im" 
                            @input="sistemajs.aplicarMascaraIM($event.target.value, (val) => formTeste.im = val)" 
                            type="text" maxlength="13" placeholder="000.000.000-0" 
                            class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" 
                        />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm text-texto-claro/80 font-medium">Data de Nascimento <span class="dadorequerido">*</span></label>
                        <input v-model="formTeste.data_nascimento" type="date" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all" />
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-texto-claro/80">Apenas Números</label>
                        <input type="text" :value="formTeste.apenasNumeros" @input="sistemajs.soNumeros($event.target); formTeste.apenasNumeros = $event.target.value" placeholder="Digite apenas números" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all"/>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-texto-claro/80">Telefone</label>
                        <input type="text" :value="formTeste.telefone"
                        @input="sistemajs.aplicarMascaraTelefone($event.target.value, (val) => { 
                            formTeste.telefone = val; 
                            $event.target.value = val; // 🌟 Trava visual instantânea contra letras
                        })" maxlength="15" placeholder="(00) 00000-0000" class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all"/>
                    </div>   
                    
                                        
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-texto-claro/80">Data de Nascimento</label>
                        <input 
                                type="text"
                                :value="formTeste.dataNascimento"
                                @input="sistemajs.aplicarMascaraData($event.target.value, (val) => { 
                                    formTeste.dataNascimento = val; 
                                    $event.target.value = val; 
                                })" 
                                maxlength="10"
                                placeholder="DD/MM/AAAA"
                                :class="[
                                    'w-full p-2.5 rounded-lg border bg-layout-fundo text-texto-claro focus:ring-1 focus:outline-none text-sm transition-all',
                                    (formTeste.dataNascimento && !sistemajs.TestaData(formTeste.dataNascimento))
                                        ? 'dadoinvalido border-red-500 focus:border-red-500 focus:ring-red-500' 
                                        : 'border-comum focus:border-primary focus:ring-primary'
                                ]"
                            />
                            
                            <p v-if="formTeste.dataNascimento && !sistemajs.TestaData(formTeste.dataNascimento)" 
                            class="text-xs dadoinvalido-texto mt-1 font-medium text-red-500">
                                Por favor, informe uma data válida.
                            </p>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-texto-claro/80">Número do Cartão</label>
                        <input 
                            type="text"
                            :value="formTeste.cartao"
                            @input="sistemajs.aplicarMascaraCartao($event.target.value, (val) => { 
                                formTeste.cartao = val; 
                                $event.target.value = val; // 🌟 Trava de letras instantânea
                            })" 
                            maxlength="19"
                            placeholder="0000 0000 0000 0000"
                            class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all"
                        />
                    </div> 

                                        
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-medium text-texto-claro/80">Valor Monetário</label>
                        <input 
                            type="text" 
                            :value="formTeste.valor_pago" 
                            @input="sistemajs.aplicarMascaraMonetaria($event); formTeste.valor_pago = $event.target.value" 
                            placeholder="R$ 0,00"
                            class="w-full p-2.5 rounded-lg border border-comum bg-layout-fundo text-texto-claro focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none text-sm transition-all"
                        />
                    </div>
                </div>
            </div>

            <div v-if="abaAtiva === 'formatacoes'" class="bg-layout-painel p-6 rounded-xl border border-comum shadow-xl space-y-6 animate-fadeIn">
                <h3 class="text-lg font-bold text-primary border-b border-comum pb-2">Saída de Dados e Relatórios (Visualização Segura)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-900 p-5 rounded-xl border border-gray-800 space-y-3">
                        <h4 class="text-xs font-bold text-amber-400 uppercase tracking-widest">Conversão Numérica (Intl.NumberFormat)</h4>
                        <div class="space-y-1">
                            <p class="text-sm text-texto-claro">Total de Contratos Ativos: <span class="text-green-400 font-bold font-mono">{{ sistemajs.formataMilhar(15300) }}</span></p>
                            <p class="text-sm text-texto-claro">Saldo de Investimento RPPS: <span class="text-green-400 font-bold font-mono">R$ {{ sistemajs.formataMilhar(2450000.75) }}</span></p>
                            <p class="text-sm text-texto-claro">Valor Teste Zero: <span class="text-green-400 font-bold font-mono">{{ sistemajs.formataMilhar('') }}</span></p>
                        </div>
                    </div>

                    <div class="bg-gray-900 p-5 rounded-xl border border-gray-800 space-y-3">
                        <h4 class="text-xs font-bold text-amber-400 uppercase tracking-widest">Segurança de Dados (Conformidade LGPD)</h4>
                        <div class="space-y-1">
                            <p class="text-sm text-texto-claro">CPF Digitado na Aba 2: 
                                <span class="text-blue-400 font-bold font-mono bg-black/40 px-2 py-0.5 rounded">
                                    {{ formTeste.cpf ? sistemajs.cpfLGPD(formTeste.cpf) : 'Digite na outra aba...' }}
                                </span>
                            </p>
                            <p class="text-sm text-texto-claro">Exemplo Estático Mascarado: <span class="text-blue-400 font-mono">{{ sistemajs.cpfLGPD('45000772033') }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="abaAtiva === 'checkboxes'" class="bg-layout-painel p-6 rounded-xl border border-comum shadow-xl space-y-6 animate-fadeIn">
                <h3 class="text-lg font-bold text-primary border-b border-comum pb-2">Controles de Seleção</h3>
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="termos" v-model="termosAceitos" class="w-4 h-4 rounded border-comum text-primary focus:ring-primary bg-layout-fundo" />
                    <label for="termos" class="text-sm text-texto-claro select-none cursor-pointer">
                        Declaro que aceito as diretrizes de segurança de dados e políticas do sistema.
                    </label>
                </div>
                <p class="text-xs font-mono text-texto-claro/60">Estado do Checkbox: <span class="text-primary">{{ termosAceitos }}</span></p>
            </div>

        </div>
    </Layout>
</template>