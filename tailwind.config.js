const defaultTheme = require('tailwindcss/defaultTheme');
const defaultColors = require('tailwindcss/colors');

/** @type {import('tailwindcss').Config} */
module.exports = {

    // https://tailwindcss.com/docs/content-configuration
    content: [
        './templates/**/*.twig',
        './sources/app/js/**/*.js',
    ],

    // https://tailwindcss.com/docs/theme
    theme: {

        extend: {

            // https://tailwindcss.com/docs/customizing-colors
            colors: {
                gray: defaultColors.neutral,
            },

            // https://tailwindcss.com/docs/font-family
            fontFamily: {
                sans: ['Roboto', ...defaultTheme.fontFamily.sans],
            },

            // https://tailwindcss.com/docs/plugins#typography
            typography: (theme) => ({
                DEFAULT: {
                    css: {
                        color: theme('colors.gray.900'),
                        strong: {
                            color: theme('colors.black'),
                            fontWeight: '700',
                        },
                        a: {
                            color: theme('colors.indigo.500'),
                            fontWeight: 'normal',
                            textDecoration: 'underline',
                            '&:hover': {
                                color: theme('colors.indigo.700'),
                                textDecoration: 'underline',
                            },
                        },
                    },
                },
            }),

        },

    },

    // https://tailwindcss.com/docs/plugins
    plugins: [

        // https://tailwindcss.com/docs/plugins#typography
        require('@tailwindcss/typography'),

        // https://tailwindcss.com/docs/plugins#forms
        require('@tailwindcss/forms'),

        // https://tailwindcss.com/docs/plugins#aspect-ratio
        require('@tailwindcss/aspect-ratio'),

        // https://tailwindcss.com/docs/plugins#container-queries
        require('@tailwindcss/container-queries'),

    ],

};
