<template>
    <div style="padding: 40px; font-family: sans-serif;">
        <h1>Olá, {{ nome }}! 🚀</h1>
        <p>Seu Laravel 12 + Inertia + Vue 3 sem Auth está rodando perfeitamente!</p>

        <footer style="margin-top: 50px; color: #666; font-size: 0.9em;">
            Laravel v{{ $page.props.versions.laravel_version }} (PHP v{{ $page.props.versions.php_version }})
        </footer>        
    </div>
</template>

<script setup>
defineProps({
    nome: String
})
</script>