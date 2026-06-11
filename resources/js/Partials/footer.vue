<script setup>
import { usePage } from '@inertiajs/vue3';

const company = import.meta.env.VITE_COMPANY || '';
const page = usePage();

</script>


<template>
    <footer class="bg-layout-painel/30 border-t border-comum p-4 text-center text-xs text-texto-claro/90 transition-colors duration-300">
        <p>&copy; 2026 {{ company }} - Todos os direitos reservados. Versão Beta: {{ $page.props.sistema.versao }}.{{ $page.props.sistema.hash }}</p>
    </footer>
</template>