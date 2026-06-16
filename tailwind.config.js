import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import scrollbar from 'tailwind-scrollbar';

export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["OpenSans", ...defaultTheme.fontFamily.sans],
                header: ["Gwendolyn", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                hearthlight: {
                    DEFAULT: '#bf8028',
                    deep: '#8b5c18',
                    subtle: '#fdf3e0',
                },
                'album-rose': '#c47070',
                inkwell: '#1c1410',
                'faded-ink': '#584038',
                'old-binding': '#8c7468',
                'aged-edge': '#d4c2b4',
            },
        },
    },

    plugins: [
        forms,
        typography,
        scrollbar,
    ],
};
