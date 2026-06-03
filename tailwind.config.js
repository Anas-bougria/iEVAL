/** @type {import('tailwindcss').Config} */
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                display: ['"Fraunces"', 'ui-serif', 'Georgia', 'serif'],
                sans: ['"DM Sans"', 'ui-sans-serif', 'system-ui'],
                mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
            },
            colors: {
                ink: {
                    50:  '#f4f5f7',
                    100: '#e6e8ed',
                    200: '#c5cad4',
                    300: '#a0a8b8',
                    400: '#6f7a90',
                    500: '#4a5570',
                    600: '#363f57',
                    700: '#262d42',
                    800: '#181d2d',
                    900: '#0e1320',
                    950: '#070912',
                },
                paper: {
                    50:  '#fbf8f2',
                    100: '#f5efe3',
                    200: '#ebe1ca',
                    300: '#ddcca8',
                },
                saffron: {
                    50:  '#fff9e6',
                    100: '#fdefb8',
                    200: '#fbe07a',
                    300: '#f6c63d',
                    400: '#e9a91a',
                    500: '#c98906',
                    600: '#a06a04',
                    700: '#754d09',
                },
                clay: {
                    400: '#c97b5a',
                    500: '#b35d3d',
                    600: '#933f24',
                },
            },
            boxShadow: {
                soft: '0 1px 2px rgba(14,19,32,0.04), 0 4px 12px rgba(14,19,32,0.06)',
                edge: '0 0 0 1px rgba(14,19,32,0.06), 0 1px 2px rgba(14,19,32,0.04)',
            },
            backgroundImage: {
                'grid-paper': "linear-gradient(rgba(14,19,32,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(14,19,32,0.04) 1px, transparent 1px)",
            },
        },
    },
    plugins: [forms, typography],
};
