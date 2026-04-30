import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                // Primary Action Color
                indigo: {
                    50: '#EEF2F9',
                    100: '#BFCEE8',
                    200: '#94AEDB',
                    300: '#688FCE',
                    400: '#4373C3',
                    500: '#2A5298',
                    600: '#1B3A6B', // Police Navy (Primary)
                    700: '#142D54',
                    800: '#0F223F',
                    900: '#0A172A',
                },
                blue: {
                    50: '#EEF2F9',
                    100: '#BFCEE8',
                    200: '#94AEDB',
                    300: '#688FCE',
                    400: '#4373C3',
                    500: '#2A5298',
                    600: '#1B3A6B', // Police Navy (Primary)
                    700: '#142D54',
                    800: '#0F223F',
                    900: '#0A172A',
                },
                // Dark Police Navy Palette for Backgrounds and Cards
                gray: {
                    50: '#F0F4F8',
                    100: '#D9E2EC',
                    200: '#BCCCDC',
                    300: '#9FB3C8',
                    400: '#829AB1',
                    500: '#627D98',
                    600: '#486581',
                    700: '#2A4365', // Lighter Border
                    800: '#162D5A', // Cards Background
                    900: '#0B1B3D', // App Background
                    950: '#050B16', // Sidebar / Deepest Background
                },
                slate: {
                    50: '#F0F4F8',
                    100: '#D9E2EC',
                    200: '#BCCCDC',
                    300: '#9FB3C8',
                    400: '#829AB1',
                    500: '#627D98',
                    600: '#486581',
                    700: '#2A4365',
                    800: '#162D5A',
                    900: '#0B1B3D',
                    950: '#050B16',
                },
                gold: {
                    50: '#FDF6E8',
                    400: '#F0D49A',
                    500: '#C8952A',
                    600: '#A87820',
                    700: '#8A5D13',
                }
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
