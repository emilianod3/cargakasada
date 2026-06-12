<script setup>

const textoFooter = import.meta.env.VITE_FOOTER;
</script>

<template>
    <footer class="bg-layout-painel/30 border-t border-comum p-4 text-center text-xs text-texto-claro/90 transition-colors duration-300">
        <p v-html="textoFooter"></p>
    </footer>
</template>