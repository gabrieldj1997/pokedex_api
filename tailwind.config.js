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
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                'pokemon-light': ['pokemon-light', 'sans-serif'], 
                'pokemon-dark': ['pokemon-dark', 'sans-serif'],
                'flexo-demi': ['flexo_demi', 'sans-serif'],
            },
        },
    },

    plugins: [forms],
};
