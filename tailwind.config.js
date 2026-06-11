import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: 'rgb(var(--cor-marca-rgb) / <alpha-value>)',
                    hover: 'rgb(var(--cor-marca-hover) / <alpha-value>)',
                },
                layout: {
                    fundo: 'rgb(var(--cor-fundo-rgb) / <alpha-value>)',
                    painel: 'rgb(var(--cor-painel-rgb) / <alpha-value>)',
                },
                texto: {
                    claro: 'rgb(var(--cor-texto-claro) / <alpha-value>)',
                    escuro: 'rgb(var(--cor-texto-escuro) / <alpha-value>)',
                }
            },
        },
    },

    plugins: [forms],
};
