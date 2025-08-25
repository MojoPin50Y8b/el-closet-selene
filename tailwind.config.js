import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                gondola: "#1b1116",
                razzmatazz: "#df0f7b",
                alabaster: "#fbfbfb",
                "oslo-gray": "#9a9b9c",
                "silver-sand": "#c8cacb",
                "pink-swan": "#bfb1ba",
                lola: "#dfd0d9",
                "persian-rose": "#fc249c",
                "friar-gray": "#7c7c74",
                "mist-gray": "#bcbdb4",
            },
        },
    },

    plugins: [forms],
};
