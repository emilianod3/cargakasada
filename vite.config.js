import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import fs from 'fs-extra';

export default defineConfig({
    base: '',    
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
        /*vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),*/        
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    build: {
        outDir: 'public/build', // Diretório de saída
        rollupOptions: {
            output: {
                //assetFileNames: 'assets/[name].[ext]',
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]'
            },
            plugins: [
                {
                    name: 'copy-assets',
                    writeBundle() {
                        try {
                            const sourceDirectory = path.resolve(__dirname, 'resources/assets/images');
                            const destinationDirectory = path.resolve(__dirname, 'public/build/assets/images');

                            /* const sourceDirectoryIcons = path.resolve(__dirname, 'resources/assets/icons');
                            const destinationDirectoryIcons = path.resolve(__dirname, 'public/build/assets/icons');

                            const sourceDirectoryTheme = path.resolve(__dirname, 'resources/theme1');
                            const destinationDirectoryTheme = path.resolve(__dirname, 'public/build/theme1');

                            const sourceDirectoryTheme2 = path.resolve(__dirname, 'resources/themeelite');
                            const destinationDirectoryTheme2 = path.resolve(__dirname, 'public/build/themeelite');                            

                            const sourceDirectoryJS = path.resolve(__dirname, 'resources/js');
                            const destinationDirectoryJS = path.resolve(__dirname, 'public/build/assets/js');

                            const sourceDirectoryTiny = path.resolve(__dirname, 'node_modules/tinymce');
                            const destinationDirectoryTiny = path.resolve(__dirname, 'public/tinymce');

                            const sourceDirectoryVite = path.resolve(__dirname, 'public/build/.vite');
                            const destinationDirectoryVite = path.resolve(__dirname, 'public/build');*/

                            // Utilizando fs-extra para copiar diretórios
                            fs.copy(sourceDirectory, destinationDirectory, (err) => {
                                if (err) {
                                    console.error(err);
                                } else {
                                    console.log('Diretório Imagens Copiado com Sucesso no Build!');
                                }
                            });

                            /*fs.copy(sourceDirectoryIcons, destinationDirectoryIcons, (err) => {
                                if (err) {
                                    console.error(err);
                                } else {
                                    console.log('Diretório Icons Copiado com Sucesso!');
                                }
                            });

                            fs.copy(sourceDirectoryTheme, destinationDirectoryTheme, (err) => {
                                if (err) {
                                    console.error(err);
                                } else {
                                    console.log('Diretório de Theme Copiado!');
                                }
                            });

                            fs.copy(sourceDirectoryTheme2, destinationDirectoryTheme2, (err) => {
                                if (err) {
                                    console.error(err);
                                } else {
                                    console.log('Diretório de Themeelite Copiado!');
                                }
                            });                            

                            fs.copy(sourceDirectoryJS, destinationDirectoryJS, (err) => {
                                if (err) {
                                    console.error(err);
                                } else {
                                    console.log('Diretório de JS Copiado!');
                                }
                            });*/

                            /*
                            fs.copy(sourceDirectoryTiny, destinationDirectoryTiny, (err) => {
                                if (err) {
                                    console.error(err);
                                } else {
                                    console.log('Diretório de Tinymce Copiado!');
                                }
                            });*/


                            /*
                            fs.copy(sourceDirectoryVite, destinationDirectoryVite, (err) => {
                                if (err) {
                                    console.error(err);
                                } else {
                                    console.log('Vite Manifest Copiado!');
                                }
                            });*/
                        } catch (err) {
                            console.error('Erro ao copiar arquivos no build:', err);
                        }
                    },
                },
            ],
        },
    },    
});
