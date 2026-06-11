<script setup>

import { Head } from '@inertiajs/vue3';
import { inject } from 'vue';

const nameapp = import.meta.env.VITE_APP_NAME;
const appUrl1 = inject('appUrl');
const logoEmpresa = import.meta.env.VITE_COMPANY_LOGO || 'logo_text';


</script>

<template>
    <Head>
        <title>{{ nameapp }}</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link class="bg-primary" rel="icon" type="image/x-icon" :href="`${appUrl1}/build/assets/images/company/${logoEmpresa}_icon.png`" />
        <link rel="shortcut icon" type="image/x-icon" :href="`${appUrl1}/build/assets/images/company/${logoEmpresa}_icon.ico`" />
    </Head>
    
</template>